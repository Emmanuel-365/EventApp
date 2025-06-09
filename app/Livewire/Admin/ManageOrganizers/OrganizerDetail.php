<?php

namespace App\Livewire\Admin\ManageOrganizers;

use App\Models\Organizer;
use App\Services\BanService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class OrganizerDetail extends Component
{
    public Organizer $organizer;
    public bool $showPieceIdentiteRecto = true;
    public Collection $banHistory;
    public string $banMotif = '';


    protected $listeners = [
        'refreshOrganizerDetail' => '$refresh'
    ];


    public function mount(int $organizerId, BanService $banService): void
    {

        $this->organizer = Organizer::withTrashed()->findOrFail($organizerId);

        $this->banHistory = $banService->getBanHistory($this->organizer);
    }



    public function getPhotoProfilUrlProperty(): ?string
    {
        return $this->organizer->photoProfil ? Storage::url($this->organizer->photoProfil) : null;
    }


    public function getPieceIdentiteRectoUrlProperty(): ?string
    {
        return $this->organizer->pieceIdentiteRecto ? Storage::url($this->organizer->pieceIdentiteRecto) : null;
    }


    public function getPieceIdentiteVersoUrlProperty(): ?string
    {
        return $this->organizer->pieceIdentiteVerso ? Storage::url($this->organizer->pieceIdentiteVerso) : null;
    }


    public function getPasswordStatusProperty(): string
    {
        return $this->organizer->password ? 'Défini' : 'Non défini';
    }


    public function getPasswordChangedAtFormattedProperty(): string
    {
        return $this->organizer->password_changed_at ? $this->organizer->password_changed_at->format('d/m/Y H:i') : 'Jamais';
    }


    public function getPasscodeStatusProperty(): string
    {
        return $this->organizer->passcode ? 'Défini' : 'Non défini';
    }


    public function getPasscodeResetDateFormattedProperty(): string
    {
        return $this->organizer->passcode_reset_date ? $this->organizer->passcode_reset_date->format('d/m/Y H:i') : 'Jamais';
    }


    public function getProfileVerificationStatusFormattedProperty(): string
    {
        return ucfirst($this->organizer->profile_verification_status);
    }


    public function togglePieceIdentite(): void
    {
        $this->showPieceIdentiteRecto = !$this->showPieceIdentiteRecto;
    }


    public function validateOrganizerProfile(): void
    {
        $adminUser = Auth::guard('admin')->user();
        if (!$adminUser || !$adminUser->can('validate-organizer-profile')) {
            session()->flash('error', "Vous n'avez pas la permission de valider ce profil.");
            return;
        }

        if ($this->organizer->profile_verification_status !== 'en attente') {
            session()->flash('error', 'Le profil doit être en statut "en attente" pour être validé.');
            return;
        }

        $this->organizer->profile_verification_status = 'validé';
        $this->organizer->save();

        session()->flash('success', 'Profil organisateur validé avec succès.');
        $this->dispatch('profileActionCompleted');
        $this->dispatch('refreshOrganizerDetail');
    }


    public function rejectOrganizerProfile(): void
    {
        $adminUser = Auth::guard('admin')->user();
        if (!$adminUser || !$adminUser->can('reject-organizer-profile')) {
            session()->flash('error', "Vous n'avez pas la permission de rejeter ce profil.");
            return;
        }

        if ($this->organizer->profile_verification_status !== 'en attente') {
            session()->flash('error', 'Le profil doit être en statut "en attente" pour être rejeté.');
            return;
        }

        $this->organizer->profile_verification_status = 'rejeté';
        $this->organizer->save();

        session()->flash('success', 'Profil organisateur rejeté avec succès.');
        $this->dispatch('profileActionCompleted');
        $this->dispatch('refreshOrganizerDetail');
    }


    public function banOrganizer(BanService $banService): void
    {
        $adminUser = Auth::guard('admin')->user();
        if (!$adminUser || !$adminUser->can('ban-organizer')) {
            session()->flash('error', "Vous n'avez pas la permission de bannir cet organisateur.");
            return;
        }

        if ($banService->isUserBanned($this->organizer)) {
            session()->flash('message', 'Cet organisateur est déjà banni.');
            return;
        }

        if (empty($this->banMotif)) {
            session()->flash('error', 'Un motif de bannissement est requis.');
            return;
        }

        $banService->banUser($this->organizer, $this->banMotif, $adminUser);
        $this->banMotif = '';
        $this->banHistory = $banService->getBanHistory($this->organizer);
        session()->flash('success', 'Organisateur banni avec succès.');
        $this->dispatch('banActionCompleted');
        $this->dispatch('refreshOrganizerDetail');
    }


    public function unbanOrganizer(BanService $banService): void
    {
        $adminUser = Auth::guard('admin')->user();
        if (!$adminUser || !$adminUser->can('unban-organizer')) {
            session()->flash('error', "Vous n'avez pas la permission de débannir cet organisateur.");
            return;
        }

        if (!$banService->isUserBanned($this->organizer)) {
            session()->flash('message', 'Cet organisateur n\'est pas banni.');
            return;
        }

        $banService->unbanUser($this->organizer, $adminUser);
        $this->banHistory = $banService->getBanHistory($this->organizer);
        session()->flash('success', 'Organisateur débanni avec succès.');
        $this->dispatch('banActionCompleted');
        $this->dispatch('refreshOrganizerDetail');
    }



    public function render(BanService $banService): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        $adminUser = Auth::guard('admin')->user();

        if (!$adminUser || !$adminUser->can('see-organizer-profile')) {
            session()->flash('error', "Vous n'avez pas la permission de voir les détails des organisateurs.");
            $this->dispatch('organizerDetailClosed');
            return view('livewire.admin.manage-organizers.organizer-detail', [
                'organizer' => null,
                'isBanned' => false,
                'canValidateProfile' => false,
                'canRejectProfile' => false,
                'canBanOrganizer' => false,
                'canUnbanOrganizer' => false,
                'banHistory' => collect()
            ]);
        }

        $isBanned = $banService->isUserBanned($this->organizer);

        $canValidateProfile = $adminUser->can('validate-organizer-profile');
        $canRejectProfile = $adminUser->can('reject-organizer-profile');
        $canBanOrganizer = $adminUser->can('ban-organizer');
        $canUnbanOrganizer = $adminUser->can('unban-organizer');

        return view('livewire.admin.manage-organizers.organizer-detail', [
            'isBanned' => $isBanned,
            'canValidateProfile' => $canValidateProfile,
            'canRejectProfile' => $canRejectProfile,
            'canBanOrganizer' => $canBanOrganizer,
            'canUnbanOrganizer' => $canUnbanOrganizer,
            'banHistory' => $this->banHistory
        ]);
    }
}
