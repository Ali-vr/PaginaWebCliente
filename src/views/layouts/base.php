<!doctype html>
<html lang="es" data-bs-theme="light">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= html($title ?? "Maderas Artesanales | Tienda Online") ?></title>
    <link href="/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/css/tienda.css" rel="stylesheet" />
  </head>
  <body>

    <!-- Barra promo top (nueva) -->
    <div class="promo-bar">
      🪵 Envíos a todo el país en compras mayores a $100.000
    </div>

    <!-- NAVBAR PRINCIPAL -->
    <nav class="navbar navbar-expand-lg navbar-principal" data-bs-theme="dark">
      <div class="container">
        <a class="navbar-brand" href="/">🪵 Maderas Artesanales</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPrincipal">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarPrincipal">
          <form class="d-flex mx-auto my-2 my-lg-0" style="max-width: 420px; width: 100%;" role="search" action="/buscar" method="get">
            <input class="form-control" type="search" name="q" placeholder="Buscar productos..." aria-label="Buscar">
            <button class="btn btn-outline-light ms-2" type="submit">
              <svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/></svg>
            </button>
          </form>

          <div class="navbar-acciones d-flex align-items-center gap-3 ms-lg-3">
            <a href="/contacto" class="nav-link-accion">Contacto</a>
            <?php if (!empty($_SESSION["usuario_id"])): ?>
              <a href="/mi-cuenta" class="nav-link-accion nav-link-cuenta" aria-label="Mi cuenta">
                <svg width="22" height="22" viewBox="0 0 16 16" fill="currentColor"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.025 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/></svg>
                <span class="nav-link-cuenta-label">Mi cuenta</span>
              </a>
              <?php if (($_SESSION["usuario"]["rol"] ?? "usuario") === "admin"): ?><a href="/admin" class="nav-link-accion">Admin</a><?php endif; ?>
              <a href="/logout" class="nav-link-accion">Salir</a>
            <?php else: ?>
              <a href="/login" class="nav-link-accion nav-link-cuenta" aria-label="Iniciar sesión">
                <svg width="22" height="22" viewBox="0 0 16 16" fill="currentColor"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.025 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/></svg>
                <span class="nav-link-cuenta-label">Ingresar</span>
              </a>
              <a href="/registro" class="nav-link-accion">Registro</a>
            <?php endif; ?>
            <a href="/carrito" class="btn-carrito" aria-label="Carrito de compras">
              <svg width="22" height="22" viewBox="0 0 16 16" fill="currentColor"><path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 14a1 1 0 1 1 0 2 1 1 0 0 1 0-2m9 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/></svg>
              <span class="badge bg-danger badge-carrito"><?= (int) ($cantidadCarrito ?? 0) ?></span>
            </a>
          </div>
        </div>
      </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <main><?= $content ?? "" ?></main>

    <!-- FOOTER -->
    <footer class="footer-madera py-4 mt-5">
      <div class="container">
        <div class="row g-4 align-items-start">
          <div class="col-lg-4">
            <p class="mb-1 fw-semibold">🪵 Maderas Artesanales</p>
            <p class="small mb-3">Muebles y objetos de madera hechos a mano, pieza por pieza.</p>
          </div>
          <div class="col-6 col-lg-2 offset-lg-2">
            <h6 class="small fw-semibold mb-2 text-uppercase letter-spacing-1">Categorías</h6>
            <ul class="list-unstyled small">
              <?php foreach ($categorias ?? [] as $slug => $nombre): ?>
                <li><a href="/categoria/<?= html($slug) ?>" class="footer-link"><?= html($nombre) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="col-6 col-lg-2">
            <h6 class="small fw-semibold mb-2 text-uppercase letter-spacing-1">Tienda</h6>
            <ul class="list-unstyled small">
              <li><a href="/" class="footer-link">Inicio</a></li>
              <li><a href="/contacto" class="footer-link">Contacto</a></li>
              <li><a href="/login" class="footer-link">Mi cuenta</a></li>
              <li><a href="/carrito" class="footer-link">Carrito</a></li>
            </ul>
          </div>
          <div class="col-lg-2">
            <h6 class="small fw-semibold mb-2 text-uppercase letter-spacing-1">Contacto</h6>
            <ul class="list-unstyled small">
              <li>
                <a href="https://wa.me/5491100000000" target="_blank" rel="noopener" class="footer-link d-flex align-items-center gap-1">
                  <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326"/></svg>
                  WhatsApp
                </a>
              </li>
              <li class="mt-1">Berazategui, Bs.As.</li>
            </ul>
          </div>
        </div>
        <hr style="border-color: rgba(246,239,228,.15); margin: 1rem 0;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small">
          <p class="mb-0">&copy; <?= date("Y") ?> Maderas Artesanales. Todos los derechos reservados.</p>
          <p class="mb-0">Hecho a mano en Argentina 🇦🇷</p>
        </div>
      </div>
    </footer>

    <script src="/js/bootstrap.bundle.min.js"></script>
    <script src="/js/catalogo-filtros.js"></script>
  </body>
</html>