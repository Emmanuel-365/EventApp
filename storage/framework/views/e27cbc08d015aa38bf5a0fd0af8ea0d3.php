<div>
    

    <div class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-300 ease-in-out">
        <div class="bg-white dark:bg-gray-800 p-6 md:p-8 rounded-lg shadow-xl max-w-sm md:max-w-md w-full transition-colors duration-300 ease-in-out">

            <h2 class="text-2xl md:text-3xl font-bold mb-6 text-center text-gray-800 dark:text-white">Inscription Employee</h2>

            
            <!--[if BLOCK]><![endif]--><?php if($currentStep === 1): ?>
                <form wire:submit.prevent="verifyMatriculeAndSendOtp" x-transition>
                    <div class="mb-4">
                        <label for="matricule" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Matricule:</label>
                        <input type="text" id="matricule" wire:model.live="matricule"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                               required>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['matricule'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs italic mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="mb-6">
                        <label for="email" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Adresse email:</label>
                        <input type="email" id="email" wire:model.live="email"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                               required autocomplete="email">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs italic mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="flex justify-center">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-600 transition-colors duration-300 ease-in-out">
                            Vérifier et Envoyer OTP
                        </button>
                    </div>
                </form>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            
            <!--[if BLOCK]><![endif]--><?php if($currentStep === 2): ?>
                <form wire:submit.prevent="verifyOtp" x-transition>
                    <div class="mb-4">
                        <p class="text-gray-700 dark:text-gray-300 text-center mb-4">Un code OTP a été envoyé à <span class="font-semibold"><?php echo e($email); ?></span>. Veuillez le saisir ci-dessous.</p>
                        <label for="otp" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Code OTP:</label>
                        <input type="text" id="otp" wire:model.live="otp"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                               required maxlength="6">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['otp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs italic mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="flex flex-col items-center justify-center">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-600 transition-colors duration-300 ease-in-out mb-4">
                            Vérifier OTP
                        </button>
                        <a wire:click.prevent="resendOtp" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer">
                            Renvoyer le code OTP
                        </a>
                        <a wire:click.prevent="resetForm" class="inline-block align-baseline font-bold text-sm text-red-500 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 cursor-pointer mt-2">
                            Annuler et recommencer
                        </a>
                    </div>
                </form>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            
            <!--[if BLOCK]><![endif]--><?php if($currentStep === 3): ?>
                <form wire:submit.prevent="registerEmployee" x-transition>
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nouveau mot de passe:</label>
                        <input type="password" id="password" wire:model.live="password"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                               required autocomplete="new-password">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs italic mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Confirmer le mot de passe:</label>
                        <input type="password" id="password_confirmation" wire:model.live="password_confirmation"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                               required autocomplete="new-password">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs italic mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="flex flex-col items-center justify-center">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-600 transition-colors duration-300 ease-in-out mb-4">
                            Définir le mot de passe
                        </button>
                        <a wire:click.prevent="resetForm" class="inline-block align-baseline font-bold text-sm text-red-500 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 cursor-pointer mt-2">
                            Annuler et recommencer
                        </a>
                    </div>
                </form>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            
            <div class="text-center mt-6">
                <a href="<?php echo e(route('employee.auth.disconnected.loginView')); ?>" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer">
                    Déjà inscrit ? Connectez-vous
                </a>
            </div>
        </div>
    </div>

</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EventApp\resources\views/livewire/employee/auth/signup-form.blade.php ENDPATH**/ ?>