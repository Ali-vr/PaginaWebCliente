<div class="container my-5">
  <h1 class="mb-4">Solicitudes de personalización</h1>
  <?php if (!empty($mensaje)): ?><div class="alert alert-info" role="alert"><?= html($mensaje) ?></div><?php endif; ?>
  <?php if ($solicitudes === []): ?>
    <div class="alert alert-info">No hay solicitudes de personalización.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>#</th><th>Cliente</th><th>Descripción</th><th>Archivo</th><th>Estado</th><th>Fecha</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($solicitudes as $solicitud): ?>
            <tr>
              <td><?= (int) $solicitud["id"] ?></td>
              <td><?= html($solicitud["cliente_nombre"]) ?><br><small><?= html($solicitud["cliente_email"]) ?></small></td>
              <td><?= html(mb_strimwidth($solicitud["descripcion"], 0, 100, "…")) ?></td>
              <td><?= html($solicitud["archivo_nombre"]) ?></td>
              <td><?= html(ucfirst($solicitud["estado"])) ?></td>
              <td><?= html((string) $solicitud["created_at"]) ?></td>
              <td><a class="btn btn-sm btn-outline-primary" href="/admin/personalizaciones/<?= (int) $solicitud["id"] ?>">Revisar</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
