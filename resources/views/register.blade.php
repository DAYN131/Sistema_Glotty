<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Glotty</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        /* ================================================
           ESTILOS - REGISTRO GLOTTY
           ================================================ */

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #38bdf8 100%);
            background-attachment: fixed;
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

        .logo-glotty img, .logo-tecnm img {
            height: 60px; width: auto; object-fit: contain; border-radius: 50%;
        }

        .divider-line {
            width: 2px; height: 50px;
            background: linear-gradient(180deg, transparent, rgba(30, 58, 138, 0.3), transparent);
        }

        /* CONTENEDOR PRINCIPAL */
        .main-container {
            flex: 1; display: flex; max-width: 1200px; width: 100%; margin: 30px auto; gap: 0;
            background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        /* LADO IZQUIERDO */
        .left-side {
            flex: 1; background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            position: relative; display: flex; align-items: center; justify-content: center; padding: 60px; overflow: hidden;
        }

        .left-side::before {
            content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 500px; height: 500px;
            background-image: url('{{ asset("images/tecnmGlotty.jpg") }}');
            background-size: contain; background-repeat: no-repeat; background-position: center;
            opacity: 0.08; z-index: 1; pointer-events: none;
        }

        .watermark-container { position: relative; z-index: 2; text-align: center; color: white; }
        .watermark-logo { margin-bottom: 40px; animation: float 6s ease-in-out infinite; }
        .watermark-logo img { width: 180px; height: auto; border-radius: 50%; filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.3)); }
        
        .watermark-text h2 { font-size: 28px; font-weight: 300; letter-spacing: 1px; margin-bottom: 10px; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); }
        .watermark-text h1 {
            font-size: 48px; font-weight: 700; letter-spacing: 2px; margin-bottom: 20px;
            background: linear-gradient(90deg, #ffffff, #dbeafe); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        .watermark-text .subtitle { font-size: 18px; font-weight: 300; letter-spacing: 1px; opacity: 0.9; }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        /* LADO DERECHO */
        .right-side { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px; background: white; }
        .form-card { width: 100%; max-width: 480px; padding: 30px; animation: fadeIn 0.6s ease-out; }
        .form-header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 32px; font-weight: 700; color: #1e3a8a; margin-bottom: 10px; }
        .subtitle-form { font-size: 15px; color: #64748b; }

        /* FORMULARIO */
        .form-group { margin-bottom: 24px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 8px; }
        
        /* Inputs Generales */
        .form-group input[type="text"], .form-group input[type="email"],
        .form-group input[type="date"], .form-group input[type="tel"],
        .form-group input[type="password"], .form-group select {
            width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px;
            font-size: 15px; transition: all 0.3s ease; background: #f8fafc;
        }
        .form-group input:focus, .form-group select:focus { 
            outline: none; border-color: #3b82f6; background: white; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); 
        }

        /* Estilo Radio Buttons (Tipo Usuario) */
        .radio-group { display: flex; gap: 20px; align-items: center; }
        .radio-label { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 500; color: #475569; cursor: pointer; }
        .radio-label input[type="radio"] { width: 18px; height: 18px; accent-color: #3b82f6; cursor: pointer; }

        /* CAMPOS INSTITUCIONALES */
        .campos-institucionales {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #bae6fd; border-radius: 16px; padding: 24px; margin-bottom: 24px; transition: all 0.3s ease;
        }
        .campos-institucionales.hidden { display: none; }
        .interno-field:required { border-left: 4px solid #2563eb; }

        /* ================================================
           ESTILOS ORIGINALES DE BOTONES DE GÉNERO
           ================================================ */
        .gender-input { display: flex; gap: 12px; margin-top: 8px; }
        
        .icon-btn {
            flex: 1; padding: 16px; border: 2px solid #e2e8f0; background: #f8fafc; border-radius: 12px; cursor: pointer;
            transition: all 0.3s ease; display: flex; align-items: center; justify-content: center;
        }
        
        .icon-btn svg { width: 32px; height: 32px; color: #64748b; transition: all 0.3s ease; }
        
        .icon-btn:hover { border-color: #3b82f6; background: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2); }
        
        /* Estado Activo */
        .icon-btn.active { border-color: #3b82f6; background: linear-gradient(135deg, #dbeafe, #bfdbfe); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
        
        /* Colores específicos activos */
        .male-btn.active svg { color: #3b82f6; }
        .female-btn.active svg { color: #ec4899; }
        .other-btn.active svg { color: #8b5cf6; }

        /* BOTÓN SUBMIT */
        .submit-btn {
            width: 100%; padding: 16px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 600;
            cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3); margin-top: 10px;
        }
        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(30, 58, 138, 0.4); }

        /* ALERTAS */
        .alert-error {
            background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca;
            padding: 12px; border-radius: 12px; margin-bottom: 20px; font-size: 0.9rem;
        }
        .alert-error ul { list-style: none; padding: 0; }

        /* FOOTER */
        .form-footer { text-align: center; margin-top: 24px; padding-top: 24px; border-top: 1px solid #e2e8f0; }
        .form-footer p { font-size: 14px; color: #64748b; }
        .form-footer a { color: #3b82f6; text-decoration: none; font-weight: 600; transition: color 0.3s ease; }
        .form-footer a:hover { color: #2563eb; text-decoration: underline; }

        /* RESPONSIVE */
        @media (max-width: 1200px) { .main-container { max-width: 95%; margin: 20px auto; } }
        @media (max-width: 1024px) { .main-container { flex-direction: column; margin: 20px; } .left-side { min-height: 300px; padding: 40px; } }
        @media (max-width: 768px) { 
            .left-side { display: none; } 
            .form-card { max-width: 100%; padding: 20px; }
            .gender-input { flex-direction: column; } /* Botones apilados en móvil */
        }
    </style>
</head>

<body>
    <header class="top-header">
        <div class="header-content">
            <div class="logo-glotty">
                <img src="{{ asset('images/glotty.png') }}" alt="Glotty Logo">
            </div>
            <div class="divider-line"></div>
            <div class="logo-tecnm">
                <img src="{{ asset('images/itsjrGlotty.jpg') }}" alt="TecNM Logo">
            </div>
        </div>
    </header>

    <div class="main-container">
        <div class="left-side">
            <div class="watermark-container">
                <div class="watermark-logo">
                    <img src="{{ asset('images/tecnmGlotty.jpg') }}" alt="TecNM Marca de Agua">
                </div>
                <div class="watermark-text">
                    <h2>Instituto Tecnológico de</h2>
                    <h1>San Juan del Río</h1>
                    <p class="subtitle">Plataforma TecNM 5.0</p>
                </div>
            </div>
        </div>

        <div class="right-side">
            <div class="form-card">
                <div class="form-header">
                    <h1 class="title">Regístrate</h1>
                    <p class="subtitle-form">Completa el formulario para crear tu cuenta</p>
                </div>

                @if ($errors->any())
                <div class="alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form id="registrationForm" method="POST" action="{{ route('register.post') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label>Tipo de Usuario</label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="tipo_usuario" value="interno" checked>
                                Interno
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="tipo_usuario" value="externo">
                                Externo
                            </label>
                        </div>
                    </div>

                    <div id="campos-interno" class="campos-institucionales">
                        <div class="form-group">
                            <label for="numeroControl">Número de control:</label>
                            <input type="text" id="numeroControl" name="numero_control" class="interno-field" placeholder="Número de Control" value="{{ old('numero_control') }}">
                        </div>

                        <div class="form-group">
                            <label for="correoInstitucional">Correo institucional:</label>
                            <input type="email" id="correoInstitucional" name="correo_institucional" class="interno-field" placeholder="Correo Institucional" value="{{ old('correo_institucional') }}">
                        </div>

                        <div class="form-group">
                            <label for="especialidad">Especialidad / Carrera:</label>
                            <select id="especialidad" name="especialidad" class="interno-field">
                                <option value="">Selecciona tu carrera</option>
                                <option value="Ingeniería en Sistemas Computacionales" {{ old('especialidad') == 'Ingeniería en Sistemas Computacionales' ? 'selected' : '' }}>Ingeniería en Sistemas Computacionales</option>
                                <option value="Ingeniería e Tecnologias de la Informacion y Comunicaciones" {{ old('especialidad') == 'Ingeniería e Tecnologias de la Informacion y Comunicaciones' ? 'selected' : '' }}>Ingeniería e Tecnologias de la Informacion y Comunicaciones</option>
                                <option value="Ingeniería Industrial" {{ old('especialidad') == 'Ingeniería Industrial' ? 'selected' : '' }}>Ingeniería Industrial</option>
                                <option value="Ingeniería Electrónica" {{ old('especialidad') == 'Ingeniería Electrónica' ? 'selected' : '' }}>Ingeniería Electrónica</option>
                                <option value="Ingeniería Electromecanica" {{ old('especialidad') == 'Ingeniería Electromecanica' ? 'selected' : '' }}>Ingeniería Electromecanica</option>
                                <option value="Ingeniería en Gestion Empresarial" {{ old('especialidad') == 'Ingeniería en Gestion Empresarial' ? 'selected' : '' }}>Ingeniería en Gestion Empresarial</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nombre_completo">Nombre Completo:</label>
                        <input type="text" id="nombre_completo" name="nombre_completo" placeholder="Nombre Completo" value="{{ old('nombre_completo') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="correo_personal">Correo Personal:</label>
                        <input type="email" id="correo_personal" name="correo_personal" placeholder="Correo Personal" value="{{ old('correo_personal') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="numero_telefonico">Número Telefónico:</label>
                        <input type="tel" id="numero_telefonico" name="numero_telefonico" placeholder="Número Telefónico" value="{{ old('numero_telefonico') }}">
                    </div>

                    <div class="form-group">
                        <label for="genero">Género:</label>
                        <div class="gender-input">
                            <button type="button" class="icon-btn male-btn {{ old('genero') == 'M' ? 'active' : '' }}" data-gender="M" title="Masculino">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 9c0-1.7 1.3-3 3-3s3 1.3 3 3-1.3 3-3 3-3-1.3-3-3zm12-5v6h-2V6.4l-3.4 3.4C16.4 11 16 12.4 16 14c0 3.3-2.7 6-6 6s-6-2.7-6-6 2.7-6 6-6c1.5 0 2.9.5 4 1.3L17.6 6H14V4h7z" />
                                </svg>
                            </button>
                            <button type="button" class="icon-btn female-btn {{ old('genero') == 'F' ? 'active' : '' }}" data-gender="F" title="Femenino">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.5 9.5C17.5 6.46 15.04 4 12 4S6.5 6.46 6.5 9.5c0 2.7 1.94 4.93 4.5 5.4V17H9v2h2v2h2v-2h2v-2h-2v-2.1c2.56-.47 4.5-2.7 4.5-5.4zm-9 0C8.5 7.57 10.07 6 12 6s3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5z" />
                                </svg>
                            </button>
                            <button type="button" class="icon-btn other-btn {{ old('genero') == 'Otro' ? 'active' : '' }}" data-gender="Otro" title="Otro">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="12" r="3" />
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                                </svg>
                            </button>
                        </div>
                        <input type="hidden" id="genero" name="genero" value="{{ old('genero') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="fecha_nacimiento">Fecha de nacimiento:</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}">
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" placeholder="Contraseña" required>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirmar Contraseña:</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirmar Contraseña" required>
                    </div>

                    <button type="submit" class="submit-btn">Registrarse</button>

                    <div class="form-footer">
                        <p>¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tipoUsuarioRadios = document.querySelectorAll('input[name="tipo_usuario"]');
            const camposInterno = document.getElementById('campos-interno');
            const camposInternoInputs = document.querySelectorAll('.interno-field');
            const registrationForm = document.getElementById("registrationForm");

            // ===================================
            // TOGGLE DE INTERNO/EXTERNO
            // ===================================
            function toggleCamposInterno() {
                const seleccionado = document.querySelector('input[name="tipo_usuario"]:checked');
                const esInterno = seleccionado && seleccionado.value === 'interno';
                
                if (esInterno) {
                    camposInterno.classList.remove("hidden");
                    camposInternoInputs.forEach(input => input.required = true);
                } else {
                    camposInterno.classList.add("hidden");
                    camposInternoInputs.forEach(input => {
                        input.required = false;
                        input.value = '';
                    });
                }
            }
            toggleCamposInterno();
            tipoUsuarioRadios.forEach(radio => radio.addEventListener('change', toggleCamposInterno));

            // ===================================
            // LÓGICA DE BOTONES DE GÉNERO
            // ===================================
            const genderButtons = document.querySelectorAll(".icon-btn");
            const generoInput = document.getElementById("genero");

            if (genderButtons.length > 0 && generoInput) {
                genderButtons.forEach((button) => {
                    button.addEventListener("click", function () {
                        // Quitar clase active de todos
                        genderButtons.forEach((btn) => btn.classList.remove("active"));
                        
                        // Añadir a este
                        this.classList.add("active");
                        
                        // Actualizar input oculto
                        generoInput.value = this.getAttribute("data-gender");
                        
                        // Efecto visual
                        this.style.transform = "scale(1.05)";
                        setTimeout(() => { this.style.transform = ""; }, 200);
                    });
                });
            }

            // ===================================
            // ANIMACIÓN INPUTS Y SUBMIT
            // ===================================
            const allInputs = document.querySelectorAll('input, select');
            allInputs.forEach((input) => {
                input.addEventListener("focus", function () {
                    this.parentElement.style.transform = "scale(1.01)";
                    this.parentElement.style.transition = "transform 0.2s ease";
                });
                input.addEventListener("blur", function () {
                    this.parentElement.style.transform = "scale(1)";
                });
            });

            if (registrationForm) {
                registrationForm.addEventListener("submit", function (e) {
                    const submitBtn = this.querySelector(".submit-btn");
                    submitBtn.textContent = "Procesando...";
                    submitBtn.style.opacity = "0.7";
                });
            }
        });
    </script>
</body>
</html>