<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Documentos - Sistema de Constancias</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a56db;
            --primary-dark: #0e3fa9;
            --light-bg: #fafbff;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: scale(1.03);
            box-shadow: 0 5px 15px rgba(26, 86, 219, 0.3);
        }
        
        .gradient-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        }
        
        .status-icon {
            width: 60px;
            height: 60px;
        }
        
        .section-title {
            font-size: 2.2rem;
            letter-spacing: -0.5px;
        }
        
        .title-underline {
            width: 80px;
            border-radius: 2px;
        }
    </style>
</head>

    
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="text-primary fw-bold mb-0 section-title">Mis Documentos</h2>
                <div class="border-bottom border-primary border-3 mt-2 title-underline"></div>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow border-0 rounded-4 overflow-hidden card-hover">
                    <div class="card-header py-4 gradient-header">
                        <h5 class="card-title mb-0 fw-bold text-white d-flex align-items-center">
                            <i class="fas fa-file-pdf me-3" style="font-size: 1.4rem;"></i>
                            <span style="font-size: 1.25rem; letter-spacing: 0.2px;">Constancia de Finalización</span>
                        </h5>
                    </div>
                    <div class="card-body p-4 p-lg-5" style="background-color: var(--light-bg);">
                        @if(Storage::disk('sftp')->exists("constancias/{$alumno->no_control}.pdf"))
                            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start">
                                <div class="mb-3 mb-md-0 me-md-4">
                                    <div class="rounded-circle bg-success bg-opacity-10 p-3 d-flex align-items-center justify-content-center status-icon">
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                    </div>
                                </div>
                                <div class="text-center text-md-start">
                                    <p class="mb-4 text-muted" style="font-size: 1.05rem;">Tu constancia está lista para descargar.</p>
                                    <a href="{{ route('constancias.descargar', ['no_control' => $alumno->no_control]) }}" 
                                        class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                                        <i class="fas fa-download me-2"></i> Descargar Constancia
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start">
                                <div class="mb-3 mb-md-0 me-md-4">
                                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 d-flex align-items-center justify-content-center status-icon">
                                        <i class="fas fa-exclamation-circle text-warning fa-2x"></i>
                                    </div>
                                </div>
                                <div class="text-center text-md-start">
                                    <p class="mb-0 text-muted" style="font-size: 1.05rem;">Tu constancia aún no está disponible. Te notificaremos cuando esté lista.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>