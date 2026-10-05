<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <h1 class="mb-3">Contacto</h1>
      <p class="text-muted">¿Tenés dudas sobre un pedido o querés encargar algo a medida? Escribinos y te respondemos en breve.</p>

      <?php if (($success ?? null) !== null): ?>
        <div class="alert alert-success" role="alert">
          <?= html((string) $success) ?>
        </div>
      <?php endif; ?>

      <?php if (($error ?? null) !== null): ?>
        <div class="alert alert-danger" role="alert">
          <?= html((string) $error) ?>
        </div>
      <?php endif; ?>

      <form method="post" action="/contacto" class="card border-0 shadow-sm p-4">
        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre</label>
          <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
          <label for="mensaje" class="form-label">Mensaje</label>
          <textarea class="form-control" id="mensaje" name="mensaje" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary px-4">Enviar</button>
      </form>
    </div>
  </div>
</div>