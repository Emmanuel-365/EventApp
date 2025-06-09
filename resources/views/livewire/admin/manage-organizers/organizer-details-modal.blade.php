<div x-data="{ showModal: @entangle('show').live }"
     x-show="showModal"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50"
     style="display: none;"
     @click.away="$wire.close()"
     @keydown.escape.window="$wire.close()">

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-5xl relative flex flex-col max-h-[95vh] overflow-hidden">
        {{-- Bouton Fermer --}}
        <button type="button" @click="$wire.close()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-150">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Titre de la modale --}}
        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6 text-center flex-shrink-0">
            Détails de l'Organisateur
        </h3>

        {{-- Conteneur principal pour le contenu défilant --}}
        <div class="overflow-y-auto flex-grow">
            @if ($organizerId)
                @livewire('admin.manage-organizers.organizer-detail', ['organizerId' => $organizerId], key($organizerId))
            @else
                <p class="text-center text-gray-500 dark:text-gray-400 py-8">Chargement des détails de l'organisateur...</p>
            @endif
        </div>
    </div>
</div>
