// Helper para imprimir desde el navegador en el agente local
// (tools/usb-print-agent). Usado cuando Bluewine corre en hosting y PHP
// no puede alcanzar la red local del cajero.
//
// Espera el objeto `client_print` que devuelve el backend:
//   {
//     agent_url: 'http://127.0.0.1:8765/print/raw',
//     agent_token: '...',
//     mode: 'raw' | 'pdf',
//     filename: 'voucher-123.bin',
//     copies: 1,
//     base64_data: '....'
//   }
//
// Devuelve { ok: true, jobId } o lanza un Error con mensaje legible.

export async function dispatchClientPrint(clientPrint) {
  if (!clientPrint || !clientPrint.agent_url) {
    throw new Error('Payload de impresion local invalido')
  }

  const headers = { 'Content-Type': 'application/json' }
  if (clientPrint.agent_token) {
    headers['X-Print-Token'] = clientPrint.agent_token
  }

  let response
  try {
    response = await fetch(clientPrint.agent_url, {
      method: 'POST',
      headers: headers,
      body: JSON.stringify({
        base64_data: clientPrint.base64_data,
        filename: clientPrint.filename,
        copies: clientPrint.copies || 1,
      }),
    })
  } catch (e) {
    // Errores de red: agente apagado, firewall, mixed-content (HTTPS -> HTTP no-loopback), etc.
    throw new Error(
      'No se pudo conectar con el agente local de impresion (' +
        clientPrint.agent_url +
        '). Verifica que el agente este corriendo en esta PC.'
    )
  }

  let body = null
  try {
    body = await response.json()
  } catch (_) {
    body = null
  }

  if (!response.ok) {
    const msg =
      (body && (body.error || body.message)) ||
      'El agente respondio HTTP ' + response.status
    throw new Error(msg)
  }

  return {
    ok: true,
    jobId: body && body.job_id ? body.job_id : null,
  }
}
