<div id="carruselOfertas" class="carousel slide mb-5" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <?php foreach ($carrusel as $indice => $slide): ?><button type="button" data-bs-target="#carruselOfertas" data-bs-slide-to="<?= (int) $indice ?>" class="<?= $indice === 0 ? "active" : "" ?>"></button><?php endforeach; ?>
  </div>

  <div class="carousel-inner">
    <?php foreach ($carrusel as $indice => $slide): ?><div class="carousel-item <?= $indice === 0 ? "active" : "" ?>">
      <img src="<?= html($slide["imagen"]) ?>" class="d-block w-100" alt="<?= html($slide["titulo"]) ?>">
      <div class="carousel-caption"><h2><?= html($slide["titulo"]) ?></h2><p><?= html($slide["descripcion"]) ?></p></div>
    </div><?php endforeach; ?>
  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#carruselOfertas" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
  <button class="carousel-control-next" type="button" data-bs-target="#carruselOfertas" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
</div>

<div class="catalogo-layout container my-4">
  <div class="row g-4">
    <?= $this->fetch("partials/filtrador.php", ["categorias" => $categorias]) ?>
    <div class="col-lg-9 col-md-8">
      <div class="catalogo-header mb-3"><h2 class="mb-0">Todos los productos</h2><span class="catalogo-contador" id="contadorResultados"><?= count($productos) ?> productos</span></div>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-3 g-4" id="grilla-productos">
        <?php foreach ($productos as $producto): ?>
          <div class="col producto-filtrable" data-categoria="<?= html($producto["categoria"]) ?>" data-stock="<?= $producto["stock"] ? "disponible" : "agotado" ?>">
            <?= $this->fetch("layouts/producto-card.php", ["producto" => $producto]) ?>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="sin-resultados d-none" id="sinResultados"><p>No hay productos que coincidan con los filtros seleccionados.</p><button class="btn-limpiar-filtros" onclick="limpiarFiltros()">Ver todos</button></div>
    </div>
  </div>
</div>