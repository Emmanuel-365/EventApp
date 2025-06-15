<?php

namespace App\Jobs;

use Illuminate\Support\Facades\File;
use Stancl\Tenancy\Contracts\Tenant;

class CreateTenantStorageDirectories
{
    protected Tenant $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle(): void
    {
        $tenantId = $this->tenant->getTenantKey();

        $paths = [
            storage_path("tenant{$tenantId}/framework/cache"),
            storage_path("tenant{$tenantId}/framework/sessions"),
            storage_path("tenant{$tenantId}/framework/views"),
        ];

        foreach ($paths as $path) {
            File::ensureDirectoryExists($path);
        }
    }
}
