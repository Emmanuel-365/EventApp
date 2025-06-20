<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Tenant\Employee;
use App\Models\Tenant\Event;
use App\Models\Tenant\EventStatusHistory;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EventStatusService
{
    protected EventActivityService $eventActivityService;

    public function __construct(EventActivityService $eventActivityService)
    {
        $this->eventActivityService = $eventActivityService;
    }

    /**
     * Annule un événement.
     * Log les changements de statut.
     *
     * @param Event $event L'événement à annuler.
     * @param Authenticatable|null $actor L'utilisateur (Admin ou Employee) qui annule l'événement. Si null, on tente de récupérer l'utilisateur authentifié.
     * @param string $reason Le motif de l'annulation.
     * @return Event
     * @throws InvalidArgumentException|\Exception Si l'acteur n'a pas la permission ou si l'événement est déjà annulé.
     */
    public function cancelEvent(Event $event, ?Authenticatable $actor = null, string $reason): Event
    {
        if (!$actor) {
            $actor = Auth::guard('employee')->user() ?? Auth::guard('admin')->user();
        }

        if (!$actor) {
            throw new InvalidArgumentException("Impossible d'annuler l'événement '{$event->id}': aucun acteur n'a été fourni ou trouvé.");
        }

        // Seule la vérification de permission générale est maintenue
        if ($actor instanceof Admin && !$actor->can('cancel-event-admin')) { // Adapter la permission si un Admin peut annuler
            throw new InvalidArgumentException("L'administrateur n'a pas la permission d'annuler cet événement.");
        }
        if ($actor instanceof Employee && !$actor->can('cancel-event')) { // Permission pour l'employé/patron
            throw new InvalidArgumentException("L'employé n'a pas la permission d'annuler cet événement.");
        }


        if ($event->status === 'cancelled') {
            throw new InvalidArgumentException("L'événement est déjà annulé.");
        }

        $oldStatus = $event->status;
        $newStatus = 'cancelled';
        $actorType = $this->getUserGuard($actor);

        $this->logStatusChange(
            $event,
            'cancellation',
            $oldStatus,
            $newStatus,
            $reason,
            $actor
        );

        $this->eventActivityService->logFieldChange(
            $event,
            'status',
            $oldStatus,
            $newStatus,
            $actor,
            $reason
        );

        $event->update([
            'status' => $newStatus,
            'cancelled_reason' => $reason,
            'cancelled_by_type' => $actorType,
            'cancelled_by_id' => $actor->id,
        ]);

        return $event;
    }

    /**
     * Restaure un événement annulé, le remettant en statut 'active'.
     * Log les changements de statut.
     *
     * @param Event $event L'événement à restaurer.
     * @param Authenticatable|null $actor L'utilisateur (Admin ou Employee) qui restaure l'événement. Si null, on tente de récupérer l'utilisateur authentifié.
     * @param string $reason Le motif de la restauration (facultatif).
     * @return Event
     * @throws InvalidArgumentException|\Exception Si l'acteur n'a pas la permission ou si l'événement n'est pas annulé.
     */
    public function restoreEvent(Event $event, ?Authenticatable $actor = null, string $reason = "Événement restauré."): Event
    {
        if (!$actor) {
            $actor = Auth::guard('employee')->user() ?? Auth::guard('admin')->user();
        }

        if (!$actor) {
            throw new InvalidArgumentException("Impossible de restaurer l'événement '{$event->id}': aucun acteur n'a été fourni ou trouvé.");
        }

        // Seule la vérification de permission générale est maintenue
        if ($actor instanceof Admin && !$actor->can('restore-event-admin')) { // Adapter la permission si un Admin peut restaurer
            throw new InvalidArgumentException("L'administrateur n'a pas la permission de restaurer cet événement.");
        }
        if ($actor instanceof Employee && !$actor->can('restore-event')) { // Permission pour l'employé/patron
            throw new InvalidArgumentException("L'employé n'a pas la permission de restaurer cet événement.");
        }

        if ($event->status !== 'cancelled') {
            throw new InvalidArgumentException("L'événement n'est pas en statut 'cancelled' et ne peut être restauré.");
        }

        $oldStatus = $event->status;
        $newStatus = 'active';
        $actorType = $this->getUserGuard($actor);

        $this->logStatusChange(
            $event,
            'restoration',
            $oldStatus,
            $newStatus,
            $reason,
            $actor
        );

        $this->eventActivityService->logFieldChange(
            $event,
            'status',
            $oldStatus,
            $newStatus,
            $actor,
            $reason
        );

        $event->update([
            'status' => $newStatus,
            'cancelled_reason' => null,
            'cancelled_by_type' => null,
            'cancelled_by_id' => null,
        ]);

        return $event;
    }

    /**
     * Retourne une requête Eloquent pour l'historique des statuts d'un événement.
     * Permet d'appliquer des filtres et du tri après.
     *
     * @param Event $event
     * @return Builder
     */
    public function getStatusHistoryQuery(Event $event): Builder
    {
        $query = EventStatusHistory::query()
            ->where('event_id', $event->id);

        $query->with([
            'changerEmployee' => function ($q) {
                $q->select('id', 'name');
            }
        ]);

        $query->latest();

        return $query;
    }

    /**
     * Log un changement de statut dans la table d'historique des événements.
     *
     * @param Event $event
     * @param string $statusType Le type de changement de statut (ex: 'cancellation', 'restoration').
     * @param string|null $oldStatus L'ancien statut, null si premier statut.
     * @param string $newStatus Le nouveau statut.
     * @param string $reason Le motif du changement.
     * @param Authenticatable $actor L'utilisateur qui a effectué le changement.
     * @return EventStatusHistory
     */
    protected function logStatusChange(
        Event $event,
        string $statusType,
        ?string $oldStatus,
        string $newStatus,
        string $reason,
        Authenticatable $actor
    ): EventStatusHistory {
        return EventStatusHistory::create([
            'event_id' => $event->id,
            'status_type' => $statusType,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'reason' => $reason,
            'changed_by_id' => $actor->id,
            'changed_by_type' => $this->getUserGuard($actor),
        ]);
    }

    /**
     * Détermine le guard d'un modèle utilisateur donné.
     *
     * @param Authenticatable|Model $user
     * @return string|null
     */
    private function getUserGuard(Authenticatable|Model $user): ?string
    {
        return match (true) {
            $user instanceof Admin => 'admin',
            $user instanceof Employee => 'employee',
            default => null,
        };
    }
}
