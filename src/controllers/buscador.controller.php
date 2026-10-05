<?php

require_once __DIR__ . "/../services/catalogo.service.php";

function show_buscar($request, $response)
{
    global $render, $productos;

    $q = trim((string) ($request->getQueryParams()["q"] ?? ""));
    $resultados = buscar_productos_por_nombre($productos, $q);

    return $render($response, "buscar.php", [
        "query" => $q,
        "resultados" => $resultados,
        "title" => $q !== "" ? "Búsqueda: $q | Maderas Artesanales" : "Buscar | Maderas Artesanales",
    ]);
}
