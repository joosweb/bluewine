package main

import (
	"context"
	"encoding/base64"
	"encoding/json"
	"errors"
	"fmt"
	"io"
	"log"
	"net/http"
	"os"
	"os/exec"
	"path/filepath"
	"strconv"
	"strings"
	"time"
)

type appConfig struct {
	ListenAddr string
	Token      string
	Printer    string
	LPPath     string
}

type printRequest struct {
	Base64Data string `json:"base64_data"`
	Filename   string `json:"filename"`
	Copies     int    `json:"copies"`
}

type printResponse struct {
	Status string `json:"status"`
	JobID  string `json:"job_id,omitempty"`
	Error  string `json:"error,omitempty"`
}

func main() {
	loadDotEnv(".env")

	cfg := appConfig{
		ListenAddr: envOrDefault("PRINT_AGENT_LISTEN", "127.0.0.1:8765"),
		Token:      strings.TrimSpace(os.Getenv("PRINT_AGENT_TOKEN")),
		Printer:    strings.TrimSpace(os.Getenv("PRINT_AGENT_PRINTER")),
		LPPath:     envOrDefault("PRINT_AGENT_LP_PATH", "lp"),
	}

	mux := http.NewServeMux()
	mux.HandleFunc("/health", func(w http.ResponseWriter, _ *http.Request) {
		writeJSON(w, http.StatusOK, map[string]any{
			"status":  "ok",
			"printer": cfg.Printer,
		})
	})
	mux.HandleFunc("/print/pdf", cfg.auth(cfg.handlePrint(".pdf", false)))
	mux.HandleFunc("/print/raw", cfg.auth(cfg.handlePrint(".bin", true)))

	srv := &http.Server{
		Addr:              cfg.ListenAddr,
		Handler:           loggingMiddleware(mux),
		ReadHeaderTimeout: 5 * time.Second,
	}

	log.Printf("usb-print-agent listening on http://%s", cfg.ListenAddr)
	if cfg.Printer == "" {
		log.Println("PRINT_AGENT_PRINTER not set, using default system printer")
	}

	if err := srv.ListenAndServe(); err != nil && !errors.Is(err, http.ErrServerClosed) {
		log.Fatalf("server failed: %v", err)
	}
}

func (cfg appConfig) auth(next http.HandlerFunc) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		if cfg.Token == "" {
			next(w, r)
			return
		}

		got := strings.TrimSpace(r.Header.Get("X-Print-Token"))
		if got == "" || got != cfg.Token {
			writeJSON(w, http.StatusUnauthorized, printResponse{Status: "error", Error: "invalid print token"})
			return
		}
		next(w, r)
	}
}

func (cfg appConfig) handlePrint(defaultExt string, rawMode bool) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		if r.Method != http.MethodPost {
			writeJSON(w, http.StatusMethodNotAllowed, printResponse{Status: "error", Error: "method not allowed"})
			return
		}

		var req printRequest
		if err := json.NewDecoder(io.LimitReader(r.Body, 20<<20)).Decode(&req); err != nil {
			writeJSON(w, http.StatusBadRequest, printResponse{Status: "error", Error: "invalid json"})
			return
		}

		if strings.TrimSpace(req.Base64Data) == "" {
			writeJSON(w, http.StatusBadRequest, printResponse{Status: "error", Error: "base64_data is required"})
			return
		}

		payload, err := base64.StdEncoding.DecodeString(req.Base64Data)
		if err != nil {
			writeJSON(w, http.StatusBadRequest, printResponse{Status: "error", Error: "invalid base64_data"})
			return
		}

		if req.Copies <= 0 {
			req.Copies = 1
		}
		if req.Copies > 10 {
			writeJSON(w, http.StatusBadRequest, printResponse{Status: "error", Error: "copies must be between 1 and 10"})
			return
		}

		tmpFile, err := writeTempFile(payload, req.Filename, defaultExt)
		if err != nil {
			writeJSON(w, http.StatusInternalServerError, printResponse{Status: "error", Error: "failed to write temp file"})
			return
		}
		defer os.Remove(tmpFile)

		jobID, err := cfg.sendToPrinter(r.Context(), tmpFile, req.Copies, rawMode)
		if err != nil {
			writeJSON(w, http.StatusBadGateway, printResponse{Status: "error", Error: err.Error()})
			return
		}

		writeJSON(w, http.StatusOK, printResponse{Status: "ok", JobID: jobID})
	}
}

func (cfg appConfig) sendToPrinter(ctx context.Context, filePath string, copies int, rawMode bool) (string, error) {
	ctx, cancel := context.WithTimeout(ctx, 15*time.Second)
	defer cancel()

	args := []string{}
	if cfg.Printer != "" {
		args = append(args, "-d", cfg.Printer)
	}
	args = append(args, "-n", fmt.Sprintf("%d", copies))
	if rawMode {
		args = append(args, "-o", "raw")
	}
	args = append(args, filePath)

	cmd := exec.CommandContext(ctx, cfg.LPPath, args...)
	out, err := cmd.CombinedOutput()
	if err != nil {
		return "", fmt.Errorf("lp failed: %s", strings.TrimSpace(string(out)))
	}

	result := strings.TrimSpace(string(out))
	if result == "" {
		return "queued", nil
	}
	return result, nil
}

func writeTempFile(payload []byte, filename string, defaultExt string) (string, error) {
	ext := defaultExt
	if filename != "" {
		candidateExt := strings.ToLower(filepath.Ext(filename))
		if candidateExt != "" && len(candidateExt) <= 6 {
			ext = candidateExt
		}
	}

	tmpFile, err := os.CreateTemp("", "usb-print-*"+ext)
	if err != nil {
		return "", err
	}
	defer tmpFile.Close()

	if _, err := tmpFile.Write(payload); err != nil {
		return "", err
	}
	return tmpFile.Name(), nil
}

func envOrDefault(k string, fallback string) string {
	v := strings.TrimSpace(os.Getenv(k))
	if v == "" {
		return fallback
	}
	return v
}

func writeJSON(w http.ResponseWriter, code int, payload any) {
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(code)
	_ = json.NewEncoder(w).Encode(payload)
}

func loggingMiddleware(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		start := time.Now()
		next.ServeHTTP(w, r)
		log.Printf("%s %s (%s)", r.Method, r.URL.Path, time.Since(start))
	})
}

func loadDotEnv(path string) {
	content, err := os.ReadFile(path)
	if err != nil {
		return
	}

	lines := strings.Split(string(content), "\n")
	for _, rawLine := range lines {
		line := strings.TrimSpace(rawLine)
		if line == "" || strings.HasPrefix(line, "#") {
			continue
		}

		if strings.HasPrefix(line, "export ") {
			line = strings.TrimSpace(strings.TrimPrefix(line, "export "))
		}

		parts := strings.SplitN(line, "=", 2)
		if len(parts) != 2 {
			continue
		}

		key := strings.TrimSpace(parts[0])
		if key == "" {
			continue
		}

		if _, exists := os.LookupEnv(key); exists {
			continue
		}

		value := strings.TrimSpace(parts[1])
		if value == "" {
			_ = os.Setenv(key, "")
			continue
		}

		if unquoted, err := strconv.Unquote(value); err == nil {
			value = unquoted
		} else {
			value = strings.Trim(value, "\"'")
		}

		_ = os.Setenv(key, value)
	}
}
