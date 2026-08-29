<div id="carruselOfertas" class="carousel slide mb-5" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carruselOfertas" data-bs-slide-to="0" class="active"></button>
    <button type="button" data-bs-target="#carruselOfertas" data-bs-slide-to="1"></button>
    <button type="button" data-bs-target="#carruselOfertas" data-bs-slide-to="2"></button>
  </div>

  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="https://placehold.co/1200x400/3a2a1e/f6efe4?text=Hecho+a+mano" class="d-block w-100" alt="Hecho a mano">
      <div class="carousel-caption"><h2>Hecho a mano, pieza por pieza</h2><p>Muebles de madera maciza, sin atajos.</p></div>
    </div>
    <div class="carousel-item">
      <img src="https://placehold.co/1200x400/8b5a2b/f6efe4?text=Comedores" class="d-block w-100" alt="Comedores">
      <div class="carousel-caption"><h2>Comedores a medida</h2><p>Diseñamos según el espacio que tengas.</p></div>
    </div>
    <div class="carousel-item">
      <img src="https://placehold.co/1200x400/d9b98c/2b231c?text=Envios" class="d-block w-100" alt="Envíos">
      <div class="carousel-caption"><h2>Envíos a todo el país</h2><p>Tu pedido, embalado con el mismo cuidado con el que lo hacemos.</p></div>
    </div>
  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#carruselOfertas" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
  <button class="carousel-control-next" type="button" data-bs-target="#carruselOfertas" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
</div>

<div class="container mb-5">
  <h2 class="mb-4">Todos los productos</h2>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php foreach ($productos as $producto): ?>
      <?= $this->fetch("layouts/producto-card.php", ["producto" => $producto]) ?>
    <?php endforeach; ?>
  </div>
</div>