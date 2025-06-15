<?php

namespace App\Livewire\Organization\Patron\ManageEmployees;

use App\Models\Tenant\Employee;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EmployeeProfileCard extends Component
{
    use WithFileUploads;

    public Employee|null $employee = null;

    public string $nom = '';
    public string $prenom = '';
    public string|null $email = '';
    public string $telephone = '';
    public ?string $pays = null;
    public ?string $ville = null;

    public $newPhotoProfil;
    public $newPieceIdentiteRecto;
    public $newPieceIdentiteVerso;

    public bool $deletePhotoProfil = false;
    public bool $deletePieceIdentiteRecto = false;
    public bool $deletePieceIdentiteVerso = false;

    public array $selectedRoles = [];
    public bool $showModal = false;
    public bool $showPieceIdentiteRecto = true;

    protected $listeners = ['openEmployeeProfileCard' => 'openModal'];

    public string $fileBasePath ;
    public function mount(): void
    {
        $this->fileBasePath = 'storage/tenant'.tenant('id') ;
    }


    public function getAvailableRolesProperty()
    {
        return Role::where('guard_name', 'employee')->orderBy('name')->get();
    }

    protected function rules(): array
    {
        $employeeId = $this->employee ? $this->employee->id : null;

        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('employees')->ignore($employeeId)->where(fn ($query) => $query->whereNotNull('email')),
            ],
            'telephone' => ['nullable', 'string', 'max:20', Rule::unique('employees', 'telephone')->ignore($employeeId)],
            'pays' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:255'],
            'newPhotoProfil' => ['nullable', 'image', 'max:1024'],
            'newPieceIdentiteRecto' => ['nullable', 'image', 'max:2048'],
            'newPieceIdentiteVerso' => ['nullable', 'image', 'max:2048'],
            'selectedRoles' => ['nullable', 'array'],
            'selectedRoles.*' => ['exists:roles,id'],
            'deletePhotoProfil' => ['boolean'],
            'deletePieceIdentiteRecto' => ['boolean'],
            'deletePieceIdentiteVerso' => ['boolean'],
        ];
    }

    protected array $messages = [
        'email.unique' => 'Cet e-mail est déjà utilisé par un autre employee.',
        'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé par un autre employee.',
        'newPhotoProfil.max' => 'La photo de profil ne doit pas dépasser 1MB.',
    ];



    public function openModal(string $employeeId): void
    {
        $this->employee = Employee::withTrashed()->find($employeeId);

        if (!$this->employee) {
            session()->flash('error', "Employee non trouvé.");
            $this->closeModal();
            return;
        }

        $this->fill([
            'nom' => $this->employee->nom,
            'prenom' => $this->employee->prenom,
            'email' => $this->employee->email,
            'telephone' => $this->employee->telephone,
            'pays' => $this->employee->pays,
            'ville' => $this->employee->ville,
            'selectedRoles' => $this->employee->roles->pluck('id')->toArray(),
        ]);

        $this->reset([
            'newPhotoProfil', 'newPieceIdentiteRecto', 'newPieceIdentiteVerso',
            'deletePhotoProfil', 'deletePieceIdentiteRecto', 'deletePieceIdentiteVerso',
        ]);

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->reset([
            'employee', 'nom', 'prenom', 'email', 'telephone', 'pays', 'ville',
            'newPhotoProfil', 'newPieceIdentiteRecto', 'newPieceIdentiteVerso',
            'deletePhotoProfil', 'deletePieceIdentiteRecto', 'deletePieceIdentiteVerso',
            'selectedRoles','showPieceIdentiteRecto'
        ]);
    }

    public function updateEmployee(): void
    {
        if (!$this->employee) {
            session()->flash('error', "Aucun employee sélectionné pour la mise à jour.");
            return;
        }

        $this->validate();

        try {
            $employeeData = [
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'email' => !empty($this->email) ? $this->email : null,
                'telephone' => $this->telephone,
                'pays' => $this->pays,
                'ville' => $this->ville,
            ];

            if ($this->deletePhotoProfil && $this->employee->photoProfil) {
                Storage::disk('public')->delete($this->employee->photoProfil);
                $employeeData['photoProfil'] = null;
            }
            if ($this->deletePieceIdentiteRecto && $this->employee->pieceIdentiteRecto) {
                Storage::disk('public')->delete($this->employee->pieceIdentiteRecto);
                $employeeData['pieceIdentiteRecto'] = null;
            }
            if ($this->deletePieceIdentiteVerso && $this->employee->pieceIdentiteVerso) {
                Storage::disk('public')->delete($this->employee->pieceIdentiteVerso);
                $employeeData['pieceIdentiteVerso'] = null;
            }

            if ($this->newPhotoProfil) {
                if ($this->employee->photoProfil) {
                    Storage::disk('public')->delete($this->employee->photoProfil);
                }
                $employeeData['photoProfil'] = $this->newPhotoProfil->store('employee_profiles', 'public');
            }
            if ($this->newPieceIdentiteRecto) {
                if ($this->employee->pieceIdentiteRecto) {
                    Storage::disk('public')->delete($this->employee->pieceIdentiteRecto);
                }
                $employeeData['pieceIdentiteRecto'] = $this->newPieceIdentiteRecto->store('id_pieces', 'public');
            }
            if ($this->newPieceIdentiteVerso) {
                if ($this->employee->pieceIdentiteVerso) {
                    Storage::disk('public')->delete($this->employee->pieceIdentiteVerso);
                }
                $employeeData['pieceIdentiteVerso'] = $this->newPieceIdentiteVerso->store('id_pieces', 'public');
            }

            $this->employee->update($employeeData);

            if (!empty($this->selectedRoles)) {
                $roles = Role::whereIn('id', $this->selectedRoles)
                    ->where('guard_name', 'employee')
                    ->with('permissions')
                    ->get();
                $this->employee->syncRoles($roles);
            } else {
                $this->employee->syncRoles([]);
            }

            session()->flash('success', 'Profil de "' . $this->employee->prenom . ' ' . $this->employee->nom . '" mis à jour avec succès !');
            $this->dispatch('employeeUpdated');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la mise à jour du profil: ' . $e->getMessage());
            Log::error('EmployeeProfileCard - Erreur updateEmployee: ' . $e->getMessage(), ['exception' => $e]);
        }
    }


    public function generatePdf(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!$this->employee) {
            session()->flash('error', "Impossible de générer le PDF. Aucun employee sélectionné.");
            $this->closeModal();
            abort(404, "Employee not found for PDF generation.");
        }

        $employee = Employee::with(['roles' => function($query) {
            $query->with('permissions');
        }])->find($this->employee->id);

        if (!$employee) {
            session()->flash('error', "Employee non trouvé pour la génération du PDF.");
            $this->closeModal();
            abort(404, "Employee not found for PDF generation.");
        }

        $photoProfilPath = null;
        if ($employee->photoProfil && Storage::disk('public')->exists($employee->photoProfil)) {
            $photoProfilPath = Storage::disk('public')->path($employee->photoProfil);
        }

        $pieceIdentiteRectoPath = null;
        if ($employee->pieceIdentiteRecto && Storage::disk('public')->exists($employee->pieceIdentiteRecto)) {
            $pieceIdentiteRectoPath = Storage::disk('public')->path($employee->pieceIdentiteRecto);
        }

        $pieceIdentiteVersoPath = null;
        if ($employee->pieceIdentiteVerso && Storage::disk('public')->exists($employee->pieceIdentiteVerso)) {
            $pieceIdentiteVersoPath = Storage::disk('public')->path($employee->pieceIdentiteVerso);
        }

        $qrCodeSvg = QrCode::size(150)->generate($employee->matricule)->toHtml();
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        $pdf = Pdf::loadView('pdf.employee_profile', [
            'employee' => $employee,
            'photoProfilPath' => $photoProfilPath,
            'pieceIdentiteRectoPath' => $pieceIdentiteRectoPath,
            'pieceIdentiteVersoPath' => $pieceIdentiteVersoPath,
            'qrCodeBase64' => $qrCodeBase64,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'fiche_employee_' . $employee->nom.' '.$employee->prenom . '.pdf');
    }

    public function togglePieceIdentiteView(): void
    {
        $this->showPieceIdentiteRecto = !$this->showPieceIdentiteRecto;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.organization.patron.manage-employees.employee-profile-card');
    }
}
