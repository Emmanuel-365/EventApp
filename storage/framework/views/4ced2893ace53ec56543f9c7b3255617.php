<div x-data="
    {
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
                // Dispatch event to show the modal
                this.$dispatch('open-camera-modal');
            })
            .catch(err => {
                console.error('Erreur d\'accès à la caméra: ', err);
                // Using a simple message box instead of alert()
                // You might need a custom modal for a better UX
                $wire.message = 'Impossible d\'accéder à la caméra. Vérifiez vos permissions ou si une autre application l\'utilise.';
                $wire.isSuccess = false; // Indicate failure
                this.cameraActive = false;
                this.stopCamera();
                this.currentCameraProperty = null;
                // Assuming 'camera-modal' has an ID or x-ref to hide it
                const cameraModal = document.getElementById('camera-modal');
                if (cameraModal) cameraModal.classList.add('hidden');
            });
        },
        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
                this.cameraActive = false;
                // Ensure modal is hidden when camera stops
                const cameraModal = document.getElementById('camera-modal');
                if (cameraModal) cameraModal.classList.add('hidden');
            }
        },
        takePhoto() {
            if (!this.videoElement || !this.currentCameraProperty) return;

            const canvas = document.createElement('canvas');
            canvas.width = this.videoElement.videoWidth;
            canvas.height = this.videoElement.videoHeight;
            const context = canvas.getContext('2d');
            context.drawImage(this.videoElement, 0, 0, canvas.width, canvas.height);

            const imageData = canvas.toDataURL('image/jpeg', 0.9);

            // Call Livewire method to process the image data
            $wire.processCameraImage(this.currentCameraProperty, imageData);

            this.stopCamera();
            this.currentCameraProperty = null;
        },
        openCameraModal(propertyName) {
            this.currentCameraProperty = propertyName;
            this.initCamera();
        },
        // Nouvelle fonction Alpine.js pour gérer le glisser-déposer de manière plus robuste
        handleDrop(event, propertyName) {
            this.isDragging = false;
            if (event.dataTransfer.files.length > 0) {
                $wire.upload(propertyName, event.dataTransfer.files[0]);
            } else {
                console.warn('Aucun fichier déposé ou type de fichier invalide.');
                // Optionnel: afficher un message à l'utilisateur via Livewire ou une autre variable Alpine
                // $wire.message = 'Veuillez déposer un fichier image valide.';
                // $wire.isSuccess = false;
            }
        }
    }"
     @open-camera-modal.window="document.getElementById('camera-modal').classList.remove('hidden')">

    
    <div class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center hidden" id="camera-modal"
         x-show="cameraActive" x-transition.opacity
         @click.away="stopCamera(); currentCameraProperty = null;">
        <div class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-xl w-full max-w-2xl mx-auto" @click.stop>
            <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Prendre une photo</h3>
            <video x-ref="video" class="w-full h-auto bg-gray-200 dark:bg-gray-700 rounded-md mb-4" autoplay playsinline></video>
            <div class="flex justify-between">
                <button type="button" @click="stopCamera()"
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg">Annuler</button>
                <button type="button" @click="takePhoto()"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Capturer</button>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8 transition-colors duration-300 ease-in-out">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-white">Mes Informations Personnelles</h2>

        
        <!--[if BLOCK]><![endif]--><?php if($message): ?>
            <div class="<?php echo e($isSuccess ? 'bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100' : 'bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100'); ?> px-4 py-3 rounded relative mb-4" role="alert">
                <?php echo e($message); ?>

            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if($organizer): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Matricule</label>
                        
                        <p class="form-display-text px-4 py-2 text-base bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"><?php echo e($matricule); ?></p>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut de Vérification du Profil</label>
                        
                        <p class="form-display-text px-4 py-2 text-base bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200">
                            <?php echo e($organizer->profile_verification_status); ?>

                        </p>
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if($organizer->profile_verification_status !== 'en attente'): ?>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom:</label>
                            
                            <p class="form-display-text px-4 py-2 text-base bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"><?php echo e($organizer->nom); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prénom:</label>
                            
                            <p class="form-display-text px-4 py-2 text-base bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"><?php echo e($organizer->prenom); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email:</label>
                            
                            <p class="form-display-text px-4 py-2 text-base bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"><?php echo e($organizer->email); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Téléphone:</label>
                            
                            <p class="form-display-text px-4 py-2 text-base bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"><?php echo e($organizer->telephone); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pays:</label>
                            
                            <p class="form-display-text px-4 py-2 text-base bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"><?php echo e($organizer->pays); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ville:</label>
                            
                            <p class="form-display-text px-4 py-2 text-base bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"><?php echo e($organizer->ville); ?></p>
                        </div>
                    <?php else: ?>
                        
                        <form wire:submit.prevent="saveProfile" class="col-span-full grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom:</label>
                                <input type="text" id="nom" wire:model.live="nom"
                                       class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label for="prenom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prénom:</label>
                                <input type="text" id="prenom" wire:model.live="prenom"
                                       class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['prenom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email:</label>
                                
                                <p class="form-display-text px-4 py-2 text-base bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"><?php echo e($organizer->email); ?></p>
                            </div>
                            <div>
                                <label for="telephone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Téléphone:</label>
                                <input type="text" id="telephone" wire:model.live="telephone"
                                       class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['telephone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label for="pays" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pays:</label>
                                <input type="text" id="pays" wire:model.live="pays"
                                       class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['pays'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label for="ville" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ville:</label>
                                <input type="text" id="ville" wire:model.live="ville"
                                       class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ville'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            
                            <div class="col-span-full border-t pt-6 mt-6 border-gray-200 dark:border-gray-700">
                                <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Uploader Documents et Images</h3>

                                
                                <div class="mb-6">
                                    <label for="photoProfil" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo de Profil (optionnel):</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md"
                                         x-data="{ isDragging: false }"
                                         @dragover.prevent="isDragging = true"
                                         @dragleave.prevent="isDragging = false"
                                         @drop.prevent="handleDrop($event, 'photoProfil')"> 
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
                                                <button type="button" @click="openCameraModal('photoProfil')" class="text-blue-600 hover:text-blue-500 font-medium">Prendre une photo</button>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF jusqu'à 1MB</p>
                                        </div>
                                    </div>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['photoProfil'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->

                                    <!--[if BLOCK]><![endif]--><?php if($photoProfilPreview): ?>
                                        <div class="mt-4 flex justify-center items-center">
                                            <img src="<?php echo e($photoProfilPreview); ?>" class="h-32 w-32 object-cover rounded-full shadow-md" alt="Preview Photo de Profil">
                                            <button type="button" wire:click="$set('photoProfil', null); $set('photoProfilPreview', null);" class="ml-2 text-red-600 hover:text-red-800 self-start">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 01-2 0v6a1 1 0 112 0V8z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                
                                <div class="mb-6">
                                    <label for="pieceIdentiteRecto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pièce d'Identité - Recto (optionnel):</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md"
                                         x-data="{ isDragging: false }"
                                         @dragover.prevent="isDragging = true"
                                         @dragleave.prevent="isDragging = false"
                                         @drop.prevent="handleDrop($event, 'pieceIdentiteRecto')"> 
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
                                                <button type="button" @click="openCameraModal('pieceIdentiteRecto')" class="text-blue-600 hover:text-blue-500 font-medium">Prendre une photo</button>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF jusqu'à 2MB</p>
                                        </div>
                                    </div>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['pieceIdentiteRecto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    <!--[if BLOCK]><![endif]--><?php if($pieceIdentiteRectoPreview): ?>
                                        <div class="mt-4 flex justify-center items-center">
                                            <img src="<?php echo e($pieceIdentiteRectoPreview); ?>" class="h-48 w-full object-contain shadow-md rounded-md" alt="Preview Pièce d'Identité Recto">
                                            <button type="button" wire:click="$set('pieceIdentiteRecto', null); $set('pieceIdentiteRectoPreview', null);" class="ml-2 text-red-600 hover:text-red-800 self-start">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 01-2 0v6a1 1 0 112 0V8z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                
                                <div class="mb-6">
                                    <label for="pieceIdentiteVerso" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pièce d'Identité - Verso (optionnel):</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md"
                                         x-data="{ isDragging: false }"
                                         @dragover.prevent="isDragging = true"
                                         @dragleave.prevent="isDragging = false"
                                         @drop.prevent="handleDrop($event, 'pieceIdentiteVerso')"> 
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
                                                <button type="button" @click="openCameraModal('pieceIdentiteVerso')" class="text-blue-600 hover:text-blue-500 font-medium">Prendre une photo</button>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF jusqu'à 2MB</p>
                                        </div>
                                    </div>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['pieceIdentiteVerso'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    <!--[if BLOCK]><![endif]--><?php if($pieceIdentiteVersoPreview): ?>
                                        <div class="mt-4 flex justify-center items-center">
                                            <img src="<?php echo e($pieceIdentiteVersoPreview); ?>" class="h-48 w-full object-contain shadow-md rounded-md" alt="Preview Pièce d'Identité Verso">
                                            <button type="button" wire:click="$set('pieceIdentiteVerso', null); $set('pieceIdentiteVersoPreview', null);" class="ml-2 text-red-600 hover:text-red-800 self-start">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 01-2 0v6a1 1 0 112 0V8z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>

                            
                            <div class="col-span-full flex justify-end mt-8">
                                <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-600 transition-colors duration-200 text-base">
                                    Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]--> 

                    
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mot de Passe</label>
                        
                        <p class="form-display-text px-4 py-2 text-base bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"><?php echo e($organizer->password ? 'Défini' : 'Non défini'); ?></p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dernier changement mot de passe</label>
                        
                        <p class="form-display-text px-4 py-2 text-base bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"><?php echo e($passwordChangedAt); ?></p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Passcode</label>
                        
                        <p class="form-display-text px-4 py-2 text-base bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"><?php echo e($hasPasscode ? 'Défini' : 'Non défini'); ?></p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dernière réinitialisation Passcode</label>
                        
                        <p class="form-display-text px-4 py-2 text-base bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"><?php echo e($passcodeResetDate); ?></p>
                    </div>
                </div>

                
                <div class="md:col-span-1 space-y-6">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo de Profil</label>
                        <div class="flex items-center justify-center">
                            <!--[if BLOCK]><![endif]--><?php if($photoProfilUrl): ?>
                                <img src="<?php echo e($photoProfilUrl); ?>" class="h-32 w-32 object-cover rounded-full shadow-lg" alt="Photo de Profil">
                            <?php else: ?>
                                <div class="h-32 w-32 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center text-gray-500 dark:text-gray-300 text-5xl font-bold shadow-lg">
                                    <?php echo e(strtoupper(substr($prenom, 0, 1))); ?><?php echo e(strtoupper(substr($nom, 0, 1))); ?>

                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    
                    <div x-data="{ isFlipping: false }" class="relative perspective-1000">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pièce d'Identité</label>
                        <div class="flip-card w-full h-48 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 shadow-lg">
                            <div class="flip-card-inner w-full h-full relative"
                                 :style="$wire.showPieceIdentiteRecto ? 'transform: rotateY(0deg)' : 'transform: rotateY(180deg)'"
                                 :class="isFlipping ? 'transition-transform duration-600 ease-in-out' : ''">

                                
                                <div class="flip-card-front w-full h-full absolute bg-gray-100 dark:bg-gray-700 flex items-center justify-center backface-hidden">
                                    <!--[if BLOCK]><![endif]--><?php if($pieceIdentiteRectoUrl): ?>
                                        <img src="<?php echo e($pieceIdentiteRectoUrl); ?>" alt="Pièce d'Identité Recto"
                                             class="object-contain max-h-full max-w-full cursor-pointer p-2"
                                             @click="
                                        isFlipping = true;
                                        $wire.togglePieceIdentite();
                                        setTimeout(() => { isFlipping = false; }, 600);
                                    ">
                                        <span class="absolute bottom-2 text-xs text-gray-600 dark:text-gray-300">Recto (cliquez pour Verso)</span>
                                    <?php else: ?>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">Aucun Recto disponible</p>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                
                                <div class="flip-card-back w-full h-full absolute bg-gray-100 dark:bg-gray-700 flex items-center justify-center backface-hidden">
                                    <!--[if BLOCK]><![endif]--><?php if($pieceIdentiteVersoUrl): ?>
                                        <img src="<?php echo e($pieceIdentiteVersoUrl); ?>" alt="Pièce d'Identité Verso"
                                             class="object-contain max-h-full max-w-full cursor-pointer p-2"
                                             @click="
                                        isFlipping = true;
                                        $wire.togglePieceIdentite();
                                        setTimeout(() => { isFlipping = false; }, 600);
                                    ">
                                        <span class="absolute bottom-2 text-xs text-gray-600 dark:text-gray-300">Verso (cliquez pour Recto)</span>
                                    <?php else: ?>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">Aucun Verso disponible</p>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Code QR de mon Matricule</label>
                        <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-xl flex items-center justify-center">
                            <!--[if BLOCK]><![endif]--><?php if($matricule): ?>
                                <?php echo QrCode::size(150)->generate($matricule); ?>

                            <?php else: ?>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">Matricule non disponible pour QR Code.</p>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <p class="text-center text-base text-gray-500 dark:text-gray-400 mt-2">Matricule: <span class="font-bold"><?php echo e($matricule); ?></span></p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center text-gray-500 dark:text-gray-400 py-8 text-lg">
                Impossible de charger votre profil. Veuillez vous reconnecter.
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    
    <style>
        .form-input {
            @apply shadow-sm border rounded py-2 px-3 leading-tight focus:outline-none focus:ring-2;
        }

        .form-display-text {
            @apply px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-sm
            text-gray-800 dark:text-gray-200 font-medium break-words;
        }

        .perspective-1000 {
            perspective: 1000px;
        }
        .flip-card-inner {
            transform-style: preserve-3d;
        }
        .transition-transform {
            transition-property: transform;
        }
        .duration-600 {
            transition-duration: 600ms;
        }
        .ease-in-out {
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
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
    </style>
</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EventApp\resources\views/livewire/organization/organizer/display-profile.blade.php ENDPATH**/ ?>