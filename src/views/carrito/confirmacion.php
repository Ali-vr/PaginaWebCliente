<div class="container my-5 text-center">
<?= $this->fetch("carrito/pasos.php", ["pasoActual" => 3]) ?>

  <div class="bg-white rounded p-5 mx-auto" style="max-width: 600px;">
    <h1 class="h3 mb-3">¡Gracias por tu compra!</h1>
    <p class="text-secondary mb-4">Pedido #<?= html($pedido["numeroPedido"]) ?></p>

    <div class="text-start mb-4">
      <?php foreach ($pedido["items"] as $item): ?>
        <div class="d-flex justify-content-between small mb-2">
          <span><?= html($item["producto"]["nombre"]) ?> x<?= (int) $item["cantidad"] ?></span>
          <span>$<?= number_format($item["subtotal"], 0, ",", ".") ?></span>
        </div>
      <?php endforeach; ?>
      <hr>
      <div class="d-flex justify-content-between fw-bold">
        <span>Total</span>
        <span>$<?= number_format($pedido["total"], 0, ",", ".") ?></span>
      </div>
    </div>

    <p class="small text-secondary mb-4">
      Te vamos a contactar a <?= html($pedido["direccion"]["telefono"]) ?> para coordinar la entrega en
      <?= html($pedido["direccion"]["calle"]) ?>, <?= html($pedido["direccion"]["localidad"]) ?>.
    </p>

    <a href="/" class="btn btn-primary">Volver al inicio</a>
  </div>
</div>