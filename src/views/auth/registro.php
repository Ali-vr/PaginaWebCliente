<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <h1 class="mb-4">Crear cuenta</h1>
      <?php if (!empty($error)): ?><div class="alert alert-danger"><?= html($error) ?></div><?php endif; ?>
      <form method="post" action="/registro">
        <div class="mb-3"><label for="nombre" class="form-label">Nombre</label><input type="text" class="form-control" id="nombre" name="nombre" value="<?= html($nombre ?? "") ?>" required></div>
        <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" value="<?= html($email ?? "") ?>" required></div>
        <div class="mb-3"><label for="password" class="form-label">Contraseña</label><input type="password" class="form-control" id="password" name="password" minlength="8" required></div>
        <div class="mb-3"><label for="password_confirmation" class="form-label">Confirmar contraseña</label><input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="8" required></div>
        <button type="submit" class="btn btn-primary w-100">Crear cuenta</button>
      </form>
      <p class="mt-3 mb-0">¿Ya tenés cuenta? <a href="/login">Iniciá sesión</a>.</p>
    </div>
  </div>
</div>
