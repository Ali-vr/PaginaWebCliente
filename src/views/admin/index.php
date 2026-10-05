<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4"><h1 class="mb-0">Panel administrador</h1><a href="/logout" class="btn btn-outline-secondary">Cerrar sesión</a></div>
  <div class="row g-4">
    <div class="col-md-3"><a class="card p-4 h-100 text-decoration-none text-reset" href="/admin/productos"><h2 class="h4">Productos</h2><p class="mb-0"><?= (int) $counts["productos"] ?> registros</p></a></div>
    <div class="col-md-3"><a class="card p-4 h-100 text-decoration-none text-reset" href="/admin/categorias"><h2 class="h4">Categorías</h2><p class="mb-0"><?= (int) $counts["categorias"] ?> registros</p></a></div>
    <div class="col-md-3"><a class="card p-4 h-100 text-decoration-none text-reset" href="/admin/carrusel"><h2 class="h4">Carrusel</h2><p class="mb-0"><?= (int) $counts["carrusel"] ?> registros</p></a></div>
    <div class="col-md-3"><a class="card p-4 h-100 text-decoration-none text-reset" href="/admin/pedidos"><h2 class="h4">Pedidos</h2><p class="mb-0"><?= (int) $counts["pedidos"] ?> registros</p></a></div>
  </div>
</div>
