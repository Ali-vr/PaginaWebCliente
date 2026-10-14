<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <h1 class="mb-3">Solicitar un producto a medida</h1>
      <p class="text-muted">Contanos qué necesitás y adjuntá un plano o imagen de referencia. Revisaremos la solicitud y te enviaremos una respuesta con el presupuesto estimado.</p>
      <?php if (!empty($error)): ?><div class="alert alert-danger" role="alert"><?= html($error) ?></div><?php endif; ?>
      <form method="post" action="/personalizacion" enctype="multipart/form-data" class="card border-0 shadow-sm p-4">
        <div class="mb-3">
          <label for="descripcion" class="form-label">Descripción detallada</label>
          <textarea class="form-control" id="descripcion" name="descripcion" rows="7" minlength="20" maxlength="5000" required><?= html($descripcion ?? "") ?></textarea>
          <div class="form-text">Incluí medidas, materiales, terminaciones y cualquier detalle importante.</div>
        </div>
        <div class="mb-3">
          <label for="archivo" class="form-label">Plano o imagen de referencia</label>
          <input class="form-control" type="file" id="archivo" name="archivo" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" required>
          <div class="form-text">Formatos permitidos: PDF, JPG o PNG. Tamaño máximo: 10 MB.</div>
        </div>
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">Enviar solicitud</button>
          <a href="/mi-cuenta" class="btn btn-outline-secondary">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
</div>
