<?php

class ContactoService {

    public function enviarCorreoContacto(string $nombre, string $email, string $mensaje): bool {
        $apiKey = $_ENV['RESEND_API_KEY'] ?? getenv('RESEND_API_KEY');
        $propietarioEmail = $_ENV['PROPIETARIO_EMAIL'] ?? getenv('PROPIETARIO_EMAIL');

        if (!$apiKey || !$propietarioEmail) {
            error_log('Resend API Key o email del propietario no configurado.');
            return false;
        }

        $data = [
            'from' => 'Formulario Web <onboarding@resend.dev>',
            'to' => [$propietarioEmail],
            'reply_to' => $email,
            'subject' => "Nuevo mensaje de contacto de: {$nombre}",
            'html' => "
                <h2>Nuevo mensaje recibido desde la página web</h2>
                <p><strong>Nombre:</strong> " . htmlspecialchars($nombre) . "</p>
                <p><strong>Correo electrónico:</strong> " . htmlspecialchars($email) . "</p>
                <p><strong>Mensaje:</strong></p>
                <p>" . nl2br(htmlspecialchars($mensaje)) . "</p>
            "
        ];

        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT => 15
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || $httpCode < 200 || $httpCode >= 300) {
            error_log("Error enviando correo con Resend API [HTTP {$httpCode}]: " . ($error ?: $response));
            return false;
        }

        return true;
    }
}