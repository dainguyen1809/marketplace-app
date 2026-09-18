<?php

namespace Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;

class InfrastructureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $configDir = base_path('infrastructure/Configs');
        if (is_dir($configDir)) {
            foreach (scandir($configDir) as $file) {
                if ($file === '.' || $file === '..' || pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                    continue;
                }
                $name = pathinfo($file, PATHINFO_FILENAME);

                $customConfig = require $configDir.'/'.$file;
                $existingConfig = $this->app['config']->get($name, []);

                $this->app['config']->set(
                    $name,
                    array_replace_recursive($existingConfig, $customConfig)
                );
            }
        }
    }

    public function boot()
    {
        // Load migrations
        $this->loadMigrationsFrom(base_path('infrastructure/Databases/Migrations'));

        // Publish configuration files for easy customization
        if ($this->app->runningInConsole()) {
            $configDir = base_path('infrastructure/Configs');
            if (is_dir($configDir)) {
                $publishes = [];
                foreach (scandir($configDir) as $file) {
                    if ($file === '.' || $file === '..' || pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                        continue;
                    }
                    $name = pathinfo($file, PATHINFO_FILENAME);
                    $publishes[$configDir.'/'.$file] = config_path($name.'.php');
                }
                $this->publishes($publishes, 'infrastructure-config');
            }
        }
    }
}
