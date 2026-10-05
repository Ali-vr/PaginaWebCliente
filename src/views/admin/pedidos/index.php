<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4"><h1 class="mb-0">Pedidos</h1><a href="/admin" class="btn btn-outline-secondary">Volver al panel</a></div>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Pedido</th>
          <th>Cliente</th>
          <th>Fecha</th>
          <th>Total</th>
          <th>Estado</th>
          <th>Items</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pedidos as $pedido): ?>
          <tr>
            <td><?= html($pedido["numero"]) ?></td>
            <td><?= html($pedido["cliente"] ?? $pedido["email"] ?? "Cliente") ?></td>
            <td><?= date("d/m/Y H:i", strtotime((string) $pedido["created_at"])) ?></td>
            <td>$<?= number_format((float) $pedido["total"], 0, ",", ".") ?></td>
            <td>
              <form method="post" action="/admin/pedidos/<?= (int) $pedido["id"] ?>/estado" class="d-flex gap-2 align-items-center">
                <select name="estado" class="form-select form-select-sm">
                  <?php foreach (["pendiente", "confirmado", "en preparacion", "enviado", "entregado", "cancelado"] as $estado): ?>
                    <option value="<?= html($estado) ?>" <?= (($pedido["estado"] ?? "pendiente") === $estado) ? "selected" : "" ?>><?= html($estado) ?></option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
              </form>
            </td>
            <td><?= (int) ($pedido["items_count"] ?? 0) ?></td>
            <td></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
