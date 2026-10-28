<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="/">Mi Tienda</a>

    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['usuario']) || isset($_SESSION['user_id'])): ?>
          <!-- Opciones para usuario autenticado -->
          <li class="nav-item">
            <a class="nav-link" href="/mi-perfil">Mi Perfil</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/mis-pedidos">Mis Pedidos</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-outline-danger btn-sm ms-2" href="/logout">Cerrar Sesión</a>
          </li>
        <?php else: ?>
          <!-- Opciones para visitante / usuario no autenticado -->
          <li class="nav-item">
            <a class="nav-link" href="/login">Iniciar Sesión</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/registro">Registrarse</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Limpieza adicional de almacenamiento en frontend tras cerrar sesión -->
<script>
  // Elimina datos locales temporales si el usuario ya no tiene sesión
  <?php if (!isset($_SESSION['usuario'])): ?>
    localStorage.removeItem('user_token');
    sessionStorage.clear();
  <?php endif; ?>
</script>