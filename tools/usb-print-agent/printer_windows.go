//go:build windows

package main

import (
	"context"
	"fmt"
	"os"
	"syscall"
	"unsafe"
)

var (
	winspool         = syscall.NewLazyDLL("winspool.drv")
	procOpenPrinter  = winspool.NewProc("OpenPrinterW")
	procClosePrinter = winspool.NewProc("ClosePrinter")
	procStartDoc     = winspool.NewProc("StartDocPrinterW")
	procStartPage    = winspool.NewProc("StartPagePrinter")
	procEndPage      = winspool.NewProc("EndPagePrinter")
	procEndDoc       = winspool.NewProc("EndDocPrinter")
	procWritePrinter = winspool.NewProc("WritePrinter")
)

type docInfo1 struct {
	DocName    *uint16
	OutputFile *uint16
	DataType   *uint16
}

func (cfg appConfig) sendToPrinterOS(_ context.Context, filePath string, copies int, _ bool) (string, error) {
	if cfg.Printer == "" {
		return "", fmt.Errorf("PRINT_AGENT_PRINTER no está definido en .env")
	}

	data, err := os.ReadFile(filePath)
	if err != nil {
		return "", fmt.Errorf("error leyendo archivo temporal: %v", err)
	}

	for i := 0; i < copies; i++ {
		if err := rawPrintWindows(cfg.Printer, data); err != nil {
			return "", err
		}
	}

	return "queued", nil
}

func rawPrintWindows(printerName string, data []byte) error {
	namePtr, err := syscall.UTF16PtrFromString(printerName)
	if err != nil {
		return fmt.Errorf("nombre de impresora inválido: %v", err)
	}

	var handle uintptr
	r, _, lastErr := procOpenPrinter.Call(
		uintptr(unsafe.Pointer(namePtr)),
		uintptr(unsafe.Pointer(&handle)),
		0,
	)
	if r == 0 {
		return fmt.Errorf("OpenPrinter(%q) falló: %v — verifica que el nombre en .env coincide exactamente con el nombre en Windows", printerName, lastErr)
	}
	defer procClosePrinter.Call(handle)

	docName, _ := syscall.UTF16PtrFromString("Bluewine RAW")
	dataType, _ := syscall.UTF16PtrFromString("RAW")
	doc := docInfo1{
		DocName:  docName,
		DataType: dataType,
	}

	r, _, lastErr = procStartDoc.Call(handle, 1, uintptr(unsafe.Pointer(&doc)))
	if r == 0 {
		return fmt.Errorf("StartDocPrinter falló: %v", lastErr)
	}
	defer procEndDoc.Call(handle)

	r, _, lastErr = procStartPage.Call(handle)
	if r == 0 {
		return fmt.Errorf("StartPagePrinter falló: %v", lastErr)
	}
	defer procEndPage.Call(handle)

	var written uint32
	r, _, lastErr = procWritePrinter.Call(
		handle,
		uintptr(unsafe.Pointer(&data[0])),
		uintptr(len(data)),
		uintptr(unsafe.Pointer(&written)),
	)
	if r == 0 {
		return fmt.Errorf("WritePrinter falló: %v", lastErr)
	}

	return nil
}
