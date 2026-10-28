<?php require_once __DIR__ . '/layouts/base.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4">Contáctanos</h2>

            <!-- Alertas dinámicas (AJAX o Sesión) -->
            <div id="alert-container">
                <?php if (isset($_SESSION['exito_contacto'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['exito_contacto']; unset($_SESSION['exito_contacto']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_general_contacto'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error_general_contacto']; unset($_SESSION['error_general_contacto']); ?>
                    </div>
                <?php endif; ?>
            </div>

            <form id="formContacto" action="/contacto" method="POST" novalidate>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre completo *</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                    <div class="invalid-feedback" id="err-nombre">El nombre es obligatorio.</div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico *</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="invalid-feedback" id="err-email">Ingrese un correo electrónico válido.</div>
                </div>

                <div class="mb-3">
                    <label for="mensaje" class="form-label">Mensaje *</label>
                    <textarea class="form-control" id="mensaje" name="mensaje" rows="5" required></textarea>
                    <div class="invalid-feedback" id="err-mensaje">El mensaje es obligatorio.</div>
                </div>

                <button type="submit" id="btnEnviar" class="btn btn-primary w-100">
                    <span id="btnText">Enviar mensaje</span>
                    <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formContacto');
    const alertContainer = document.getElementById('alert-container');
    const btnEnviar = document.getElementById('btnEnviar');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        limpiarErrores();

        let valido = true;
        const nombre = document.getElementById('nombre');
        const email = document.getElementById('email');
        const mensaje = document.getElementById('mensaje');

        // Validación Frontend
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

        if (!valido) return;

        // Estado de carga en botón
        btnEnviar.disabled = true;
        btnText.textContent = 'Enviando...';
        btnSpinner.classList.remove('d-none');

        // Enviar vía AJAX al Backend
        fetch('/contacto', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(form)
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw data;
            }
            return data;
        })
        .then(data => {
            if (data.exito) {
                alertContainer.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${data.mensaje}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                form.reset(); // Limpia los campos del formulario tras el éxito
            }
        })
        .catch(err => {
            if (err.errores) {
                // Errores de validación devueltos por el servidor
                if (err.errores.nombre) mostrarError(nombre, 'err-nombre', err.errores.nombre);
                if (err.errores.email) mostrarError(email, 'err-email', err.errores.email);
                if (err.errores.mensaje) mostrarError(mensaje, 'err-mensaje', err.errores.mensaje);
            } else {
                // Error de servidor o fallo en Resend API (Manejo seguro)
                const msj = err.error_general || 'Ocurrió un error al enviar el mensaje. Por favor, inténtelo de nuevo más tarde.';
                alertContainer.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ${msj}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            }
        })
        .finally(() => {
            btnEnviar.disabled = false;
            btnText.textContent = 'Enviar mensaje';
            btnSpinner.classList.add('d-none');
        });
    });

    function mostrarError(element, errId, msg) {
        element.classList.add('is-invalid');
        const errDiv = document.getElementById(errId);
        if (errDiv) errDiv.textContent = msg;
    }

    function limpiarErrores() {
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    }
});
</script>