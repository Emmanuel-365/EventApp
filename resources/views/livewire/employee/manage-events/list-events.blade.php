<div>
    {{-- Entête de la page --}}
    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mb-8 tracking-tight">Liste des Événements</h2>

    {{-- Barre de recherche et filtres --}}
    <div class="mb-6 flex flex-col md:flex-row justify-between items-stretch md:items-center space-y-4 md:space-y-0 md:space-x-4">
        {{-- Champ de recherche --}}
        <div class="relative flex-grow">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher par titre ou matricule..."
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        {{-- Filtre par statut --}}
        <select wire:model.live="statusFilter"
                class="w-full md:w-auto px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm
                       bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
            <option value="">Tous les statuts</option>
            <option value="published">Publié</option>
            <option value="not_published">Non Publié</option>
            <option value="cancelled">Annulé</option>
            <option value="pending_cancellation">Annulation en attente</option>
            <option value="pending_deletion">Suppression en attente</option>
            {{-- Ajoutez d'autres statuts si nécessaire --}}
        </select>

        {{-- Option "Afficher les événements supprimés" --}}
        <label for="showDeletedEvents" class="flex items-center text-gray-700 dark:text-gray-300">
            <input type="checkbox" id="showDeletedEvents" wire:model.live="showDeletedEvents"
                   class="form-checkbox h-5 w-5 text-blue-600 dark:bg-gray-700 dark:border-gray-600 rounded
                          focus:ring-blue-500 transition duration-150 ease-in-out">
            <span class="ml-2 text-sm md:text-base">Afficher les supprimés</span>
        </label>

        {{-- Bouton Créer un Événement --}}
        @if ($user && $user->can('create-events'))
            <button wire:click="createEvent"
                    class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold py-2 px-6 rounded-lg shadow-md
                           flex items-center justify-center space-x-2 transition duration-300 ease-in-out
                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-900">
                <i class="fas fa-plus-circle text-lg"></i>
                <span>Créer un Événement</span>
            </button>
        @endif
    </div>

    {{-- Tableau des événements --}}
    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden transition-colors duration-300 ease-in-out">
        <div class="overflow-x-auto"> {{-- Pour la responsivité du tableau sur petits écrans --}}
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-200 uppercase tracking-wider">
                        Matricule
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-200 uppercase tracking-wider">
                        Titre
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-200 uppercase tracking-wider">
                        Date & Heure
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-200 uppercase tracking-wider">
                        Lieu
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-200 uppercase tracking-wider">
                        Statut
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-200 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($events as $event)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $event->matricule }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $event->title }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ \Carbon\Carbon::parse($event->date)->format('d/m/Y') }} à {{ \Carbon\Carbon::parse($event->time)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $event->location }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if ($event->status === 'published') bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100
                                    @elseif ($event->status === 'not_published') bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200
                                    @elseif ($event->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100
                                    @elseif ($event->status === 'pending_cancellation' || $event->status === 'pending_deletion') bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100
                                    @endif
                                ">
                                    {{ ucfirst(str_replace('_', ' ', $event->status)) }}
                                </span>
                            @if ($event->deleted_at)
                                <span class="ml-2 px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white dark:bg-red-900">
                                        Supprimé
                                    </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center space-x-2 justify-center">
                                @if ($user && $user->can('see-events'))
                                    <button wire:click="viewEventDetails('{{ $event->id }}')"
                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200 p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150" title="Voir les détails">
                                        <i class="fas fa-eye text-lg"></i>
                                    </button>
                                @endif

                              {{-- Bouton Annuler --}}
                                @if ($user && $user->can('update-events') && $event->status === 'published' && !$event->deleted_at)
                                    <button wire:click="confirmCancelEvent('{{ $event->id }}')"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150" title="Annuler l'événement">
                                        <i class="fas fa-times-circle text-lg"></i>
                                    </button>
                                @endif

                                {{-- Bouton Restaurer (si l'événement est annulé ou soft-deleted) --}}
                                @if ($user && $user->can('restore-events') && ($event->status === 'cancelled' || $event->deleted_at))
                                    <button wire:click="restoreEvent('{{ $event->id }}')"
                                            class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150" title="Restaurer l'événement">
                                        <i class="fas fa-undo text-lg"></i>
                                    </button>
                                @endif

                                {{-- Bouton Supprimer (soft delete) --}}
                                @if ($user && $user->can('delete-events') && !$event->deleted_at)
                                    <button wire:click="confirmDeleteEvent('{{ $event->id }}')"
                                            class="text-red-500 hover:text-red-700 dark:text-red-300 dark:hover:text-red-100 p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150" title="Supprimer l'événement">
                                        <i class="fas fa-trash-alt text-lg"></i>
                                    </button>
                                @endif

                                {{-- Bouton pour initier les remboursements --}}
                                @if ($user && $user->can('refund-tickets') && ($event->status === 'pending_cancellation' || $event->status === 'pending_deletion'))
                                    <button wire:click="initiateRefunds('{{ $event->id }}')"
                                            class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-200 p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150" title="Initier les remboursements">
                                        <i class="fas fa-money-bill-wave text-lg"></i>
                                    </button>
                                @endif

                                {{-- Bouton Voir Tickets (si permission 'see-tickets') --}}
                                @if ($user && $user->can('see-tickets'))
                                    <a href="{{ route('employee.manageTickets', ['event' => $event]) }}"
                                       class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-200 p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150" title="Voir les tickets">
                                        <i class="fas fa-ticket-alt text-lg"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-6 whitespace-nowrap text-base text-gray-500 dark:text-gray-300 text-center">
                            Aucun événement trouvé. Ajustez vos filtres ou créez un nouvel événement.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            {{ $events->links('pagination::tailwind') }}
        </div>
    </div>
</div>
