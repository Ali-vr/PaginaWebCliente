<?php
global $router; // <--- Añade esto para importar la instancia global del enrutador

$router->get('/contacto', [ContactoController::class, 'index']);
$router->post('/contacto', [ContactoController::class, 'enviar']);