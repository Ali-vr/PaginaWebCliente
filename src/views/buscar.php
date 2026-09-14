<div class="catalogo-layout container my-4">
  <div class="row g-4">
    <?= $this->fetch("partials/filtrador.php", ["categorias" => $categorias]) ?>
    <div class="col-lg-9 col-md-8">
      <div class="catalogo-header mb-3">
        <h1 class="mb-0">Resultados<?= $query !== "" ? " para \"" . html($query) . "\"" : " de búsqueda" ?></h1>
        <span class="catalogo-contador" id="contadorResultados"><?= count($resultados) ?> productos</span>
      </div>
      <?php if (empty($resultados)): ?>
        <div class="sin-resultados" id="sinResultados"><p>No hay productos que coincidan con la búsqueda.</p><a class="btn btn-primary" href="/">Ver todos</a></div>
      <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-3 g-4" id="grilla-productos">
          <?php foreach ($resultados as $producto): ?>
            <div class="col producto-filtrable" data-categoria="<?= html($producto["categoria"]) ?>" data-stock="<?= $producto["stock"] ? "disponible" : "agotado" ?>">
              <?= $this->fetch("layouts/producto-card.php", ["producto" => $producto]) ?>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="sin-resultados d-none" id="sinResultados"><p>No hay productos que coincidan con los filtros seleccionados.</p><button class="btn-limpiar-filtros" onclick="limpiarFiltros()">Ver todos</button></div>
      <?php endif; ?>
    </div>
  </div>
</div>
