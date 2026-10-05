<?php

require_once __DIR__ . "/../controllers/buscador.controller.php";

$app->get("/buscar", "show_buscar");
