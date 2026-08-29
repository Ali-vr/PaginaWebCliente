<div class="container my-5">
  <h1 class="mb-4"><?= html($categoriaNombre) ?></h1>

  <?php if (empty($productos)): ?>
    <p>Todavía no hay productos cargados en esta categoría.</p>
  <?php else: ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
      <?php foreach ($productos as $producto): ?>
        <?= $this->fetch("layouts/producto-card.php", ["producto" => $producto]) ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>