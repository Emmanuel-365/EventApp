<?php

namespace App\Livewire\Admin\ManageOrganizations;

use App\Models\Admin;
use App\Models\Organization;
use App\Models\Organizer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminOrganizationsList extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $validationStatusFilter = null;
    public ?string $activationStatusFilter = null;

    public bool $showDetailsModal = false;
    public ?string $selectedOrganizationId = null;

    protected $listeners = [
        'organizationUpdated' => 'refreshOrganizationsList',
    ];

    public ?Organizer $organizer = null;

    public function refreshOrganizationsList(): void
    {
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

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        if (!$admin || !$admin->can('see-organization')) {
            abort(403, 'Accès non autorisé à la liste des organisations.');
        }

        $organizations = Organization::query()
            ->when($this->organizer,function ($query){
                $query->where('organizer_id',$this->organizer->id);
            })
            ->when($this->search, function ($query) {
                $query->where('nom', 'like', '%' . $this->search . '%')
                    ->orWhere('NIU', 'like', '%' . $this->search . '%')
                    ->orWhere('type', 'like', '%' . $this->search . '%');
            })
            ->when($this->validationStatusFilter, function ($query) {
                $query->where('validation_status', $this->validationStatusFilter);
            })
            ->when($this->activationStatusFilter, function ($query) {
                $query->where('activation_status', $this->activationStatusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.manage-organizations.admin-organizations-list', [
            'organizations' => $organizations,
            'admin' => $admin,
        ]);
    }

    public function openDetailsModal(string $organizationId): void
    {
        $this->selectedOrganizationId = $organizationId;
        $this->showDetailsModal = true;
        $this->dispatch('openOrganizationDetailsModal', organizationId: $organizationId);
    }

    public function closeDetailsModal(): void
    {
        $this->showDetailsModal = false;
        $this->selectedOrganizationId = null;
        $this->dispatch('closeOrganizationDetailsModal');
    }
}
