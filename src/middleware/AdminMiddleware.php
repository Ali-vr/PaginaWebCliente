<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

final class AdminMiddleware implements MiddlewareInterface
{
  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
  {
    if (empty($_SESSION["usuario_id"])) {
      return (new Response(302))->withHeader("Location", "/login");
    }

    if (($_SESSION["usuario"]["rol"] ?? "usuario") !== "admin") {
      $response = new Response(403);
      $response->getBody()->write("Acceso prohibido");
      return $response->withHeader("Content-Type", "text/plain; charset=UTF-8");
    }

    return $handler->handle($request);
  }
}
