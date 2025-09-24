<form class="d-grid gap-3 pb-3" x-data @submit.prevent="
  const formData = new FormData($el);

  fetch('./api/ingresar', { body: formData, method: 'post' })
    .then(async response => {
      if (!response.ok) {
        throw new Error(await response.text());
      }

      return response;
    })
    .then(() => {
      location.href = './administracion';
    })
    .catch(error => alert(error.message));
">
  <div>
    <label for="correo" class="form-label">Correo Electrónico</label>
    <input type="email" class="form-control" id="correo" name="correo" required />
  </div>
  <div>
    <label for="clave" class="form-label">Contraseña</label>
    <input
      type="password"
      class="form-control"
      id="clave"
      name="clave"
      required />
  </div>
  <button class="btn btn-danger">Iniciar Sesión</button>
</form>

<p class="text-center m-0">
  ¿No tienes cuenta? <a href="./registrarse" class="link-danger">Regístrate aquí</a>
</p>
