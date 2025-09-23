<div class="container">
  <h1 class="text-center">Registra tu Negocio</h1>
  <p class="text-center text-muted">
    Completa el formulario para registrar tu negocio en nuestra plataforma.
  </p>

  <form x-data @submit.prevent="
    const formData = new FormData($el);

    fetch('./api/registrarse', { body: formData, method: 'post' })
      .then(async response => {
        if (!response.ok) {
          throw new Error(await response.text());
        }

        return response;
      })
      .then(() => {
        location.href = './ingresar';
      })
      .catch(error => alert(error.message));

  ">

    <!-- 📌 SECCIÓN: INFORMACIÓN DEL NEGOCIO -->
    <section class="col d-grid gap-3">
      <h2 class="text-center">Información del Negocio</h2>

      <div>
        <label for="nombre_negocio" class="form-label">Nombre del Negocio</label>
        <input
          id="nombre_negocio"
          name="nombre_negocio"
          class="form-control"
          placeholder="Ejemplo: Panadería Delicias"
          required />
      </div>

      <div>
        <label for="estado" class="form-label">Estado</label>
        <input
          id="estado"
          name="estado"
          class="form-control"
          placeholder="Ejemplo: Miranda"
          required />
      </div>

      <div>
        <label for="ciudad" class="form-label">Ciudad</label>
        <input
          id="ciudad"
          name="ciudad"
          class="form-control"
          placeholder="Ejemplo: Caracas"
          required />
      </div>

      <div>
        <label for="sector" class="form-label">Sector</label>
        <input
          id="sector"
          name="sector"
          class="form-control"
          placeholder="Ejemplo: Chacao"
          required />
      </div>

      <div>
        <label for="telefono" class="form-label">Teléfono</label>
        <input
          type="tel"
          id="telefono"
          name="telefono"
          class="form-control"
          pattern="^\+\d{7,15}$"
          placeholder="Ejemplo: +584141234567"
          required />
      </div>

      <div>
        <label for="correo" class="form-label">Correo Electrónico</label>
        <input
          type="email"
          id="correo"
          name="correo"
          class="form-control"
          placeholder="Ejemplo: negocio@correo.com"
          required />
      </div>

      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="es_principal" name="es_principal" checked>
        <label class="form-check-label" for="es_principal">
          Marcar como negocio principal
        </label>
      </div>
    </section>

    <!-- 📌 SECCIÓN: INFORMACIÓN DEL ADMINISTRADOR -->
    <section class="col d-grid gap-3">
      <h2 class="text-center">Información del Administrador</h2>

      <div>
        <label for="primer_nombre" class="form-label">Primer Nombre</label>
        <input
          id="primer_nombre"
          name="primer_nombre"
          class="form-control"
          placeholder="Ejemplo: Juan"
          required />
      </div>

      <div>
        <label for="segundo_nombre" class="form-label">Segundo Nombre (Opcional)</label>
        <input
          id="segundo_nombre"
          name="segundo_nombre"
          class="form-control"
          placeholder="Ejemplo: Carlos" />
      </div>

      <div>
        <label for="primer_apellido" class="form-label">Primer Apellido</label>
        <input
          id="primer_apellido"
          name="primer_apellido"
          class="form-control"
          placeholder="Ejemplo: Pérez"
          required />
      </div>

      <div>
        <label for="segundo_apellido" class="form-label">Segundo Apellido (Opcional)</label>
        <input
          id="segundo_apellido"
          name="segundo_apellido"
          class="form-control"
          placeholder="Ejemplo: López" />
      </div>

      <div>
        <label for="clave" class="form-label">Contraseña</label>
        <input
          type="password"
          id="clave"
          name="clave"
          class="form-control"
          placeholder="Tu contraseña segura"
          required
          onchange="document.querySelector('#clave_confirm').setAttribute('pattern', this.value)" />
      </div>

      <div>
        <label for="clave_confirm" class="form-label">Confirma tu Contraseña</label>
        <input
          type="password"
          id="clave_confirm"
          name="clave_confirm"
          class="form-control"
          placeholder="Confirma tu contraseña"
          required
          title="Las contraseñas deben ser iguales" />
      </div>
    </section>

    <!-- 📌 BOTONES -->
    <footer class="col-lg-12 text-center">
      <button class="btn btn-primary">Registrar Negocio</button>
      <button type="reset" class="btn btn-outline-dark">Limpiar</button>
    </footer>
  </form>

  <script>
    document.querySelector('form').addEventListener('submit', function(event) {
      let valid = true;
      this.querySelectorAll('[required]').forEach(function(input) {
        if (!input.value) {
          valid = false;
          input.classList.add('is-invalid');
        } else {
          input.classList.remove('is-invalid');
        }
      });
      if (!valid) {
        event.preventDefault();
        alert('Por favor, completa todos los campos obligatorios.');
      }
    });
  </script>
