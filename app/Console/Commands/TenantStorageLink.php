<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Organization;

class TenantStorageLink extends Command
{
    protected $signature = 'tenant:storage:link';
    protected $description = 'Créer des liens symboliques pour les dossiers de stockage des tenants';

    public function handle(): int
    {
        $tenancy = app(Organization::class);

        $tenancy->all()->each(function (Organization $tenant) {
            $tenantId = $tenant->getTenantKey();

            // Le dossier que Laravel peut servir via public/storage
            $storagePath = storage_path("tenant{$tenantId}/app");

            // Le lien symbolique à créer
            $publicPath = public_path("storage/tenant{$tenantId}");

            // Crée le dossier source s’il n’existe pas
            File::ensureDirectoryExists($storagePath);

            // Supprime l'ancien lien symbolique s'il existe
            if (is_link($publicPath) || file_exists($publicPath)) {
                exec(PHP_OS_FAMILY === 'Windows' ? "rmdir \"{$publicPath}\"" : "rm -rf \"{$publicPath}\"");
            }


            // Crée le lien symbolique
            symlink($storagePath, $publicPath);

            $this->info("✔ Lien symbolique créé pour le tenant {$tenantId}");
        });

        $this->info("✅ Tous les liens symboliques ont été créés.");
        return self::SUCCESS;
    }
}
