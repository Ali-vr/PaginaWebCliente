<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <h1 class="mb-4">Recuperar contraseña</h1>
      <p>Ingresá el email de tu cuenta. Si está registrado, te enviaremos un enlace para crear una contraseña nueva.</p>
      <?php if (!empty($error)): ?><div class="alert alert-danger" role="alert"><?= html($error) ?></div><?php endif; ?>
      <form method="post" action="/recuperar-contrasena">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" value="<?= html($email ?? "") ?>" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Enviar enlace</button>
      </form>
      <p class="mt-3 mb-0"><a href="/login">Volver a iniciar sesión</a></p>
    </div>
  </div>
</div>
