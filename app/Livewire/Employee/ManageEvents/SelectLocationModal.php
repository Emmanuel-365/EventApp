<?php

namespace App\Livewire\Employee\ManageEvents;

use App\Services\GoogleMapsService; // Assurez-vous d'importer votre service
use Livewire\Component;
use Illuminate\Support\Facades\Log; // Pour le débogage si nécessaire

class SelectLocationModal extends Component
{
    public bool $show = false; // Propriété pour contrôler l'affichage de la modale
    public ?float $latitude = null; // Latitude sélectionnée
    public ?float $longitude = null; // Longitude sélectionnée
    public ?string $address = null; // Adresse formatée du lieu sélectionné
    public string $search = ''; // Champ pour la recherche d'adresse (utilisé par l'autocomplétion JS)

    // Injection du service GoogleMapsService
    protected GoogleMapsService $googleMapsService;

    public function boot(GoogleMapsService $googleMapsService): void
    {
        $this->googleMapsService = $googleMapsService;
    }

    // Listeners Livewire pour ouvrir la modale
    protected $listeners = [
        'openSelectLocationModal' => 'openModal',
    ];

    /**
     * Ouvre la modale de sélection de localisation.
     */
    public function openModal(): void
    {
        $this->show = true;
        $this->resetSearch(); // Réinitialise le champ de recherche à l'ouverture
        // Dispatch un événement pour que le JS sache que la modale est ouverte
        // et puisse réinitialiser/redimensionner la carte
        $this->dispatch('mapModalOpened');
    }

    /**
     * Ferme la modale de sélection de localisation.
     */
    public function closeModal(): void
    {
        $this->show = false;
        $this->resetSearch();
    }

    /**
     * Reçoit les coordonnées et l'adresse depuis le JavaScript de la carte.
     * Cette méthode est appelée via @this.call('selectLocation', ...) dans le JS.
     *
     * @param float $lat Latitude sélectionnée.
     * @param float $lng Longitude sélectionnée.
     * @param string $address Adresse formatée.
     */
    public function selectLocation(float $lat, float $lng, string $address): void
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->address = $address;

        // Émet un événement vers le composant parent (CreateEvent) pour lui transmettre la localisation
        $this->dispatch('locationSelected', $this->latitude, $this->longitude, $this->address);

        $this->closeModal(); // Ferme la modale après sélection
    }

    /**
     * Réinitialise le champ de recherche.
     */
    public function resetSearch(): void
    {
        $this->search = '';
    }

    // Le `updatedSearch` peut être utilisé si vous vouliez faire une recherche côté serveur,
    // mais pour l'autocomplétion Google Places, tout se passe côté client.
    public function updatedSearch(): void
    {
        // Pas de logique serveur ici, la recherche d'autocomplétion est gérée par le JS de Google Maps.
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        // Passe la clé API à la vue pour l'initialisation de la carte JavaScript
        $apiKey = $this->googleMapsService->getApiKey();
        return view('livewire.employee.manage-events.select-location-modal', ['apiKey' => $apiKey]);
    }
}
