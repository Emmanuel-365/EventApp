<?php

namespace App\Console\Commands;

use App\Models\Organization;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Jobs\CreateDatabase;
use Stancl\Tenancy\Jobs\MigrateDatabase;
use Stancl\Tenancy\Jobs\SeedDatabase;

class SetupTenants extends Command
{
    protected $signature = 'tenants:setup';
    protected $description = 'Create databases, run migrations, seed, and set up storage directories for all tenants';

    public function handle(): void
    {
        $tenants = Organization::all();

        foreach ($tenants as $tenant) {
            $database = $tenant->database()->getName();
            $tenantId = $tenant->id; // UUID du tenant

            $this->info("Processing tenant: {$tenantId}");

            // Créer la base de données si elle n'existe pas
            if (!$tenant->database()->manager()->databaseExists($database)) {
                $this->info("Creating database...");
                try {
                    CreateDatabase::dispatchSync($tenant);
                    $this->info("✅ Database created");
                } catch (\Exception $e) {
                    $this->error("❌ Failed to create database: " . $e->getMessage());
                    continue;
                }
            } else {
                $this->info("Database already exists. Skipping creation.");
            }

            // Créer les répertoires de stockage pour le tenant
            $this->info("Creating storage directories for tenant: {$tenantId}...");
            try {
                tenancy()->initialize($tenant);

                $paths = [
                    storage_path("tenant{$tenantId}/framework/cache"),
                    storage_path("tenant{$tenantId}/framework/sessions"),
                    storage_path("tenant{$tenantId}/framework/views"),
                ];

                foreach ($paths as $path) {
                    File::ensureDirectoryExists($path);
                }

                $this->info("Tenant storage path for: {$tenant}");
            } catch (\Exception $e) {
                $this->error("❌ Failed to create storage directories: " . $e->getMessage());
                continue;
            } finally {
                tenancy()->end(); // Revenir au contexte central
            }

            // Exécuter les migrations
            $this->info("Running migrations...");
            try {
                MigrateDatabase::dispatchSync($tenant);
                $this->info("✅ Migrations completed");
            } catch (\Exception $e) {
                $this->error("❌ Failed to migrate: " . $e->getMessage());
                continue;
            }

            // Exécuter le seeding
            $this->info("Seeding database...");
            try {
                SeedDatabase::dispatchSync($tenant);
                $this->info("✅ Database seeded");
            } catch (\Exception $e) {
                $this->error("❌ Failed to seed: " . $e->getMessage());
            }

            $this->info("---");
        }

        $this->info('Setup completed for all tenants.');
    }
}
