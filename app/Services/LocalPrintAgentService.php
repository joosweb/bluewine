<?php

namespace App\Services;

class LocalPrintAgentService
{
    public function isEnabled()
    {
        return (bool) config('print_agent.enabled', false);
    }

    public function dispatchPdf($pdfBinary, $filename = 'voucher.pdf', $copies = 1)
    {
        $url = (string) config('print_agent.url', 'http://127.0.0.1:8765/print/pdf');
        return $this->dispatch($url, $pdfBinary, $filename, $copies);
    }

    public function dispatchRaw($rawBinary, $filename = 'voucher.bin', $copies = 1)
    {
        $url = (string) config('print_agent.raw_url', 'http://127.0.0.1:8765/print/raw');
        return $this->dispatch($url, $rawBinary, $filename, $copies);
    }

    public function mode()
    {
        return strtolower((string) config('print_agent.mode', 'raw'));
    }

    private function dispatch($url, $binary, $filename, $copies)
    {
        if (!$this->isEnabled()) {
            return [
                'ok' => false,
                'message' => 'Print agent disabled',
            ];
        }

        $token = (string) config('print_agent.token', '');
        $timeout = (int) config('print_agent.timeout', 8);

        $payload = json_encode([
            'base64_data' => base64_encode($binary),
            'filename' => $filename,
            'copies' => max((int) $copies, 1),
        ]);

        if ($payload === false) {
            return [
                'ok' => false,
                'message' => 'Failed to encode print payload',
            ];
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
        ];

        if ($token !== '') {
            $headers[] = 'X-Print-Token: ' . $token;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $responseBody = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($responseBody === false) {
            return [
                'ok' => false,
                'message' => $curlError ?: 'Failed to connect to local print agent',
            ];
        }

        $decoded = json_decode($responseBody, true);
        if ($httpCode < 200 || $httpCode >= 300) {
            return [
                'ok' => false,
                'message' => is_array($decoded) && isset($decoded['error']) ? $decoded['error'] : 'Agent responded with HTTP ' . $httpCode,
                'http_code' => $httpCode,
            ];
        }

        return [
            'ok' => true,
            'job_id' => is_array($decoded) && isset($decoded['job_id']) ? $decoded['job_id'] : null,
            'agent_response' => $decoded,
        ];
    }
}
