(function () {
  var checks = document.querySelectorAll('.filtrador-check');
  var checkStock = document.querySelector('.filtrador-check-stock');
  var cards = document.querySelectorAll('.producto-filtrable');
  var contador = document.getElementById('contadorResultados');
  var sinResultados = document.getElementById('sinResultados');
  var grilla = document.getElementById('grilla-productos');

  if (!cards.length) return;

  function aplicarFiltros() {
    var categoriasActivas = [];
    checks.forEach(function (check) {
      if (check.checked) categoriasActivas.push(check.getAttribute('data-categoria'));
    });

    var soloStock = checkStock && checkStock.checked;
    var visibles = 0;

    cards.forEach(function (card) {
      var pasaCategoria = categoriasActivas.length === 0 || categoriasActivas.indexOf(card.getAttribute('data-categoria')) !== -1;
      var pasaStock = !soloStock || card.getAttribute('data-stock') === 'disponible';
      card.style.display = pasaCategoria && pasaStock ? '' : 'none';
      if (pasaCategoria && pasaStock) visibles++;
    });

    if (contador) contador.textContent = visibles + ' producto' + (visibles !== 1 ? 's' : '');
    if (sinResultados && grilla) {
      sinResultados.classList.toggle('d-none', visibles !== 0);
      grilla.classList.toggle('d-none', visibles === 0);
    }
  }

  checks.forEach(function (check) { check.addEventListener('change', aplicarFiltros); });
  if (checkStock) checkStock.addEventListener('change', aplicarFiltros);

  window.limpiarFiltros = function () {
    checks.forEach(function (check) { check.checked = false; });
    if (checkStock) checkStock.checked = false;
    aplicarFiltros();
  };

  document.querySelectorAll('#btnLimpiarFiltros').forEach(function (button) {
    button.addEventListener('click', window.limpiarFiltros);
  });

  aplicarFiltros();
})();
