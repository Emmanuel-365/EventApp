<?php

namespace App\Livewire\Organization\Organizer\ManageOrganizations;

use App\Models\Organization;
use App\Models\Organizer;
use App\Services\OrganizationStatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Livewire\Component;

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

        $this->organization = Organization::where('id', $this->organizationId)
            ->where('organizer_id', $organizer->id)
            ->first();
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
