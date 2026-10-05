<?php

function show_contacto($request, $response)
{
    global $render;

    return $render($response, "contacto.php", [
        "title" => "Contacto | Maderas Artesanales",
    ]);
}
