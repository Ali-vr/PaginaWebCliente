<?php
$esProducto = $tipo === "producto";
$esCategoria = $tipo === "categoria";
$valor = static fn(string $campo): string => html((string) ($item[$campo] ?? ""));
?>
<div class="container my-5">
  <h1 class="mb-4"><?= html($title) ?></h1>
  <?php if (!empty($error)): ?><div class="alert alert-danger"><?= html($error) ?></div><?php endif; ?>
  <form method="post" class="row g-3">
    <?php if ($esProducto): ?>
      <div class="col-md-8"><label class="form-label">Nombre</label><input class="form-control" name="nombre" value="<?= $valor("nombre") ?>" required></div>
      <div class="col-md-4"><label class="form-label">Precio</label><input class="form-control" type="number" step="0.01" min="0" name="precio" value="<?= $valor("precio") ?>" required></div>
      <div class="col-12"><label class="form-label">Descripción</label><textarea class="form-control" name="descripcion" rows="4"><?= $valor("descripcion") ?></textarea></div>
      <div class="col-md-6"><label class="form-label">Categoría</label><select class="form-select" name="categoria_id" required><option value="">Seleccionar</option><?php foreach ($categoriasAdmin as $categoria): ?><option value="<?= (int) $categoria["id"] ?>" <?= (int) ($item["categoria_id"] ?? 0) === (int) $categoria["id"] ? "selected" : "" ?>><?= html($categoria["nombre"]) ?></option><?php endforeach; ?></select></div>
      <div class="col-md-6"><label class="form-label">Imagen</label><input class="form-control" name="imagen" value="<?= $valor("imagen") ?>"></div>
      <div class="col-md-6"><label class="form-label">Material</label><input class="form-control" name="material" value="<?= $valor("material") ?>"></div>
      <div class="col-md-6"><label class="form-label">Medidas</label><input class="form-control" name="medidas" value="<?= $valor("medidas") ?>"></div>
      <div class="col-12 form-check ms-2"><input class="form-check-input" type="checkbox" name="stock" id="stock" <?= !empty($item["stock"]) ? "checked" : "" ?>><label class="form-check-label" for="stock">Disponible en stock</label></div>
    <?php elseif ($esCategoria): ?>
      <div class="col-md-6"><label class="form-label">Nombre</label><input class="form-control" name="nombre" value="<?= $valor("nombre") ?>" required></div>
      <div class="col-md-6"><label class="form-label">Slug</label><input class="form-control" name="slug" value="<?= $valor("slug") ?>" required></div>
    <?php else: ?>
      <div class="col-md-8"><label class="form-label">Título</label><input class="form-control" name="titulo" value="<?= $valor("titulo") ?>" required></div>
      <div class="col-md-4"><label class="form-label">Orden</label><input class="form-control" type="number" name="orden" value="<?= $valor("orden") ?>"></div>
      <div class="col-12"><label class="form-label">Descripción</label><textarea class="form-control" name="descripcion" rows="3"><?= $valor("descripcion") ?></textarea></div>
      <div class="col-md-8"><label class="form-label">Imagen</label><input class="form-control" name="imagen" value="<?= $valor("imagen") ?>" required></div>
      <div class="col-md-4"><label class="form-label">Link</label><input class="form-control" name="link" value="<?= $valor("link") ?>"></div>
    <?php endif; ?>
    <div class="col-12"><button class="btn btn-primary" type="submit">Guardar</button> <a class="btn btn-outline-secondary" href="/admin">Cancelar</a></div>
  </form>
</div>
