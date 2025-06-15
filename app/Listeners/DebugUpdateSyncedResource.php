<?php

namespace App\Listeners;

use App\Models\Tenant\Patron;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Events\SyncedResourceSaved;
use Stancl\Tenancy\Listeners\UpdateSyncedResource;

class DebugUpdateSyncedResource extends UpdateSyncedResource implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SyncedResourceSaved $event): void
    {
        Log::info('DEBUG SYNC: DebugUpdateSyncedResource listener STARTED for SyncedResourceSaved event.', [
            'central_model_class' => get_class($event->model),
            'central_model_id' => $event->model->getGlobalIdentifierKey(),
        ]);

        try {
            // Loguer l'état de l'organizer_global_id avant que Stancl n'agisse.
            // Ceci est une *prédiction* de ce que le Patron devrait avoir.
            $predicted_organizer_global_id = $event->model->getGlobalIdentifierKey();
            Log::info('DEBUG SYNC: Predicted organizer_global_id for Patron (from Central Organizer):', [
                'predicted_organizer_global_id' => $predicted_organizer_global_id,
            ]);

            // Appelez la méthode handle() du parent pour exécuter la logique de synchronisation de Stancl.
            // C'est cette méthode qui va créer ou mettre à jour le modèle dans le tenant.
            parent::handle($event);

            // APRÈS l'appel à parent::handle($event), Stancl a mis à jour/créé le modèle.
            // Cependant, tenter de le lire immédiatement via une nouvelle requête
            // peut échouer si les migrations ne sont pas encore terminées (comme ici).
            // Nous allons donc nous fier au fait que Stancl a fait son travail.
            // Nous ne pouvons pas le vérifier directement ici si la table n'existe pas.

            Log::info('DEBUG SYNC: parent::handle($event) executed successfully. Synchronization attempt made.', [
                'central_model_id' => $event->model->getGlobalIdentifierKey(),
            ]);

            // *** COMMENTER OU SUPPRIMER CE BLOC POUR L'INSTANT ***
            // La logique suivante déclenche "no such table"
            /*
            $tenantModelClass = $event->model->getTenantModelName();
            $globalIdentifierKeyName = (new $tenantModelClass())->getGlobalIdentifierKeyName();
            $globalIdentifierValue = $event->model->getGlobalIdentifierKey();

            $patron = tenancy()->central(function ($tenant) use ($tenantModelClass, $globalIdentifierKeyName, $globalIdentifierValue) {
                return $tenantModelClass::where($globalIdentifierKeyName, $globalIdentifierValue)->first();
            });

            if ($patron) {
                Log::info('DEBUG SYNC: ETAT DU PATRON DANS LA DB DU TENANT APRES SYNCHRONISATION (SI TABLE EXISTE):', [
                    'patron_id_in_db' => $patron->id,
                    'patron_email_in_db' => $patron->email,
                    'organizer_global_id_in_db' => $patron->organizer_global_id,
                    'profile_verification_status_in_db' => $patron->profile_verification_status,
                    'synced_at' => now()->toDateTimeString(),
                ]);
            } else {
                Log::warning('DEBUG SYNC: Patron NOT FOUND in tenant DB after sync attempt (table might not exist yet).', [
                    'global_identifier_key_name' => $globalIdentifierKeyName,
                    'global_identifier_value' => $globalIdentifierValue,
                ]);
            }
            */

        } catch (\Throwable $e) {
            Log::error('DEBUG SYNC: DebugUpdateSyncedResource listener FAILED UNEXPECTEDLY.', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
