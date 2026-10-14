<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <h1 class="mb-4">Restablecer contraseña</h1>
      <?php if (!empty($error)): ?><div class="alert alert-danger" role="alert"><?= html($error) ?></div><?php endif; ?>
      <?php if (!empty($tokenValido)): ?>
        <form method="post" action="/restablecer-contrasena">
          <input type="hidden" name="token" value="<?= html($token ?? "") ?>">
          <div class="mb-3">
            <label for="password" class="form-label">Nueva contraseña</label>
            <input type="password" class="form-control" id="password" name="password" minlength="8" required>
          </div>
          <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmar nueva contraseña</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="8" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Guardar contraseña</button>
        </form>
      <?php else: ?>
        <p><a href="/recuperar-contrasena">Solicitar un enlace nuevo</a></p>
      <?php endif; ?>
      <p class="mt-3 mb-0"><a href="/login">Volver a iniciar sesión</a></p>
    </div>
  </div>
</div>
