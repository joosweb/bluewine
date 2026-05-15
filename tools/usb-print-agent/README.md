# USB Print Agent — Guía de instalación en Windows

Agente local que recibe trabajos de impresión y los envía a la impresora térmica conectada a este PC.

> **Modelo de operación (importante).** Bluewine vive en hosting, así que **PHP no
> puede conectarse al agente** directamente (no tiene ruta a `192.168.x.x` ni a
> `127.0.0.1` del local). El navegador del cajero **sí**. Por eso el flujo es:
>
> ```
>   Navegador (POS / Reimpresion) ──fetch──▶ http://127.0.0.1:8765/print/raw
>                ▲                                       │
>                │ JSON con base64                       ▼
>          PHP (hosting) ────────────────────────▶ Impresora USB
> ```
>
> Laravel arma el ticket (ESC/POS o PDF), lo manda al navegador en base64, y el
> navegador hace `POST` directo al agente local. El agente debe correr en la
> **misma PC del cajero**, escuchando en `127.0.0.1:8765`.

---

## Archivos en esta carpeta

| Archivo               | Descripción                          |
|-----------------------|--------------------------------------|
| `usb-print-agent.exe` | El agente ejecutable                 |
| `.env`                | Configuración (impresora, token, CORS) |

---

## Paso 1 — Verificar el nombre exacto de la impresora

Abre **CMD** (Win + R → `cmd`) y ejecuta:

```cmd
wmic printer get name
```

Busca la impresora térmica en la lista (ej. `NATIONS CLA58`) y copia el nombre exactamente como aparece.

---

## Paso 2 — Editar el archivo `.env`

Abre `.env` con el Bloc de Notas y actualiza los tres valores:

```
PRINT_AGENT_LISTEN="127.0.0.1:8765"
PRINT_AGENT_PRINTER="NATIONS CLA58"
PRINT_AGENT_TOKEN="secret-local"
PRINT_AGENT_CORS_ORIGIN="https://app.bluewine.cl"
```

- `PRINT_AGENT_LISTEN` debe quedar en `127.0.0.1:8765`. Es lo que evita que
  el navegador bloquee la llamada por *mixed content* cuando Bluewine corre
  en HTTPS.
- `PRINT_AGENT_TOKEN` debe coincidir con `PRINT_AGENT_TOKEN` en el `.env`
  de Laravel. Es lo que llega al navegador como `X-Print-Token`.
- `PRINT_AGENT_CORS_ORIGIN` debe ser **exactamente** el dominio del hosting
  (con `https://`). Acepta varios separados por coma. Solo en pruebas usar `*`.
- Si el nombre de la impresora tiene espacios, déjalo entre comillas.

---

## Paso 3 — Ejecutar el agente

Abre **CMD en esta carpeta** (puedes hacer Shift + clic derecho en la carpeta → "Abrir ventana de comandos aquí") y ejecuta:

```cmd
usb-print-agent.exe
```

Deberías ver:

```
usb-print-agent listening on http://0.0.0.0:8765
```

Deja esta ventana abierta mientras uses el sistema. Cada vez que llegue un trabajo de impresión verás un log en la pantalla.

---

## Paso 4 — Verificar que funciona

Desde **el mismo PC del cajero**, abre el navegador y accede a:

```
http://127.0.0.1:8765/health
```

Debe responder:

```json
{"status":"ok","printer":"NATIONS CLA58"}
```

> El agente debe vivir en la misma PC desde la que se imprime. No expongas
> el puerto a la red salvo que sepas lo que estás haciendo: el front siempre
> apunta a `http://127.0.0.1:8765` y eso es lo que evita el bloqueo de
> mixed-content con Bluewine en HTTPS.

---

## Paso 5 — Arranque automático con Windows

Hay dos opciones. **Recomendamos la Opción B (servicio)** porque arranca sin que nadie inicie sesión y no deja ventanas abiertas.

---

### Opción A — Carpeta de Inicio (simple, requiere login)

1. Crea un archivo `iniciar-agente.bat` en esta carpeta con este contenido:
   ```bat
   @echo off
   cd /d "%~dp0"
   start "" /B usb-print-agent.exe
   ```
2. Presiona **Win + R**, escribe `shell:startup` y presiona Enter.
3. Copia el archivo `iniciar-agente.bat` dentro de esa carpeta de Inicio.

El agente arrancará cada vez que el usuario inicie sesión. Si el PC se reinicia solo (sin login), el agente **no** arrancará.

---

### Opción B — Servicio de Windows con NSSM (recomendado)

NSSM convierte cualquier ejecutable en un servicio real de Windows: arranca solo al encender el PC, sin login, y sin ventanas abiertas.

#### 1. Descargar NSSM

Ve a https://nssm.cc/download y descarga `nssm.exe` (versión 64-bit). Copia `nssm.exe` dentro de esta carpeta.

#### 2. Instalar el servicio

Abre **CMD como Administrador** y ejecuta (ajusta la ruta si pusiste los archivos en otra carpeta):

```cmd
cd C:\usb-print-agent
nssm install BluewineAgent
```

Se abrirá una ventana de configuración. Rellena así:

| Campo | Valor |
|---|---|
| **Path** | `C:\usb-print-agent\usb-print-agent.exe` |
| **Startup directory** | `C:\usb-print-agent` |
| **Service name** | `BluewineAgent` |

Haz clic en **Install service**.

#### 3. Iniciar el servicio

```cmd
nssm start BluewineAgent
```

#### 4. Verificar que está corriendo

```cmd
nssm status BluewineAgent
```

Debe mostrar `SERVICE_RUNNING`.

También puedes verlo en: Panel de Control → Herramientas administrativas → Servicios → busca `BluewineAgent`.

#### Comandos útiles

```cmd
nssm stop BluewineAgent       # detener
nssm restart BluewineAgent    # reiniciar
nssm remove BluewineAgent     # desinstalar el servicio
```

> Si cambias el `.env`, reinicia el servicio con `nssm restart BluewineAgent` para que tome los nuevos valores.

---

## Firewall de Windows

Como el agente escucha solo en `127.0.0.1`, normalmente **no hace falta abrir
el firewall**: el navegador del propio PC accede en loopback. Si por alguna
razón cambias `PRINT_AGENT_LISTEN` a `0.0.0.0:8765` para que otra PC del local
también imprima, recién ahí abre el puerto:

```cmd
netsh advfirewall firewall add rule name="USB Print Agent" dir=in action=allow protocol=TCP localport=8765
```

> Aviso: si Bluewine corre por `https://`, exponer el agente en `0.0.0.0` no
> alcanza, porque Chrome bloquea `fetch('http://192.168.x.x:8765/...')` desde
> una página HTTPS (mixed content). Mantén el agente en `127.0.0.1` salvo que
> sirvas el agente con TLS.

---

## Solución de problemas

| Síntoma | Causa probable | Solución |
|---|---|---|
| `copy /b failed` en los logs | Nombre de impresora incorrecto | Verificar con `wmic printer get name` y actualizar `.env` |
| El servidor no conecta al agente | Firewall bloqueando puerto 8765 | Ver sección Firewall arriba |
| La impresora no imprime nada | La impresora está offline o en pausa | Verificar en Panel de control → Dispositivos e impresoras |

---

## Referencia técnica (macOS/Linux)

```bash
base64 -i ./ticket.bin | tr -d '\n' > /tmp/ticket.b64
curl -X POST http://127.0.0.1:8765/print/raw \
  -H "Content-Type: application/json" \
  -H "X-Print-Token: secret-local" \
  -d "{\"base64_data\":\"$(cat /tmp/ticket.b64)\",\"filename\":\"ticket.bin\",\"copies\":1}"
```

## 6) Integracion con Laravel (modo `client` — el navegador despacha)

En `config/print_agent.php` deja:

```
'enabled'        => true,
'dispatch'       => 'client',
'mode'           => 'raw',                  // o 'pdf'
'token'          => 'secret-local',
'client_url'     => 'http://127.0.0.1:8765/print/pdf',
'client_raw_url' => 'http://127.0.0.1:8765/print/raw',
```

Equivalente en `.env` de Laravel:

```
PRINT_AGENT_ENABLED=true
PRINT_AGENT_DISPATCH=client
PRINT_AGENT_MODE=raw
PRINT_AGENT_TOKEN=secret-local
PRINT_AGENT_CLIENT_URL=http://127.0.0.1:8765/print/pdf
PRINT_AGENT_CLIENT_RAW_URL=http://127.0.0.1:8765/print/raw
```

Con eso, los endpoints `/sales/{id}/print/original` y
`/sales/{id}/print/reprint-execute` devuelven una clave extra `client_print`:

```json
{
  "status": "OK",
  "client_print": {
    "agent_url": "http://127.0.0.1:8765/print/raw",
    "agent_token": "secret-local",
    "mode": "raw",
    "filename": "voucher-1234.bin",
    "copies": 1,
    "base64_data": "..."
  }
}
```

El front (Vue) detecta `client_print` y hace el POST al agente con
`utils/printAgent.js → dispatchClientPrint(...)`.

### Modo `server` (legacy, solo si Laravel y agente están en la misma red)

Pon `PRINT_AGENT_DISPATCH=server`. Laravel intentará el `curl` directo a
`PRINT_AGENT_URL` / `PRINT_AGENT_RAW_URL`. Útil si corres todo en LAN, no
sirve para hosting.

### Notas

- Si no defines `PRINT_AGENT_TOKEN`, la API queda sin auth (solo recomendado para pruebas locales).
- Si no defines `PRINT_AGENT_PRINTER`, el sistema usa la impresora por defecto.
- `PRINT_AGENT_CORS_ORIGIN` debe ser el dominio del hosting; sin esto el
  navegador rechaza la llamada en el preflight.
