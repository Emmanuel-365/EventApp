<?php

namespace App\Livewire\Admin\ManageOrganizers;


use Livewire\Component;

class OrganizerDetailsModal extends Component
{
    public bool $show = false;
    public ?string $organizerId = null;

    protected $listeners = [
        'openOrganizerDetailsModal' => 'open',
        'closeOrganizerDetailsModal' => 'close',
    ];


    public function open(string $organizerId): void
    {

        $this->organizerId = $organizerId;
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
        $this->organizerId = null;
        $this->dispatch('organizerDetailsModalClosed');
    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View
    {
        return view('livewire.admin.manage-organizers.organizer-details-modal');
    }
}
