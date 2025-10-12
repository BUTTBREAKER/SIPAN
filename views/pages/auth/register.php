<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - SIPAN</title>
    <base href="<?= str_replace('index.php', '', $_SERVER['SCRIPT_NAME']) ?>" />

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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }

        .register-header {
            background: linear-gradient(135deg, #8B6F47 0%, #D4A574 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .register-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .register-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #D4A574;
            box-shadow: 0 0 0 0.2rem rgba(212, 165, 116, 0.25);
        }

        .btn-register {
            background: linear-gradient(135deg, #D4A574 0%, #8B6F47 100%);
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
            margin-top: 1rem;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }

        .login-link a {
            color: #8B6F47;
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover {
            color: #D4A574;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .step {
            flex: 1;
            text-align: center;
            padding: 1rem;
            border-bottom: 3px solid #e0e0e0;
            color: #999;
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
        <div class="register-header">
            <i class="fas fa-bread-slice fa-3x mb-3"></i>
            <h1>Registro en SIPAN</h1>
            <p>Sistema Integral para Panaderías</p>
        </div>

        <div class="register-body">
            <!-- Indicador de pasos -->
            <div class="step-indicator">
                <div class="step" :class="{'active': step === 1, 'completed': step > 1}">
                    <i class="fas fa-user"></i> Datos Personales
                </div>
                <div class="step" :class="{'active': step === 2, 'completed': step > 2}">
                    <i class="fas fa-store"></i> Sucursal
                </div>
                <div class="step" :class="{'active': step === 3}">
                    <i class="fas fa-lock"></i> Credenciales
                </div>
            </div>

            <form @submit.prevent="handleSubmit()">
                <!-- Paso 1: Datos Personales -->
                <div x-show="step === 1">
                    <h3 class="mb-4">Datos Personales</h3>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" x-model="formData.nombre" required placeholder="Juan Pérez">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cédula o RIF <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" x-model="formData.dni" required placeholder="V-12345678">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" x-model="formData.telefono" required placeholder="0414-1234567">
                        </div>
                    </div>

                    <button type="button" @click="nextStep()" class="btn btn-register">
                        Siguiente <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                <!-- Paso 2: Sucursal -->
                <div x-show="step === 2">
                    <h3 class="mb-4">Información de Sucursal</h3>

                    <div class="mb-3">
                        <label class="form-label">Clave de Sucursal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" x-model="formData.clave_sucursal" required placeholder="Ingrese la clave proporcionada por el administrador" maxlength="8" style="text-transform: uppercase;">
                        <small class="text-muted">Solicite esta clave al administrador de la sucursal</small>
                    </div>

                    <button type="button" @click="verificarClaveSucursal()" class="btn btn-info w-100 mb-3">
                        <i class="fas fa-search"></i> Verificar Clave
                    </button>

                    <div x-show="sucursalVerificada" class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Sucursal verificada: <strong x-text="sucursalInfo.nombre"></strong>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" @click="prevStep()" class="btn btn-secondary flex-fill">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button type="button" @click="nextStep()" class="btn btn-register flex-fill" :disabled="!sucursalVerificada">
                            Siguiente <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Paso 3: Credenciales -->
                <div x-show="step === 3">
                    <h3 class="mb-4">Credenciales de Acceso</h3>

                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" x-model="formData.correo" required placeholder="correo@ejemplo.com">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" x-model="formData.clave" required minlength="6" placeholder="Mínimo 6 caracteres">
                        <small class="text-muted">Mínimo 6 caracteres</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" x-model="formData.clave_confirmacion" required placeholder="Repita la contraseña">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rol <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="formData.rol" required>
                            <option value="">Seleccionar rol</option>
                            <option value="cajero">Cajero</option>
                            <option value="empleado">Empleado</option>
                        </select>
                        <small class="text-muted">Solo puede seleccionar Cajero o Empleado</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" @click="prevStep()" class="btn btn-secondary flex-fill">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button type="submit" class="btn btn-register flex-fill">
                            <i class="fas fa-user-plus"></i> Registrarse
                        </button>
                    </div>
                </div>
            </form>

            <div class="login-link">
                ¿Ya tienes una cuenta? <a href="./login">Iniciar Sesión</a>
            </div>
        </div>
    </div>

    <script>
    function registerApp() {
        return {
            step: 1,
            sucursalVerificada: false,
            sucursalInfo: {},
            formData: {
                nombre: '',
                dni: '',
                telefono: '',
                clave_sucursal: '',
                correo: '',
                clave: '',
                clave_confirmacion: '',
                rol: ''
            },

            nextStep() {
                if (this.validateStep()) {
                    this.step++;
                }
            },

            prevStep() {
                this.step--;
            },

            validateStep() {
                if (this.step === 1) {
                    if (!this.formData.nombre || !this.formData.dni || !this.formData.telefono) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Complete los campos requeridos',
                            confirmButtonColor: '#D4A574'
                        });
                        return false;
                    }
                }

                if (this.step === 2) {
                    if (!this.sucursalVerificada) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Debe verificar la clave de sucursal',
                            confirmButtonColor: '#D4A574'
                        });
                        return false;
                    }
                }

                return true;
            },

            async verificarClaveSucursal() {
                if (!this.formData.clave_sucursal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ingrese la clave de sucursal',
                        confirmButtonColor: '#D4A574'
                    });
                    return;
                }

                try {
                    const response = await fetch('./auth/verificar-clave-sucursal', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ clave_sucursal: this.formData.clave_sucursal.toUpperCase() })
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.sucursalVerificada = true;
                        this.sucursalInfo = result.sucursal;
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucursal Verificada',
                            text: `Sucursal: ${result.sucursal.nombre}`,
                            confirmButtonColor: '#D4A574'
                        });
                    } else {
                        this.sucursalVerificada = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: result.message || 'Clave de sucursal inválida',
                            confirmButtonColor: '#D4A574'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión. Intente nuevamente.',
                        confirmButtonColor: '#D4A574'
                    });
                }
            },

            async handleSubmit() {
                // Validar contraseñas
                if (this.formData.clave !== this.formData.clave_confirmacion) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Las contraseñas no coinciden',
                        confirmButtonColor: '#D4A574'
                    });
                    return;
                }

                // Convertir clave_sucursal a mayúsculas
                this.formData.clave_sucursal = this.formData.clave_sucursal.toUpperCase();

                try {
                    const response = await fetch('./auth/register', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(this.formData)
                    });

                    const result = await response.json();

                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Registro exitoso!',
                            text: 'Tu cuenta ha sido creada. Ahora puedes iniciar sesión.',
                            confirmButtonColor: '#D4A574'
                        }).then(() => {
                            window.location.href = './login';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: result.message || 'Error al registrar usuario',
                            confirmButtonColor: '#D4A574'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión. Intente nuevamente.',
                        confirmButtonColor: '#D4A574'
                    });
                }
            }
        };
    }
    </script>
</body>
</html>

