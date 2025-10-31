<!DOCTYPE html>
<html>
<head>
    <title>Subir Constancias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .btn-action {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 2px;
            border-radius: 50%;
        }
        .btn-action:hover {
            transform: scale(1.1);
            transition: all 0.2s ease;
        }
        .card-header {
            font-weight: 600;
        }
        .badge-count {
            font-size: 0.9rem;
            padding: 5px 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Gestión de Constancias</h2>
        
        @include('partials.alertas')

        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Subir Nueva Constancia</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('constancias.subir') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Alumno:</label>
                            <select name="no_control" class="form-select" required>
                                <option value="">Seleccionar alumno...</option>
                                @foreach($alumnos as $alumno)
                                    <option value="{{ $alumno->no_control }}" 
                                        {{ Storage::disk('sftp')->exists("constancias/{$alumno->no_control}.pdf") ? 'data-constancia="true"' : '' }}>
                                        {{ $alumno->no_control }} - {{ $alumno->nombre_alumno }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Los alumnos con constancia aparecen marcados</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Archivo PDF:</label>
                            <input type="file" name="file" class="form-control" accept=".pdf" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-upload me-1"></i> Subir
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow mt-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Alumnos con Constancia Registrada</h5>
                <span class="badge bg-white text-info badge-count">
                    <i class="fas fa-file-pdf me-1"></i>
                    {{ $alumnos->filter(fn($a) => Storage::disk('sftp')->exists("constancias/{$a->no_control}.pdf"))->count() }} constancias
                </span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="tabla-constancias">
                        <thead>
                            <tr>
                                <th>No. Control</th>
                                <th>Nombre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alumnos as $alumno)
                                @if(Storage::disk('sftp')->exists("constancias/{$alumno->no_control}.pdf"))
                                    <tr>
                                        <td>{{ $alumno->no_control }}</td>
                                        <td>{{ $alumno->nombre_alumno }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('constancias.descargar', $alumno->no_control) }}" 
                                                   class="btn btn-action btn-success" 
                                                   title="Descargar constancia">
                                                   <i class="fas fa-download"></i>
                                                </a>
                                                
                                                @auth('coordinador')
                                                <form action="{{ route('constancias.eliminar', $alumno->no_control) }}" 
                                                      method="POST"
                                                      class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-action btn-danger" 
                                                            title="Eliminar constancia">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                                @endauth
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle me-2"></i> No hay constancias registradas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Configuración de DataTable
            $('#tabla-constancias').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-MX.json'
                },
                columnDefs: [
                    { orderable: false, targets: 2 } // Deshabilitar ordenación en columna Acciones
                ]
            });
            
            // Marcar alumnos con constancia existente
            $('select[name="no_control"] option').each(function() {
                if ($(this).data('constancia')) {
                    $(this).append(' (Ya tiene constancia)');
                }
            });
            
            // Confirmación para eliminar
            $('.delete-form').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                const alumnoName = $(this).closest('tr').find('td:nth-child(2)').text();
                
                Swal.fire({
                    title: '¿Eliminar constancia?',
                    html: `Estás por eliminar la constancia de <b>${alumnoName}</b><br>¿Deseas continuar?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>