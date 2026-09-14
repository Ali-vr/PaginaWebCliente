<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4"><h1 class="mb-0">Carrusel</h1><a href="/admin/carrusel/create" class="btn btn-primary">Nueva oferta</a></div>
  <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Orden</th><th>Título</th><th>Imagen</th><th>Estado</th><th></th></tr></thead><tbody>
  <?php foreach ($items as $item): ?><tr><td><?= (int) $item["orden"] ?></td><td><?= html($item["titulo"]) ?></td><td><?= html($item["imagen"]) ?></td><td><?= $item["activo"] ? "Activo" : "Inactivo" ?></td><td class="text-nowrap"><a href="/admin/carrusel/<?= (int) $item["id"] ?>/edit" class="btn btn-sm btn-outline-secondary">Editar</a><?php if ($item["activo"]): ?><form class="d-inline" method="post" action="/admin/carrusel/<?= (int) $item["id"] ?>/delete"><button class="btn btn-sm btn-outline-danger" type="submit">Desactivar</button></form><?php endif; ?></td></tr><?php endforeach; ?>
  </tbody></table></div>
</div>
