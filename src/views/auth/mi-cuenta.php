<div class="container my-5">
  <h1 class="mb-4">Mi cuenta</h1>
  <div class="card p-4" style="max-width: 560px;">
    <dl class="row mb-0">
      <dt class="col-sm-4">Nombre</dt><dd class="col-sm-8"><?= html($usuario["nombre"] ?? "") ?></dd>
      <dt class="col-sm-4">Email</dt><dd class="col-sm-8"><?= html($usuario["email"] ?? "") ?></dd>
      <dt class="col-sm-4">Rol</dt><dd class="col-sm-8"><?= html($usuario["rol"] ?? "usuario") ?></dd>
    </dl>
  </div>
</div>
