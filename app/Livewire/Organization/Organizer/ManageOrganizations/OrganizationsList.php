<?php

namespace App\Livewire\Organization\Organizer\ManageOrganizations;

use App\Models\Organization;
use App\Models\Organizer;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class OrganizationsList extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $validationStatusFilter = null;
    public ?string $activationStatusFilter = null;

    public ?string $globalSuccessMessage = null;

    protected $listeners = [
        'organizationCreatedSuccess' => 'handleOrganizationCreatedSuccess',
        'organizationStatusUpdated' => 'handleOrganizationStatusUpdated',
    ];

    public function mount(): void
    {
        if (session()->has('success')) {
            $this->globalSuccessMessage = session('success');
        }
    }

    /**
     * Gère l'événement de succès de création d'organisation.
     */
    public function handleOrganizationCreatedSuccess(string $message): void
    {
        $this->globalSuccessMessage = $message;
        $this->resetPage();
    }

    /**
     * Gère l'événement de mise à jour de statut d'organisation.
     */
    public function handleOrganizationStatusUpdated(string $message): void
    {
        $this->globalSuccessMessage = $message;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingValidationStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingActivationStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        /** @var Organizer $organizer */
        $organizer = Auth::guard('organizer')->user();

        if (!$organizer) {
              return view('livewire.organization.organizer.manage-organizations.organizations-list', [
                'organizations' => collect(),
                'organizer' => null,
            ]);
        }

        $organizations = Organization::query()
            ->where('organizer_id', $organizer->id)
             ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nom', 'like', '%' . $this->search . '%')
                        ->orWhere('NIU', 'like', '%' . $this->search . '%')
                        ->orWhere('type', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->validationStatusFilter, function ($query) {
                $query->where('validation_status', $this->validationStatusFilter);
            })
            ->when($this->activationStatusFilter, function ($query) {
                $query->where('activation_status', $this->activationStatusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.organization.organizer.manage-organizations.organizations-list', [
            'organizations' => $organizations,
            'organizer' => $organizer,
        ]);
    }
}
