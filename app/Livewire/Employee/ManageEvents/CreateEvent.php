<?php

namespace App\Livewire\Employee\ManageEvents;

use App\Models\Tenant\Employee;
use App\Models\Tenant\Event;
use App\Jobs\SyncEventToCentralDB;
use App\Services\EventActivityService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class CreateEvent extends Component
{
    use WithFileUploads;

    // Propriétés du formulaire
    public string $title = '';
    public ?string $description = null;
    public string $date = '';
    public string $time = '';
    public string $location = '';
    public float $price = 0.0;
    public int $capacity = 1;
    public $image_file;

    // Propriétés pour la localisation (maintenues pour la DB, mais sans sélection carte)
    public float $latitude = 0.0;  // <-- Valeur par défaut
    public float $longitude = 0.0; // <-- Valeur par défaut

    // Indique si la modale est ouverte
    public bool $showCreateModal = false;

    protected EventActivityService $eventActivityService;

    public function boot(EventActivityService $eventActivityService): void
    {
        $this->eventActivityService = $eventActivityService;
    }

    protected $listeners = [
        'openCreateEventModal' => 'openModal',
        // 'locationSelected' => 'setLocation'
    ];

    protected array $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'date' => 'required|date|after_or_equal:today',
        'time' => 'required|date_format:H:i',
        'location' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'capacity' => 'required|integer|min:1',
        'image_file' => 'nullable|image|max:2048',
        'latitude' => 'numeric',
        'longitude' => 'numeric',
    ];

    protected array $messages = [
        'title.required' => 'Le titre est obligatoire.',
        'location.required' => 'Le lieu est obligatoire.',
    ];


    public function openModal(): void
    {
        /** @var Employee $user */
        $user = Auth::guard('employee')->user();
        if (!$user || !$user->can('create-events')) {
            abort(403, 'Vous n\'avez pas la permission de créer des événements.');
        }

        $this->resetValidation();
        $this->reset(['title', 'description', 'date', 'time', 'price', 'capacity', 'image_file', 'location']); // <-- reset 'location' au lieu de 'displayLocation', 'latitude', 'longitude'
        $this->latitude = 0.0;
        $this->longitude = 0.0;
        $this->showCreateModal = true;
    }

    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->resetErrorBag();
    }
    public function clearImage(): void
    {
        $this->image_file = null;
    }

    #[NoReturn] public function store(): void
    {
        /** @var Employee $user */
        $user = Auth::guard('employee')->user();
        if (!$user || !$user->can('create-events')) {
            abort(403, 'Vous n\'avez pas la permission de créer des événements.');
        }

        $this->validate();


        $imageUrl = null;
        if ($this->image_file) {
            $path = $this->image_file->store('event_images', 'public_tenant');
            $imageUrl = Storage::disk('public_tenant')->url($path);
        }

        $generatedMatricule = 'EVT-' . now()->format('Ymd') . '-' . Str::uuid()->toString();

        try {
            $event = Event::create([
                'title' => $this->title,
                'description' => $this->description,
                'date' => $this->date,
                'time' => $this->time,
                'location' => $this->location,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'price' => $this->price,
                'capacity' => $this->capacity,
                'available_tickets' => $this->capacity,
                'matricule' => $generatedMatricule,
                'status' => 'not_published',
                'image_url' => $imageUrl,
            ]);

            $this->eventActivityService->logEventCreation($event, $user);
            Log::info('Event created in tenant DB via CreateEvent component.', [
                'event_id' => $event->id,
                'tenant_id' => tenant('id'),
                'employee_id' => $user->id,
                'event_title' => $this->title,
            ]);

            SyncEventToCentralDB::dispatch(
                tenant('id'),
                $event->id,
                $event->matricule,
                $event->title,
                $event->description,
                $event->date,
                $event->time,
                $event->location,
                $event->latitude,
                $event->longitude,
                $event->price,
                $event->capacity,
                $event->available_tickets,
                $event->status,
                $event->image_url,
                $event->cancelled_reason,
                $event->cancelled_by_type,
                $event->cancelled_by_id
            );

            $this->dispatch('swal:success', [
                'icon' => 'success',
                'title' => 'Succès !',
                'text' => 'L\'événement a été créé avec succès et la synchronisation est en cours.',
            ]);

            $this->closeModal();
            $this->dispatch('eventCreated');

        } catch (\Exception $e) {
            Log::error('Error creating event: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'tenant_id' => tenant('id'),
                'error_trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('swal:error', [
                'icon' => 'error',
                'title' => 'Erreur !',
                'text' => 'Une erreur est survenue lors de la création de l\'événement. Veuillez réessayer.',
            ]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.employee.manage-events.create-event');
    }
}
