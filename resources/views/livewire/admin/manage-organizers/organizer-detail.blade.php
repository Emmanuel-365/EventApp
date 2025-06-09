<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}



    <div class="p-6">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-white">Détails de l'Organisateur</h2>

        {{-- Affichage des messages flash DANS le modal --}}
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

        @if ($organizer)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Colonne de gauche: Infos Générales --}}
                <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6 bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-inner">
                    {{-- Nom et Prénom en haut --}}
                    <div class="sm:col-span-2">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $organizer->nom }} {{ $organizer->prenom }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Matricule: <span class="font-mono text-gray-800 dark:text-gray-200">{{ $organizer->matricule }}</span></p>
                    </div>
                    <hr class="sm:col-span-2 border-gray-200 dark:border-gray-600">

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <p class="form-display-text">{{ $organizer->email }}</p>
                    </div>

                    {{-- Téléphone --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Téléphone</label>
                        <p class="form-display-text">{{ $organizer->telephone ?: 'Non spécifié' }}</p>
                    </div>

                    {{-- Pays --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pays</label>
                        <p class="form-display-text">{{ $organizer->pays ?: 'Non spécifié' }}</p>
                    </div>

                    {{-- Ville --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ville</label>
                        <p class="form-display-text">{{ $organizer->ville ?: 'Non spécifiée' }}</p>
                    </div>

                    {{-- Statut du mot de passe --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mot de Passe</label>
                        <p class="form-display-text">{{ $this->passwordStatus }}</p>
                    </div>

                    {{-- Dernier changement de mot de passe --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dernier changement mot de passe</label>
                        <p class="form-display-text">{{ $this->passwordChangedAtFormatted }}</p>
                    </div>

                    {{-- Statut du passcode --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Passcode</label>
                        <p class="form-display-text">{{ $this->passcodeStatus }}</p>
                    </div>

                    {{-- Date de réinitialisation Passcode --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dernière réinitialisation Passcode</label>
                        <p class="form-display-text">{{ $this->passcodeResetDateFormatted }}</p>
                    </div>

                    {{-- Statut de vérification du profil --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Statut de vérification du profil</label>
                        <p class="form-display-text">
                            @php
                                $statusClass = [
                                    'en attente' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                                    'validé' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                    'rejeté' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
                                ];
                            @endphp
                            <span class="px-2 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusClass[$organizer->profile_verification_status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200' }}">
                            {{ $this->profileVerificationStatusFormatted }}
                        </span>
                        </p>
                    </div>
                </div>

                {{-- Colonne de droite: Photo de Profil, Pièces d'identité, QR Code --}}
                <div class="md:col-span-1 space-y-6 bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-inner">
                    {{-- Photo de Profil --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Photo de Profil</label>
                        <div class="flex items-center justify-center">
                            @if ($this->photoProfilUrl)
                                <img src="{{ $this->photoProfilUrl }}" class="h-32 w-32 object-contain rounded-full shadow-md border border-gray-300 dark:border-gray-600" alt="Photo de Profil">
                            @else
                                <div class="h-32 w-32 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center text-gray-500 dark:text-gray-300 text-5xl font-bold">
                                    {{ strtoupper(substr($organizer->prenom, 0, 1)) }}{{ strtoupper(substr($organizer->nom, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Pièces d'Identité (Recto/Verso) avec animation --}}
                    <div class="relative perspective-1000">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pièce d'Identité (cliquez pour basculer)</label>
                        <div class="flip-card w-full h-48 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 shadow-sm">
                            {{-- La classe 'flip-card-inner' gère la rotation basée sur $wire.showPieceIdentiteRecto --}}
                            <div class="flip-card-inner w-full h-full relative"
                                 :style="$wire.showPieceIdentiteRecto ? 'transform: rotateY(0deg)' : 'transform: rotateY(180deg)'">

                                {{-- Recto --}}
                                <div class="flip-card-front w-full h-full absolute bg-gray-100 dark:bg-gray-700 flex flex-col items-center justify-center backface-hidden">
                                    @if ($this->pieceIdentiteRectoUrl)
                                        <img src="{{ $this->pieceIdentiteRectoUrl }}" alt="Pièce d'Identité Recto"
                                             class="object-contain max-h-full max-w-full cursor-pointer p-2"
                                             @click="$wire.togglePieceIdentite()">
                                        <span class="absolute bottom-2 text-xs text-gray-600 dark:text-gray-300">Recto</span>
                                    @else
                                        <p class="text-gray-500 dark:text-gray-400">Aucun Recto disponible</p>
                                    @endif
                                </div>

                                {{-- Verso --}}
                                <div class="flip-card-back w-full h-full absolute bg-gray-100 dark:bg-gray-700 flex flex-col items-center justify-center backface-hidden">
                                    @if ($this->pieceIdentiteVersoUrl)
                                        <img src="{{ $this->pieceIdentiteVersoUrl }}" alt="Pièce d'Identité Verso"
                                             class="object-contain max-h-full max-w-full cursor-pointer p-2"
                                             @click="$wire.togglePieceIdentite()">
                                        <span class="absolute bottom-2 text-xs text-gray-600 dark:text-gray-300">Verso</span>
                                    @else
                                        <p class="text-gray-500 dark:text-gray-400">Aucun Verso disponible</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- QR Code --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code QR du Matricule</label>
                        <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-inner flex items-center justify-center border border-gray-200 dark:border-gray-600">
                            @if ($organizer->matricule)
                                {!! QrCode::size(150)->generate($organizer->matricule) !!}
                            @else
                                <p class="text-gray-500 dark:text-gray-400">Matricule non disponible pour QR Code.</p>
                            @endif
                        </div>
                        <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-2">Matricule: {{ $organizer->matricule }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Actions Administratives</h3>

                {{-- Actions de validation/rejet (si statut 'en attente') --}}
                @if ($organizer->profile_verification_status === 'en attente')
                    <div class="flex flex-col sm:flex-row gap-4 mb-6 p-4 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg items-center">
                        <p class="text-yellow-800 dark:text-yellow-100 text-sm font-medium flex items-center mb-2 sm:mb-0 mr-auto">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.354 18c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Le profil est en attente de vérification.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                            @if($canValidateProfile)
                                <button wire:click="validateOrganizerProfile"
                                        class="w-full sm:w-auto px-6 py-3 rounded-md bg-green-600 text-white font-semibold hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 shadow-md">
                                    Valider le Profil
                                </button>
                            @endif
                            @if($canRejectProfile)
                                <button wire:click="rejectOrganizerProfile"
                                        class="w-full sm:w-auto px-6 py-3 rounded-md bg-red-600 text-white font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200 shadow-md">
                                    Rejeter le Profil
                                </button>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Actions de ban/unban (si statut n'est pas 'en attente') --}}
                    <div class="flex flex-col sm:flex-row gap-4 items-center sm:items-end mb-6 p-4 bg-blue-50 dark:bg-gray-700 border border-blue-200 dark:border-gray-600 rounded-lg">
                        @if ($isBanned)
                            <p class="text-red-800 dark:text-red-100 text-sm font-medium flex items-center mb-2 sm:mb-0 mr-auto">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 0012 3.636v14.728a9 9 0 006.364 0z"></path></svg>
                                Cet organisateur est actuellement banni.
                            </p>
                            @if($canUnbanOrganizer)
                                <button wire:click="unbanOrganizer"
                                        class="w-full sm:w-auto px-6 py-3 rounded-md bg-blue-600 text-white font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 shadow-md">
                                    Débannir l'Organisateur
                                </button>
                            @endif
                        @else
                            <p class="text-green-800 dark:text-green-100 text-sm font-medium flex items-center mb-2 sm:mb-0 mr-auto">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 4.04A11.955 11.955 0 002.944 12c.044 1.488.35 2.923.905 4.264M21 12a9 9 0 01-9 9m-9-9a9 9 0 00-9-9m18 9l-3 3m0 0l-3-3m3 3L18 9"></path></svg>
                                L'organisateur n'est pas banni.
                            </p>
                            @if($canBanOrganizer)
                                <div class="flex-grow w-full md:w-auto flex flex-col md:flex-row gap-2"> {{-- Conteneur flexible pour input et bouton --}}
                                    <label for="banMotif" class="sr-only">Motif de Bannissement</label>
                                    <input type="text" id="banMotif" wire:model.live="banMotif"
                                           placeholder="Motif de bannissement (obligatoire)"
                                           class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500
                                              bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                                              shadow-sm transition-colors duration-300 ease-in-out">
                                    <button wire:click="banOrganizer"
                                            class="w-full sm:w-auto px-6 py-3 rounded-md bg-orange-600 text-white font-semibold hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200 shadow-md">
                                        Bannir l'Organisateur
                                    </button>
                                </div>
                            @endif
                        @endif
                    </div>
                @endif
            </div>

            ---

            {{-- Historique des bannissements --}}
            <div class="mt-8 p-6 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-inner">
                <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Historique des Bannissements</h3>
                @if ($banHistory->isEmpty())
                    <p class="text-gray-600 dark:text-gray-400">Aucun historique de bannissement pour cet organisateur.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-100 dark:bg-gray-600">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Motif</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Effectué par</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($banHistory as $ban)

                                {{-- Si ce ban a été débanni, afficher la ligne de DÉBANNISSEMENT juste au dessus --}}
                                @if ($ban->trashed())
                                    <tr class="bg-gray-50 dark:bg-gray-700 border-t border-dashed border-gray-200 dark:border-gray-600">
                                        <td class="px-6 py-2 text-left text-xs text-gray-600 dark:text-gray-400">
                                            {{ $ban->deleted_at ? $ban->deleted_at->format('d/m/Y H:i') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-2 text-left text-xs text-gray-600 dark:text-gray-400">
                                            {{-- Couleur bleue pour le débannissement --}}
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                            Débannissement
                                        </span>
                                        </td>
                                        <td class="px-6 py-2 text-left text-xs text-gray-600 dark:text-gray-400">
                                            (Action de levée de ban)
                                        </td>
                                        <td class="px-6 py-2 text-left text-xs text-gray-600 dark:text-gray-400">
                                            @php
                                                $unbanner = null;
                                                if ($ban->unbanned_by_id && $ban->unbanned_guard) {
                                                    $unbanner = app(\App\Services\BanService::class)->getUserModel($ban->unbanned_by_id, $ban->unbanned_guard);
                                                }
                                            @endphp
                                            @if ($unbanner)
                                                {{ $unbanner->nom }} {{ $unbanner->prenom }} ({{ $ban->unbanned_guard }})<br>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">Débanni par</span>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                @endif

                                {{-- Ligne du BANNISSEMENT --}}
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $ban->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{-- Toujours rouge pour un bannissement, qu'il soit actif ou passé --}}
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                        Banni
                                        @if (!$ban->trashed())
                                                (Actif)
                                            @endif
                                    </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $ban->motif ?: 'Aucun motif spécifié' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        @php
                                            $banner = null;
                                            if ($ban->banned_by && $ban->banner_guard) {
                                                $banner = app(\App\Services\BanService::class)->getUserModel($ban->banned_by, $ban->banner_guard);
                                            }
                                        @endphp
                                        @if ($banner)
                                            {{ $banner->nom }} {{ $banner->prenom }} ({{ $ban->banner_guard }})<br>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Banni par</span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>


                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @else
            <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                <p>Impossible de charger les détails de l'organisateur ou vous n'avez pas la permission.</p>
            </div>
        @endif
    </div>

    <style>
        .form-display-text {
            @apply px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-sm
            text-gray-800 dark:text-gray-200 font-medium break-words;
        }

        .perspective-1000 {
            perspective: 1000px;
        }
        .flip-card-inner {
            transform-style: preserve-3d;
            transition: transform 0.6s ease-in-out;
        }
        .backface-hidden {
            backface-visibility: hidden;
        }
        .flip-card-front {
            transform: rotateY(0deg);
            z-index: 2;
        }
        .flip-card-back {
            transform: rotateY(180deg);
            z-index: 1;
        }
        .flip-card-front, .flip-card-back {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>

</div>
