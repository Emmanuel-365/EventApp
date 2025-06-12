<?php

namespace App\Console\Commands;

use App\Models\Organization;
use Illuminate\Console\Command;
use Stancl\Tenancy\Jobs\CreateDatabase;
use Stancl\Tenancy\Jobs\MigrateDatabase;
use Stancl\Tenancy\Jobs\SeedDatabase;

class SetupTenants extends Command
{
    protected $signature = 'tenants:setup';
    protected $description = 'Create databases, run migrations and seed for all tenants';

    public function handle(): void
    {
        $tenants = Organization::all();

        foreach ($tenants as $tenant) {
            $database = $tenant->database()->getName();

            $this->info("Processing tenant: {$tenant->id}");

            // Créer la base de données si elle n'existe pas
            if (!$tenant->database()->manager()->databaseExists($database)) {
                $this->info("Creating database...");
                try {
                    CreateDatabase::dispatchSync($tenant);
                    $this->info("✅ Database created");
                } catch (\Exception $e) {
                    $this->error("❌ Failed to create database: " . $e->getMessage());
                    continue; // Passe au tenant suivant si la création de la BD échoue
                }
            } else {
                $this->info("Database already exists. Skipping creation.");
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

            // --- Ajout du seeding ---
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
