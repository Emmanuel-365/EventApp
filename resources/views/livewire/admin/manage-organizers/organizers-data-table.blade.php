<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md transition-colors duration-300 ease-in-out">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Gestion des Organisateurs</h1>

    {{-- Affichage des messages flash --}}
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Succès !</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Erreur !</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    @if (session()->has('message'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Information :</strong>
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif


    @if ($canSeeOrganizers)
        <div class="mb-6 flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 md:space-x-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher par nom, email, matricule..."
                   class="flex-grow w-full md:w-auto px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500
                          bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                          shadow-sm transition-colors duration-300 ease-in-out">

            <select wire:model.live="perPage"
                    class="w-full md:w-auto px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500
                           bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm transition-colors duration-300 ease-in-out">
                <option value="5">5 par page</option>
                <option value="10">10 par page</option>
                <option value="25">25 par page</option>
                <option value="50">50 par page</option>
            </select>
        </div>

        <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th wire:click="sortBy('matricule')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer rounded-tl-lg">
                        Matricule
                        @if ($sortBy === 'matricule')
                            <span class="ml-1 text-xs">
                                    @if ($sortDirection === 'asc') &uarr; @else &darr; @endif
                                </span>
                        @endif
                    </th>
                    <th wire:click="sortBy('nom')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer">
                        Nom
                        @if ($sortBy === 'nom')
                            <span class="ml-1 text-xs">
                                    @if ($sortDirection === 'asc') &uarr; @else &darr; @endif
                                </span>
                        @endif
                    </th>
                    <th wire:click="sortBy('email')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer">
                        Email
                        @if ($sortBy === 'email')
                            <span class="ml-1 text-xs">
                                    @if ($sortDirection === 'asc') &uarr; @else &darr; @endif
                                </span>
                        @endif
                    </th>
                    <th wire:click="sortBy('profile_verification_status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer">
                        Statut Profil
                        @if ($sortBy === 'profile_verification_status')
                            <span class="ml-1 text-xs">
                                    @if ($sortDirection === 'asc') &uarr; @else &darr; @endif
                                </span>
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Statut Ban
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider rounded-tr-lg">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($organizers as $organizer)
                    <tr wire:key="organizer-{{ $organizer->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $organizer->matricule }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            {{ $organizer->nom }} {{ $organizer->prenom }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            {{ $organizer->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                                $statusClass = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                                    'validated' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
                                    'en attente' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                                    'validé' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                    'rejeté' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
                                ];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass[$organizer->profile_verification_status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200' }}">
                                    {{ ucfirst($organizer->profile_verification_status) }}
                                </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                                $isBanned = app(\App\Services\BanService::class)->isUserBanned($organizer);
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $isBanned ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' }}">
                                    {{ $isBanned ? 'Banni' : 'Actif' }}
                                </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            @if ($canSeeOrganizers) {{-- Conditionné par la permission de voir les détails --}}
                            <button wire:click="selectOrganizer({{ $organizer->id }})"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200">
                                Détails
                            </button>
                            @endif
                            <a href="{{route('admin.manageOrganizerOrganizationsView',['organizer' => $organizer->id ])}}"
                            class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 transition-colors duration-200"
                               title="Voir les organisations de {{ $organizer->nom }}">
                                Organisations
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                            Aucun organisateur trouvé.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $organizers->links() }}
        </div>
    @else
        <div class="text-center text-gray-500 dark:text-gray-400 py-8">
            <p>Vous n'avez pas la permission de voir la liste des organisateurs.</p>
        </div>
    @endif

    @if ($showDetailModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50 overflow-y-auto"
             x-data="{ open: @entangle('showDetailModal') }" x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             @click.self="$wire.closeDetailModal()">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full mx-auto relative transform transition-all overflow-y-auto" style="max-width: 80%; max-height: 90vh;">
                <button @click="$wire.closeDetailModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 z-10">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- La clé est essentielle pour forcer Livewire à re-rendre le composant enfant quand l'ID change --}}
                @livewire('admin.manage-organizers.organizer-detail', ['organizerId' => $selectedOrganizerId], key($selectedOrganizerId))
            </div>
        </div>
    @endif
</div>
