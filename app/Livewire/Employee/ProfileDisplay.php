<?php

namespace App\Livewire\Employee;

use App\Models\Tenant\Employee;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileDisplay extends Component
{
    public ?Employee $employee = null;

    public ?string $nom = null;
    public ?string $prenom = null;
    public ?string $matricule = null;
    public ?string $email = null;
    public ?string $telephone = null;
    public ?string $pays = null;
    public ?string $ville = null;
    public ?string $photoProfilUrl = null;
    public ?string $pieceIdentiteRectoUrl = null;
    public ?string $pieceIdentiteVersoUrl = null;
    public ?string $passwordChangedAt = null;
    public ?string $passcodeResetDate = null;

    public bool $showPieceIdentiteRecto = true;


    public function mount(): void
    {
        $this->employee = Auth::guard('employee')->user();

        if ($this->employee) {
            $this->fill([
                'nom' => $this->employee->nom,
                'prenom' => $this->employee->prenom,
                'matricule' => $this->employee->matricule,
                'email' => $this->employee->email,
                'telephone' => $this->employee->telephone,
                'pays' => $this->employee->pays,
                'ville' => $this->employee->ville,
                'photoProfilUrl' => $this->employee->photoProfil ? asset('storage/' . $this->employee->photoProfil) : null,
                'pieceIdentiteRectoUrl' => $this->employee->pieceIdentiteRecto ? asset('storage/' . $this->employee->pieceIdentiteRecto) : null,
                'pieceIdentiteVersoUrl' => $this->employee->pieceIdentiteVerso ? asset('storage/' . $this->employee->pieceIdentiteVerso) : null,
                'passwordChangedAt' => $this->employee->password_changed_at ? $this->employee->password_changed_at->format('d/m/Y H:i') : 'Jamais',
                'passcodeResetDate' => $this->employee->passcode_reset_date ? $this->employee->passcode_reset_date->format('d/m/Y H:i') : 'Jamais',
            ]);
        }
    }


    public function togglePieceIdentite(): void
    {
        $this->showPieceIdentiteRecto = !$this->showPieceIdentiteRecto;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.employee.profile-display');
    }
}
