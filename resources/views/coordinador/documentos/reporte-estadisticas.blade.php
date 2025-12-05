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
            grid-template-columns: repeat(3, 1fr); 
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
        
        /* Secciones */
        .section { 
            margin: 30px 0; 
            page-break-inside: avoid; 
        }
        
        .section-title { 
            font-size: 16px; 
            font-weight: bold; 
            color: #2c3e50; 
            margin-bottom: 15px; 
            padding-bottom: 8px; 
            border-bottom: 2px solid #eee; 
        }
        
        /* Gráficos de barra mejorados */
        .chart-item { 
            margin: 12px 0; 
        }
        
        .chart-label { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 5px;
            font-size: 13px;
        }
        
        .chart-bar { 
            background: #3498db; 
            color: white; 
            padding: 8px 12px; 
            border-radius: 3px; 
            position: relative; 
            min-width: 60px;
            display: inline-block;
        }
        
        .chart-bar-container {
            background: #f0f0f0;
            border-radius: 3px;
            overflow: hidden;
            height: 35px;
            position: relative;
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
        
        /* Estado badges */
        .badge { 
            padding: 4px 8px; 
            border-radius: 3px; 
            font-size: 11px; 
            font-weight: bold;
            display: inline-block;
        }
        
        .badge-pendiente { background: #fef3c7; color: #92400e; }
        .badge-asignado { background: #dbeafe; color: #1e40af; }
        .badge-cursando { background: #d1fae5; color: #065f46; }
        .badge-finalizado { background: #e5e7eb; color: #374151; }
        .badge-cancelado { background: #fee2e2; color: #991b1b; }
        
        /* Responsive para PDF */
        @media print {
            .no-print { display: none; }
            body { margin: 15px; }
            .section { page-break-inside: avoid; }
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

    <!-- Sección: Distribución por Estado -->
    <div class="section">
        <div class="section-title">1. Distribución por Estado</div>
        
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
            <div class="chart-item">
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
                    <span><strong>{{ $cantidad }}</strong> estudiantes ({{ number_format($percentage, 1) }}%)</span>
                </div>
                <div class="chart-bar-container">
                    <div class="chart-bar" style="width: {{ $barWidth }}%; min-width: 60px;">
                        {{ $cantidad }}
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <p style="color: #888; font-style: italic; padding: 15px; background: #f9f9f9; border-radius: 5px;">
                No hay datos disponibles por estado
            </p>
        @endif
    </div>

    <!-- Sección: Distribución por Nivel -->
    <div class="section">
        <div class="section-title">2. Distribución por Nivel Solicitado</div>
        
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
            <div class="chart-item">
                <div class="chart-label">
                    <span><strong>Nivel {{ $item->nivel_solicitado }}</strong></span>
                    <span><strong>{{ $item->total }}</strong> estudiantes ({{ number_format($percentage, 1) }}%)</span>
                </div>
                <div class="chart-bar-container">
                    <div class="chart-bar" style="width: {{ $barWidth }}%; background-color: #2ecc71; min-width: 60px;">
                        {{ $item->total }}
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <p style="color: #888; font-style: italic; padding: 15px; background: #f9f9f9; border-radius: 5px;">
                No hay datos disponibles por nivel
            </p>
        @endif
    </div>

    <!-- Sección: Distribución por Horario -->
    <div class="section">
        <div class="section-title">3. Distribución por Horario Preferido</div>
        
        @if($porHorario && $porHorario->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Horario</th>
                    <th style="text-align: center;">Cantidad</th>
                    <th style="text-align: center;">Porcentaje</th>
                    <th style="width: 35%;">Representación Visual</th>
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
                    <td><strong>{{ $item['horario'] }}</strong></td>
                    <td style="text-align: center;"><strong>{{ $item['total'] }}</strong></td>
                    <td style="text-align: center;">{{ number_format($percentage, 1) }}%</td>
                    <td>
                        <div style="background: #e9ecef; height: 25px; width: 100%; border-radius: 4px; overflow: hidden;">
                            <div style="background: #9b59b6; height: 100%; width: {{ $percentage }}%; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white; font-size: 11px; font-weight: bold;">
                                @if($percentage > 15)
                                    {{ number_format($percentage, 0) }}%
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #2c3e50; color: white;">
                    <td><strong>TOTAL</strong></td>
                    <td style="text-align: center;"><strong>{{ $totalHorario }}</strong></td>
                    <td style="text-align: center;"><strong>100%</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        @else
        <p style="color: #888; font-style: italic; padding: 15px; background: #f9f9f9; border-radius: 5px;">
            No hay datos disponibles para distribución por horario
        </p>
        @endif
    </div>

    <!-- Sección: Resumen Detallado -->
    <div class="section">
        <div class="section-title">4. Resumen Detallado por Estado</div>
        
        <table>
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>Descripción</th>
                    <th style="text-align: center;">Cantidad</th>
                    <th style="text-align: center;">Porcentaje</th>
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
                                @case('pendiente') Estudiantes pendientes de asignación a grupo @break
                                @case('asignado') Estudiantes asignados a grupo pero sin iniciar clases @break
                                @case('cursando') Estudiantes actualmente cursando @break
                                @case('finalizado') Estudiantes que han finalizado el curso @break
                                @case('cancelado') Preregistros cancelados @break
                                @default {{ $estado }}
                            @endswitch
                        </td>
                        <td style="text-align: center;"><strong>{{ $cantidad }}</strong></td>
                        <td style="text-align: center;"><strong>{{ number_format($percentage, 1) }}%</strong></td>
                    </tr>
                    @endforeach
                    <tr style="background-color: #2c3e50; color: white; font-weight: bold;">
                        <td colspan="2" style="text-align: right;">TOTAL GENERAL:</td>
                        <td style="text-align: center;">{{ $total }}</td>
                        <td style="text-align: center;">100%</td>
                    </tr>
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

    <!-- Notas finales -->
    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px; border-left: 4px solid #3498db;">
        <div style="font-weight: bold; margin-bottom: 10px; color: #2c3e50;">
            📋 Notas Importantes:
        </div>
        <ul style="margin: 0; padding-left: 20px; color: #666; font-size: 11px; line-height: 1.6;">
            <li>Los datos presentados corresponden únicamente al período actual seleccionado</li>
            <li>Las estadísticas se actualizan en tiempo real al momento de generar el reporte</li>
            <li>Para análisis históricos, consulte los reportes archivados de períodos anteriores</li>
            <li>Este documento es confidencial y de uso exclusivo para fines académicos internos</li>
        </ul>
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
</body>
</html>