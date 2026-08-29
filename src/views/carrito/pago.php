<div class="container my-5">
<?= $this->fetch("carrito/pasos.php", ["pasoActual" => 2]) ?>

  <form action="/carrito/pago" method="post" class="row g-4">
    <div class="col-lg-8">

      <h2 class="h5 mb-3">Dirección de envío</h2>
      <div class="bg-white rounded p-4 mb-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="nombre" class="form-label">Nombre y apellido</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
          </div>
          <div class="col-md-6">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="tel" class="form-control" id="telefono" name="telefono" required>
          </div>
          <div class="col-12">
            <label for="calle" class="form-label">Calle y número</label>
            <input type="text" class="form-control" id="calle" name="calle" required>
          </div>
          <div class="col-md-6">
            <label for="localidad" class="form-label">Localidad</label>
            <input type="text" class="form-control" id="localidad" name="localidad" required>
          </div>
          <div class="col-md-4">
            <label for="provincia" class="form-label">Provincia</label>
            <input type="text" class="form-control" id="provincia" name="provincia" required>
          </div>
          <div class="col-md-2">
            <label for="codigo_postal" class="form-label">CP</label>
            <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" required>
          </div>
        </div>
      </div>

      <h2 class="h5 mb-3">Método de pago</h2>
      <div class="mb-3">
        <label class="metodo-pago-card d-flex align-items-center gap-3 p-3 mb-2 rounded"><input type="radio" name="metodo_pago" value="tarjeta" id="metodo_tarjeta"> Tarjeta de crédito/débito</label>
        <label class="metodo-pago-card d-flex align-items-center gap-3 p-3 rounded"><input type="radio" name="metodo_pago" value="mercadopago" id="metodo_mp"> Mercado Pago</label>
      </div>

      <!-- Formulario de tarjeta (visible solo cuando se elige "tarjeta") -->
      <div id="form-tarjeta" class="bg-white rounded p-4 mb-4" style="display: none;">
        <h3 class="h6 mb-3">Datos de la tarjeta</h3>

        <!-- Vista previa visual de la tarjeta -->
        <div class="tarjeta-preview mb-4">
          <div class="tarjeta-frente">
            <div class="tarjeta-chip"></div>
            <div class="tarjeta-numero" id="preview-numero">•••• •••• •••• ••••</div>
            <div class="d-flex justify-content-between align-items-end">
              <div>
                <div class="tarjeta-label">Titular</div>
                <div class="tarjeta-valor" id="preview-nombre">NOMBRE APELLIDO</div>
              </div>
              <div class="text-end">
                <div class="tarjeta-label">Vence</div>
                <div class="tarjeta-valor" id="preview-vencimiento">MM/AA</div>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-12">
            <label for="tarjeta_numero" class="form-label">Número de tarjeta</label>
            <input type="text" class="form-control" id="tarjeta_numero" name="tarjeta_numero" placeholder="1234 5678 9012 3456" maxlength="19" autocomplete="cc-number">
          </div>
          <div class="col-md-6">
            <label for="tarjeta_titular" class="form-label">Titular (como figura en la tarjeta)</label>
            <input type="text" class="form-control" id="tarjeta_titular" name="tarjeta_titular" placeholder="NOMBRE APELLIDO" autocomplete="cc-name">
          </div>
          <div class="col-md-3">
            <label for="tarjeta_vencimiento" class="form-label">Vencimiento</label>
            <input type="text" class="form-control" id="tarjeta_vencimiento" name="tarjeta_vencimiento" placeholder="MM/AA" maxlength="5" autocomplete="cc-exp">
          </div>
          <div class="col-md-3">
            <label for="tarjeta_cvv" class="form-label">CVV</label>
            <input type="text" class="form-control" id="tarjeta_cvv" name="tarjeta_cvv" placeholder="•••" maxlength="4" autocomplete="cc-csc">
          </div>
          <div class="col-12">
            <label for="tarjeta_cuotas" class="form-label">Cuotas</label>
            <select class="form-select" id="tarjeta_cuotas" name="tarjeta_cuotas">
              <option value="1">1 cuota sin interés</option>
              <option value="3">3 cuotas sin interés</option>
              <option value="6">6 cuotas sin interés</option>
              <option value="12">12 cuotas</option>
            </select>
          </div>
        </div>

        <p class="small text-secondary mt-3 mb-0">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" class="me-1"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/></svg>
          Tus datos están seguros. Este es un entorno de prueba.
        </p>
      </div>

      <!-- Aviso Mercado Pago -->
      <div id="aviso-mp" class="bg-white rounded p-4 mb-4" style="display: none;">
        <p class="mb-0">Al confirmar el pedido vas a ser redirigido a <strong>Mercado Pago</strong> para completar el pago de forma segura.</p>
      </div>

    </div>

    <div class="col-lg-4">
      <div class="bg-white rounded p-4">
        <h2 class="h5 mb-3">Resumen</h2>
        <?php foreach ($items as $item): ?>
          <div class="d-flex justify-content-between small mb-2">
            <span><?= html($item["producto"]["nombre"]) ?> x<?= (int) $item["cantidad"] ?></span>
            <span>$<?= number_format($item["subtotal"], 0, ",", ".") ?></span>
          </div>
        <?php endforeach; ?>
        <hr>
        <div class="d-flex justify-content-between mb-3">
          <span>Total</span>
          <span class="fw-bold fs-5">$<?= number_format($total, 0, ",", ".") ?></span>
        </div>
        <button type="submit" class="btn btn-primary w-100">Confirmar pedido</button>
        <a href="/carrito" class="btn btn-link w-100 mt-2">Volver al carrito</a>
      </div>
    </div>
  </form>
</div>

<script>
  const radioTarjeta = document.getElementById("metodo_tarjeta");
  const radioMp = document.getElementById("metodo_mp");
  const formTarjeta = document.getElementById("form-tarjeta");
  const avisoMp = document.getElementById("aviso-mp");

  function actualizarMetodo() {
    if (radioTarjeta.checked) {
      formTarjeta.style.display = "block";
      avisoMp.style.display = "none";
      // Campos de tarjeta requeridos solo cuando está visible
      document.getElementById("tarjeta_numero").required = true;
      document.getElementById("tarjeta_titular").required = true;
      document.getElementById("tarjeta_vencimiento").required = true;
      document.getElementById("tarjeta_cvv").required = true;
    } else {
      formTarjeta.style.display = "none";
      avisoMp.style.display = "block";
      document.getElementById("tarjeta_numero").required = false;
      document.getElementById("tarjeta_titular").required = false;
      document.getElementById("tarjeta_vencimiento").required = false;
      document.getElementById("tarjeta_cvv").required = false;
    }
  }

  radioTarjeta.addEventListener("change", actualizarMetodo);
  radioMp.addEventListener("change", actualizarMetodo);

  // Vista previa de la tarjeta en tiempo real
  document.getElementById("tarjeta_numero").addEventListener("input", function () {
    let val = this.value.replace(/\D/g, "").substring(0, 16);
    // Formato: grupos de 4
    val = val.match(/.{1,4}/g)?.join(" ") ?? val;
    this.value = val;
    document.getElementById("preview-numero").textContent = val || "•••• •••• •••• ••••";
  });

  document.getElementById("tarjeta_titular").addEventListener("input", function () {
    document.getElementById("preview-nombre").textContent = this.value.toUpperCase() || "NOMBRE APELLIDO";
  });

  document.getElementById("tarjeta_vencimiento").addEventListener("input", function () {
    let val = this.value.replace(/\D/g, "").substring(0, 4);
    if (val.length >= 3) val = val.substring(0, 2) + "/" + val.substring(2);
    this.value = val;
    document.getElementById("preview-vencimiento").textContent = val || "MM/AA";
  });

  document.getElementById("tarjeta_cvv").addEventListener("input", function () {
    this.value = this.value.replace(/\D/g, "").substring(0, 4);
  });
</script>