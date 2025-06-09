<?php

namespace App\Livewire\Admin\ManageOrganizations;

use App\Models\Admin;
use App\Models\Organization;
use App\Services\OrganizationStatusService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Livewire\Component;


class OrganizationDetailsModal extends Component
{
    public bool $show = false;
    public ?string $organizationId = null;
    public ?Organization $organization = null;
    public Collection $statusHistory;

    public string $reason = '';
    public bool $showRejectReasonModal = false;
    public bool $showDisableReasonModal = false;
    public bool $showEnableReasonModal = false;

    public string $historySearch = '';
    public string $historySortField = 'created_at';
    public string $historySortDirection = 'desc';

    protected OrganizationStatusService $organizationStatusService;

    protected $with = ['changerAdmin', 'changerOrganizer'];


    public function boot(OrganizationStatusService $organizationStatusService): void
    {
        $this->organizationStatusService = $organizationStatusService;
        $this->statusHistory = new Collection();
    }

    protected $listeners = [
        'openOrganizationDetailsModal' => 'open',
        'closeOrganizationDetailsModal' => 'close',
    ];

    public function open(string $organizationId): void
    {
        $this->organizationId = $organizationId;
        $this->loadOrganization();
        if ($this->organization) {
            $this->loadStatusHistory();
            $this->show = true;
        } else {
            session()->flash('error', "Organisation introuvable.");
            $this->close();
        }
    }

    public function close(): void
    {
        $this->show = false;
        $this->reset([
            'organizationId',
            'organization',
            'reason',
            'showRejectReasonModal',
            'showDisableReasonModal',
            'showEnableReasonModal',
            'historySearch',
            'historySortField',
            'historySortDirection',
        ]);
        $this->statusHistory = new Collection();
        $this->resetValidation();
    }

    public function loadOrganization(): void
    {
        if ($this->organizationId) {
            $this->organization = Organization::find($this->organizationId);
        }
    }

    public function loadStatusHistory(): void
    {
        if ($this->organization) {
            $query = $this->organizationStatusService->getStatusHistoryQuery($this->organization);

            $this->statusHistory = $query
                ->when($this->historySearch, function ($q) {
                    $q->where('status_type', 'like', '%' . $this->historySearch . '%')
                        ->orWhere('old_status', 'like', '%' . $this->historySearch . '%')
                        ->orWhere('new_status', 'like', '%' . $this->historySearch . '%')
                        ->orWhere('reason', 'like', '%' . $this->historySearch . '%')
                        ->orWhere('changed_by_type', 'like', '%' . $this->historySearch . '%')
                        ->orWhereHas('changerAdmin', function ($sq) {
                            $sq->where('name', 'like', '%' . $this->historySearch . '%');
                        })
                        ->orWhereHas('changerOrganizer', function ($sq) {
                            $sq->where('name', 'like', '%' . $this->historySearch . '%');
                        });
                })
                ->orderBy($this->historySortField, $this->historySortDirection)
                ->get()
                ->loadMissing(['changerAdmin', 'changerOrganizer']);
        } else {
            $this->statusHistory = new Collection();
        }
    }

    public function updatingHistorySearch(): void
    {
        $this->loadStatusHistory();
    }

    public function sortHistoryBy(string $field): void
    {
        if ($this->historySortField === $field) {
            $this->historySortDirection = $this->historySortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->historySortField = $field;
            $this->historySortDirection = 'asc';
        }
        $this->loadStatusHistory();
    }

    public function updatedOrganizationId(): void
    {
        $this->loadOrganization();
        $this->reset(['historySearch', 'historySortField', 'historySortDirection']);
        $this->historySortField = 'created_at';
        $this->historySortDirection = 'desc';
        $this->loadStatusHistory();
    }

    public function acceptOrganization(): void
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        if (!$this->organization || !$admin) {
            session()->flash('error', "Erreur : Organisation ou administrateur non trouvé.");
            return;
        }

        try {
            $this->organizationStatusService->acceptOrganization($this->organization, $admin);
            session()->flash('success', 'Organisation "' . $this->organization->nom . '" acceptée et activée avec succès.');
            $this->dispatch('organizationUpdated');
            $this->close();
        } catch (InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());
        } catch (\Exception $e) {
            session()->flash('error', "Une erreur inattendue est survenue: " . $e->getMessage());
        }
    }

    public function openRejectReasonModal(): void
    {
        $this->resetValidation();
        $this->reason = '';
        $this->showRejectReasonModal = true;
    }

    public function rejectOrganization(): void
    {
        $this->validate([
            'reason' => 'required|string|min:10',
        ]);

        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        if (!$this->organization || !$admin) {
            session()->flash('error', "Erreur : Organisation ou administrateur non trouvé.");
            return;
        }

        try {
            $this->organizationStatusService->rejectOrganization($this->organization, $admin, $this->reason);
            session()->flash('success', 'Organisation "' . $this->organization->nom . '" rejetée avec succès.');
            $this->dispatch('organizationUpdated');
            $this->close();
        } catch (InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());
        } catch (\Exception $e) {
            session()->flash('error', "Une erreur inattendue est survenue: " . $e->getMessage());
        } finally {
            $this->showRejectReasonModal = false;
        }
    }

    public function openDisableReasonModal(): void
    {
        $this->resetValidation();
        $this->reason = '';
        $this->showDisableReasonModal = true;
    }

    public function disableOrganization(): void
    {
        $this->validate([
            'reason' => 'required|string|min:10',
        ]);

        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        if (!$this->organization || !$admin) {
            session()->flash('error', "Erreur : Organisation ou administrateur non trouvé.");
            return;
        }

        try {
            $this->organizationStatusService->disableOrganization($this->organization, $admin, $this->reason);
            session()->flash('success', 'Organisation "' . $this->organization->nom . '" désactivée avec succès.');
            $this->dispatch('organizationUpdated');
            $this->close();
        } catch (InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());
        } catch (\Exception $e) {
            session()->flash('error', "Une erreur inattendue est survenue: " . $e->getMessage());
        } finally {
            $this->showDisableReasonModal = false;
        }
    }

    public function openEnableReasonModal(): void
    {
        $this->resetValidation();
        $this->reason = '';
        $this->showEnableReasonModal = true;
    }

    public function enableOrganization(): void
    {
        $this->validate([
            'reason' => 'nullable|string|min:3',
        ]);

        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        if (!$this->organization || !$admin) {
            session()->flash('error', "Erreur : Organisation ou administrateur non trouvé.");
            return;
        }

        try {
            $this->organizationStatusService->enableOrganization($this->organization, $admin, $this->reason ?: "Activée par l'administrateur.");
            session()->flash('success', 'Organisation "' . $this->organization->nom . '" activée avec succès.');
            $this->dispatch('organizationUpdated');
            $this->close();
        } catch (InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());
        } catch (\Exception $e) {
            session()->flash('error', "Une erreur inattendue est survenue: " . $e->getMessage());
        } finally {
            $this->showEnableReasonModal = false;
        }
    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View
    {
        return view('livewire.admin.manage-organizations.organization-details-modal');
    }
}
