<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIPAN</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --color-primary: #F4C4A0;
            --color-primary-dark: #E8B18F;
            --color-secondary: #FFF4E6;
            --color-accent: #D4A78F;
            --color-dark: #8B7365;
            --color-light: #FFFBF5;
            --color-pastel-pink: #FFE5E5;
            --color-pastel-peach: #FFDAB9;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 248, 240, 0.35);
            z-index: -1;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(15px);
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(212, 167, 143, 0.25);
            overflow: hidden;
            max-width: 1100px;
            width: 100%;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            animation: fadeInUp 0.6s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-left {
            background: linear-gradient(135deg, rgba(255, 229, 229, 0.75) 0%, rgba(255, 218, 185, 0.75) 100%),
                        url('https://images.unsplash.com/photo-1555507036-ab1f4038808a?q=80&w=1000') center/cover;
            color: var(--color-dark);
            padding: 4rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
        }
        
        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 240, 235, 0.65) 0%, rgba(255, 228, 210, 0.65) 100%);
            z-index: 1;
        }
        
        .login-left > * {
            position: relative;
            z-index: 2;
        }
        
        .login-logo {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            animation: bounce 2s infinite;
            text-shadow: 0 2px 8px rgba(212, 167, 143, 0.3);
            color: var(--color-primary-dark);
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .login-title {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(212, 167, 143, 0.2);
            letter-spacing: 2px;
            color: var(--color-accent);
        }
        
        .login-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 400;
            margin-bottom: 2rem;
            color: var(--color-dark);
        }
        
        .feature-list {
            text-align: left;
            margin-top: 2rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.95rem;
            color: var(--color-dark);
        }
        
        .feature-item:last-child {
            margin-bottom: 0;
        }
        
        .feature-item i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
            color: var(--color-primary-dark);
        }
        
        .login-right {
            padding: 4rem 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.95);
        }
        
        .login-form-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--color-dark);
            margin-bottom: 0.5rem;
        }
        
        .login-form-subtitle {
            color: #9a8a7f;
            margin-bottom: 2.5rem;
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1.75rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.6rem;
            font-weight: 600;
            color: var(--color-dark);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid #f5ebe0;
            border-radius: 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fffcf9;
            color: var(--color-dark);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 5px rgba(244, 196, 160, 0.15);
            background: white;
        }
        
        .form-control::placeholder {
            color: #c9b8ad;
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: #c9b8ad;
            transition: color 0.3s ease;
        }
        
        .input-icon .form-control:focus + i,
        .input-icon:focus-within i {
            color: var(--color-primary-dark);
        }
        
        .input-icon .form-control {
            padding-left: 3.2rem;
        }
        
        .btn-login {
            width: 100%;
            padding: 1.1rem;
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(244, 196, 160, 0.3);
            margin-top: 0.5rem;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(244, 196, 160, 0.45);
            background: linear-gradient(135deg, #f7cfad, #edb99a);
        }
        
        .btn-login:active {
            transform: translateY(-1px);
        }
        
        .btn-login i {
            margin-right: 0.5rem;
        }
        
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f5ebe0;
        }
        
        .register-link p {
            color: #9a8a7f;
        }
        
        .register-link a {
            color: var(--color-accent);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .register-link a:hover {
            color: var(--color-primary-dark);
            text-decoration: underline;
        }
        
        @media (max-width: 968px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 500px;
            }
            
            .login-left {
                display: none;
            }
            
            .login-right {
                padding: 3rem 2rem;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 1rem;
            }
            
            .login-right {
                padding: 2rem 1.5rem;
            }
            
            .login-form-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="login-logo">
                <i class="fas fa-bread-slice"></i>
            </div>
            <h1 class="login-title">SIPAN</h1>
            <p class="login-subtitle">Sistema Integral para Panaderías</p>
            
            <div class="feature-list">
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Gestión de inventario en tiempo real</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Control de ventas y facturación</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Reportes y estadísticas detalladas</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Gestión de empleados y turnos</span>
                </div>
            </div>
        </div>
        
        <div class="login-right">
            <h2 class="login-form-title">Bienvenido</h2>
            <p class="login-form-subtitle">Ingresa tus credenciales para acceder al sistema</p>
            
            <form id="loginForm" action="/login" method="POST">
                <?php 
                require_once __DIR__ . '/../../Helpers/CSRF.php';
                echo \App\Helpers\CSRF::field(); 
                ?>
                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-icon">
                        <input type="email" name="correo" class="form-control" placeholder="tu@correo.com" required>
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <div class="input-icon">
                        <input type="password" name="clave" class="form-control" placeholder="••••••••" required>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Ingresar
                </button>
            </form>
            
    <div class="fab-container">
        <button class="fab-btn" onclick="startLoginTour()" title="Guía de Inicio">
            <i class="fas fa-magic"></i>
        </button>
    </div>

    <style>
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
    </style>

    <script src="/assets/js/driver.js.iife.js"></script>
    <link rel="stylesheet" href="/assets/css/driver.css">
    
    <script>
        function startLoginTour() {
            const driver = window.driver.js.driver;
            const driverObj = driver({
                showProgress: true,
                steps: [
                    { popover: { title: '👋 ¡Bienvenido!', description: 'Esta es la puerta de entrada a SIPAN. Aquí podrás acceder a tu panel de control.' } },
                    { element: 'input[name="correo"]', popover: { title: 'Correo', description: 'Usa tu correo institucional registrado.', side: "bottom" } },
                    { element: 'input[name="clave"]', popover: { title: 'Contraseña', description: 'Ingresa tu clave de acceso segura.', side: "bottom" } },
                    { element: '.register-link', popover: { title: '¿Eres nuevo?', description: 'Si aún no tienes cuenta, puedes registrarte vinculándote a una sucursal.', side: "top" } }
                ]
            });
            driverObj.drive();
        }

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Bienvenido!',
                        text: data.message,
                        confirmButtonColor: '#F4C4A0',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = '/dashboard';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                        confirmButtonColor: '#F4C4A0'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al procesar la solicitud',
                    confirmButtonColor: '#F4C4A0'
                });
                console.error('Error:', error);
            }
        });
    </script>
</body>
</html>