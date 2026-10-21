<?php

function show_contacto($request, $response)
{
    global $render;

    $success = $_SESSION["flash_contacto"]["mensaje"] ?? null;
    $error = $_SESSION["flash_contacto"]["error"] ?? null;
    unset($_SESSION["flash_contacto"]);

    return $render($response, "contacto.php", [
        "title" => "Contacto | Maderas Artesanales",
        "success" => $success,
        "error" => $error,
    ]);
}

function handle_contacto($request, $response)
{
    $data = $request->getParsedBody() ?? [];
    $nombre = trim((string) ($data["nombre"] ?? ""));
    $email = trim(strtolower((string) ($data["email"] ?? "")));
    $mensaje = trim((string) ($data["mensaje"] ?? ""));

    if ($nombre === "" || $mensaje === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["flash_contacto"] = [
            "error" => "Completá tu nombre, un email válido y tu mensaje para enviar la consulta.",
        ];

        return $response
            ->withHeader("Location", "/contacto")
            ->withStatus(302);
    }

    $_SESSION["flash_contacto"] = [
        "mensaje" => "Gracias, tu consulta fue enviada correctamente. Nos pondremos en contacto a la brevedad.",
    ];

    return $response
        ->withHeader("Location", "/contacto")
        ->withStatus(302);
}

require_once __DIR__ . '/../services/contacto.service.php';

class ContactoController {

    private ContactoService $contactoService;

    public function __construct() {
        $this->contactoService = new ContactoService();
    }

    public function index() {
        require_once __DIR__ . '/../views/contacto.php';
    }

    public function enviar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /contacto');
            exit;
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mensaje = trim($_POST['mensaje'] ?? '');

        $errores = [];

        // Validaciones del servidor
        if (empty($nombre)) {
            $errores['nombre'] = 'El nombre es obligatorio.';
        }

        if (empty($email)) {
            $errores['email'] = 'El correo electrónico es obligatorio.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'El formato del correo electrónico no es válido.';
        }

        if (empty($mensaje)) {
            $errores['mensaje'] = 'El mensaje es obligatorio.';
        }

        if (!empty($errores)) {
            if ($this->esAjax()) {
                header('Content-Type: application/json', true, 400);
                echo json_encode(['exito' => false, 'errores' => $errores]);
                exit;
            }
            $_SESSION['errores_contacto'] = $errores;
            $_SESSION['old_contacto'] = $_POST;
            header('Location: /contacto');
            exit;
        }

        // Envío a través de Resend API
        $enviado = $this->contactoService->enviarCorreoContacto($nombre, $email, $mensaje);

        if ($enviado) {
            if ($this->esAjax()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'exito' => true,
                    'mensaje' => '¡Su mensaje ha sido enviado correctamente!'
                ]);
                exit;
            }
            $_SESSION['exito_contacto'] = '¡Su mensaje ha sido enviado correctamente!';
            header('Location: /contacto');
            exit;
        } else {
            // Manejo seguro de errores técnicos
            $errorMsg = 'Ocurrió un error al enviar el mensaje. Por favor, inténtelo de nuevo más tarde.';
            if ($this->esAjax()) {
                header('Content-Type: application/json', true, 500);
                echo json_encode(['exito' => false, 'error_general' => $errorMsg]);
                exit;
            }
            $_SESSION['error_general_contacto'] = $errorMsg;
            header('Location: /contacto');
            exit;
        }
    }

    private function esAjax(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
