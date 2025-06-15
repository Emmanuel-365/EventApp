<?php

namespace App\Console\Commands;

use App\Models\Organization;
use Illuminate\Console\Command;
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
                tenancy()->initialize($tenant); // Initialiser le contexte du tenant
                $directories = [
                    'framework/cache',
                    'framework/sessions',
                    'framework/views',
                    'livewire-tmp',
                ];

                foreach ($directories as $dir) {
                    if (!Storage::disk('tenant')->exists($dir)) {
                        Storage::disk('tenant')->makeDirectory($dir);
                        $this->info("✅ Created directory: {$dir}");
                    } else {
                        $this->info("Directory already exists: {$dir}");
                    }
                }

                // Vérifier le chemin du disque tenant
                $tenantPath = Storage::disk('tenant')->path('');
                $this->info("Tenant storage path: {$tenantPath}");
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
