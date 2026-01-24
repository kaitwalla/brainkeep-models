<?php

namespace Brainkeep\Models;

use Illuminate\Support\ServiceProvider;

class BrainkeepModelsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/brainkeep-models.php', 'brainkeep-models');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/brainkeep-models.php' => config_path('brainkeep-models.php'),
            ], 'brainkeep-models-config');
        }
    }
}
