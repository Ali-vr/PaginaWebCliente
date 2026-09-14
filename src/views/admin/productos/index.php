<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4"><h1 class="mb-0">Productos</h1><a href="/admin/productos/create" class="btn btn-primary">Nuevo producto</a></div>
  <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Estado</th><th></th></tr></thead><tbody>
  <?php foreach ($productos as $producto): ?><tr><td><?= html($producto["nombre"]) ?></td><td><?= html($producto["categoria_nombre"]) ?></td><td>$<?= number_format((float) $producto["precio"], 2, ",", ".") ?></td><td><?= $producto["stock"] ? "Sí" : "No" ?></td><td><?= $producto["activo"] ? "Activo" : "Inactivo" ?></td><td class="text-nowrap"><a href="/admin/productos/<?= (int) $producto["id"] ?>/edit" class="btn btn-sm btn-outline-secondary">Editar</a><?php if ($producto["activo"]): ?><form class="d-inline" method="post" action="/admin/productos/<?= (int) $producto["id"] ?>/delete"><button class="btn btn-sm btn-outline-danger" type="submit">Desactivar</button></form><?php endif; ?></td></tr><?php endforeach; ?>
  </tbody></table></div>
</div>
