<div class="container my-5">
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Inicio</a></li>
      <li class="breadcrumb-item"><a href="/categoria/<?= html($producto["categoria"]) ?>"><?= html($categorias[$producto["categoria"]]) ?></a></li>
      <li class="breadcrumb-item active" aria-current="page"><?= html($producto["nombre"]) ?></li>
    </ol>
  </nav>

  <div class="row g-5">
    <div class="col-md-6">
      <img src="<?= html($producto["imagen"]) ?>" class="img-fluid rounded" alt="<?= html($producto["nombre"]) ?>">
    </div>
    <div class="col-md-6">
      <h1 class="mb-2"><?= html($producto["nombre"]) ?></h1>

      <?php if ($producto["stock"]): ?>
        <span class="badge text-bg-success mb-3">En stock</span>
      <?php else: ?>
        <span class="badge text-bg-secondary mb-3">Sin stock</span>
      <?php endif; ?>

      <p class="producto-precio fs-3 mb-4">$<?= number_format($producto["precio"], 0, ",", ".") ?></p>
      <p><?= html($producto["descripcion"]) ?></p>

      <ul class="list-unstyled text-secondary small mb-4">
        <li><strong>Material:</strong> <?= html($producto["material"]) ?></li>
        <li><strong>Medidas:</strong> <?= html($producto["medidas"]) ?></li>
      </ul>

      <form action="/carrito/agregar" method="post" class="d-flex align-items-center gap-2 mb-3" style="max-width: 280px;">
        <input type="hidden" name="producto_id" value="<?= (int) $producto["id"] ?>">
        <input type="number" name="cantidad" class="form-control" value="1" min="1" aria-label="Cantidad">
        <button type="submit" class="btn btn-primary text-nowrap" <?= $producto["stock"] ? "" : "disabled" ?>>Agregar al carrito</button>
      </form>

      <a href="https://wa.me/5491100000000?text=<?= rawurlencode("Hola! Quería consultar por este producto: " . $producto["nombre"] . " - " . $urlActual) ?>" target="_blank" rel="noopener" class="btn btn-outline-success d-inline-flex align-items-center gap-2"><svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326M7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.337-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/></svg> Consultar por WhatsApp</a>
    </div>
  </div>

  <?php if (!empty($relacionados)): ?>
    <div class="mt-5">
      <h2 class="mb-4">También te puede interesar</h2>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php foreach ($relacionados as $relacionado): ?>
          <?= $this->fetch("layouts/producto-card.php", ["producto" => $relacionado]) ?>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</div>