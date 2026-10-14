<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4"><h1 class="mb-0">Categorías</h1><a href="/admin/categorias/create" class="btn btn-primary">Nueva categoría</a></div>
  <?php if (!empty($mensaje)): ?><div class="alert alert-info" role="alert"><?= html($mensaje) ?></div><?php endif; ?>
  <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Nombre</th><th>Slug</th><th>Productos vinculados</th><th>Estado</th><th></th></tr></thead><tbody>
  <?php foreach ($categoriasAdmin as $categoria): ?><tr><td><?= html($categoria["nombre"]) ?></td><td><?= html($categoria["slug"]) ?></td><td><?= (int) $categoria["productos_count"] ?></td><td><?= $categoria["activo"] ? "Activa" : "Inactiva" ?></td><td class="text-nowrap"><a href="/admin/categorias/<?= (int) $categoria["id"] ?>/edit" class="btn btn-sm btn-outline-secondary">Editar</a><?php if ($categoria["activo"]): ?><form class="d-inline" method="post" action="/admin/categorias/<?= (int) $categoria["id"] ?>/delete"><button class="btn btn-sm btn-outline-danger" type="submit">Desactivar</button></form><?php endif; ?>
    <form class="d-inline" method="post" action="/admin/categorias/<?= (int) $categoria["id"] ?>/purge" onsubmit="return confirm('¿Eliminar definitivamente esta categoría? Esta acción no se puede deshacer.');"><button class="btn btn-sm btn-danger" type="submit" <?= (int) $categoria["productos_count"] > 0 ? "disabled title=\"Primero eliminá permanentemente los productos vinculados.\"" : "" ?>>Eliminar definitivamente</button></form>
  </td></tr><?php endforeach; ?>
  </tbody></table></div>
</div>
