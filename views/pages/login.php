<form action="/ingresar" method="POST">
    <div class="mb-3">
        <label for="correo" class="form-label">Correo Electrónico</label>
        <input type="email" class="form-control" id="correo" name="correo" required>
    </div>
    <div class="mb-3">
        <label for="clave" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="clave" name="clave" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
</form>

<p class="text-center mt-3">
    ¿No tienes cuenta? <a href="/registrarse">Regístrate aquí</a>
</p>

