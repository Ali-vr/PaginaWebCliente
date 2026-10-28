<?php require_once __DIR__ . '/../layouts/base.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Recuperar Contraseña</h4>
                </div>
                <div class="card-body">
                    <p>Ingresa tu correo electrónico y te enviaremos las instrucciones para restablecer tu contraseña.</p>
                    <form action="/recuperar-contrasena" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Enviar enlace de recuperación</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>