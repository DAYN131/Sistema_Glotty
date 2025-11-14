{{-- resources/views/partials/notifications.blade.php --}}
@if(session('success'))
    <div id="success-notification" class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 relative">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" 
                    onclick="document.getElementById('success-notification').style.display='none'"
                    class="text-green-600 hover:text-green-800 transition-smooth">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif

@if(session('warning'))
    <div id="warning-notification" class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6 relative">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3"></i>
                <span class="text-yellow-800 font-medium">{{ session('warning') }}</span>
            </div>
            <button type="button" 
                    onclick="document.getElementById('warning-notification').style.display='none'"
                    class="text-yellow-600 hover:text-yellow-800 transition-smooth">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif

@if(session('error'))
    <div id="error-notification" class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 relative">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-600 text-xl mr-3"></i>
                <span class="text-red-800 font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" 
                    onclick="document.getElementById('error-notification').style.display='none'"
                    class="text-red-600 hover:text-red-800 transition-smooth">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif