<?php

namespace App\Livewire\Employee\ManageEvents;

use App\Models\Tenant\Employee;
use App\Models\Tenant\Event;
use App\Models\Tenant\Ticket;
use App\Services\EventActivityService;
use App\Services\EventStatusService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ViewEventDetails extends Component
{
    use WithFileUploads;
    public Event $event;
    public bool $showDetailsModal = false;
    public int $activeTab = 1;

    public int $totalTicketsSold = 0;
    public float $totalRevenue = 0.0;

    public bool $editing = false;
    public ?string $title = '';
    public ?string $description = '';
    public ?string $date = '';
    public ?string $time = '';
    public ?string $location = '';
    public ?float $latitude = null;
    public ?float $longitude = null;
    public ?float $price = 0.0;
    public ?int $capacity = 0;
    public $newImage = null;
    public ?string $currentImageUrl = '';

    protected $messages = [
        'title.required' => 'Le titre est obligatoire.',
        'description.required' => 'La description est obligatoire.',
        'date.required' => 'La date est obligatoire.',
        'date.date' => 'La date doit être une date valide.',
        'date.after_or_equal' => 'La date ne peut pas être dans le passé.',
        'time.required' => 'L\'heure est obligatoire.',
        'time.date_format' => 'L\'heure doit être au format HH:MM.',
        'location.required' => 'Le lieu est obligatoire.',
        'price.required' => 'Le prix est obligatoire.',
        'price.numeric' => 'Le prix doit être un nombre.',
        'price.min' => 'Le prix ne peut pas être négatif.',
        'capacity.required' => 'La capacité est obligatoire.',
        'capacity.integer' => 'La capacité doit être un nombre entier.',
        'capacity.min' => 'La capacité minimale est 1.',
        'newImage.image' => 'Le fichier doit être une image.',
        'newImage.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        'latitude.numeric' => 'La latitude doit être un nombre.',
        'longitude.numeric' => 'La longitude doit être un nombre.',
    ];


    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after_or_equal:' . Carbon::now()->format('Y-m-d'),
            'time' => 'required|date_format:H:i',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'newImage' => 'nullable|image|max:2048',
        ];
    }


    protected EventActivityService $eventActivityService;
    protected EventStatusService $eventStatusService;

    public Collection $activities;
    public Collection $statusHistories;

    protected $listeners =
        [
            'openViewEventDetailsModal' => 'openModal',
            'refreshEventDetails' => '$refresh',
        ];

    public function boot(EventActivityService $eventActivityService, EventStatusService $eventStatusService): void
    {
        $this->eventActivityService = $eventActivityService;
        $this->eventStatusService = $eventStatusService;
        $this->activities = collect();
        $this->statusHistories = collect();

        $this->event = new Event();
    }


    public function openModal(string $eventId): void
    {
        /** @var Employee $user */
        $user = Auth::guard('employee')->user();
        if (!$user || !$user->can('see-events')) {
            abort(403, 'Vous n\'avez pas la permission de voir les détails des événements.');
        }

        try {
            $this->event = Event::findOrFail($eventId);
            $this->resetFormFields();
            $this->calculateStats();

            $this->activities = $this->eventActivityService->getActivityHistoryQuery($this->event)->get();
            $this->statusHistories = $this->eventStatusService->getStatusHistoryQuery($this->event)->get();

            $this->showDetailsModal = true;
            $this->activeTab = 1;
            $this->editing = false;
        } catch (\Exception $e) {
            Log::error('Error opening event details modal: ' . $e->getMessage(), ['event_id' => $eventId]);
            $this->dispatch('swal:error', [
                'icon' => 'error',
                'title' => 'Erreur !',
                'text' => 'Impossible de charger les détails de l\'événement. Veuillez réessayer.',
            ]);
            $this->showDetailsModal = false;
        }
    }

    public function toggleEditMode(): void
    {
        /** @var Employee $user */
        $user = Auth::guard('employee')->user();
        if (!$user || !$user->can('update-events')) {
            $this->dispatch('swal:error', [
                'icon' => 'error',
                'title' => 'Accès Refusé',
                'text' => 'Vous n\'avez pas la permission de modifier les événements.',
            ]);
            return;
        }

        $this->editing = !$this->editing;

        if ($this->editing) {
            $this->loadEventDataForEditing();
        } else {
            $this->resetValidation();
            $this->newImage = null;
        }
    }

    protected function loadEventDataForEditing(): void
    {
        if ($this->event->exists) {
            $this->title = $this->event->title;
            $this->description = $this->event->description;
            $this->date = $this->event->date;
            $this->time = $this->event->time;
            $this->location = $this->event->location;
            $this->latitude = $this->event->latitude;
            $this->longitude = $this->event->longitude;
            $this->price = $this->event->price;
            $this->capacity = $this->event->capacity;
            $this->currentImageUrl = $this->event->image_url;
            $this->newImage = null;
        }
    }

    public function saveChanges(): void
    {
        /** @var Employee $user */
        $user = Auth::guard('employee')->user();
        if (!$user || !$user->can('update-events')) {
            $this->dispatch('swal:error', [
                'icon' => 'error',
                'title' => 'Accès Refusé',
                'text' => 'Vous n\'avez pas la permission de modifier les événements.',
            ]);
            return;
        }

        $this->validate();

        try {
            $oldValues = $this->event->only(['title', 'description', 'date', 'time', 'location', 'latitude', 'longitude', 'price', 'capacity', 'image_url']);

            $this->event->title = $this->title;
            $this->event->description = $this->description;
            $this->event->date = $this->date;
            $this->event->time = $this->time;
            $this->event->location = $this->location;
            $this->event->latitude = $this->latitude;
            $this->event->longitude = $this->longitude;
            $this->event->price = $this->price;
            $this->event->capacity = $this->capacity;

            if ($this->newImage) {
                if ($this->event->image_path && Storage::disk('public')->exists($this->event->image_path)) {
                    Storage::disk('public')->delete($this->event->image_path);
                }

                $imageName = time() . '-' . uniqid() . '.' . $this->newImage->getClientOriginalExtension();
                $imagePath = $this->newImage->storeAs('events_images', $imageName, 'public');
                $this->event->image_path = $imagePath;
                $this->event->image_url = Storage::disk('public')->url($imagePath);
            }

            $this->event->save();

            $this->eventActivityService->logActivity(
                $this->event,
                'update',
                $oldValues,
                $this->event->getAttributes(),
                'Modification des détails de l\'événement',
                Auth::guard('employee')->user()
            );


            $this->editing = false;
            $this->newImage = null;
            $this->resetValidation();
            $this->dispatch('refreshEventsList');
            $this->dispatch('swal:success', [
                'icon' => 'success',
                'title' => 'Succès !',
                'text' => 'L\'événement a été mis à jour avec succès.',
            ]);

            $this->activities = $this->eventActivityService->getActivityHistoryQuery($this->event)->get();
            $this->statusHistories = $this->eventStatusService->getStatusHistoryQuery($this->event)->get();

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('swal:error', [
                'icon' => 'error',
                'title' => 'Erreur de validation',
                'text' => 'Veuillez corriger les erreurs dans le formulaire.',
            ]);
            Log::error('Validation error updating event: ' . $e->getMessage(), ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            Log::error('Error updating event: ' . $e->getMessage(), ['event_id' => $this->event->id]);
            $this->dispatch('swal:error', [
                'icon' => 'error',
                'title' => 'Erreur !',
                'text' => 'Impossible de mettre à jour l\'événement. Veuillez réessayer.',
            ]);
        }
    }

    public function closeModal(): void
    {
        $this->showDetailsModal = false;
        $this->editing = false;
        $this->newImage = null;
        $this->resetValidation();
    }

    public function selectTab(int $tabNumber): void
    {
        $this->activeTab = $tabNumber;
    }

    protected function calculateStats(): void
    {
        $tickets = Ticket::where('event_id', $this->event->id)->get();
        $this->totalTicketsSold = $tickets->count();
        $this->totalRevenue = $tickets->sum('price');
    }

    private function resetFormFields(): void
    {
        $this->reset([
            'title', 'description', 'date', 'time', 'location',
            'latitude', 'longitude', 'price', 'capacity', 'newImage'
        ]);

        if ($this->event->exists) {
            $this->loadEventDataForEditing();
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $participants = collect();
        if ($this->event->exists) {
            $participants = Ticket::where('event_id', $this->event->id)->with('client')->get();
        }

        return view('livewire.employee.manage-events.view-event-details', [
            'participants' => $participants,
        ]);
    }
}
