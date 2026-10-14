@props([
    'viewUrl'       => null,
    'editAction'    => null,
    'deleteAction'  => null,
    'deleteConfirm' => '¿Estás seguro de que deseas eliminar este registro? Esta acción se puede deshacer.',
    'items'         => null,   {{-- slot nombrado opcional --}}
])

{{-- Acepta contenido libre en el slot por defecto cuando no se usan los props --}}

<div x-data="{ open: false }" class="relative inline-block text-left" @click.outside="open = false" @keydown.escape.window="open = false">
    {{-- Botón de tres puntos verticales ⋮ --}}
    <button type="button"
            @click.stop="open = !open"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-green-800 hover:bg-green-50 transition-colors focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-1"
            aria-haspopup="true"
            :aria-expanded="open"
            title="Opciones de registro">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
        </svg>
        <span class="sr-only">Acciones</span>
    </button>

    {{-- Menú flotante desplegable --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 z-50 mt-1 w-48 origin-top-right rounded-xl bg-white shadow-xl ring-1 ring-black/5 divide-y divide-gray-100 focus:outline-none"
         style="display: none;"
         role="menu"
         @click="open = false"
         aria-orientation="vertical">

        {{-- MODO 1: Slot nombrado "items" o slot por defecto, para uso flexible --}}
        @if($items || (!$viewUrl && !$editAction && !$deleteAction))
            <div class="py-1" role="none">
                {{ $items ?? $slot }}
            </div>

        {{-- MODO 2: Props clásicos para tablas de listados --}}
        @else
            <div class="py-1" role="none">
                {{-- Ver detalles --}}
                @if($viewUrl)
                    <a href="{{ $viewUrl }}"
                       class="group flex items-center px-4 py-2.5 text-xs font-medium text-gray-700 hover:bg-green-50 hover:text-green-900 transition-colors"
                       role="menuitem">
                        <svg class="mr-2.5 h-4 w-4 text-gray-400 group-hover:text-green-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Ver detalles
                    </a>
                @endif

                {{-- Editar --}}
                @if($editAction)
                    <button type="button"
                            wire:click="{{ $editAction }}"
                            class="group flex w-full items-center px-4 py-2.5 text-xs font-medium text-gray-700 hover:bg-green-50 hover:text-green-900 transition-colors text-left"
                            role="menuitem">
                        <svg class="mr-2.5 h-4 w-4 text-gray-400 group-hover:text-green-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar
                    </button>
                @endif
            </div>

            {{-- Eliminar --}}
            @if($deleteAction)
                <div class="py-1" role="none">
                    <button type="button"
                            wire:click="{{ $deleteAction }}"
                            wire:confirm="{{ $deleteConfirm }}"
                            class="group flex w-full items-center px-4 py-2.5 text-xs font-medium text-red-600 hover:bg-red-50 hover:text-red-800 transition-colors text-left"
                            role="menuitem">
                        <svg class="mr-2.5 h-4 w-4 text-red-400 group-hover:text-red-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Eliminar
                    </button>
                </div>
            @endif
        @endif
    </div>
</div>
