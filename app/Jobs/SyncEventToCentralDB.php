<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CentralEvent; // Le modèle de l'événement central
use Stancl\Tenancy\Features\TenantConfig; // Si vous utilisez cette fonctionnalité pour la config du tenant
use Stancl\Tenancy\Facades\Tenancy; // Pour basculer vers la base de données centrale
use Illuminate\Support\Facades\Log; // Pour le logging dans le Job

class SyncEventToCentralDB implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $tenantId;
    protected string $eventId;
    protected string $matricule;
    protected string $title;
    protected ?string $description;
    protected string $date;
    protected string $time;
    protected string $location;
    protected float $price;
    protected int $capacity;
    protected int $availableTickets;
    protected string $status;
    protected ?string $imageUrl;
    protected ?string $cancelledReason;
    protected ?string $cancelledByType;
    protected ?string $cancelledById;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $tenantId,
        string $eventId,
        string $matricule,
        string $title,
        ?string $description,
        string $date,
        string $time,
        string $location,
        float $price,
        int $capacity,
        int $availableTickets,
        string $status,
        ?string $imageUrl,
        ?string $cancelledReason,
        ?string $cancelledByType,
        ?string $cancelledById
    ) {
        $this->tenantId = $tenantId;
        $this->eventId = $eventId;
        $this->matricule = $matricule;
        $this->title = $title;
        $this->description = $description;
        $this->date = $date;
        $this->time = $time;
        $this->location = $location;
        $this->price = $price;
        $this->capacity = $capacity;
        $this->availableTickets = $availableTickets;
        $this->status = $status;
        $this->imageUrl = $imageUrl;
        $this->cancelledReason = $cancelledReason;
        $this->cancelledByType = $cancelledByType;
        $this->cancelledById = $cancelledById;
        // Le job sera par défaut mis en file d'attente sur la connexion par défaut.
        // Assurez-vous que votre configuration de queue est opérationnelle.
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Basculer vers la connexion de la base de données centrale
        Tenancy::central(function ($tenant) {
            try {
                // Trouver ou créer l'événement central
                // Nous utilisons updateOrCreate pour gérer les cas de création et de mise à jour future
                CentralEvent::updateOrCreate(
                    [
                        'tenant_id' => $this->tenantId,
                        'event_id' => $this->eventId // ID de l'événement dans le tenant
                    ],
                    [
                        'matricule' => $this->matricule,
                        'title' => $this->title,
                        'description' => $this->description,
                        'date' => $this->date,
                        'time' => $this->time,
                        'location' => $this->location,
                        'price' => $this->price,
                        'capacity' => $this->capacity,
                        'available_tickets' => $this->availableTickets,
                        'status' => $this->status,
                        'image_url' => $this->imageUrl,
                        'cancelled_reason' => $this->cancelledReason,
                        'cancelled_by_type' => $this->cancelledByType,
                        'cancelled_by_id' => $this->cancelledById,
                    ]
                );

                Log::info('Event synchronized to central DB successfully.', [
                    'tenant_id' => $this->tenantId,
                    'event_id_in_tenant_db' => $this->eventId,
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to synchronize event to central DB: ' . $e->getMessage(), [
                    'tenant_id' => $this->tenantId,
                    'event_id_in_tenant_db' => $this->eventId,
                    'error_trace' => $e->getTraceAsString(),
                ]);
                // Vous pouvez relancer l'exception ou la gérer d'une autre manière (ex: Job retries)
                throw $e;
            }
        });
    }
}
