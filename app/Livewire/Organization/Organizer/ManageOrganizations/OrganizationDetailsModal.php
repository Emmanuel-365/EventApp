<?php

namespace App\Livewire\Organization\Organizer\ManageOrganizations;

use App\Models\Organization;
use App\Models\Organizer;
use App\Services\OrganizationStatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash; // Ajouté pour Hash::check si nécessaire
use InvalidArgumentException;
use Livewire\Component;
use Stancl\Tenancy\Facades\Tenancy; // Ajouté pour initialiser le tenant
use App\Models\Tenant\Patron; // Ajouté pour le modèle Patron du tenant

class OrganizationDetailsModal extends Component
{
    public bool $show = false;
    public ?string $organizationId = null;
    public ?Organization $organization = null;
    public string $reason = '';
    public ?string $errorMessage = null;

    protected OrganizationStatusService $organizationStatusService;

    public function boot(OrganizationStatusService $organizationStatusService): void
    {
        $this->organizationStatusService = $organizationStatusService;
    }

    protected $listeners = [
        'openOrganizerOrganizationDetailsModal' => 'open',
    ];

    public function open(string $organizationId): void
    {
        $this->organizationId = $organizationId;
        $this->loadOrganization();
        if ($this->organization) {
            $this->show = true;
            $this->resetValidation();
            $this->reason = '';
            $this->errorMessage = null;
        } else {
            $this->dispatch('organizationStatusUpdated', message: 'Organisation introuvable.', type: 'error');
        }
    }

    public function close(): void
    {
        $this->show = false;
        $this->organizationId = null;
        $this->organization = null;
        $this->reason = '';
        $this->errorMessage = null;
        $this->resetValidation();
    }

    protected function loadOrganization(): void
    {
        /** @var Organizer $organizer */
        $organizer = Auth::guard('organizer')->user();

        if (!$organizer || !$this->organizationId) {
            $this->organization = null;
            return;
        }

        // Charger l'organisation et s'assurer qu'elle appartient à l'organisateur actuel
        $this->organization = Organization::where('id', $this->organizationId)
            ->where('organizer_id', $organizer->id)
            ->first();
    }

    /**
     * Gère la connexion à l'organisation (tenant) sélectionnée en tant que patron.
     */
    public function connectToTenant(): void
    {
        $this->resetValidation();
        $this->errorMessage = null;

        if (!$this->organization) {
            $this->errorMessage = "Organisation introuvable pour la connexion.";
            return;
        }

        if ($this->organization->validation_status !== 'accepted' || $this->organization->activation_status !== 'enabled') {
            $this->errorMessage = "Impossible de se connecter. L'organisation n'est pas acceptée ou activée.";
            return;
        }

        /** @var Organizer $organizer */
        $organizer = Auth::guard('organizer')->user();

        if (!$organizer) {
            $this->errorMessage = "Aucun organisateur connecté. Veuillez vous reconnecter.";
            Auth::guard('organizer')->logout();
            return;
        }

        try {
            $tenant = $this->organization; // Supposons que $this->organization est votre instance du modèle Tenant

            if (!$tenant) {
                $this->errorMessage = "Contexte de tenant introuvable pour cette organisation.";
                return;
            }


            Tenancy::initialize($tenant);

             $patron = Patron::where('email', $organizer->email)->first();

             if ($patron) {
                Auth::guard('patron')->login($patron);

                $this->close();

                $tenantDomain = $tenant->domains->first()->domain;
                $port = (app()->environment('local') || app()->environment('development')) ? ':8000' : '';
                $redirectUrl = 'http://' . $tenantDomain . $port . route('patron.patronPanel', [], false);


                $this->redirect($redirectUrl);

                return;

            } else {
                Tenancy::end();
                $this->errorMessage = "Impossible de trouver un compte patron lié pour cette organisation. La synchronisation est-elle complète ?";
                Log::warning("No Patron found for Organizer '{$organizer->email}' in tenant '{$tenant->id}'.");
            }

        } catch (\Exception $e) {
            Tenancy::end();
            $this->errorMessage = "Une erreur est survenue lors de la connexion au tenant: " . $e->getMessage();
            Log::error("Error connecting to tenant as Patron: " . $e->getMessage(), ['exception' => $e, 'organization_id' => $this->organizationId, 'organizer_id' => $organizer->id]);
        }
    }

    /**
     * Désactive l'organisation si elle est valide et appartient à l'organisateur.
     */
    public function disableOrganization(): void
    {
        $this->resetValidation();
        $this->errorMessage = null;

        $this->validate([
            'reason' => 'required|string|min:10',
        ]);

        if (!$this->organization || $this->organization->organizer_id !== Auth::guard('organizer')->id()) {
            $this->errorMessage = "Action non autorisée sur cette organisation.";
            return;
        }

        try {
            $this->organizationStatusService->disableOrganization($this->organization, Auth::guard('organizer')->user(), $this->reason);

            $this->dispatch('organizationStatusUpdated', message: 'Organisation "' . $this->organization->nom . '" désactivée avec succès.');
            $this->close();

        } catch (InvalidArgumentException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\Exception $e) {
            $this->errorMessage = "Une erreur inattendue est survenue lors de la désactivation.";
            Log::error("Erreur de désactivation d'organisation par Organizer: " . $e->getMessage(), ['exception' => $e, 'organization_id' => $this->organizationId]);
        }
    }

    /**
     * Active l'organisation si elle est valide et appartient à l'organisateur, et si l'organisateur est autorisé à le faire.
     */
    public function enableOrganization(): void
    {
        $this->resetValidation();
        $this->errorMessage = null;

        if (!$this->organization || $this->organization->organizer_id !== Auth::guard('organizer')->id()) {
            $this->errorMessage = "Action non autorisée sur cette organisation.";
            return;
        }

        try {
            if (!$this->organizationStatusService->canOrganizerEnable($this->organization, Auth::guard('organizer')->user())) {
                throw new InvalidArgumentException("Vous n'êtes pas autorisé à réactiver cette organisation car elle a été désactivée par un administrateur.");
            }

            $this->organizationStatusService->enableOrganization($this->organization, Auth::guard('organizer')->user(), "Organisation réactivée par l'organisateur.");


            $this->dispatch('organizationStatusUpdated', message: 'Organisation "' . $this->organization->nom . '" réactivée avec succès.');
            $this->close();

        } catch (InvalidArgumentException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\Exception $e) {
            $this->errorMessage = "Une erreur inattendue est survenue lors de l'activation.";
            Log::error("Erreur d'activation d'organisation par Organizer: " . $e->getMessage(), ['exception' => $e, 'organization_id' => $this->organizationId]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.organization.organizer.manage-organizations.organization-details-modal');
    }
}
