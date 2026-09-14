<?php

$app->get("/buscar", function ($request, $response) use ($render, $productos) {
  $q = trim((string) ($request->getQueryParams()["q"] ?? ""));

  $resultados = [];
  if ($q !== "") {
    $resultados = array_values(array_filter(
      $productos,
      fn(array $p): bool => stripos($p["nombre"], $q) !== false,
    ));
  }

  return $render($response, "buscar.php", [
    "query"      => $q,
    "resultados" => $resultados,
    "title"      => $q !== "" ? "Búsqueda: $q | Maderas Artesanales" : "Buscar | Maderas Artesanales",
  ]);
});
