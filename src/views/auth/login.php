<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <h1 class="mb-4">Iniciar sesión</h1>
      <?php if (!empty($error)): ?><div class="alert alert-danger"><?= html($error) ?></div><?php endif; ?>
      <form method="post" action="/login">
        <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" value="<?= html($email ?? "") ?>" required></div>
        <div class="mb-3"><label for="password" class="form-label">Contraseña</label><input type="password" class="form-control" id="password" name="password" required></div>
        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
      </form>
      <p class="mt-3 mb-0">¿No tenés cuenta? <a href="/registro">Registrate</a>.</p>
    </div>
  </div>
</div>
