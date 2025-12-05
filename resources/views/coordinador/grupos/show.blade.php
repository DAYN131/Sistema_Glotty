{{-- resources/views/coordinador/grupos/show.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Detalles del Grupo - ' . $grupo->nombre_completo)

@section('content')
<div class="container-fluid py-5" style="background: linear-gradient(135deg, #f8f7f5 0%, #efefeb 100%);">
    <!-- Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 style="color: #1f1f1f; margin: 0; font-weight: 700; font-size: 2rem;">{{ $grupo->nombre_completo }}</h1>
                        <p style="color: #666; margin: 0.5rem 0 0 0;">Gestión integral del grupo educativo</p>
                    </div>
                    <a href="{{ route('coordinador.grupos.edit', $grupo->id) }}" class="btn" style="background-color: #2D5016; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600;">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información del Grupo (izquierda) -->
        <div class="col-lg-4 mb-4">
            <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <!-- Encabezado tarjeta -->
                <div style="background-color: #2D5016; padding: 1.5rem; color: white;">
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600;">Información del Grupo</h3>
                </div>
                
                <div style="padding: 1.5rem;">
                    <!-- Información del grupo -->
                    <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #eee;">
                        <p style="color: #666; font-size: 0.9rem; margin: 0 0 0.5rem 0; text-transform: uppercase; font-weight: 600;">Grupo</p>
                        <p style="color: #1f1f1f; font-size: 1.1rem; margin: 0; font-weight: 600;">{{ $grupo->nombre_completo }}</p>
                    </div>

                    <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #eee;">
                        <p style="color: #666; font-size: 0.9rem; margin: 0 0 0.5rem 0; text-transform: uppercase; font-weight: 600;">Período</p>
                        <p style="color: #1f1f1f; font-size: 1rem; margin: 0;">{{ $grupo->periodo->nombre_periodo }}</p>
                    </div>

                    <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #eee;">
                        <p style="color: #666; font-size: 0.9rem; margin: 0 0 0.5rem 0; text-transform: uppercase; font-weight: 600;">Horario</p>
                        @if($grupo->horario)
                            <p style="color: #1f1f1f; font-size: 1rem; margin: 0; font-weight: 500;">{{ $grupo->horario->nombre }}</p>
                            <p style="color: #999; font-size: 0.9rem; margin: 0.5rem 0 0 0;">
                                <i class="fas fa-clock"></i> {{ $grupo->horario->hora_inicio }} - {{ $grupo->horario->hora_fin }}
                            </p>
                        @else
                            <span style="background: #fee; color: #c33; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.9rem;">Sin asignar</span>
                        @endif
                    </div>

                    <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #eee;">
                        <p style="color: #666; font-size: 0.9rem; margin: 0 0 0.5rem 0; text-transform: uppercase; font-weight: 600;">Profesor</p>
                        @if($grupo->profesor)
                            <p style="color: #1f1f1f; font-size: 1rem; margin: 0; font-weight: 500;">{{ $grupo->profesor->nombre_profesor }} {{ $grupo->profesor->apellidos_profesor }}</p>
                        @else
                            <span style="background: #fee; color: #c33; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.9rem;">Sin asignar</span>
                        @endif
                    </div>

                    <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #eee;">
                        <p style="color: #666; font-size: 0.9rem; margin: 0 0 0.5rem 0; text-transform: uppercase; font-weight: 600;">Aula</p>
                        @if($grupo->aula)
                            <p style="color: #1f1f1f; font-size: 1rem; margin: 0; font-weight: 500;">{{ $grupo->aula->nombre }}</p>
                            <p style="color: #999; font-size: 0.9rem; margin: 0.5rem 0 0 0;">Capacidad: <strong>{{ $grupo->aula->capacidad }} estudiantes</strong></p>
                        @else
                            <span style="background: #fee; color: #c33; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.9rem;">Sin asignar</span>
                        @endif
                    </div>

                    <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #eee;">
                        <p style="color: #666; font-size: 0.9rem; margin: 0 0 0.5rem 0; text-transform: uppercase; font-weight: 600;">Estado</p>
                        <span style="background: {{ $grupo->estado_color == 'success' ? '#d4edda' : ($grupo->estado_color == 'warning' ? '#fff3cd' : '#f8d7da') }}; color: {{ $grupo->estado_color == 'success' ? '#155724' : ($grupo->estado_color == 'warning' ? '#856404' : '#721c24') }}; padding: 0.5rem 0.75rem; border-radius: 6px; font-weight: 600; display: inline-block;">
                            {{ $grupo->estado }}
                        </span>
                    </div>

                    <!-- Capacidad del grupo -->
                    <div>
                        <p style="color: #666; font-size: 0.9rem; margin: 0 0 0.5rem 0; text-transform: uppercase; font-weight: 600;">Ocupación</p>
                        <p style="color: #1f1f1f; font-size: 1.1rem; margin: 0 0 0.75rem 0; font-weight: 600;">{{ $grupo->estudiantes_inscritos }}/{{ $grupo->capacidad_maxima }} estudiantes</p>
                        <div style="background: #eee; border-radius: 8px; height: 8px; overflow: hidden;">
                            <div style="background: {{ $grupo->porcentaje_ocupacion >= 90 ? '#dc3545' : ($grupo->porcentaje_ocupacion >= 70 ? '#ffc107' : '#28a745') }}; height: 100%; width: {{ $grupo->porcentaje_ocupacion }}%; transition: all 0.3s ease;"></div>
                        </div>
                        <p style="color: #999; font-size: 0.85rem; margin: 0.5rem 0 0 0;">{{ number_format($grupo->porcentaje_ocupacion, 1) }}% de ocupación</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estudiantes (derecha) -->
        <div class="col-lg-8">
            <!-- Estudiantes Asignados -->
            <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 2rem;">
                <div style="background-color: #2D5016; padding: 1.5rem; color: white; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600;">Estudiantes Asignados</h3>
                    <span style="background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600;">{{ $grupo->estudiantesActivos->count() }}</span>
                </div>

                <div style="overflow-x: auto;">
                    @if($grupo->estudiantesActivos->count() > 0)
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.95rem;">
                            <thead>
                                <tr style="background: #f8f7f5; border-bottom: 2px solid #eee;">
                                    <th style="padding: 1rem; text-align: left; color: #666; font-weight: 600; text-transform: uppercase; font-size: 0.85rem;">#</th>
                                    <th style="padding: 1rem; text-align: left; color: #666; font-weight: 600; text-transform: uppercase; font-size: 0.85rem;">Estudiante</th>
                                    <th style="padding: 1rem; text-align: left; color: #666; font-weight: 600; text-transform: uppercase; font-size: 0.85rem;">Email</th>
                                    <th style="padding: 1rem; text-align: left; color: #666; font-weight: 600; text-transform: uppercase; font-size: 0.85rem;">Teléfono</th>
                                    <th style="padding: 1rem; text-align: left; color: #666; font-weight: 600; text-transform: uppercase; font-size: 0.85rem;">Pago</th>
                                    <th style="padding: 1rem; text-align: center; color: #666; font-weight: 600; text-transform: uppercase; font-size: 0.85rem;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grupo->estudiantesActivos as $estudiante)
                                    <tr style="border-bottom: 1px solid #eee; transition: background 0.2s ease;" onmouseover="this.style.background='#f8f7f5'" onmouseout="this.style.background='white'">
                                        <td style="padding: 1rem; color: #666; font-weight: 500;">{{ $loop->iteration }}</td>
                                        <td style="padding: 1rem; color: #1f1f1f; font-weight: 500;">{{ $estudiante->usuario->nombre_completo ?? 'N/A' }}</td>
                                        <td style="padding: 1rem; color: #666;">{{ $estudiante->usuario->correo_institucional ?? 'N/A' }}</td>
                                        <td style="padding: 1rem; color: #666;">{{ $estudiante->usuario->telefono ?? 'N/A' }}</td>
                                        <td style="padding: 1rem;">
                                            <span style="background: {{ $estudiante->pago_estado == 'pagado' ? '#d4edda' : '#fff3cd' }}; color: {{ $estudiante->pago_estado == 'pagado' ? '#155724' : '#856404' }}; padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 600; font-size: 0.85rem; text-transform: capitalize;">
                                                {{ $estudiante->pago_estado }}
                                            </span>
                                        </td>
                                        <td style="padding: 1rem; text-align: center;">
                                            <form action="{{ route('coordinador.grupos.removerEstudiante', $grupo->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de remover a este estudiante del grupo?')">
                                                @csrf
                                                <input type="hidden" name="preregistro_id" value="{{ $estudiante->id }}">
                                                <button type="submit" class="btn btn-sm" style="background: #fee; color: #c33; border: none; padding: 0.5rem 0.75rem; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.2s ease;" onmouseover="this.style.background='#fdd'" onmouseout="this.style.background='#fee'" title="Remover del grupo">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="padding: 3rem; text-align: center; color: #999;">
                            <i class="fas fa-users" style="font-size: 2.5rem; display: block; margin-bottom: 1rem; opacity: 0.5;"></i>
                            <p style="margin: 0; font-size: 1rem;">No hay estudiantes asignados a este grupo</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Estudiantes Disponibles para Asignar -->
            @if($estudiantesDisponibles->count() > 0 && $grupo->tieneCapacidad())
                <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <div style="background-color: #2D5016; padding: 1.5rem; color: white;">
                        <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600;">Estudiantes Disponibles para Asignar</h3>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.95rem;">
                            <thead>
                                <tr style="background: #f8f7f5; border-bottom: 2px solid #eee;">
                                    <th style="padding: 1rem; text-align: left; color: #666; font-weight: 600; text-transform: uppercase; font-size: 0.85rem;">#</th>
                                    <th style="padding: 1rem; text-align: left; color: #666; font-weight: 600; text-transform: uppercase; font-size: 0.85rem;">Estudiante</th>
                                    <th style="padding: 1rem; text-align: left; color: #666; font-weight: 600; text-transform: uppercase; font-size: 0.85rem;">Email</th>
                                    <th style="padding: 1rem; text-align: left; color: #666; font-weight: 600; text-transform: uppercase; font-size: 0.85rem;">Pago</th>
                                    <th style="padding: 1rem; text-align: center; color: #666; font-weight: 600; text-transform: uppercase; font-size: 0.85rem;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($estudiantesDisponibles as $estudiante)
                                    <tr style="border-bottom: 1px solid #eee; transition: background 0.2s ease;" onmouseover="this.style.background='#f8f7f5'" onmouseout="this.style.background='white'">
                                        <td style="padding: 1rem; color: #666; font-weight: 500;">{{ $loop->iteration }}</td>
                                        <td style="padding: 1rem; color: #1f1f1f; font-weight: 500;">{{ $estudiante->usuario->nombre_completo ?? 'N/A' }}</td>
                                        <td style="padding: 1rem; color: #666;">{{ $estudiante->usuario->correo_institucional ?? 'N/A' }}</td>
                                        <td style="padding: 1rem;">
                                            <span style="background: {{ $estudiante->pago_estado == 'pagado' ? '#d4edda' : '#fff3cd' }}; color: {{ $estudiante->pago_estado == 'pagado' ? '#155724' : '#856404' }}; padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 600; font-size: 0.85rem; text-transform: capitalize;">
                                                {{ $estudiante->pago_estado }}
                                            </span>
                                        </td>
                                        <td style="padding: 1rem; text-align: center;">
                                            <form action="{{ route('coordinador.grupos.asignarEstudiante', $grupo->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="preregistro_id" value="{{ $estudiante->id }}">
                                                <button type="submit" class="btn btn-sm" style="background: #2D5016; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.2s ease;" onmouseover="this.style.background='#1e3610'" onmouseout="this.style.background='#2D5016'" title="Asignar al grupo">
                                                    <i class="fas fa-user-plus"></i> Asignar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .btn {
        transition: all 0.2s ease;
    }
</style>
@endsection
