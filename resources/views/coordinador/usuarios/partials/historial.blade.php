{{-- resources/views/coordinador/usuarios/partials/historial.blade.php --}}
@if($preregistros->count() > 0)
    <div class="space-y-3">
        @foreach($preregistros as $preregistro)
            <div class="border border-slate-200 rounded-lg p-3 hover:bg-slate-50 transition-colors">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="font-medium text-slate-800">
                                {{ $preregistro->periodo->nombre ?? 'Periodo no disponible' }}
                            </span>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800">
                                Nivel {{ $preregistro->nivel_solicitado }}
                            </span>
                        </div>
                        <div class="mt-1 text-sm text-slate-600">
                            <span class="inline-flex items-center mr-3">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $preregistro->created_at->format('d/m/Y') }}
                            </span>
                            <span class="inline-flex items-center">
                                <i class="fas fa-users mr-1"></i>
                                {{ $preregistro->grupoAsignado->nombre_completo ?? 'Sin grupo' }}
                            </span>
                        </div>
                    </div>
                    <div>
                        @php
                            $estadoColors = [
                                'pendiente' => 'bg-yellow-100 text-yellow-800',
                                'asignado' => 'bg-blue-100 text-blue-800',
                                'cursando' => 'bg-green-100 text-green-800',
                                'finalizado' => 'bg-gray-100 text-gray-800',
                                'cancelado' => 'bg-red-100 text-red-800'
                            ];
                            $color = $estadoColors[$preregistro->estado] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $color }}">
                            {{ $preregistro->estado_formateado }}
                        </span>
                    </div>
                </div>
                
                @if($preregistro->pago_estado)
                    <div class="mt-2 text-xs">
                        <span class="font-medium">Pago:</span>
                        <span class="ml-1 px-1.5 py-0.5 rounded {{ $preregistro->pago_estado == 'pagado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $preregistro->pago_estado_formateado }}
                        </span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-8 text-slate-500">
        <i class="fas fa-history text-3xl mb-3"></i>
        <p>No hay historial de preregistros</p>
    </div>
@endif