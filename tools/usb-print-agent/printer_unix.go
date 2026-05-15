//go:build !windows

package main

import (
	"context"
	"fmt"
	"os/exec"
	"strings"
)

func (cfg appConfig) sendToPrinterOS(ctx context.Context, filePath string, copies int, rawMode bool) (string, error) {
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
