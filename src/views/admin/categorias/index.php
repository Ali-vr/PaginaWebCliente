<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4"><h1 class="mb-0">Categorías</h1><a href="/admin/categorias/create" class="btn btn-primary">Nueva categoría</a></div>
  <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Nombre</th><th>Slug</th><th>Productos activos</th><th>Estado</th><th></th></tr></thead><tbody>
  <?php foreach ($categoriasAdmin as $categoria): ?><tr><td><?= html($categoria["nombre"]) ?></td><td><?= html($categoria["slug"]) ?></td><td><?= (int) $categoria["productos_count"] ?></td><td><?= $categoria["activo"] ? "Activa" : "Inactiva" ?></td><td class="text-nowrap"><a href="/admin/categorias/<?= (int) $categoria["id"] ?>/edit" class="btn btn-sm btn-outline-secondary">Editar</a><?php if ($categoria["activo"]): ?><form class="d-inline" method="post" action="/admin/categorias/<?= (int) $categoria["id"] ?>/delete"><button class="btn btn-sm btn-outline-danger" type="submit">Desactivar</button></form><?php endif; ?></td></tr><?php endforeach; ?>
  </tbody></table></div>
</div>
