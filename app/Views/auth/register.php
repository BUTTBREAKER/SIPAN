<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - SIPAN</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #D4A574 0%, #8B6F47 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 900px;
            width: 100%;
            overflow: hidden;
        }

        .register-header {
            background: linear-gradient(135deg, #8B6F47 0%, #D4A574 100%);
            color: white;
            text-align: center;
            padding: 2rem;
        }

        .register-header h1 {
            font-weight: 700;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #D4A574;
            box-shadow: 0 0 0 0.2rem rgba(212, 165, 116, 0.25);
        }

        .btn-register {
            background: linear-gradient(135deg, #D4A574 0%, #8B6F47 100%);
            border: none;
            color: white;
            padding: 1rem;
            border-radius: 10px;
            width: 100%;
            margin-top: 1rem;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .step {
            padding: 1rem;
            text-align: center;
            border-bottom: 3px solid #e0e0e0;
            font-weight: 600;
        }

        .step.active {
            border-bottom-color: #D4A574;
            color: #D4A574;
        }

        .step.completed {
            border-bottom-color: #28a745;
            color: #28a745;
        }
    </style>
</head>

<body>
    <div class="register-container" x-data="registerApp()">

        <!-- HEADER -->
        <div class="register-header">
            <i class="fas fa-bread-slice fa-3x mb-3"></i>
            <h1>Registro en SIPAN</h1>
            <p>Sistema Integral para Panaderías</p>
        </div>

        <!-- BODY -->
        <div class="register-body p-4">

            <!-- INDICADOR DE PASOS -->
            <div class="step-indicator">
                <div class="step" :class="{'active': step===1, 'completed':step>1}">
                    <i class="fas fa-user"></i> Datos Personales
                </div>
                <div class="step" :class="{'active': step===2, 'completed':step>2}">
                    <i class="fas fa-store"></i> Sucursal
                </div>
                <div class="step" :class="{'active': step===3}">
                    <i class="fas fa-lock"></i> Credenciales
                </div>
            </div>

            <form @submit.prevent="handleSubmit">

                <!-- PASO 1 -->
                <div x-show="step === 1">

                    <h3 class="mb-4">Datos Personales</h3>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Primer nombre *</label>
                            <input type="text" class="form-control"
                                x-model.trim="formData.primer_nombre"
                                pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{2,40}$"
                                required
                                placeholder="Juan">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Segundo nombre</label>
                            <input type="text" class="form-control"
                                x-model.trim="formData.segundo_nombre"
                                pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{0,40}$"
                                placeholder="Opcional">
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apellido paterno *</label>
                            <input type="text" class="form-control"
                                x-model.trim="formData.apellido_paterno"
                                pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{2,40}$"
                                required
                                placeholder="Pérez">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apellido materno</label>
                            <input type="text" class="form-control"
                                x-model.trim="formData.apellido_materno"
                                pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{0,40}$"
                                placeholder="Opcional">
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cédula o RIF *</label>
                            <input type="text" class="form-control"
                                x-model.trim="formData.dni"
                                required
                                pattern="^[V|E|J|G]-[0-9]{7,9}$"
                                placeholder="V-12345678">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono *</label>
                            <input type="text" class="form-control"
                                x-model.trim="formData.telefono"
                                required
                                pattern="^0[0-9]{3}-[0-9]{7}$"
                                placeholder="0412-1234567">
                        </div>
                    </div>

                    <button type="button" @click="nextStep" class="btn btn-register">
                        Siguiente <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                <!-- PASO 2 -->
                <div x-show="step === 2">

                    <h3 class="mb-4">Información de Sucursal</h3>

                    <div class="mb-3">
                        <label class="form-label">Clave de sucursal *</label>

                        <input type="text" class="form-control"
                            maxlength="8"
                            minlength="8"
                            pattern="^[A-Z0-9]{8}$"
                            style="text-transform: uppercase;"
                            x-model.trim="formData.clave_sucursal"
                            placeholder="ABC12345"
                            required>
                    </div>

                    <button type="button"
                        @click="verificarClaveSucursal"
                        class="btn btn-info w-100 mb-3">
                        <i class="fas fa-search"></i> Verificar clave
                    </button>

                    <div x-show="sucursalVerificada" class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        Sucursal verificada:
                        <strong x-text="sucursalInfo.nombre"></strong>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" @click="prevStep" class="btn btn-secondary flex-fill">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>

                        <button type="button" @click="nextStep"
                            class="btn btn-register flex-fill"
                            :disabled="!sucursalVerificada">
                            Siguiente <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                </div>

                <!-- PASO 3 -->
                <div x-show="step === 3">

                    <h3 class="mb-4">Credenciales</h3>

                    <div class="mb-3">
                        <label class="form-label">Correo electrónico *</label>
                        <input type="email" class="form-control"
                            required
                            x-model.trim="formData.correo"
                            placeholder="correo@ejemplo.com">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contraseña *</label>

                        <input type="password"
                            class="form-control"
                            x-model="formData.clave"
                            required
                            minlength="8"
                            placeholder="Mínimo 8 caracteres"
                            pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}">
                        <small class="text-muted">
                            Debe contener mayúscula, minúscula, número y símbolo.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirmar contraseña *</label>
                        <input type="password"
                            class="form-control"
                            x-model="formData.clave_confirmacion"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rol *</label>
                        <select class="form-select"
                            x-model="formData.rol"
                            required>
                            <option value="">Seleccione</option>
                            <option value="cajero">Cajero</option>
                            <option value="empleado">Empleado</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" @click="prevStep" class="btn btn-secondary flex-fill">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>

                        <button type="submit" class="btn btn-register flex-fill">
                            <i class="fas fa-user-plus"></i> Registrarse
                        </button>
                    </div>
                </div>

            </form>

            <div class="login-link text-center mt-3">
                ¿Ya tienes cuenta? <a href="/login">Iniciar sesión</a>
            </div>

        </div>
    </div>


    <script>
        function registerApp() {
            return {
                step: 1,
                sucursalVerificada: false,
                sucursalInfo: {},
                isSubmitting: false,

                formData: {
                    primer_nombre: "",
                    segundo_nombre: "",
                    apellido_paterno: "",
                    apellido_materno: "",
                    dni: "",
                    telefono: "",
                    clave_sucursal: "",
                    id_sucursal: "",
                    correo: "",
                    clave: "",
                    clave_confirmacion: "",
                    rol: ""
                },

                nextStep() {
                    if (this.validateStep()) this.step++;
                },

                prevStep() {
                    this.step--;
                },

                validateStep() {
                    if (this.step === 1) {
                        if (!this.validatePaso1()) return false;
                    }

                    if (this.step === 2) {
                        if (!this.sucursalVerificada) {
                            Swal.fire("Error", "Debe verificar la clave de sucursal.", "error");
                            return false;
                        }
                    }

                    return true;
                },

                validatePaso1() {
                    const patterns = {
                        nombre: /^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{2,40}$/,
                        dni: /^[V|E|J|G]-[0-9]{7,9}$/,
                        tel: /^0[0-9]{3}-[0-9]{7}$/
                    };

                    if (!patterns.nombre.test(this.formData.primer_nombre))
                        return this.error("Primer nombre inválido.");

                    if (!patterns.nombre.test(this.formData.apellido_paterno))
                        return this.error("Apellido paterno inválido.");

                    if (!patterns.dni.test(this.formData.dni))
                        return this.error("Formato de Cédula/RIF inválido.");

                    if (!patterns.tel.test(this.formData.telefono))
                        return this.error("Formato de teléfono inválido.");

                    return true;
                },

                error(msg) {
                    Swal.fire("Error", msg, "error");
                    return false;
                },

                async verificarClaveSucursal() {
                    if (!this.formData.clave_sucursal || this.formData.clave_sucursal.length !== 8) {
                        this.error("Debe ingresar una clave de 8 caracteres.");
                        return;
                    }

                    try {
                        const response = await fetch('/auth/verificar-clave-sucursal', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                clave_sucursal: this.formData.clave_sucursal
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.sucursalVerificada = true;
                            this.sucursalInfo = result.sucursal;
                            this.formData.id_sucursal = result.sucursal.id;

                            Swal.fire("Sucursal verificada",
                                `Sucursal: ${result.sucursal.nombre}`,
                                "success");
                        } else {
                            this.sucursalVerificada = false;
                            Swal.fire("Error", result.message, "error");
                        }

                    } catch (e) {
                        Swal.fire("Error", "No se pudo conectar al servidor.", "error");
                    }
                },

                async handleSubmit() {
                    if (this.isSubmitting) return;

                    if (this.formData.clave !== this.formData.clave_confirmacion) {
                        return this.error("Las contraseñas no coinciden.");
                    }

                    const passPattern = /(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}/;
                    if (!passPattern.test(this.formData.clave)) {
                        return this.error("La contraseña no cumple los requisitos.");
                    }

                    if (!this.formData.rol) {
                        return this.error("Debe seleccionar un rol.");
                    }

                    this.isSubmitting = true;

                    try {
                        const response = await fetch('/auth/register', {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const result = await response.json();

                        if (result.success) {
                            Swal.fire("Registro exitoso", "Ahora puedes iniciar sesión.", "success")
                                .then(() => window.location.href = "/login");
                        } else {
                            this.error(result.message);
                        }

                    } catch (e) {
                        this.error("Error en la conexión con el servidor.");
                    }

                    this.isSubmitting = false;
                }
            };
        }
    </script>

</body>

</html>