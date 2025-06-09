<?php

namespace App\Livewire\Admin\ManageOrganizers;

use App\Models\Organizer;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class OrganizersDataTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public ?int $selectedOrganizerId;
    public bool $showDetailModal = false;


    protected $listeners = [
        'organizerDetailClosed' => 'closeDetailModal',
        'profileActionCompleted' => '$refresh',
        'banActionCompleted' => '$refresh'
    ];




    public function sortBy(string $field): void
    {
        $adminUser = Auth::guard('admin')->user();
        if (!$adminUser || !$adminUser->can('see-organizer-profile')) {
            session()->flash('error', "Vous n'avez pas la permission de trier les organisateurs.");
            return;
        }

        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }


    public function updatedSearch(): void
    {
        $adminUser = Auth::guard('admin')->user();
        if (!$adminUser || !$adminUser->can('see-organizer-profile')) {
            session()->flash('error', "Vous n'avez pas la permission de rechercher les organisateurs.");
            $this->search = '';
            return;
        }
        $this->resetPage();
    }

    public function selectOrganizer(int $organizerId): void
    {
        $adminUser = Auth::guard('admin')->user();
        if (!$adminUser || !$adminUser->can('see-organizer-profile')) {
            session()->flash('error', "Vous n'avez pas la permission de voir les détails des organisateurs.");
            return;
        }
        $this->selectedOrganizerId = $organizerId;
        $this->showDetailModal = true;
        $this->dispatch('openOrganizerDetail', organizerId: $organizerId);
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedOrganizerId = null;
    }


    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        $adminUser = Auth::guard('admin')->user();
        $canSeeOrganizers = $adminUser && $adminUser->can('see-organizer-profile');

        if (!$canSeeOrganizers) {
            session()->flash('error', "Vous n'avez pas la permission de voir la liste des organisateurs.");
            return view('livewire.admin.manage-organizers.organizers-data-table', [
                'organizers' => new LengthAwarePaginator([], 0, $this->perPage),
                'canSeeOrganizers' => false,
            ]);
        }

        $organizers = Organizer::query()
            ->when($this->search, function ($query) {
                $query->where('nom', 'like', '%' . $this->search . '%')
                    ->orWhere('prenom', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('matricule', 'like', '%' . $this->search . '%')
                    ->orWhere('telephone', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.manage-organizers.organizers-data-table', [
            'organizers' => $organizers,
            'canSeeOrganizers' => $canSeeOrganizers,
        ]);
    }



}
