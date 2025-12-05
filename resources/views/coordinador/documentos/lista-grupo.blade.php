<!DOCTYPE html>
<html>
<head>
    <title>Lista de Grupo - {{ $grupo->nivel_ingles }}{{ strtoupper($grupo->letra_grupo) }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .title { font-size: 24px; font-weight: bold; }
        .subtitle { font-size: 16px; color: #666; }
        .info { margin: 20px 0; padding: 15px; background: #f5f5f5; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #2c3e50; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #888; border-top: 1px solid #ddd; padding-top: 10px; }
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 12px; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">LISTA DE ESTUDIANTES</div>
        <div class="subtitle">Grupo: Nivel {{ $grupo->nivel_ingles }} - Grupo {{ strtoupper($grupo->letra_grupo) }}</div>
    </div>

    <div class="info">
        <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
            <div>
                <strong>Profesor:</strong> {{ $profesor->nombre_profesor ?? 'No asignado' }} {{ $profesor->apellidos_profesor ?? 'No asignado' }}<br>
                <strong>Horario:</strong> {{ $horario->nombre ?? 'No asignado' }}
            </div>
            <div>
                <strong>Nivel:</strong> {{ $grupo->nivel_ingles ?? 'No asignado' }}<br>
                <strong>Aula:</strong> {{ $aula->nombre ?? 'No asignada' }}<br>
                <strong>Total de estudiantes:</strong> {{ $totalEstudiantes }}<br>
                <strong>Fecha:</strong> {{ $fechaGeneracion }}
            </div>
        </div>
    </div>

    @if($estudiantes->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre Completo</th>
                    <th>Número de Control</th>
                    <th>Correo Electrónico</th>
                    <th>Nivel</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estudiantes as $index => $estudiante)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $estudiante->usuario->nombre_completo ?? 'N/A' }}</td>
                    <td>{{ $estudiante->usuario->numero_control ?? 'N/A' }}</td>
                    <td>{{ $estudiante->usuario->correo_personal ?? 'N/A' }}</td>
                    <td>Nivel {{ $estudiante->nivel_solicitado ?? 'N/A' }}</td>
                    <td>
                        @if($estudiante->estado == 'asignado')
                            <span class="badge badge-info">Asignado</span>
                        @elseif($estudiante->estado == 'cursando')
                            <span class="badge badge-success">Cursando</span>
                        @else
                            {{ $estudiante->estado }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; margin: 50px 0; color: #888;">
            <h3>No hay estudiantes asignados a este grupo</h3>
            <p>Este grupo aún no tiene estudiantes asignados.</p>
        </div>
    @endif

    <div class="footer">
        Documento generado automáticamente por el Sistema de Gestión Académica<br>
        {{ $fechaGeneracion }}
    </div>
</body>
</html>