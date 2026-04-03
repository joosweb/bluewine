# USB Print Agent (Go)

Micro servicio local para imprimir desde una maquina con impresora USB usando CUPS (`lp`).

## 1) Requisitos

- macOS con impresora instalada en el sistema
- Go 1.21+
- comando `lp` disponible

## 2) Ejecutar

Desde esta carpeta:

```bash
go run .
```

Variables opcionales:

- `PRINT_AGENT_LISTEN` (default: `127.0.0.1:8765`)
- `PRINT_AGENT_PRINTER` (nombre exacto de impresora en CUPS)
- `PRINT_AGENT_TOKEN` (token simple para auth por header)
- `PRINT_AGENT_LP_PATH` (default: `lp`)

Ejemplo:

```bash
PRINT_AGENT_PRINTER="EPSON_TM_T20" PRINT_AGENT_TOKEN="secret-local" go run .
```

## 3) Probar salud

```bash
curl http://127.0.0.1:8765/health
```

## 4) Imprimir PDF

```bash
base64 -i ./sample.pdf | tr -d '\n' > /tmp/voucher.b64
curl -X POST http://127.0.0.1:8765/print/pdf \
  -H "Content-Type: application/json" \
  -H "X-Print-Token: secret-local" \
  -d "{\"base64_data\":\"$(cat /tmp/voucher.b64)\",\"filename\":\"voucher.pdf\",\"copies\":1}"
```

## 5) Imprimir RAW (ESC/POS)

```bash
base64 -i ./ticket.bin | tr -d '\n' > /tmp/ticket.b64
curl -X POST http://127.0.0.1:8765/print/raw \
  -H "Content-Type: application/json" \
  -H "X-Print-Token: secret-local" \
  -d "{\"base64_data\":\"$(cat /tmp/ticket.b64)\",\"filename\":\"ticket.bin\",\"copies\":1}"
```

## 6) Integracion recomendada con Laravel

- Hosting envia trabajo de impresion a este agente local.
- El navegador no recibe PDF.
- Solo agente local imprime por USB.

Notas:

- Si no defines `PRINT_AGENT_TOKEN`, la API queda sin auth (solo recomendado para pruebas locales).
- Si no defines `PRINT_AGENT_PRINTER`, CUPS usa la impresora por defecto.
