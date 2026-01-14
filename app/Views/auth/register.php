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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Driver.js para el Tour -->
    <link rel="stylesheet" href="/assets/css/driver.css">
    <script src="/assets/js/driver.js.iife.js"></script>

    <style>
        :root {
            --color-primary: #F4C4A0;
            --color-primary-dark: #E8B18F;
            --color-secondary: #FFF4E6;
            --color-accent: #D4A78F;
            --color-dark: #8B7365;
            --color-light: #FFFBF5;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: url('https://images.unsplash.com/photo-1509440159596-0249088772ff?q=80&w=2000') center/cover fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 248, 240, 0.35);
            z-index: -1;
        }

        .glass-container {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(15px);
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(212, 167, 143, 0.25);
            padding: 3rem;
            max-width: 800px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.8);
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .brand-logo {
            font-size: 3.5rem;
            color: var(--color-primary-dark);
            margin-bottom: 0.5rem;
            display: inline-block;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: var(--color-accent);
            letter-spacing: 1px;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            position: relative;
        }

        .step-indicator::after {
            content: '';
            position: absolute;
            top: 25px; left: 0; right: 0;
            height: 2px;
            background: #f5ebe0;
            z-index: 1;
        }

        .step-item {
            position: relative;
            z-index: 2;
            background: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #f5ebe0;
            color: #c9b8ad;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .step-item.active {
            border-color: var(--color-primary);
            color: var(--color-primary-dark);
            box-shadow: 0 0 0 5px rgba(244, 196, 160, 0.2);
        }

        .step-item.completed {
            background: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
        }

        .form-label {
            font-weight: 600;
            color: var(--color-dark);
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 12px;
            padding: 0.8rem 1.2rem;
            border: 2px solid #f5ebe0;
            background: #fffcf9;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 4px rgba(244, 196, 160, 0.1);
            background: white;
        }

        .btn-premium {
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-next {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(244, 196, 160, 0.3);
        }

        .btn-next:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(244, 196, 160, 0.4);
        }

        .btn-prev {
            background: white;
            border: 2px solid #f5ebe0;
            color: var(--color-dark);
        }

        .btn-prev:hover {
            background: #f5ebe0;
        }

        /* Floating Help Button */
        .fab-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 999;
        }

        .fab-btn {
            width: 60px;
            height: 60px;
            background: var(--color-accent);
            color: white;
            border-radius: 50%;
            border: none;
            box-shadow: 0 10px 30px rgba(212, 167, 143, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.5rem;
            display: flex; align-items: center; justify-content: center;
        }

        .fab-btn:hover {
            transform: scale(1.1) rotate(15deg);
            background: var(--color-dark);
        }

        @media (max-width: 576px) {
            .glass-container { padding: 1.5rem; }
            .brand-title { font-size: 1.8rem; }
        }
    </style>
</head>
<body>

    <div class="glass-container" x-data="registerApp()">
        <header class="brand-header">
            <div class="brand-logo"><i class="fas fa-bread-slice"></i></div>
            <h1 class="brand-title">SIPAN</h1>
            <p class="text-muted">Crea tu cuenta para comenzar</p>
        </header>

        <!-- Stepper -->
        <div class="step-indicator">
            <div class="step-item" :class="{'active': step === 1, 'completed': step > 1}" @click="goToStep(1)" id="step-1-btn">1</div>
            <div class="step-item" :class="{'active': step === 2, 'completed': step > 2}" @click="goToStep(2)" id="step-2-btn">2</div>
            <div class="step-item" :class="{'active': step === 3}" @click="goToStep(3)" id="step-3-btn">3</div>
        </div>

        <form @submit.prevent="handleSubmit" id="registerForm">
            <?php
            require_once __DIR__ . '/../../Helpers/CSRF.php';
            echo \App\Helpers\CSRF::field();
            ?>

            <!-- STEP 1: Personal -->
            <div x-show="step === 1" x-transition id="personal-data-section">
                <h4 class="mb-4 fw-bold text-dark"><i class="fas fa-user-circle me-2 text-primary"></i> Datos Personales</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Primer Nombre *</label>
                        <input type="text" class="form-control" x-model="formData.primer_nombre" required placeholder="Ej: Maria">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Apellido Paterno *</label>
                        <input type="text" class="form-control" x-model="formData.apellido_paterno" required placeholder="Ej: Garcia">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Cédula / RIF *</label>
                        <input type="text" class="form-control" x-model="formData.dni" required placeholder="V-12345678" pattern="^[V|E|J|G]-[0-9]{7,9}$">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teléfono *</label>
                        <input type="text" class="form-control" x-model="formData.telefono" required placeholder="0412-1234567" pattern="^0[0-9]{3}-[0-9]{7}$">
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-5">
                    <button type="button" @click="nextStep" class="btn btn-premium btn-next" id="btn-next-1">
                        Siguiente <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 2: Branch -->
            <div x-show="step === 2" x-transition id="branch-section">
                <h4 class="mb-4 fw-bold text-dark"><i class="fas fa-store me-2 text-primary"></i> Vincular Sucursal</h4>
                <div class="mb-4">
                    <label class="form-label">Clave de Sucursal *</label>
                    <div class="input-group">
                        <input type="text" class="form-control text-uppercase" maxlength="8" x-model="formData.clave_sucursal" placeholder="ABC12345" id="input-clave-sucursal">
                        <button class="btn btn-outline-secondary" type="button" @click="verificarClave" id="btn-verificar-clave">
                            <i class="fas fa-search me-1"></i> Verificar
                        </button>
                    </div>
                </div>

                <div x-show="sucursalVerificada" class="alert alert-success d-flex align-items-center" id="sucursal-success-alert">
                    <i class="fas fa-check-circle me-3 fs-3"></i>
                    <div>
                        <strong>Sucursal encontrada:</strong><br>
                        <span x-text="sucursalInfo.nombre"></span> - <span x-text="sucursalInfo.direccion"></span>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-5">
                    <button type="button" @click="prevStep" class="btn btn-premium btn-prev">Atrás</button>
                    <button type="button" @click="nextStep" class="btn btn-premium btn-next" :disabled="!sucursalVerificada" id="btn-next-2">
                        Continuar <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 3: Credentials -->
            <div x-show="step === 3" x-transition id="credentials-section">
                <h4 class="mb-4 fw-bold text-dark"><i class="fas fa-lock me-2 text-primary"></i> Credenciales</h4>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Correo Electrónico *</label>
                        <input type="email" class="form-control" x-model="formData.correo" required placeholder="user@example.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contraseña *</label>
                        <input type="password" class="form-control" x-model="formData.clave" required placeholder="••••••••">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Rol *</label>
                        <select class="form-select" x-model="formData.rol" required id="select-rol">
                            <option value="">Seleccione...</option>
                            <option value="cajero">Cajero</option>
                            <option value="empleado">Empleado</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-5">
                    <button type="button" @click="prevStep" class="btn btn-premium btn-prev">Atrás</button>
                    <button type="submit" class="btn btn-premium btn-next" :disabled="isSubmitting" id="btn-finish-register">
                        <i class="fas fa-user-plus me-2"></i> Unirse ahora
                    </button>
                </div>
            </div>
        </form>

        <footer class="mt-4 text-center">
            <p class="mb-0">¿Ya tienes cuenta? <a href="/login" class="text-primary fw-bold text-decoration-none">Inicia Sesión</a></p>
        </footer>
    </div>

    <!-- FAB for Tour -->
    <div class="fab-container">
        <button class="fab-btn" onclick="startRegisterTour()" title="Guía de Registro">
            <i class="fas fa-magic"></i>
        </button>
    </div>

    <script>
        function registerApp() {
            return {
                step: 1,
                sucursalVerificada: false,
                sucursalInfo: {},
                isSubmitting: false,
                formData: {
                    primer_nombre: '', apellido_paterno: '', dni: '', telefono: '',
                    clave_sucursal: '', id_sucursal: '', correo: '', clave: '', rol: ''
                },

                nextStep() {
                    if (this.step === 1) {
                        if (!this.formData.primer_nombre || !this.formData.apellido_paterno || !this.formData.dni) {
                            return Swal.fire('Error', 'Completa los campos obligatorios', 'error');
                        }
                    }
                    if (this.step === 2 && !this.sucursalVerificada) return;
                    this.step++;
                },

                prevStep() { this.step--; },
                goToStep(n) {
                    if (n < this.step) this.step = n;
                },

                async verificarClave() {
                    if (!this.formData.clave_sucursal) return;
                    try {
                        const res = await fetch('/auth/verificar-clave-sucursal', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ clave_sucursal: this.formData.clave_sucursal })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.sucursalVerificada = true;
                            this.sucursalInfo = data.sucursal;
                            this.formData.id_sucursal = data.sucursal.id;
                        } else {
                            this.sucursalVerificada = false;
                            Swal.fire('Error', data.message, 'error');
                        }
                    } catch (e) { console.error(e); }
                },

                async handleSubmit() {
                    this.isSubmitting = true;
                    try {
                        // Obtener CSRF token del campo oculto
                        const csrfToken = document.querySelector('input[name="csrf_token"]').value;
                        
                        const response = await fetch('/auth/register', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'X-CSRF-Token': csrfToken
                            },
                            body: JSON.stringify(this.formData)
                        });
                        const result = await response.json();
                        if (result.success) {
                            Swal.fire('¡Éxito!', result.message, 'success').then(() => window.location.href = '/login');
                        } else {
                            Swal.fire('Error', result.message, 'error');
                        }
                    } catch (e) {
                        Swal.fire('Error', 'Falló la conexión', 'error');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }
        }

        function startRegisterTour() {
            const driver = window.driver.js.driver;
            const driverObj = driver({
                showProgress: true,
                steps: [
                    { element: '#step-1-btn', popover: { title: 'Datos Personales', description: 'Comienza completando tu información básica.', side: "bottom" } },
                    { element: '#personal-data-section', popover: { title: 'Formulario', description: 'Asegúrate de que tu Cédula y Teléfono respeten el formato indicado.', side: "top" } },
                    { element: '#step-2-btn', popover: { title: 'Vincular Sucursal', description: 'Aquí conectarás tu cuenta con una panadería existente mediante su clave única.', side: "bottom" } },
                    { element: '#step-3-btn', popover: { title: 'Credenciales', description: 'Por último, elige un correo, contraseña fuerte y tu rol en el sistema.', side: "bottom" } }
                ]
            });
            driverObj.drive();
        }
    </script>
</body>
</html>
