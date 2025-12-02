{{-- resources/views/coordinador/documentos/reporte-estadisticas.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Estadísticas {{ $periodo ? '- ' . $periodo->nombre_periodo : '' }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            font-size: 12px;
        }
        
        /* Encabezado */
        .header { 
            text-align: center; 
            margin-bottom: 25px; 
            border-bottom: 3px solid #2c3e50; 
            padding-bottom: 15px; 
        }
        
        .title { 
            font-size: 22px; 
            font-weight: bold; 
            color: #2c3e50; 
        }
        
        .subtitle { 
            font-size: 14px; 
            color: #666; 
            margin-top: 5px; 
        }
        
        .periodo-info { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 20px 0; 
            border-left: 4px solid #3498db; 
        }
        
        /* Estadísticas */
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 15px; 
            margin: 25px 0; 
        }
        
        .stat-card { 
            background: white; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            padding: 15px; 
            text-align: center; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }
        
        .stat-number { 
            font-size: 28px; 
            font-weight: bold; 
            color: #2c3e50; 
            margin: 10px 0; 
        }
        
        .stat-label { 
            font-size: 12px; 
            color: #666; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
        }
        
        /* Tablas */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 15px 0; 
        }
        
        th { 
            background-color: #2c3e50; 
            color: white; 
            padding: 12px; 
            text-align: left; 
            font-weight: bold; 
        }
        
        td { 
            padding: 10px; 
            border-bottom: 1px solid #ddd; 
            vertical-align: top; 
        }
        
        tr:nth-child(even) { 
            background-color: #f8f9fa; 
        }
        
        /* Gráficos (representación simple) */
        .chart-container { 
            margin: 25px 0; 
            page-break-inside: avoid; 
        }
        
        .chart-title { 
            font-size: 16px; 
            font-weight: bold; 
            color: #2c3e50; 
            margin-bottom: 15px; 
            padding-bottom: 8px; 
            border-bottom: 2px solid #eee; 
        }
        
        .chart-bar { 
            background: #3498db; 
            color: white; 
            padding: 8px; 
            margin: 5px 0; 
            border-radius: 3px; 
            position: relative; 
        }
        
        .chart-label { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 5px; 
        }
        
        /* Footer */
        .footer { 
            margin-top: 40px; 
            padding-top: 15px; 
            border-top: 1px solid #ddd; 
            text-align: center; 
            font-size: 10px; 
            color: #888; 
        }
        
        /* Columnas para distribución */
        .two-columns { 
            column-count: 2; 
            column-gap: 30px; 
            margin: 20px 0; 
        }
        
        /* Estado badges */
        .badge { 
            padding: 4px 8px; 
            border-radius: 3px; 
            font-size: 11px; 
            font-weight: bold; 
        }
        
        .badge-pendiente { background: #fef3c7; color: #92400e; }
        .badge-asignado { background: #dbeafe; color: #1e40af; }
        .badge-cursando { background: #d1fae5; color: #065f46; }
        .badge-finalizado { background: #e5e7eb; color: #374151; }
        .badge-cancelado { background: #fee2e2; color: #991b1b; }
        
        /* Responsive para PDF */
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <div class="title">REPORTE DE ESTADÍSTICAS DEL SISTEMA</div>
        <div class="subtitle">Sistema Académico Glotty</div>
        
        @if($periodo)
        <div class="periodo-info">
            <strong>PERÍODO ACTUAL:</strong> {{ $periodo->nombre_periodo }}<br>
            <strong>Estado:</strong> {{ $periodo->estado_legible ?? ucfirst(str_replace('_', ' ', $periodo->estado)) }}<br>
            <strong>Fecha de inicio:</strong> {{ $periodo->fecha_inicio->format('d/m/Y') }}<br>
            <strong>Fecha de fin:</strong> {{ $periodo->fecha_fin->format('d/m/Y') }}
        </div>
        @else
        <div class="periodo-info" style="border-left-color: #e74c3c;">
            <strong>ADVERTENCIA:</strong> No hay período activo actualmente.
        </div>
        @endif
        
        <div style="text-align: right; color: #666; font-size: 11px;">
            Generado: {{ $fechaGeneracion }}
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Preregistros</div>
            <div class="stat-number">{{ $totalPreregistros }}</div>
        </div>
        
        @if($periodo)
        <div class="stat-card">
            <div class="stat-label">Grupos Activos</div>
            <div class="stat-number">{{ $periodo->grupos()->count() }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-label">Duración del Período</div>
            <div class="stat-number">{{ $periodo->dias_duracion ?? 0 }}</div>
            <div class="stat-label">días</div>
        </div>
        @endif
    </div>

    <!-- Distribución en 2 columnas -->
    <div class="two-columns">
        <!-- Distribución por Estado -->
        <div class="chart-container">
            <div class="chart-title">Distribución por Estado</div>
            
            @if($porEstado && $porEstado->count() > 0)
                @php
                    $total = $porEstado->sum();
                    $maxValue = $porEstado->max();
                @endphp
                
                @foreach($porEstado as $estado => $cantidad)
                @php
                    $percentage = $total > 0 ? ($cantidad / $total) * 100 : 0;
                    $barWidth = $maxValue > 0 ? ($cantidad / $maxValue) * 100 : 0;
                @endphp
                <div class="chart-label">
                    <span>
                        @switch($estado)
                            @case('pendiente')
                                <span class="badge badge-pendiente">Pendiente</span>
                                @break
                            @case('asignado')
                                <span class="badge badge-asignado">Asignado</span>
                                @break
                            @case('cursando')
                                <span class="badge badge-cursando">Cursando</span>
                                @break
                            @case('finalizado')
                                <span class="badge badge-finalizado">Finalizado</span>
                                @break
                            @case('cancelado')
                                <span class="badge badge-cancelado">Cancelado</span>
                                @break
                            @default
                                {{ ucfirst($estado) }}
                        @endswitch
                    </span>
                    <span><strong>{{ $cantidad }}</strong> ({{ number_format($percentage, 1) }}%)</span>
                </div>
                <div class="chart-bar" style="width: {{ $barWidth }}%; min-width: 5%;">
                    {{ $cantidad }}
                </div>
                @endforeach
            @else
                <p style="color: #888; font-style: italic;">No hay datos disponibles por estado</p>
            @endif
        </div>

        <!-- Distribución por Nivel -->
        <div class="chart-container">
            <div class="chart-title">Distribución por Nivel Solicitado</div>
            
            @if($porNivel && $porNivel->count() > 0)
                @php
                    $totalNivel = $porNivel->sum('total');
                    $maxNivel = $porNivel->max('total');
                @endphp
                
                @foreach($porNivel as $item)
                @php
                    $percentage = $totalNivel > 0 ? ($item->total / $totalNivel) * 100 : 0;
                    $barWidth = $maxNivel > 0 ? ($item->total / $maxNivel) * 100 : 0;
                @endphp
                <div class="chart-label">
                    <span>Nivel {{ $item->nivel_solicitado }}</span>
                    <span><strong>{{ $item->total }}</strong> ({{ number_format($percentage, 1) }}%)</span>
                </div>
                <div class="chart-bar" style="width: {{ $barWidth }}%; min-width: 5%; background-color: #2ecc71;">
                    {{ $item->total }}
                </div>
                @endforeach
            @else
                <p style="color: #888; font-style: italic;">No hay datos disponibles por nivel</p>
            @endif
        </div>
    </div>

    <!-- Tabla de distribución por Horario -->
    <div class="chart-container">
        <div class="chart-title">Distribución por Horario Preferido</div>
        
        @if($porHorario && $porHorario->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Horario</th>
                    <th>Cantidad de Estudiantes</th>
                    <th>Porcentaje</th>
                    <th>Representación</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalHorario = $porHorario->sum('total');
                @endphp
                
                @foreach($porHorario as $item)
                @php
                    $percentage = $totalHorario > 0 ? ($item['total'] / $totalHorario) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ $item['horario'] }}</td>
                    <td><strong>{{ $item['total'] }}</strong></td>
                    <td>{{ number_format($percentage, 1) }}%</td>
                    <td>
                        <div style="background: #ddd; height: 10px; width: 100%; border-radius: 5px;">
                            <div style="background: #9b59b6; height: 100%; width: {{ $percentage }}%; border-radius: 5px;"></div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f8f9fa;">
                    <td><strong>TOTAL</strong></td>
                    <td><strong>{{ $totalHorario }}</strong></td>
                    <td><strong>100%</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        @else
        <p style="color: #888; font-style: italic; padding: 20px; text-align: center;">
            No hay datos disponibles para distribución por horario
        </p>
        @endif
    </div>

    <!-- Tabla detallada de estados -->
    <div class="chart-container">
        <div class="chart-title">Resumen Detallado</div>
        
        <table>
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                @if($porEstado && $porEstado->count() > 0)
                    @php $total = $porEstado->sum(); @endphp
                    @foreach($porEstado as $estado => $cantidad)
                    @php $percentage = $total > 0 ? ($cantidad / $total) * 100 : 0; @endphp
                    <tr>
                        <td>
                            @switch($estado)
                                @case('pendiente')
                                    <span class="badge badge-pendiente">Pendiente</span>
                                    @break
                                @case('asignado')
                                    <span class="badge badge-asignado">Asignado</span>
                                    @break
                                @case('cursando')
                                    <span class="badge badge-cursando">Cursando</span>
                                    @break
                                @case('finalizado')
                                    <span class="badge badge-finalizado">Finalizado</span>
                                    @break
                                @case('cancelado')
                                    <span class="badge badge-cancelado">Cancelado</span>
                                    @break
                                @default
                                    {{ ucfirst($estado) }}
                            @endswitch
                        </td>
                        <td>
                            @switch($estado)
                                @case('pendiente') Pendiente de asignación a grupo @break
                                @case('asignado') Asignado a grupo pero sin iniciar @break
                                @case('cursando') Actualmente cursando @break
                                @case('finalizado') Ha finalizado el curso @break
                                @case('cancelado') Preregistro cancelado @break
                                @default {{ $estado }}
                            @endswitch
                        </td>
                        <td><strong>{{ $cantidad }}</strong></td>
                        <td>{{ number_format($percentage, 1) }}%</td>
                    </tr>
                    @endforeach
                @else
                <tr>
                    <td colspan="4" style="text-align: center; color: #888; font-style: italic; padding: 20px;">
                        No hay preregistros para mostrar estadísticas
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pie de página -->
    <div class="footer">
        <div style="margin-bottom: 5px;">
            <strong>Sistema Académico Glotty</strong> - Reporte generado automáticamente
        </div>
        <div>
            Página 1 de 1 • {{ now()->format('d/m/Y H:i:s') }} • 
            @if($periodo)
                Período: {{ $periodo->nombre_periodo }}
            @else
                Sin período activo
            @endif
        </div>
        <div style="margin-top: 10px; color: #aaa;">
            Este documento es confidencial y para uso interno exclusivo.
        </div>
    </div>

    <!-- Notas finales -->
    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px; font-size: 11px; color: #666;">
        <div style="display: flex; align-items: center; margin-bottom: 10px;">
            <div style="width: 15px; height: 15px; background: #3498db; margin-right: 8px; border-radius: 3px;"></div>
            <strong>Leyenda de colores:</strong>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
            <div>
                <div style="display: flex; align-items: center; margin-bottom: 3px;">
                    <div style="width: 12px; height: 12px; background: #fef3c7; margin-right: 5px; border-radius: 2px;"></div>
                    <span>Pendiente: Esperando asignación</span>
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 3px;">
                    <div style="width: 12px; height: 12px; background: #dbeafe; margin-right: 5px; border-radius: 2px;"></div>
                    <span>Asignado: Grupo asignado</span>
                </div>
                <div style="display: flex; align-items: center;">
                    <div style="width: 12px; height: 12px; background: #d1fae5; margin-right: 5px; border-radius: 2px;"></div>
                    <span>Cursando: En desarrollo</span>
                </div>
            </div>
            <div>
                <div style="display: flex; align-items: center; margin-bottom: 3px;">
                    <div style="width: 12px; height: 12px; background: #e5e7eb; margin-right: 5px; border-radius: 2px;"></div>
                    <span>Finalizado: Curso completado</span>
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 3px;">
                    <div style="width: 12px; height: 12px; background: #fee2e2; margin-right: 5px; border-radius: 2px;"></div>
                    <span>Cancelado: No completado</span>
                </div>
                <div style="display: flex; align-items: center;">
                    <div style="width: 12px; height: 12px; background: #3498db; margin-right: 5px; border-radius: 2px;"></div>
                    <span>Gráficos de distribución</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>