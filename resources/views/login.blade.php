<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión - Glotty</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        /* ================================================
           1. ESTILOS BASE Y COMPONENTES
           ================================================ */
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        /* Checkbox Personalizado */
        .checkbox-container {
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer;
            user-select: none;
            gap: 8px;
        }
        .checkbox-container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }
        .checkmark {
            height: 20px;
            width: 20px;
            background-color: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .checkbox-container:hover .checkmark {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        .checkbox-container input:checked ~ .checkmark {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-color: #3b82f6;
        }
        .checkmark:after {
            content: "";
            display: none;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        .checkbox-container input:checked ~ .checkmark:after { display: block; }
        
        /* Animaciones */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        .form-group.error .input-with-icon {
            animation: shake 0.4s ease-in-out;
            border-radius: 12px;
            border: 1px solid transparent; 
        }
        .form-group.error input {
            border-color: #ef4444 !important;
        }
        .error-message {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 5px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .error-message::before {
            content: "\f06a";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }

        /* Loading Spinner */
        .submit-btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }
        .submit-btn.loading .btn-text { display: none; }
        .submit-btn.loading .fa-arrow-right { display: none; }
        .submit-btn.loading::after {
            content: '';
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ================================================
           2. ESTILOS ESPECÍFICOS DEL DISEÑO AZUL
           ================================================ */

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #38bdf8 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* HEADER */
        .top-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky; top: 0; z-index: 100;
        }
        .header-content {
            max-width: 1400px; margin: 0 auto; display: flex; align-items: center; justify-content: center; gap: 30px;
        }
        .logo-glotty img, .logo-tecnm img { height: 60px; width: auto; object-fit: contain; border-radius: 50%; }
        .divider-line { width: 2px; height: 50px; background: linear-gradient(180deg, transparent, rgba(30, 58, 138, 0.3), transparent); }

        /* CONTENEDOR PRINCIPAL */
        .main-container {
            flex: 1; display: flex; max-width: 1200px; width: 95%; margin: 30px auto;
            background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        /* IZQUIERDA */
        .left-side {
            flex: 1; 
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            position: relative; display: flex; align-items: center; justify-content: center; padding: 60px; overflow: hidden;
        }
        .left-side::before {
            content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 500px; height: 500px;
            background-image: url("{{ asset('images/tecnmGlotty.jpg') }}");
            background-size: contain; background-repeat: no-repeat; background-position: center;
            opacity: 0.08; z-index: 1; pointer-events: none;
        }
        .watermark-container { position: relative; z-index: 2; text-align: center; color: white; }
        .watermark-logo img { width: 180px; height: auto; border-radius: 50%; margin-bottom: 20px; }
        .watermark-text h1 {
            font-size: 48px; font-weight: 700; margin-bottom: 20px;
            background: linear-gradient(90deg, #ffffff, #dbeafe); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .watermark-text h2 { font-size: 28px; font-weight: 300; margin-bottom: 10px; }
        .watermark-text .subtitle { font-size: 22px; font-weight: 600; }

        /* DERECHA */
        .right-side { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; background: white; }
        .form-card { width: 100%; max-width: 420px; opacity: 0; transform: translateY(20px); animation: fadeIn 0.6s ease-out forwards; }
        
        @keyframes fadeIn { to { opacity: 1; transform: translateY(0); } }

        .form-header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 32px; font-weight: 700; color: #1e3a8a; margin-bottom: 10px; }
        .subtitle-form { font-size: 15px; color: #64748b; }

        /* INPUTS */
        .form-group { margin-bottom: 24px; position: relative; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 8px; }
        .input-with-icon { position: relative; display: flex; align-items: center; }
        .input-icon { position: absolute; left: 16px; color: #64748b; font-size: 18px; pointer-events: none; z-index: 1; }
        .input-with-icon input {
            width: 100%; padding: 14px 48px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 15px;
            transition: all 0.3s ease; background: #f8fafc;
        }
        .input-with-icon input:focus { outline: none; border-color: #3b82f6; background: white; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        .toggle-password { position: absolute; right: 16px; background: none; border: none; color: #64748b; cursor: pointer; font-size: 18px; transition: color 0.3s; }
        .toggle-password:hover { color: #3b82f6; }

        /* BOTONES Y EXTRAS */
        .form-options { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; font-size: 14px; }
        
        .submit-btn {
            width: 100%; padding: 16px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 12px; transition: transform 0.3s, box-shadow 0.3s;
        }
        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px -5px rgba(30, 58, 138, 0.3); }
        
        .divider { display: flex; align-items: center; margin: 32px 0; color: #94a3b8; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #e2e8f0; }
        .divider span { padding: 0 16px; }

        .form-footer { text-align: center; margin-top: 24px; border-top: 1px solid #e2e8f0; padding-top: 24px; font-size: 14px; color: #64748b; }
        .form-footer a, .forgot-password { color: #3b82f6; text-decoration: none; font-weight: 600; transition: color 0.2s; }
        .form-footer a:hover, .forgot-password:hover { color: #1e3a8a; text-decoration: underline; }
        
        .additional-options { display: flex; flex-direction: column; gap: 12px; margin-top: 24px; }
        .option-link {
            display: flex; align-items: center; gap: 12px; padding: 14px 16px; background: #f8fafc;
            border: 2px solid #e2e8f0; border-radius: 12px; color: #475569; text-decoration: none; font-weight: 600; font-size: 14px; transition: all 0.3s;
        }
        .option-link:hover { border-color: #3b82f6; background: white; color: #1e3a8a; transform: translateX(5px); }
        .option-link i { width: 20px; text-align: center; color: #64748b; }
        .option-link:hover i { color: #3b82f6; }

        /* ALERTAS */
        .alert { padding: 14px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; display: flex; gap: 10px; align-items: flex-start; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
        .alert-error ul { list-style: none; margin: 0; padding: 0; }
        .hidden { display: none; }
        
        /* RESPONSIVE */
        @media (max-width: 900px) {
            .left-side { display: none; }
            .main-container { max-width: 100%; margin: 0; border-radius: 0; min-height: 100vh; box-shadow: none; }
            .form-card { padding: 20px; }
            .top-header { padding: 15px 20px; }
        }
        
        .border-r-5 { border-radius: 20%; }
    </style>
</head>

<body>
    <header class="top-header">
        <div class="header-content">
            <div class="logo-glotty">
                <img src="{{ asset('images/glotty.png') }}" alt="Glotty Logo" class="border-r-5">
            </div>
            <div class="divider-line"></div>
            <div class="logo-tecnm">
                <img src="{{ asset('images/itsjrGlotty.jpg') }}" alt="TECNM Logo" class="border-r-5">
            </div>
        </div>
    </header>

    <div class="main-container">
        <div class="left-side">
            <div class="watermark-container">
                <div class="watermark-logo border-r-5">
                    <img src="{{ asset('images/tecnmGlotty.jpg') }}" alt="TecNM Marca de Agua" class="border-r-5">
                </div>
                <div class="watermark-text">
                    <h2>Instituto Tecnológico de</h2>
                    <h1>San Juan del Río</h1>
                    <p class="subtitle">Plataforma TecNM 5.0 Inicia sesión</p>
                </div>
            </div>
        </div>

        <div class="right-side">
            <div class="form-card">
                <div class="form-header">
                    <h1 class="title">Iniciar Sesión</h1>
                    <p class="subtitle-form">Ingresa tus credenciales para acceder</p>
                </div>

                @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle" style="margin-top: 3px;"></i>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form id="loginForm" method="POST" action="/login">
                    @csrf 
                    
                    <div class="form-group">
                        <label for="email">Correo electrónico:</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" placeholder="Correo Electrónico" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" placeholder="Contraseña" required>
                            <button type="button" class="toggle-password" id="togglePassword"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-container">
                            <input type="checkbox" id="recordar" name="remember">
                            <span class="checkmark"></span>
                            <span class="checkbox-label">Recordar mi sesión</span>
                        </label>
                        <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="submit-btn">
                        <span class="btn-text">Iniciar Sesión</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>

                    <div class="divider"><span>O</span></div>

                    <div class="form-footer">
                        <p>¿No tienes cuenta? <a href="/register">Regístrate aquí</a></p>
                    </div>

                    <div class="additional-options">
                        <a href="#" class="option-link"><i class="fas fa-user-shield"></i> Acceso para profesores</a>
                        <a href="#" class="option-link"><i class="fas fa-user-tie"></i> Acceso para coordinadores</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Actualizado para buscar los nuevos IDs 'email' y 'password'
            const loginForm = document.getElementById("loginForm");
            const emailInput = document.getElementById("email");
            const passwordInput = document.getElementById("password");
            const togglePassword = document.getElementById("togglePassword");
            const submitBtn = loginForm.querySelector(".submit-btn");

            // ===================================
            // EFECTOS VISUALES DE ENTRADA
            // ===================================
            const inputs = document.querySelectorAll("input");
            inputs.forEach((input, index) => {
                input.style.opacity = "0";
                input.style.transform = "translateY(10px)";
                setTimeout(() => {
                    input.style.transition = "all 0.4s ease";
                    input.style.opacity = "1";
                    input.style.transform = "translateY(0)";
                }, index * 100);
            });

            // ===================================
            // TOGGLE PASSWORD
            // ===================================
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener("click", function () {
                    const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
                    passwordInput.setAttribute("type", type);
                    
                    const icon = this.querySelector("i");
                    icon.classList.toggle("fa-eye");
                    icon.classList.toggle("fa-eye-slash");

                    // Efecto de feedback
                    this.style.transform = "scale(1.1)";
                    setTimeout(() => this.style.transform = "scale(1)", 200);
                });
            }

            // ===================================
            // FUNCIONES DE VALIDACIÓN VISUAL
            // ===================================
            function mostrarErrorInput(input, mensaje) {
                const formGroup = input.closest(".form-group");
                formGroup.classList.add("error");
                
                let errorMsg = formGroup.querySelector(".error-message");
                if (!errorMsg) {
                    errorMsg = document.createElement("div");
                    errorMsg.className = "error-message";
                    formGroup.appendChild(errorMsg);
                }
                errorMsg.textContent = mensaje;
            }

            function limpiarErrorInput(input) {
                const formGroup = input.closest(".form-group");
                formGroup.classList.remove("error");
                const errorMsg = formGroup.querySelector(".error-message");
                if (errorMsg) errorMsg.remove();
            }

            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            // ===================================
            // EVENTOS DE VALIDACIÓN
            // ===================================
            if(emailInput) {
                emailInput.addEventListener("blur", function () {
                    if (this.value && !isValidEmail(this.value)) {
                        mostrarErrorInput(this, "Correo electrónico no válido");
                    } else {
                        limpiarErrorInput(this);
                    }
                });
                emailInput.addEventListener("input", function() { limpiarErrorInput(this); });
            }
            
            if(passwordInput) {
                passwordInput.addEventListener("input", function() { limpiarErrorInput(this); });
            }

            // ===================================
            // SUBMIT REAL (Hacia /login)
            // ===================================
            if (loginForm) {
                loginForm.addEventListener("submit", function (e) {
                    const email = emailInput.value.trim();
                    const password = passwordInput.value;
                    let hasError = false;

                    // Validación local visual
                    if (!isValidEmail(email)) {
                        mostrarErrorInput(emailInput, "Correo inválido");
                        hasError = true;
                    }
                    if (password.length < 1) {
                        mostrarErrorInput(passwordInput, "Ingresa tu contraseña");
                        hasError = true;
                    }

                    if (hasError) {
                        e.preventDefault(); 
                        return;
                    }

                    // Si todo está bien, animar botón y enviar
                    submitBtn.classList.add("loading");
                });
            }
        });
    </script>
</body>
</html>