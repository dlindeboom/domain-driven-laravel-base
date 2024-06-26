<?php

declare(strict_types=1);

namespace App\Framework\Providers;

use App\Framework\Services\ModulesDirectoryService;
use Illuminate\View\FileViewFinder;
use Illuminate\View\ViewServiceProvider as ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerFactory();
        $this->registerViewFinder();
        $this->registerBladeCompiler();
        $this->registerEngineResolver();
    }

    public function registerViewFinder(): void
    {
        $domainDirectoryService = app(ModulesDirectoryService::class);
        $modulesMakerViews = $this->getDomainViewPaths($domainDirectoryService);

        $this->app->bind('view.finder', function ($app) use ($modulesMakerViews) {
            $paths = $app['config']['view.paths'];

            foreach ($modulesMakerViews as $path) {
                $paths[] = $path;
            }

            return new FileViewFinder($app['files'], $paths);
        });
    }

    /**
     * @return string[]
     * @throws \Exception
     */
    public function getDomainViewPaths(ModulesDirectoryService $domainDirectoryService): array
    {
        $viewPaths = [];
        $listDomainPaths = $domainDirectoryService->listDomainPaths();

        foreach ($listDomainPaths as $domain) {
            $resourcedDir = $domain . '/resources';
            $viewDir = $resourcedDir . '/views';

            // checking if resources and views/resources directories exist
            if (!is_dir($resourcedDir) || !is_dir($viewDir)) {
                continue;
            }

            $viewPaths[] = $viewDir;
        }

        // Add Frontend views
        $viewPaths[] = app_path('Frontend/views');

        return $viewPaths;
    }
}
