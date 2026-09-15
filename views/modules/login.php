<div class="card shadow-sm p-4 style-login" style="max-width: 400px; width: 100%;">
    <div class="text-center mb-4">
        <h3 class="fw-bold text-primary">SGA Minorista</h3>
        <p class="text-muted">Ingresá tus credenciales para acceder</p>
    </div>

    <form method="post">
        <div class="mb-3">
            <label for="ingUsuario" class="form-label">Usuario</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" id="ingUsuario" name="ingUsuario" placeholder="Usuario" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="ingPassword" class="form-label">Contraseña</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" id="ingPassword" name="ingPassword" placeholder="Contraseña" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-2">Ingresar</button>
    </form>
</div>