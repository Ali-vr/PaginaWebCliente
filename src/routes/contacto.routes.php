<?php

require_once __DIR__ . "/../controllers/contacto.controller.php";

$app->get("/contacto", "show_contacto");
