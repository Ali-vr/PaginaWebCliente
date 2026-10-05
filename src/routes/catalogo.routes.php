<?php

require_once __DIR__ . "/../controllers/catalogo.controller.php";

$app->get("/", "show_home");
$app->get("/categoria/{slug}", "show_categoria");
$app->get("/producto/{id}", "show_producto");
