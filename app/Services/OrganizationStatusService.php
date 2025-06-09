<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Organization;
use App\Models\Organizer;
use App\Models\OrganizationStatusHistory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder;

class OrganizationStatusService
{
    /**
     * Valide une organisation en la passant de 'pending' à 'accepted' et 'enabled'.
     * Log les changements de statut.
     *
     * @param Organization $organization L'organisation à valider.
     * @param Admin $admin L'administrateur qui effectue la validation.
     * @return Organization
     * @throws InvalidArgumentException Si l'admin n'a pas la permission ou si l'organisation n'est pas en attente.
     */
    public function acceptOrganization(Organization $organization, Admin $admin): Organization
    {
        if (!$admin->can('validate-organization')) {
            throw new InvalidArgumentException("L'administrateur n'a pas la permission de valider cette organisation.");
        }

        if ($organization->validation_status !== 'pending') {
            throw new InvalidArgumentException("L'organisation n'est pas en statut 'pending' et ne peut être acceptée.");
        }

        $this->logStatusChange(
            $organization,
            'validation',
            $organization->validation_status,
            'accepted',
            "Organisation acceptée par l'administrateur.",
            $admin
        );

        $this->logStatusChange(
            $organization,
            'activation',
            $organization->activation_status,
            'enabled',
            "Organisation activée par défaut suite à l'acceptation.",
            $admin
        );

        // Mise à jour de l'organisation
        $organization->update([
            'validation_status' => 'accepted',
            'rejected_reason' => null,
            'activation_status' => 'enabled',
            'disabled_reason' => null,
            'disabled_by_type' => null,
            'disabled_by_id' => null,
        ]);

        return $organization;
    }

    /**
     * Rejette une organisation en la passant de 'pending' à 'rejected'.
     * Log le changement de statut.
     *
     * @param Organization $organization L'organisation à rejeter.
     * @param Admin $admin L'administrateur qui effectue le rejet.
     * @param string $reason Le motif du rejet.
     * @return Organization
     * @throws InvalidArgumentException Si l'admin n'a pas la permission ou si l'organisation n'est pas en attente.
     */
    public function rejectOrganization(Organization $organization, Admin $admin, string $reason): Organization
    {
        if (!$admin->can('reject-organization')) {
            throw new InvalidArgumentException("L'administrateur n'a pas la permission de rejeter cette organisation.");
        }

        if ($organization->validation_status !== 'pending') {
            throw new InvalidArgumentException("L'organisation n'est pas en statut 'pending' et ne peut être rejetée. Seules les organisations en attente peuvent être rejetées.");
        }

        $this->logStatusChange(
            $organization,
            'validation',
            $organization->validation_status,
            'rejected',
            $reason,
            $admin
        );

        $organization->update([
            'validation_status' => 'rejected',
            'rejected_reason' => $reason,
        ]);

        return $organization;
    }

    /**
     * Désactive une organisation, quel que soit l'acteur.
     * Log le changement de statut.
     *
     * @param Organization $organization L'organisation à désactiver.
     * @param Authenticatable $actor L'utilisateur (Admin ou Organizer) qui effectue la désactivation.
     * @param string $reason Le motif de la désactivation.
     * @return Organization
     * @throws InvalidArgumentException Si l'acteur n'a pas la permission.
     */
    public function disableOrganization(Organization $organization, Authenticatable $actor, string $reason): Organization
    {
        // Vérification des permissions pour l'administrateur
        if ($actor instanceof Admin && !$actor->can('disable-organization')) {
            throw new InvalidArgumentException("L'administrateur n'a pas la permission de désactiver cette organisation.");
        }

        // Vérification du statut actuel
        if ($organization->activation_status === 'disabled') {
            throw new InvalidArgumentException("L'organisation est déjà désactivée.");
        }

        // Une organisation doit être acceptée pour être désactivée
        if ($organization->validation_status !== 'accepted') {
            throw new InvalidArgumentException("Une organisation doit être acceptée avant de pouvoir être désactivée.");
        }

        $oldStatus = $organization->activation_status;
        $newStatus = 'disabled';
        $actorType = $this->getUserGuard($actor);

        $this->logStatusChange(
            $organization,
            'activation',
            $oldStatus,
            $newStatus,
            $reason,
            $actor
        );

        $organization->update([
            'activation_status' => $newStatus,
            'disabled_reason' => $reason,
            'disabled_by_type' => $actorType,
            'disabled_by_id' => $actor->id,
        ]);

        return $organization;
    }

    /**
     * Active une organisation.
     * Log le changement de statut.
     *
     * @param Organization $organization L'organisation à activer.
     * @param Authenticatable $actor L'utilisateur (Admin ou Organizer) qui effectue l'activation.
     * @param string $reason Le motif de l'activation (facultatif, car souvent implicite).
     * @return Organization
     * @throws InvalidArgumentException Si l'acteur n'a pas la permission ou ne peut pas activer.
     */
    public function enableOrganization(Organization $organization, Authenticatable $actor, string $reason = "Organisation activée."): Organization
    {
        // Vérification du statut actuel
        if ($organization->activation_status === 'enabled') {
            throw new InvalidArgumentException("L'organisation est déjà activée.");
        }
        // Une organisation doit être acceptée pour être activée
        if ($organization->validation_status !== 'accepted') {
            throw new InvalidArgumentException("Une organisation doit être acceptée avant de pouvoir être activée.");
        }

        // Logique de permission basée sur qui a désactivé l'organisation
        if ($organization->disabled_by_type === 'admin') {
            if (!($actor instanceof Admin)) {
                throw new InvalidArgumentException("Seul un administrateur peut réactiver cette organisation car elle a été désactivée par un administrateur.");
            }
            if (!$actor->can('enable-organization')) {
                throw new InvalidArgumentException("L'administrateur n'a pas la permission d'activer cette organisation.");
            }
        } else {
            if ($actor instanceof Admin && !$actor->can('enable-organization')) {
                throw new InvalidArgumentException("L'administrateur n'a pas la permission d'activer cette organisation.");
            }
               }


        $oldStatus = $organization->activation_status;
        $newStatus = 'enabled';

        // Log le changement de statut
        $this->logStatusChange(
            $organization,
            'activation',
            $oldStatus,
            $newStatus,
            $reason,
            $actor
        );

        $organization->update([
            'activation_status' => $newStatus,
            'disabled_reason' => null,
            'disabled_by_type' => null,
            'disabled_by_id' => null,
        ]);

        return $organization;
    }

    /**
     * Retourne une requête Eloquent pour l'historique des statuts d'une organisation.
     * Permet d'appliquer des filtres et du tri après.
     *
     * @param Organization $organization
     * @return Builder
     */
    public function getStatusHistoryQuery(Organization $organization): Builder
    {

        $query = OrganizationStatusHistory::query()
            ->where('organization_id', $organization->id);

        $query->with([
            'changerAdmin' => function ($q) {
                $q->select('id', 'name');
            },
            'changerOrganizer' => function ($q) {
                $q->select('id', 'name');
            }
        ]);


        $query->latest();

        return $query;
    }
    /**
     * Vérifie si un organizer peut activer une organisation.
     * Utile pour la logique d'affichage des boutons dans le frontend.
     *
     * @param Organization $organization
     * @param Organizer $organizer
     * @return bool
     */
    public function canOrganizerEnable(Organization $organization, Organizer $organizer): bool
    {
        if ($organization->activation_status !== 'disabled' || $organization->validation_status !== 'accepted') {
            return false;
        }

        if ($organization->disabled_by_type === 'admin') {
            return false;
        }

        return $organization->organizer_id === $organizer->id;
    }

    /**
     * Log un changement de statut dans la table d'historique.
     *
     * @param Organization $organization
     * @param string $statusType 'validation' ou 'activation'
     * @param string|null $oldStatus L'ancien statut, null si premier statut.
     * @param string $newStatus Le nouveau statut.
     * @param string $reason Le motif du changement.
     * @param Authenticatable $actor L'utilisateur qui a effectué le changement.
     * @return OrganizationStatusHistory
     */
    protected function logStatusChange(
        Organization $organization,
        string $statusType,
        ?string $oldStatus,
        string $newStatus,
        string $reason,
        Authenticatable $actor
    ): OrganizationStatusHistory {
        return OrganizationStatusHistory::create([
            'organization_id' => $organization->id,
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
            $user instanceof Organizer => 'organizer',
            default => null,
        };
    }
}
