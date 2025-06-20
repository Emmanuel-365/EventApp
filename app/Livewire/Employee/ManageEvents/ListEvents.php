<?php

namespace App\Livewire\Employee\ManageEvents;

use App\Models\Tenant\Employee;
use App\Models\Tenant\Event;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ListEvents extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public bool $showDeletedEvents = false;
    public int $perPage = 10;

    protected $listeners =
        [
            'eventCreated' => '$refresh',
            'eventUpdated' => '$refresh',
            'eventStatusChanged' => '$refresh',
            'refreshEventsList' => '$refresh'
        ];



    public function createEvent(): void
    {
        /** @var Employee $user */
        $user = Auth::guard('employee')->user();
        if (!$user || !$user->can('create-events')) {
            abort(403, 'Vous n\'avez pas la permission de créer des événements.');
        }
        $this->dispatch('openCreateEventModal');
    }

    public function viewEventDetails(string $eventId): void
    {
        /** @var Employee $user */
        $user = Auth::guard('employee')->user();
        if (!$user || !$user->can('see-events')) {
            abort(403, 'Vous n\'avez pas la permission de voir les détails des événements.');
        }
        $this->dispatch('openViewEventDetailsModal', $eventId);
    }
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingShowDeletedEvents(): void
    {
        $this->resetPage();
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        /** @var Employee $user */
        $user = Auth::guard('employee')->user();

        if (!$user || !$user->can('see-events')) {
            abort(403, 'Accès non autorisé à la liste des événements.');
        }

        $eventsQuery = Event::query();

        if ($this->showDeletedEvents) {
            $eventsQuery->withTrashed();
        }

        if ($this->search) {
            $eventsQuery->where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('matricule', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $eventsQuery->where('status', $this->statusFilter);
        }

        $events = $eventsQuery->latest()->paginate($this->perPage);

        return view('livewire.employee.manage-events.list-events', [
            'events' => $events,
            'user' => $user,
        ]);
    }






    // public function confirmCancelEvent(string $eventId) { ... }
    // public function deleteEvent(string $eventId) { ... }
    // public function restoreEvent(string $eventId) { ... }
    // public function initiateRefunds(string $eventId) { ... }
}
