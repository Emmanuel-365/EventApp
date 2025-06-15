<?php

namespace App\Livewire\Organization\Patron\ManageEmployees;

use App\Models\Tenant\Employee;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ListEmployees extends Component
{
    use WithPagination;

    public string $search = '';


    protected array $queryString = [
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    protected  $listeners = [
        'dataUpdate' => 'refreshEmployeesList',
        'employeeCreated' => 'refreshEmployeesList',
        'employeeUpdated' => 'refreshEmployeesList',
        'employeeDeleted' => 'refreshEmployeesList',
        'refreshEmployeesList' => '$refresh',
    ];


    public string $fileBasePath ;
    public function mount(): void
    {
        $this->fileBasePath = 'storage/tenant'.tenant('id') ;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function refreshEmployeesList(): void
    {
        $this->resetPage();
        $this->render();
    }

    public function deleteEmployee(int $employeeId): void
    {
        try {
            $employee = Employee::find($employeeId);

            if (!$employee) {
                session()->flash('error', "Employee non trouvé.");
                return;
            }

//            if ($employee->hasRole('super-employee', 'employee)) {
//                session()->flash('error', "Impossible de supprimer un employee Super Employee.");
//                return;
//            }

            $employeeName = $employee->nom . ' ' . $employee->prenom;
            $employee->delete();

            session()->flash('success', "L'employee '{$employeeName}' a été supprimé avec succès.");
            $this->refreshEmployeesList();
            $this->dispatch('dataUpdate');

        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de la suppression de l'employee: " . $e->getMessage());
            Log::error('ListEmployees - Erreur deleteEmployee: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    public function restoreEmployee(int $employeeId): void
    {
        try {
            $employee = Employee::withTrashed()->find($employeeId);

            if (!$employee) {
                session()->flash('error', "Employee non trouvé.");
                return;
            }

            if (!$employee->trashed()) {
                session()->flash('error', "L'employee n'est pas supprimé.");
                return;
            }

            $employeeName = $employee->nom . ' ' . $employee->prenom;
            $employee->restore();

            session()->flash('success', "L'employee '{$employeeName}' a été restauré avec succès.");
            $this->refreshEmployeesList();
            $this->dispatch('dataUpdate');
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de la restauration de l'employee: " . $e->getMessage());
            Log::error('ListEmployees - Erreur restoreEmployee: ' . $e->getMessage(), ['exception' => $e]);
        }
    }


    public function openEmployeeProfileCard(string $employeeId): void
    {
      $this->dispatch('openEmployeeProfileCard', employeeId: $employeeId);
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        $employees = Employee::query()
            ->with('roles')
            ->where(function ($query) {

            })
            ->when($this->search, function ($query) {
                $query->where('nom', 'like', '%' . $this->search . '%')
                    ->orWhere('prenom', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('matricule', 'like', '%' . $this->search . '%')
                    ->orWhereHas('roles', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->where('guard_name', 'employee');
                    });
            })
            ->orderBy('nom')
            ->withTrashed()
            ->paginate(10);

        return view('livewire.organization.patron.manage-employees.list-employees', [
            'employees' => $employees,
        ]);
    }
}
