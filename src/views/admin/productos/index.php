<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4"><h1 class="mb-0">Productos</h1><a href="/admin/productos/create" class="btn btn-primary">Nuevo producto</a></div>
  <?php if (!empty($mensaje)): ?><div class="alert alert-info" role="alert"><?= html($mensaje) ?></div><?php endif; ?>
  <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Imagen</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Estado</th><th></th></tr></thead><tbody>
  <?php foreach ($productos as $producto): ?><tr><td><?php if (!empty($producto["imagen"])): ?><img src="<?= html($producto["imagen"]) ?>" alt="<?= html($producto["nombre"]) ?>" width="56" height="48" style="object-fit: cover"><?php else: ?>—<?php endif; ?></td><td><?= html($producto["nombre"]) ?></td><td><?= html($producto["categoria_nombre"]) ?></td><td>$<?= number_format((float) $producto["precio"], 2, ",", ".") ?></td><td><?= $producto["stock"] ? "Sí" : "No" ?></td><td><?= $producto["activo"] ? "Activo" : "Inactivo" ?></td><td class="text-nowrap">
    <a href="/admin/productos/<?= (int) $producto["id"] ?>/edit" class="btn btn-sm btn-outline-secondary">Editar</a>
    <?php if ($producto["activo"]): ?><form class="d-inline" method="post" action="/admin/productos/<?= (int) $producto["id"] ?>/delete"><button class="btn btn-sm btn-outline-danger" type="submit">Desactivar</button></form><?php endif; ?>
    <form class="d-inline" method="post" action="/admin/productos/<?= (int) $producto["id"] ?>/purge" onsubmit="return confirm('¿Eliminar definitivamente este producto? Esta acción no se puede deshacer.');"><button class="btn btn-sm btn-danger" type="submit">Eliminar definitivamente</button></form>
  </td></tr><?php endforeach; ?>
  </tbody></table></div>
</div>
