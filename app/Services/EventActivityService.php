<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Tenant\Employee;
use App\Models\Tenant\Event;
use App\Models\Tenant\EventActivityLog;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EventActivityService
{
    /**
     * Enregistre une modification de champ pour un événement dans l'historique d'activité.
     * C'est aussi utilisé pour logger la création d'un événement en utilisant 'created_at'.
     *
     * @param Event $event L'événement modifié.
     * @param string $fieldName Le nom du champ modifié.
     * @param mixed $oldValue L'ancienne valeur du champ.
     * @param mixed $newValue La nouvelle valeur du champ.
     * @param Authenticatable|null $actor L'utilisateur qui a effectué le changement. Si null, on tente de récupérer l'utilisateur authentifié.
     * @param string|null $reason La raison du changement (facultatif).
     * @return EventActivityLog
     * @throws \Exception Si aucun acteur ne peut être déterminé.
     */
    public function logFieldChange(
        Event $event,
        string $fieldName,
        $oldValue,
        $newValue,
        ?Authenticatable $actor = null,
        ?string $reason = null
    ): EventActivityLog {
        if (!$actor) {
            $actor = Auth::guard('employee')->user() ?? Auth::guard('admin')->user();
        }

        if (!$actor) {
            throw new \Exception("Impossible de logguer le changement d'activité pour l'événement '{$event->id}': aucun acteur n'a été fourni ou trouvé via les guards 'employee' ou 'admin'.");
        }

        return EventActivityLog::create([
            'event_id' => $event->id,
            'field_name' => $fieldName,
            'old_value' => is_array($oldValue) || is_object($oldValue) ? json_encode($oldValue) : $oldValue,
            'new_value' => is_array($newValue) || is_object($newValue) ? json_encode($newValue) : $newValue,
            'changed_by_id' => $actor->id,
            'changed_by_type' => $this->getUserGuard($actor),
            'reason' => $reason,
        ]);
    }

    /**
     * Spécialement pour logger la création d'un événement.
     *
     * @param Event $event
     * @param Authenticatable|null $actor
     * @return EventActivityLog
     * @throws \Exception
     */
    public function logEventCreation(Event $event, ?Authenticatable $actor = null): EventActivityLog
    {
        return $this->logFieldChange(
            $event,
            'created_at',
            'indéfini', // Ancienne valeur pour la création
            $event->created_at->toDateTimeString(), // La valeur réelle de created_at
            $actor,
            'Création de l\'événement'
        );
    }

    /**
     * Retourne une requête Eloquent pour l'historique des activités d'un événement.
     * Permet d'appliquer des filtres et du tri après.
     *
     * @param Event $event
     * @return Builder
     */
    public function getActivityHistoryQuery(Event $event): Builder
    {
        $query = EventActivityLog::query()
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
