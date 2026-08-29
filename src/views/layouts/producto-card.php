<div class="col">
  <a href="/producto/<?= (int) $producto["id"] ?>" class="text-decoration-none text-reset">
    <div class="card producto-card h-100">
      <img src="<?= html($producto["imagen"]) ?>" class="card-img-top" alt="<?= html($producto["nombre"]) ?>">
      <div class="card-body d-flex flex-column">
        <h3 class="producto-nombre h6"><?= html($producto["nombre"]) ?></h3>

        <?php if ($producto["stock"]): ?>
          <span class="badge text-bg-success align-self-start mb-2">En stock</span>
        <?php else: ?>
          <span class="badge text-bg-secondary align-self-start mb-2">Sin stock</span>
        <?php endif; ?>

        <p class="producto-precio mt-auto mb-0">$<?= number_format($producto["precio"], 0, ",", ".") ?></p>
      </div>
    </div>
  </a>
</div>