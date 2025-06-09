<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Ban;
use App\Models\Client;
use App\Models\Organizer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;

class BanService
{
    /**
     * Bannit un utilisateur spécifique sous un certain guard.
     * Enregistre l'utilisateur (admin, etc.) qui a effectué l'action.
     * @param Authenticatable $user L'instance de l'utilisateur à bannir.
     * @param string $motif Le motif du bannissement (maintenant obligatoire).
     * @param Authenticatable $banner L'instance de l'utilisateur qui effectue le bannissement (ex: Admin, Organizer).
     * @return Ban L'instance du ban créé.
     */
    public function banUser(Authenticatable $user, string $motif, Authenticatable $banner): Ban
    {
        $bannerId = $banner->id;
        $bannerGuard = $this->getUserGuard($banner);

        return Ban::create([
            'user_id' => $user->id,
            'guard' => $this->getUserGuard($user),
            'motif' => $motif,
            'banned_by' => $bannerId,
            'banner_guard' => $bannerGuard,
        ]);
    }

    /**
     * Débannit un utilisateur en soft-supprimant le ban le plus récent.
     * Enregistre l'utilisateur (admin, etc.) qui a effectué l'action de débannissement.
     *
     * @param Authenticatable $user L'instance de l'utilisateur qui est à débannir.
     * @param Authenticatable $unbanner L'instance de l'utilisateur qui effectue le débannissement.
     * @return bool Vrai si un ban a été débanni, faux sinon.
     */
    public function unbanUser(Authenticatable $user, Authenticatable $unbanner): bool
    {
        $unbannerId = $unbanner->id;
        $unbannerGuard = $this->getUserGuard($unbanner);

        $ban = Ban::where('user_id', $user->id)
            ->where('guard', $this->getUserGuard($user))
            ->whereNull('deleted_at')
            ->latest()
            ->first();

        if ($ban) {
            $ban->unbanned_by_id = $unbannerId;
            $ban->unbanned_guard = $unbannerGuard;
            $ban->save();

            $ban->delete();
            return true;
        }

        return false;
    }

    /**
     * Vérifie si un utilisateur est actuellement banni.
     *
     * @param Authenticatable $user
     * @return bool Vrai si l'utilisateur est banni (soft-delete non appliqué), faux sinon.
     */
    public function isUserBanned(Authenticatable $user): bool
    {
        return Ban::where('user_id', $user->id)
            ->where('guard', $this->getUserGuard($user))
            ->whereNull('deleted_at')
            ->exists();
    }

    /**
     * Récupère l'historique complet des bannissements d'un utilisateur (y compris les bans débannis).
     *
     * @param Authenticatable $user
     * @return Collection Collection de modèles Ban.
     */
    public function getBanHistory(Authenticatable $user): Collection
    {
        return Ban::where('user_id', $user->id)
            ->where('guard', $this->getUserGuard($user))
            ->withTrashed()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Détermine le guard d'un modèle utilisateur donné.
     *
     * @param Authenticatable|Model $user
     * @return string|null
     */
    public function getUserGuard(Authenticatable|Model $user): ?string
    {
        return match (true) {
            $user instanceof Admin => 'admin',
            $user instanceof Organizer => 'organizer',
            $user instanceof Client => 'client',
            default => null,
        };
    }

    /**
     * Récupère le modèle d'utilisateur en fonction de l'ID et du guard.
     *
     * @param int $userId
     * @param string $guard
     * @return Model|null
     */
    public function getUserModel(int $userId, string $guard): ?Model
    {
        return match ($guard) {
            'organizer' => Organizer::find($userId),
            'admin' => Admin::find($userId),
            'client' => Client::find($userId),
            default => null,
        };
    }

}

