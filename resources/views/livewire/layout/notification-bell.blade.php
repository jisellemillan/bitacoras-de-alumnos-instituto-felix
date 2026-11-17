<div x-data="{ open: false }" class="relative">
    <!-- Icono de la campana -->
    <button @click="open = !open" class="relative p-2 text-gray-200 hover:text-white transition-colors focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        <!-- Burbuja roja de contador -->
        @if($this->notifications->count() > 0)
            <span class="absolute top-1 right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full">
                {{ $this->notifications->count() }}
            </span>
        @endif
    </button>

    <!-- Menú desplegable de notificaciones -->
    <div x-show="open" @click.outside="open = false" x-cloak style="display: none;" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50 overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-800">Notificaciones</h3>
            @if($this->notifications->count() > 0)
                <button wire:click="markAllAsRead" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Marcar leídas</button>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse($this->notifications as $notification)
                <div class="px-4 py-3 border-b border-gray-50 hover:bg-slate-50 transition-colors {{ $notification->read_at ? 'opacity-50' : 'bg-blue-50/30' }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $notification->data['title'] }}</p>
                            <p class="text-xs text-gray-600 mt-0.5">{{ $notification->data['message'] }}</p>
                            <p class="text-[10px] text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @if(is_null($notification->read_at))
                            <button wire:click="markAsRead('{{ $notification->id }}')" class="text-blue-500 hover:text-blue-700" title="Marcar como leída">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-6 text-center text-sm text-gray-500">
                    No tienes notificaciones nuevas.
                </div>
            @endforelse
        </div>
    </div>
</div>