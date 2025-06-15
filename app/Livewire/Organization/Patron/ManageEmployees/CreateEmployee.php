<?php

namespace App\Livewire\Organization\Patron\ManageEmployees;

use App\Models\Tenant\Employee;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

class CreateEmployee extends Component
{
    use WithFileUploads;

    public string $nom = '';
    public string $prenom = '';
    public string $telephone = '';

    public ?string $email = null;
    public ?string $pays = null;
    public ?string $ville = null;

    public $photoProfil;
    public $pieceIdentiteRecto;
    public $pieceIdentiteVerso;

    public array $selectedRoles = [];
    public bool $showModal = false;

    protected $listeners = ['openCreateEmployeeModal' => 'openModal'];

    public function getAvailableRolesProperty()
    {
        return Role::where('guard_name', 'employee')->orderBy('name')->get();
    }

    protected function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('employees')->where(fn ($query) => $query->whereNotNull('email')),
            ],

            'telephone' => ['nullable', 'string', 'max:20', Rule::unique('employees', 'telephone')],
            'pays' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:255'],
            'photoProfil' => ['nullable', 'image', 'max:1024'],
            'pieceIdentiteRecto' => ['nullable', 'image', 'max:2048'],
            'pieceIdentiteVerso' => ['nullable', 'image', 'max:2048'],
            'selectedRoles' => ['nullable', 'array'],
            'selectedRoles.*' => ['exists:roles,id'],
        ];
    }

    protected $messages = [
        'nom.required' => 'Le nom est obligatoire.',
        'prenom.required' => 'Le prénom est obligatoire.',
        'email.email' => 'L\'adresse e-mail doit être une adresse valide.',
        'email.unique' => 'Cet e-mail est déjà utilisé.',
        'telephone.required' => 'Le numéro de téléphone est obligatoire.',
        'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
        'photoProfil.image' => 'Le fichier de la photo de profil doit être une image.',
        'photoProfil.max' => 'La photo de profil ne doit pas dépasser 1MB.',
        'pieceIdentiteRecto.image' => 'Le fichier recto de la pièce d\'identité doit être une image.',
        'pieceIdentiteRecto.max' => 'Le fichier recto de la pièce d\'identité ne doit pas dépasser 2MB.',
        'pieceIdentiteVerso.image' => 'Le fichier verso de la pièce d\'identité doit être une image.',
        'pieceIdentiteVerso.max' => 'Le fichier verso de la pièce d\'identité ne doit pas dépasser 2MB.',
        'selectedRoles.*.exists' => 'Un rôle sélectionné n\'est pas valide.',
    ];


    public function openModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'nom', 'prenom', 'email',
            'telephone', 'pays', 'ville',
            'photoProfil', 'pieceIdentiteRecto', 'pieceIdentiteVerso',
            'selectedRoles'
        ]);

    }


    public function createEmployee(): void
    {
        $this->validate();

        try {
            $employeeData = [
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'email' =>  $this->email,
                'telephone' => $this->telephone,
                'pays' => $this->pays,
                'ville' => $this->ville,
            ];

            if ($this->photoProfil) {
                $employeeData['photoProfil'] = $this->photoProfil->store('employee_profiles', 'public');
            }
            if ($this->pieceIdentiteRecto) {
                $employeeData['pieceIdentiteRecto'] = $this->pieceIdentiteRecto->store('id_pieces', 'public');
            }
            if ($this->pieceIdentiteVerso) {
                $employeeData['pieceIdentiteVerso'] = $this->pieceIdentiteVerso->store('id_pieces', 'public');
            }

            $employee = Employee::create($employeeData);

            if (!empty($this->selectedRoles)) {
                $roles = Role::whereIn('id', $this->selectedRoles)
                    ->where('guard_name', 'employee')
                    ->get();
                $employee->syncRoles($roles);
            }

            session()->flash('success', 'Employee "' . $this->prenom . ' ' . $this->nom . '" créé avec succès !');
            $this->dispatch('employeeCreated');
            $this->dispatch('dataUpdate');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la création de l\'employee: ' . $e->getMessage());
            Log::error('CreateEmployee - Erreur createEmployee: ' . $e->getMessage(), ['exception' => $e]);
        }
    }


    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.organization.patron.manage-employees.create-employee');
    }
}
