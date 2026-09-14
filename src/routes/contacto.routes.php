<?php

$app->get("/contacto", function ($request, $response) use ($render) {
  return $render($response, "contacto.php", [
    "title" => "Contacto | Maderas Artesanales",
  ]);
});
