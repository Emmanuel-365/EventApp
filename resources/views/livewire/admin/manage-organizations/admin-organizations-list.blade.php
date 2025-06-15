<div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mb-8 text-center">
        Gestion des Organisations  @if($organizer !== null) de  M/Mme {{$organizer->nom .' '. $organizer->prenom}} @endif
    </h2>

    {{-- Messages de session --}}
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Succès !</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none'">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Fermer</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Erreur !</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none'">
                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Fermer</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6 sm:p-8 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6"> {{-- Augmenté le gap --}}
            {{-- Filtre de recherche --}}
            <div>
                <label for="search" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Recherche rapide:</label>
                <div class="relative">
                    <input type="text" id="search" wire:model.live.debounce.300ms="search" placeholder="Nom, NIU, Type d'organisation..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm
                                  dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200
                                  focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Filtre par statut de validation --}}
            <div>
                <label for="validationStatusFilter" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Statut de Validation:</label>
                <select id="validationStatusFilter" wire:model.live="validationStatusFilter"
                        class="w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm
                               dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200
                               focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                    <option value="">-- Tous les statuts --</option>
                    <option value="pending">En attente</option>
                    <option value="accepted">Acceptée</option>
                    <option value="rejected">Rejetée</option>
                </select>
            </div>

            {{-- Filtre par statut d'activation --}}
            <div>
                <label for="activationStatusFilter" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Statut d'Activation:</label>
                <select id="activationStatusFilter" wire:model.live="activationStatusFilter"
                        class="w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm
                               dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200
                               focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                    <option value="">-- Tous les statuts --</option>
                    <option value="enabled">Activée</option>
                    <option value="disabled">Désactivée</option>
                </select>
            </div>
        </div>

        {{-- Tableau des organisations --}}
        <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200 dark:border-gray-700"> {{-- Ajouté bordure --}}
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700"> {{-- Couleur de fond légèrement différente --}}
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider">
                        Nom
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider">
                        NIU
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider">
                        Type
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider">
                        Validation
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider">
                        Activation
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($organizations as $organization)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $organization->nom }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $organization->NIU }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $organization->type }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($organization->validation_status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($organization->validation_status === 'accepted') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($organization->validation_status) }}
                                </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($organization->activation_status === 'enabled') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($organization->activation_status) }}
                                </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="openDetailsModal('{{ $organization->id }}')"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-700 dark:hover:bg-indigo-800 dark:focus:ring-indigo-600">
                                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Gérer
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">
                            Aucune organisation trouvée.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $organizations->links() }}
        </div>
    </div>

    {{-- Intégration du composant modal de détails --}}
    @if ($showDetailsModal)
        <livewire:admin.manage-organizations.organization-details-modal :organizationId="$selectedOrganizationId" />
    @endif
</div>
