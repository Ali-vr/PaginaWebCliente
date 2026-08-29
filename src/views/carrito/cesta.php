<div class="container my-5">
  <?= $this->fetch("carrito/pasos.php", ["pasoActual" => 1]) ?>

  <div class="row g-4">
    <div class="col-lg-8">
      <h1 class="h3 mb-4">Carrito</h1>
      <?php if (empty($items)): ?>
        <div class="text-center py-5 bg-white rounded">
          <p class="mb-3">Tu carrito está vacío.</p>
          <a href="/" class="btn btn-primary">Ver productos</a>
        </div>
      <?php else: ?>
        <?php foreach ($items as $item): ?>
          <div class="d-flex align-items-center gap-3 bg-white rounded p-3 mb-3">
            <img src="<?= html($item["producto"]["imagen"]) ?>" alt="<?= html($item["producto"]["nombre"]) ?>" width="80" height="60" class="rounded" style="object-fit: cover;">
            <div class="flex-grow-1">
              <p class="mb-1 fw-semibold"><?= html($item["producto"]["nombre"]) ?></p>
              <p class="mb-0 text-secondary small">Cantidad: <?= (int) $item["cantidad"] ?></p>
            </div>
            <p class="mb-0 fw-semibold">$<?= number_format($item["subtotal"], 0, ",", ".") ?></p>
            <form action="/carrito/eliminar/<?= (int) $item["producto"]["id"] ?>" method="post">
              <button type="submit" class="btn btn-sm btn-outline-secondary">Quitar</button>
            </form>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="col-lg-4">
      <div class="bg-white rounded p-4">
        <h2 class="h5 mb-3">Resumen</h2>
        <div class="d-flex justify-content-between mb-3">
          <span>Total</span>
          <span class="fw-bold fs-5">$<?= number_format($total, 0, ",", ".") ?></span>
        </div>
        <a href="/carrito/pago" class="btn btn-primary w-100 <?= empty($items) ? "disabled" : "" ?>" <?= empty($items) ? 'aria-disabled="true" tabindex="-1"' : "" ?>>Siguiente</a>
        <a href="/" class="btn btn-link w-100 mt-2">Seguir comprando</a>
      </div>
    </div>
  </div>
</div>