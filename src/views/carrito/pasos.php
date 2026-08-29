<?php
  $pasos = [1 => "Carrito", 2 => "Pago", 3 => "Confirmación"];
?>
<div class="d-flex justify-content-center align-items-center gap-2 gap-md-4 my-4 flex-wrap">
  <?php foreach ($pasos as $numero => $nombre): ?>
    <div class="d-flex align-items-center gap-2">
      <span class="paso-circulo <?= $numero <= $pasoActual ? "paso-activo" : "" ?>">
        <?= $numero < $pasoActual ? "✓" : $numero ?>
      </span>
      <span class="<?= $numero === $pasoActual ? "fw-semibold" : "text-secondary" ?>"><?= $nombre ?></span>
    </div>
    <?php if ($numero < count($pasos)): ?>
      <div class="paso-linea"></div>
    <?php endif; ?>
  <?php endforeach; ?>
</div>