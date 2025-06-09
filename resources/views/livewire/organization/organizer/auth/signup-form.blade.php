<div x-data="
    {
        step: @entangle('step').live,
        cameraActive: false,
        currentCameraProperty: null,
        stream: null,
        videoElement: null,

        initCamera() {
            this.videoElement = this.$refs.video;
            if (!this.videoElement) {
                console.error(`Video element not found. Make sure x-ref='video' is set.`);
                return;
            }

            navigator.mediaDevices.getUserMedia({ video: true })
            .then(s => {
                this.stream = s;
                this.videoElement.srcObject = s;
                this.videoElement.play();
                this.cameraActive = true;
            })
            .catch(err => {
                console.error('Erreur d\'accès à la caméra: ', err);
                alert('Impossible d\'accéder à la caméra. Vérifiez vos permissions ou si une autre application l\'utilise.');
                this.cameraActive = false;
                this.stopCamera();
                this.currentCameraProperty = null;
                document.getElementById('camera-modal').classList.add('hidden');
            });
        },
        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
                this.cameraActive = false;
            }
        },
        takePhoto() {
            if (!this.videoElement) return;

            const canvas = document.createElement('canvas');
            canvas.width = this.videoElement.videoWidth;
            canvas.height = this.videoElement.videoHeight;
            const context = canvas.getContext('2d');
            context.drawImage(this.videoElement, 0, 0, canvas.width, canvas.height);

            const imageData = canvas.toDataURL('image/jpeg', 0.9);

            if (this.currentCameraProperty) {
                $wire.processCameraImage(this.currentCameraProperty, imageData);
            }

            this.stopCamera();
            this.currentCameraProperty = null;
        },
        openCameraModal(propertyName) {
            this.currentCameraProperty = propertyName;
            this.initCamera();
            this.$dispatch('open-camera-modal');
        }
    }"
     @open-camera-modal.window="document.getElementById('camera-modal').classList.remove('hidden')">

    {{-- Modal pour la caméra --}}
    <div class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center hidden" id="camera-modal"
         x-show="cameraActive" x-transition.opacity
         @click.away="stopCamera(); currentCameraProperty = null; document.getElementById('camera-modal').classList.add('hidden')">
        <div class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-xl w-full max-w-2xl mx-auto" @click.stop>
            <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Prendre une photo</h3>
            <video x-ref="video" class="w-full h-auto bg-gray-200 dark:bg-gray-700 rounded-md mb-4" autoplay playsinline></video>
            <div class="flex justify-between">
                <button type="button" @click="stopCamera(); currentCameraProperty = null; document.getElementById('camera-modal').classList.add('hidden')"
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg">Annuler</button>
                <button type="button" @click="takePhoto()"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Capturer</button>
            </div>
        </div>
    </div>


    <h1 class="text-3xl font-bold mb-8 text-gray-800 dark:text-white">Inscription d'un nouvel Organisateur</h1>

    {{-- Messages de session --}}
    @if (session()->has('success_message'))
        <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('success_message') }}
        </div>
    @endif
    @if (session()->has('error_message'))
        <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('error_message') }}
        </div>
    @endif
    @if (session()->has('otp_sent_success'))
        <div class="bg-blue-100 dark:bg-blue-800 border border-blue-400 dark:border-blue-700 text-blue-700 dark:text-blue-100 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('otp_sent_success') }}
        </div>
    @endif
    @if (session()->has('otp_error'))
        <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('otp_error') }}
        </div>
    @endif
    @if (session()->has('otp_verified_success'))
        <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('otp_verified_success') }}
        </div>
    @endif

    {{-- Indicateurs d'étape --}}
    <div class="mb-8 flex justify-center space-x-4">
        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold" :class="step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600'">1</div>
        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold" :class="step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600'">2</div>
        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold" :class="step >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600'">3</div>
        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold" :class="step >= 4 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600'">4</div>
    </div>

    {{-- Étape 1: Informations Personnelles --}}
    <div x-show="step === 1" x-transition>
        <form wire:submit.prevent="nextStep" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-white">1. Informations Personnelles</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom:</label>
                    <input type="text" id="nom" wire:model.live="nom"
                           class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                    @error('nom') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prénom:</label>
                    <input type="text" id="prenom" wire:model.live="prenom"
                           class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                    @error('prenom') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Téléphone:</label>
                    <input type="text" id="telephone" wire:model.live="telephone"
                           class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                    @error('telephone') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="pays" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pays (optionnel):</label>
                    <input type="text" id="pays" wire:model.live="pays"
                           class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                    @error('pays') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="ville" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ville (optionnel):</label>
                    <input type="text" id="ville" wire:model.live="ville"
                           class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                    @error('ville') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end mt-8">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Continuer
                </button>
            </div>
        </form>
    </div>

    {{-- Étape 2: Photos et Pièces d'Identité --}}
    <div x-show="step === 2" x-transition>
        <form wire:submit.prevent="nextStep" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-white">2. Photos et Pièces d'Identité</h2>

            <div class="mb-6">
                <label for="photoProfil" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo de Profil (optionnel):</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md"
                     x-data="{ isDragging: false }"
                     @dragover.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop.prevent="isDragging = false; $wire.upload('photoProfil', event.dataTransfer.files[0])">
                    <div class="space-y-1 text-center">
                        <div class="mx-auto h-12 w-12 text-gray-400 flex items-center justify-center">
                            <svg class="h-full w-full" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4m32-4V12a4 4 0 00-4-4H12a4 4 0 00-4 4v20m32-4v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="flex flex-col sm:flex-row items-center justify-center text-sm text-gray-600 dark:text-gray-400">
                            <label for="photoProfil" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 px-1">
                                <span>Déposer ou cliquer pour uploader</span>
                                <input id="photoProfil" wire:model="photoProfil" type="file" class="sr-only" accept="image/*">
                            </label>
                            <p class="pl-1 hidden sm:block">une image</p>
                            <span class="px-2 hidden sm:block">ou</span>
                            <button type="button" @click="openCameraModal('photoProfilCamera')" class="text-blue-600 hover:text-blue-500 font-medium">Prendre une photo</button>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF jusqu'à 1MB</p>
                    </div>
                </div>
                @error('photoProfil') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                @error('photoProfilCamera') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                @if ($photoProfilPreview)
                    <div class="mt-4 flex justify-center items-center">
                        <img src="{{ $photoProfilPreview }}" class="h-32 w-32 object-cover rounded-full shadow-md" alt="Preview Photo de Profil">
                        <button type="button" wire:click="$set('photoProfil', null); $set('photoProfilCamera', null); $set('photoProfilPreview', null);" class="ml-2 text-red-600 hover:text-red-800 self-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 01-2 0v6a1 1 0 112 0V8z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            <div class="mb-6">
                <label for="pieceIdentiteRecto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pièce d'Identité - Recto (optionnel):</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md"
                     x-data="{ isDragging: false }"
                     @dragover.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop.prevent="isDragging = false; $wire.upload('pieceIdentiteRecto', event.dataTransfer.files[0])">
                    <div class="space-y-1 text-center">
                        <div class="mx-auto h-12 w-12 text-gray-400 flex items-center justify-center">
                            <svg class="h-full w-full" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4m32-4V12a4 4 0 00-4-4H12a4 4 0 00-4 4v20m32-4v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="flex flex-col sm:flex-row items-center justify-center text-sm text-gray-600 dark:text-gray-400">
                            <label for="pieceIdentiteRecto" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 px-1">
                                <span>Déposer ou cliquer pour uploader</span>
                                <input id="pieceIdentiteRecto" wire:model="pieceIdentiteRecto" type="file" class="sr-only" accept="image/*">
                            </label>
                            <p class="pl-1 hidden sm:block">une image</p>
                            <span class="px-2 hidden sm:block">ou</span>
                            <button type="button" @click="openCameraModal('pieceIdentiteRectoCamera')" class="text-blue-600 hover:text-blue-500 font-medium">Prendre une photo</button>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF jusqu'à 2MB</p>
                    </div>
                </div>
                @error('pieceIdentiteRecto') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                @error('pieceIdentiteRectoCamera') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                @if ($pieceIdentiteRectoPreview)
                    <div class="mt-4 flex justify-center items-center">
                        <img src="{{ $pieceIdentiteRectoPreview }}" class="h-48 w-full object-contain shadow-md" alt="Preview Pièce d'Identité Recto">
                        <button type="button" wire:click="$set('pieceIdentiteRecto', null); $set('pieceIdentiteRectoCamera', null); $set('pieceIdentiteRectoPreview', null);" class="ml-2 text-red-600 hover:text-red-800 self-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 01-2 0v6a1 1 0 112 0V8z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            <div class="mb-6">
                <label for="pieceIdentiteVerso" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pièce d'Identité - Verso (optionnel):</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md"
                     x-data="{ isDragging: false }"
                     @dragover.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop.prevent="isDragging = false; $wire.upload('pieceIdentiteVerso', event.dataTransfer.files[0])">
                    <div class="space-y-1 text-center">
                        <div class="mx-auto h-12 w-12 text-gray-400 flex items-center justify-center">
                            <svg class="h-full w-full" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4m32-4V12a4 4 0 00-4-4H12a4 4 0 00-4 4v20m32-4v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="flex flex-col sm:flex-row items-center justify-center text-sm text-gray-600 dark:text-gray-400">
                            <label for="pieceIdentiteVerso" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 px-1">
                                <span>Déposer ou cliquer pour uploader</span>
                                <input id="pieceIdentiteVerso" wire:model="pieceIdentiteVerso" type="file" class="sr-only" accept="image/*">
                            </label>
                            <p class="pl-1 hidden sm:block">une image</p>
                            <span class="px-2 hidden sm:block">ou</span>
                            <button type="button" @click="openCameraModal('pieceIdentiteVersoCamera')" class="text-blue-600 hover:text-blue-500 font-medium">Prendre une photo</button>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF jusqu'à 2MB</p>
                    </div>
                </div>
                @error('pieceIdentiteVerso') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                @error('pieceIdentiteVersoCamera') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                @if ($pieceIdentiteVersoPreview)
                    <div class="mt-4 flex justify-center items-center">
                        <img src="{{ $pieceIdentiteVersoPreview }}" class="h-48 w-full object-contain shadow-md" alt="Preview Pièce d'Identité Verso">
                        <button type="button" wire:click="$set('pieceIdentiteVerso', null); $set('pieceIdentiteVersoCamera', null); $set('pieceIdentiteVersoPreview', null);" class="ml-2 text-red-600 hover:text-red-800 self-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 01-2 0v6a1 1 0 112 0V8z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            <div class="flex justify-between mt-8">
                <button type="button" wire:click="previousStep"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-colors duration-200">
                    Précédent
                </button>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Continuer
                </button>
            </div>
        </form>
    </div>

    {{-- Étape 3: Saisie Email et Envoi OTP --}}
    <div x-show="step === 3" x-transition>
        <form wire:submit.prevent="nextStep" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-white">3. Vérification de l'Email</h2>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email:</label>
                <input type="email" id="email" wire:model.live="email"
                       class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-between mt-8">
                <button type="button" wire:click="previousStep"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-colors duration-200">
                    Précédent
                </button>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Envoyer le code OTP et Continuer
                </button>
            </div>
        </form>
    </div>

    {{-- Étape 4: Validation OTP et Définition du mot de passe --}}
    <div x-show="step === 4" x-transition>
        <form wire:submit.prevent="submitForm" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-white">4. Confirmer et Définir le mot de passe</h2>

            <div class="mb-4">
                <label for="otp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code OTP:</label>
                <input type="text" id="otp" wire:model.live="otp"
                       class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Entrez le code à 6 chiffres">
                @error('otp') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="mt-4 text-center mb-6">
                <button type="button" wire:click="sendOtp" class="text-blue-600 hover:text-blue-700 text-sm">Renvoyer le code OTP</button>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mot de passe:</label>
                <input type="password" id="password" wire:model.live="password"
                       class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                @error('password') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
            <div class="mb-6">
                <label for="passwordConfirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmer le mot de passe:</label>
                <input type="password" id="passwordConfirmation" wire:model.live="passwordConfirmation"
                       class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                @error('passwordConfirmation') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-between items-center mt-8">
                <button type="button" wire:click="previousStep"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-colors duration-200">
                    Précédent
                </button>
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                    S'inscrire
                </button>
            </div>
        </form>
    </div>
</div>
