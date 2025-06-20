<div x-data="{ show: <?php if ((object) ('show') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('show'->value()); ?>')<?php echo e('show'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('show'); ?>')<?php endif; ?>.live, map: null, marker: null, autocomplete: null, geocoder: null }"
     x-init="
        // Fonction pour initialiser la carte. Appelé par le watcher 'show' et une fois au démarrage.
        const initializeMap = () => {
            if (document.getElementById('map') && !map) { // N'initialiser la carte qu'une seule fois
                map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: 4.05, lng: 9.77 }, // Yaoundé, Cameroun comme centre par défaut
                    zoom: 12,
                    mapTypeId: 'roadmap',
                    fullscreenControl: false, // Contrôle plein écran
                    mapTypeControl: false,    // Contrôle du type de carte
                    streetViewControl: false, // Contrôle Street View
                });

                marker = new google.maps.Marker({
                    map: map,
                    draggable: true, // Le marqueur peut être déplacé
                    position: map.getCenter() // Position initiale au centre de la carte
                });

                geocoder = new google.maps.Geocoder();

                // Écoute les changements de position du marqueur
                marker.addListener('dragend', () => {
                    const lat = marker.getPosition().lat();
                    const lng = marker.getPosition().lng();
                    // Appelle la fonction de géocodage inversé
                    reverseGeocode({ lat: lat, lng: lng });
                });

                // Écoute les clics sur la carte pour placer le marqueur
                map.addListener('click', (e) => {
                    marker.setPosition(e.latLng);
                    reverseGeocode({ lat: e.latLng.lat(), lng: e.latLng.lng() });
                });

                // Autocomplétion de la recherche de lieux
                const searchInput = document.getElementById('search-input');
                autocomplete = new google.maps.places.Autocomplete(searchInput);
                autocomplete.bindTo('bounds', map); // Lie l'autocomplétion à la vue actuelle de la carte

                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();

                    if (!place.geometry || !place.geometry.location) {
                        console.error('Le lieu sélectionné n\'a pas de géométrie.');
                        return;
                    }

                    // Centrer la carte sur le lieu sélectionné
                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17); // Zoom plus précis pour un lieu spécifique
                    }

                    // Placer le marqueur au lieu sélectionné
                    marker.setPosition(place.geometry.location);
                    // Mettre à jour les propriétés Livewire directement
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('latitude', place.geometry.location.lat());
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('longitude', place.geometry.location.lng());
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('address', place.formatted_address);
                });
            }
        };

        // Fonction de géocodage inversé (coordonnées -> adresse)
        const reverseGeocode = (latLng) => {
            if (!geocoder) {
                geocoder = new google.maps.Geocoder(); // Assurez-vous que le géocodeur est initialisé
            }
            geocoder.geocode({ 'location': latLng }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('latitude', latLng.lat);
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('longitude', latLng.lng);
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('address', results[0].formatted_address);
                } else {
                    console.error('Geocoder failed due to: ' + status);
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('address', 'Adresse non trouvée'); // Fallback si l'adresse n'est pas trouvée
                }
            });
        };

        // Initialisation de la carte si elle est déjà visible au chargement de la page (peu probable pour une modale)
        // ou si l'API Google Maps est déjà chargée.
        if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
             initializeMap();
        } else {
            // Si l'API n'est pas encore chargée (cas le plus fréquent pour une modale Livewire)
            // La fonction initMap dans le script global sera appelée par l'API de Google,
            // et vous pouvez y ajouter un appel à initializeMap() si vous le souhaitez,
            // mais le watcher Alpine.js est généralement suffisant pour les modales.
        }

        // Watcher pour réagir à l'ouverture de la modale
        $watch('show', (newShow) => {
            if (newShow) {
                // Attendre un court instant que la modale soit visible dans le DOM
                setTimeout(() => {
                    if (map) {
                        google.maps.event.trigger(map, 'resize'); // Très important pour redimensionner la carte correctement
                        if (window.Livewire.find('<?php echo e($_instance->getId()); ?>').latitude && window.Livewire.find('<?php echo e($_instance->getId()); ?>').longitude) {
                            const currentLatLng = new google.maps.LatLng(window.Livewire.find('<?php echo e($_instance->getId()); ?>').latitude, window.Livewire.find('<?php echo e($_instance->getId()); ?>').longitude);
                            map.setCenter(currentLatLng);
                            marker.setPosition(currentLatLng);
                            reverseGeocode(currentLatLng); // Mettre à jour l'adresse si des coordonnées sont déjà là
                        } else {
                            map.setCenter({ lat: 4.05, lng: 9.77 }); // Centre par défaut si aucune coordonnée n'est définie
                            marker.setPosition({ lat: 4.05, lng: 9.77 });
                        }
                    } else {
                         // Si la carte n'est pas encore initialisée (première ouverture), on l'initialise
                         initializeMap();
                    }
                }, 100); // Un petit délai
            }
        });

        // Listener pour l'événement dispatché par openModal()
        Livewire.on('mapModalOpened', () => {
             // Le watcher 'show' gère déjà le resize et le recentrage.
             // Ce listener est ici si vous avez besoin d'autres actions spécifiques à l'ouverture via dispatch().
        });
     }"
     x-show="show"
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title" role="dialog" aria-modal="true"
>
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-2xl leading-6 font-bold text-gray-900 dark:text-gray-100 mb-6" id="modal-title">
                        Sélectionner l'emplacement de l'événement
                    </h3>
                    
                    <div class="mb-4">
                        <input type="text" id="search-input" placeholder="Rechercher un lieu..."
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                      focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>

                    
                    <div id="map" style="height: 450px; width: 100%; border-radius: 8px; overflow: hidden;"></div>

                    
                    <!--[if BLOCK]><![endif]--><?php if($latitude && $longitude): ?>
                        <div class="mt-4 text-sm text-gray-700 dark:text-gray-300">
                            <strong>Lat:</strong> <?php echo e(number_format($latitude, 5)); ?>,
                            <strong>Lng:</strong> <?php echo e(number_format($longitude, 5)); ?>

                            <br>
                            <strong>Adresse:</strong> <?php echo e($address ?? 'N/A'); ?>

                        </div>
                    <?php else: ?>
                        <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                            Cliquez sur la carte ou déplacez le marqueur pour sélectionner un lieu.
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 rounded-b-lg">
            <button type="button"
                    @click="() => {
                        // Appel de la méthode Livewire pour sauvegarder la sélection
                        window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('selectLocation', latitude, longitude, address);
                    }"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm
                           dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-offset-gray-800 transition duration-150"
                    x-bind:disabled="!latitude || !longitude || !address"> 
                <i class="fas fa-check-circle mr-2"></i>
                Sélectionner ce lieu
            </button>
            <button type="button" wire:click="closeModal"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-6 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm
                           transition duration-150">
                <i class="fas fa-times mr-2"></i>
                Annuler
            </button>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EventApp\resources\views/livewire/employee/manage-events/select-location-modal.blade.php ENDPATH**/ ?>