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

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-4xl relative flex flex-col max-h-[90vh]">
        <button type="button" @click="$wire.close()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-150">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        @if ($organization)
            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6 text-center flex-shrink-0">
                Détails de l'Organisation: {{ $organization->nom }}
            </h3>

            <div class="overflow-y-auto pr-2 flex-grow">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nom:</p>
                        <p class="text-base text-gray-900 dark:text-gray-100">{{ $organization->nom }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">NIU:</p>
                        <p class="text-base text-gray-900 dark:text-gray-100">{{ $organization->NIU }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Type:</p>
                        <p class="text-base text-gray-900 dark:text-gray-100">{{ $organization->type }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date de création:</p>
                        <p class="text-base text-gray-900 dark:text-gray-100">{{ $organization->date_creation->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Statut Validation:</p>
                        <p class="text-base">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($organization->validation_status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($organization->validation_status === 'accepted') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($organization->validation_status) }}
                            </span>
                            @if($organization->validation_status === 'rejected' && $organization->rejected_reason)
                                <span class="block text-xs text-red-500 mt-1">Motif: {{ $organization->rejected_reason }}</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Statut Activation:</p>
                        <p class="text-base">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($organization->activation_status === 'enabled') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($organization->activation_status) }}
                            </span>
                            @if($organization->activation_status === 'disabled' && $organization->disabled_reason)
                                <span class="block text-xs text-red-500 mt-1">Motif: {{ $organization->disabled_reason }} (Désactivé par: {{ $organization->disabled_by_type }})</span>
                            @endif
                        </p>
                    </div>
                    @if ($organization->organizer)
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Organisateur:</p>
                                <p class="text-base text-gray-900 dark:text-gray-100" >
                                    <button wire:click="showOrganizer('{{$organization->organizer->id}}')"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-semibold underline transition-colors duration-150">
                                        {{ $organization->organizer->nom ?? 'N/A' }} {{ $organization->organizer->prenom ?? '' }}
                                    </button>
                                </p>
                            </div>

                            @if($showDetailModal)
                                @livewire('admin.manage-organizers.organizer-details-modal' ,  ['organizerId' => $selectedOrganizerId], key($selectedOrganizerId))
                            @endif

                    @endif
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 my-6 pt-6">
                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Historique des Statuts</h4>

                    {{-- Champ de recherche pour l'historique --}}
                    <div class="mb-4">
                        <label for="historySearch" class="sr-only">Rechercher dans l'historique</label>
                        <div class="relative">
                            <input type="text" id="historySearch" wire:model.live.debounce.300ms="historySearch" placeholder="Rechercher dans l'historique..."
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

                    @if ($statusHistory->isNotEmpty())
                        <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortHistoryBy('status_type')">
                                        Type
                                        @if ($historySortField === 'status_type')
                                            <span>{!! $historySortDirection === 'asc' ? '&uarr;' : '&darr;' !!}</span>
                                        @endif
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortHistoryBy('old_status')">
                                        Ancien Statut
                                        @if ($historySortField === 'old_status')
                                            <span>{!! $historySortDirection === 'asc' ? '&uarr;' : '&darr;' !!}</span>
                                        @endif
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortHistoryBy('new_status')">
                                        Nouveau Statut
                                        @if ($historySortField === 'new_status')
                                            <span>{!! $historySortDirection === 'asc' ? '&uarr;' : '&darr;' !!}</span>
                                        @endif
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortHistoryBy('reason')">
                                        Motif
                                        @if ($historySortField === 'reason')
                                            <span>{!! $historySortDirection === 'asc' ? '&uarr;' : '&darr;' !!}</span>
                                        @endif
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider">
                                        Par
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortHistoryBy('created_at')">
                                        Date
                                        @if ($historySortField === 'created_at')
                                            <span>{!! $historySortDirection === 'asc' ? '&uarr;' : '&darr;' !!}</span>
                                        @endif
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($statusHistory as $history)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ ucfirst($history->status_type) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ ucfirst($history->old_status) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if($history->new_status === 'accepted' || $history->new_status === 'enabled') bg-green-100 text-green-800
                                                    @elseif($history->new_status === 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ ucfirst($history->new_status) }}
                                                </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                            {{ $history->reason ?: 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            @if($history->changed_by_type === 'admin' )
                                                Admin: {{ \App\Models\Admin::withTrashed()->find($history->changed_by_id)->nom ?? 'Nom inconnu' }}
                                            @elseif($history->changed_by_type === 'organizer' )
                                                Organizer: {{ \App\Models\Organizer::withTrashed()->find($history->changed_by_id)->nom ?? 'Nom inconnu' }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $history->created_at->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-gray-500 dark:text-gray-400 mb-6">
                            @if($historySearch)
                                Aucun historique trouvé pour la recherche "{{ $historySearch }}".
                            @else
                                Aucun historique de statut disponible pour cette organisation.
                            @endif
                        </p>
                    @endif
                </div>

                <div class="flex flex-wrap justify-center gap-4 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
                    @if (Auth::guard('admin')->user()->can('validate-organization') && $organization->validation_status === 'pending')
                        <button wire:click="acceptOrganization" class="px-5 py-2 rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-150">
                            Valider l'Organisation
                        </button>
                        <button wire:click="openRejectReasonModal" class="px-5 py-2 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150">
                            Rejeter l'Organisation
                        </button>
                    @endif

                    @if (Auth::guard('admin')->user()->can('disable-organization') && $organization->activation_status === 'enabled' && $organization->validation_status === 'accepted')
                        <button wire:click="openDisableReasonModal" class="px-5 py-2 rounded-md text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-150">
                            Désactiver l'Organisation
                        </button>
                    @endif

                    @if (Auth::guard('admin')->user()->can('enable-organization') && $organization->activation_status === 'disabled' && $organization->validation_status === 'accepted')
                        <button wire:click="openEnableReasonModal" class="px-5 py-2 rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                            Activer l'Organisation
                        </button>
                    @endif
                </div>

                {{-- Modals internes pour les motifs (inchangés) --}}
                <div x-data="{ show: @entangle('showRejectReasonModal').live }" x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50" style="display: none;">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Motif du Rejet</h3>
                        <div class="mb-4">
                            <label for="reject-reason-modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motif:</label>
                            <textarea id="reject-reason-modal" wire:model.lazy="reason" rows="4"
                                      class="form-textarea block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"></textarea>
                            @error('reason') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="show = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                Annuler
                            </button>
                            <button wire:click="rejectOrganization" class="px-4 py-2 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150">
                                Confirmer le Rejet
                            </button>
                        </div>
                    </div>
                </div>

                <div x-data="{ show: @entangle('showDisableReasonModal').live }" x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50" style="display: none;">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Motif de la Désactivation</h3>
                        <div class="mb-4">
                            <label for="disable-reason-modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motif:</label>
                            <textarea id="disable-reason-modal" wire:model.lazy="reason" rows="4"
                                      class="form-textarea block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"></textarea>
                            @error('reason') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="show = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                Annuler
                            </button>
                            <button wire:click="disableOrganization" class="px-4 py-2 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150">
                                Confirmer la Désactivation
                            </button>
                        </div>
                    </div>
                </div>

                <div x-data="{ show: @entangle('showEnableReasonModal').live }" x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50" style="display: none;">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Activer l'Organisation</h3>
                        <div class="mb-4">
                            <label for="enable-reason-modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motif de l'activation (optionnel):</label>
                            <textarea id="enable-reason-modal" wire:model.lazy="reason" rows="4"
                                      class="form-textarea block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"></textarea>
                            @error('reason') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="show = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                Annuler
                            </button>
                            <button wire:click="enableOrganization" class="px-4 py-2 rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                Confirmer l'Activation
                            </button>
                        </div>
                    </div>
                </div>

                @else
                    <p class="text-center text-gray-500 dark:text-gray-400">Chargement des détails de l'organisation...</p>
                @endif
            </div>
    </div>
</div>
