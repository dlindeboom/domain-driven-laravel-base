<?php

declare(strict_types=1);

namespace App\Framework\Providers;

use App\Framework\Services\ModulesDirectoryService;
use Illuminate\Support\ServiceProvider;

class MigrationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (app()->runningInConsole()) {
            $this->registerMigrations();
        }
    }

    /**
     * Register Sanctum's migration files.
     *
     * @return void
     */
    protected function registerMigrations(): void
    {
        $domainDirectoryService = app(ModulesDirectoryService::class);
        $domains = $domainDirectoryService->listDomainPaths();

        foreach ($domains as $domainPath) {
            $migrationsPath = $domainPath . '/Database/Migrations/';

            // check if migrations directory exists
            if (!is_dir($migrationsPath)) {
                continue;
            }

            $this->loadMigrationsFrom($migrationsPath);
        }
    }
}
