<div class="container my-5">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="mb-0">Mis solicitudes de personalización</h1>
    <a href="/personalizacion" class="btn btn-primary">Nueva solicitud</a>
  </div>
  <?php if (!empty($mensaje)): ?><div class="alert alert-success" role="alert"><?= html($mensaje) ?></div><?php endif; ?>
  <?php if ($solicitudes === []): ?>
    <div class="alert alert-info">Todavía no enviaste solicitudes de personalización.</div>
  <?php else: ?>
    <div class="d-flex flex-column gap-3">
      <?php foreach ($solicitudes as $solicitud): ?>
        <article class="card">
          <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between gap-2">
              <h2 class="h5">Solicitud #<?= (int) $solicitud["id"] ?></h2>
              <span class="badge text-bg-<?= $solicitud["estado"] === "pendiente" ? "warning" : ($solicitud["estado"] === "aceptada" ? "success" : "secondary") ?>"><?= html(ucfirst($solicitud["estado"])) ?></span>
            </div>
            <p class="small text-muted">Enviada <?= html((string) $solicitud["created_at"]) ?></p>
            <p><?= nl2br(html($solicitud["descripcion"])) ?></p>
            <p><a href="/mi-cuenta/personalizaciones/<?= (int) $solicitud["id"] ?>/archivo">Descargar mi archivo: <?= html($solicitud["archivo_nombre"]) ?></a></p>
            <?php if ($solicitud["estado"] !== "pendiente"): ?>
              <?php if ($solicitud["estado"] === "aceptada"): ?><p class="fw-semibold">Presupuesto estimado: $<?= number_format((float) $solicitud["presupuesto"], 2, ",", ".") ?></p><?php endif; ?>
              <div class="alert alert-light border mb-0"><?= nl2br(html((string) $solicitud["respuesta"])) ?></div>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
