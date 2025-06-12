<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stancl\Tenancy\Database\Models\Domain;
use Symfony\Component\Console\Command\Command as CommandAlias;



class ListTenantDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:list-domains';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lists all configured tenant domains and their associated tenant IDs.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $domains = Domain::all(['domain', 'tenant_id']);

        if ($domains->isEmpty()) {
            $this->info('No tenant domains found in the database.');
            return CommandAlias::SUCCESS;
        }

        $this->info('--- Configured Tenant Domains ---');

        $headers = ['Domain', 'Tenant ID'];
        $data = $domains->map(function ($domain) {
            return [$domain->domain, $domain->tenant_id];
        })->toArray();

        $this->table($headers, $data);

        $this->info("\nNote: These domains need to be configured in your system's hosts file or DNS for local access.");

        return CommandAlias::SUCCESS;
    }
}
