<aside class="col-lg-3 col-md-4">
  <div class="filtrador">
    <div class="filtrador-seccion">
      <h3 class="filtrador-titulo">Filtrar por categoría</h3>
      <ul class="filtrador-lista">
        <?php foreach ($categorias ?? [] as $slug => $nombre): ?>
          <li class="filtrador-item">
            <label class="filtrador-label">
              <input type="checkbox" class="filtrador-check" data-categoria="<?= html($slug) ?>" <?= isset($categoriaSlug) && $categoriaSlug === $slug ? "checked" : "" ?>>
              <span><?= html($nombre) ?></span>
            </label>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="filtrador-seccion">
      <h3 class="filtrador-titulo">Disponibilidad</h3>
      <ul class="filtrador-lista">
        <li class="filtrador-item">
          <label class="filtrador-label">
            <input type="checkbox" class="filtrador-check-stock" data-stock="disponible">
            <span>Solo en stock</span>
          </label>
        </li>
      </ul>
    </div>
    <button class="btn-limpiar-filtros" id="btnLimpiarFiltros" type="button">Limpiar filtros</button>
  </div>
</aside>
