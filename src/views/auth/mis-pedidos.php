<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Mis pedidos</h1>
    <a href="/mi-cuenta" class="btn btn-outline-secondary">Volver a mi cuenta</a>
  </div>

  <?php if (empty($pedidos)): ?>
    <div class="alert alert-light border">
      Todavía no realizaste compras. <a href="/" class="alert-link">Explorá el catálogo</a>.
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Pedido</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Items</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pedidos as $pedido): ?>
            <tr>
              <td><?= html($pedido["numero"]) ?></td>
              <td><?= date("d/m/Y H:i", strtotime((string) $pedido["created_at"])) ?></td>
              <td>$<?= number_format((float) $pedido["total"], 0, ",", ".") ?></td>
              <td><span class="badge text-bg-light border"><?= html($pedido["estado"] ?? "pendiente") ?></span></td>
              <td><?= (int) ($pedido["items_count"] ?? 0) ?> artículos</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
