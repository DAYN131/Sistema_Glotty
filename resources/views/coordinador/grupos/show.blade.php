{{-- resources/views/coordinador/grupos/show.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Detalles del Grupo - ' . $grupo->nombre_completo)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Información del Grupo -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información del Grupo</h3>
                    <div class="card-tools">
                        <a href="{{ route('coordinador.grupos.edit', $grupo->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Grupo:</th>
                            <td><strong>{{ $grupo->nombre_completo }}</strong></td>
                        </tr>
                        <tr>
                            <th>Periodo:</th>
                            <td>{{ $grupo->periodo->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Horario:</th>
                            <td>
                                @if($grupo->horario)
                                    {{ $grupo->horario->descripcion }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $grupo->horario->hora_inicio }} - {{ $grupo->horario->hora_fin }}
                                    </small>
                                @else
                                    <span class="text-danger">Sin horario asignado</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Profesor:</th>
                            <td>
                                @if($grupo->profesor)
                                    {{ $grupo->profesor->nombre_completo }}
                                @else
                                    <span class="text-danger">Sin asignar</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Aula:</th>
                            <td>
                                @if($grupo->aula)
                                    {{ $grupo->aula->nombre }}
                                    <br>
                                    <small class="text-muted">Capacidad: {{ $grupo->aula->capacidad }}</small>
                                @else
                                    <span class="text-danger">Sin asignar</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Capacidad:</th>
                            <td>
                                {{ $grupo->estudiantes_inscritos }} / {{ $grupo->capacidad_maxima }} estudiantes
                                <div class="progress mt-1" style="height: 10px;">
                                    <div class="progress-bar {{ $grupo->porcentaje_ocupacion >= 90 ? 'bg-danger' : ($grupo->porcentaje_ocupacion >= 70 ? 'bg-warning' : 'bg-success') }}" 
                                         style="width: {{ $grupo->porcentaje_ocupacion }}%">
                                    </div>
                                </div>
                                <small class="text-muted">{{ number_format($grupo->porcentaje_ocupacion, 1) }}% de ocupación</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                <span class="badge bg-{{ $grupo->estado_color }}">
                                    {{ $grupo->estado_texto }}
                                </span>
                            </td>
                        </tr>
                    </table>

                    <!-- Cambiar Estado -->
                    <form action="{{ route('coordinador.grupos.cambiarEstado', $grupo->id) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="form-group">
                            <label for="estado">Cambiar Estado:</label>
                            <select name="estado" id="estado" class="form-control" onchange="this.form.submit()">
                                <option value="planificado" {{ $grupo->estado == 'planificado' ? 'selected' : '' }}>Planificado</option>
                                <option value="con_profesor" {{ $grupo->estado == 'con_profesor' ? 'selected' : '' }}>Con Profesor</option>
                                <option value="con_aula" {{ $grupo->estado == 'con_aula' ? 'selected' : '' }}>Con Aula</option>
                                <option value="activo" {{ $grupo->estado == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="cancelado" {{ $grupo->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Estudiantes Asignados -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Estudiantes Asignados ({{ $grupo->estudiantesActivos->count() }})</h3>
                </div>
                <div class="card-body p-0">
                    @if($grupo->estudiantesActivos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Estudiante</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Estado Pago</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($grupo->estudiantesActivos as $estudiante)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $estudiante->usuario->nombre_completo ?? 'N/A' }}</td>
                                            <td>{{ $estudiante->usuario->email ?? 'N/A' }}</td>
                                            <td>{{ $estudiante->usuario->telefono ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $estudiante->pago_estado == 'pagado' ? 'success' : 'warning' }}">
                                                    {{ $estudiante->pago_estado }}
                                                </span>
                                            </td>
                                            <td>
                                                <form action="{{ route('coordinador.grupos.removerEstudiante', $grupo->id) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('¿Está seguro de remover a este estudiante del grupo?')">
                                                    @csrf
                                                    <input type="hidden" name="preregistro_id" value="{{ $estudiante->id }}">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Remover del grupo">
                                                        <i class="fas fa-user-minus"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <br>
                            No hay estudiantes asignados a este grupo
                        </div>
                    @endif
                </div>
            </div>

            <!-- Estudiantes Disponibles para Asignar -->
            @if($estudiantesDisponibles->count() > 0 && $grupo->tieneCapacidad())
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Estudiantes Disponibles para Asignar</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Estudiante</th>
                                        <th>Email</th>
                                        <th>Horario Preferido</th>
                                        <th>Estado Pago</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($estudiantesDisponibles as $estudiante)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $estudiante->usuario->nombre_completo ?? 'N/A' }}</td>
                                            <td>{{ $estudiante->usuario->email ?? 'N/A' }}</td>
                                            <td>
                                                @if($estudiante->horarioPreferido)
                                                    {{ $estudiante->horarioPreferido->descripcion }}
                                                @else
                                                    <span class="text-muted">No especificado</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $estudiante->pago_estado == 'pagado' ? 'success' : 'warning' }}">
                                                    {{ $estudiante->pago_estado }}
                                                </span>
                                            </td>
                                            <td>
                                                <form action="{{ route('coordinador.grupos.asignarEstudiante', $grupo->id) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="preregistro_id" value="{{ $estudiante->id }}">
                                                    <button type="submit" class="btn btn-success btn-sm" title="Asignar al grupo">
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
                </div>
            @endif
        </div>
    </div>
</div>
@endsection