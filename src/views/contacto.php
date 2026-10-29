<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <h1 class="mb-3">Contacto</h1>
      <p class="text-muted">¿Tenés dudas sobre un pedido o querés encargar algo a medida? Escribinos y te respondemos en breve.</p>

      <div id="alert-container">
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
      </div>

      <form id="formContacto" method="post" action="/contacto" class="card border-0 shadow-sm p-4" novalidate>
        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre</label>
          <input type="text" class="form-control" id="nombre" name="nombre" required>
          <div class="invalid-feedback" id="err-nombre">El nombre es obligatorio.</div>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" required>
          <div class="invalid-feedback" id="err-email">Ingrese un correo electrónico válido.</div>
        </div>
        <div class="mb-3">
          <label for="mensaje" class="form-label">Mensaje</label>
          <textarea class="form-control" id="mensaje" name="mensaje" rows="5" required></textarea>
          <div class="invalid-feedback" id="err-mensaje">El mensaje es obligatorio.</div>
        </div>
        <button type="submit" id="btnEnviar" class="btn btn-primary px-4">
          <span id="btnText">Enviar</span>
          <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formContacto');
    const alertContainer = document.getElementById('alert-container');
    const btnEnviar = document.getElementById('btnEnviar');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    const mostrarError = (element, errId, msg) => {
        element.classList.add('is-invalid');
        const errorDiv = document.getElementById(errId);
        if (errorDiv) {
            errorDiv.textContent = msg;
        }
    };

    const limpiarErrores = () => {
        document.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
    };

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        limpiarErrores();

        const nombre = document.getElementById('nombre');
        const email = document.getElementById('email');
        const mensaje = document.getElementById('mensaje');

        let valido = true;

        if (!nombre.value.trim()) {
            mostrarError(nombre, 'err-nombre', 'El nombre es obligatorio.');
            valido = false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value.trim()) {
            mostrarError(email, 'err-email', 'El correo electrónico es obligatorio.');
            valido = false;
        } else if (!emailRegex.test(email.value.trim())) {
            mostrarError(email, 'err-email', 'El formato del correo electrónico no es válido.');
            valido = false;
        }

        if (!mensaje.value.trim()) {
            mostrarError(mensaje, 'err-mensaje', 'El mensaje es obligatorio.');
            valido = false;
        }

        if (!valido) {
            return;
        }

        btnEnviar.disabled = true;
        btnText.textContent = 'Enviando...';
        btnSpinner.classList.remove('d-none');

        fetch('/contacto', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form),
        })
            .then(async (response) => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw data;
                }
                return data;
            })
            .then((data) => {
                if (data.exito) {
                    alertContainer.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            ${data.mensaje}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    form.reset();
                    return;
                }

                throw data;
            })
            .catch((error) => {
                if (error && error.errores) {
                    if (error.errores.nombre) mostrarError(nombre, 'err-nombre', error.errores.nombre);
                    if (error.errores.email) mostrarError(email, 'err-email', error.errores.email);
                    if (error.errores.mensaje) mostrarError(mensaje, 'err-mensaje', error.errores.mensaje);
                    return;
                }

                const mensajeError = error && error.error_general ? error.error_general : 'Ocurrió un error al enviar el mensaje. Por favor, intentá de nuevo más tarde.';
                alertContainer.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ${mensajeError}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            })
            .finally(() => {
                btnEnviar.disabled = false;
                btnText.textContent = 'Enviar';
                btnSpinner.classList.add('d-none');
            });
    });
});
</script>
