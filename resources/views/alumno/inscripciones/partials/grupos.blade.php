@foreach($grupos as $grupo)
<label class="block grupo-card border rounded-lg p-4 cursor-pointer">
    <div class="flex items-start">
        <input type="radio" name="id_grupo" value="{{ $grupo['id'] }}" class="mt-1 mr-3" required>
        <div class="flex-1">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-gray-800">{{ $grupo['nombre_grupo'] }}</h3>
                <div class="flex space-x-2">
                    <span class="text-sm px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                        {{ $grupo['periodo'] ?? 'Periodo no definido' }}
                    </span>
                    <span class="text-sm px-2 py-1 rounded-full {{ $grupo['cupo_disponible'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $grupo['cupo_disponible'] }} cupos
                    </span>
                </div>
            </div>
            
            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="flex items-start">
                    <i class="fas fa-clock text-gray-500 mt-1 mr-2"></i>
                    <div>
                        <p class="font-medium text-gray-700">Horario</p>
                        <p class="text-sm text-gray-600">{{ $grupo['horario'] }}</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <i class="fas fa-chalkboard-teacher text-gray-500 mt-1 mr-2"></i>
                    <div>
                        <p class="font-medium text-gray-700">Profesor</p>
                        <p class="text-sm text-gray-600">{{ $grupo['profesor'] }}</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <i class="fas fa-door-open text-gray-500 mt-1 mr-2"></i>
                    <div>
                        <p class="font-medium text-gray-700">Aula</p>
                        <p class="text-sm text-gray-600">{{ $grupo['aula'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</label>
@endforeach