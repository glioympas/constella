<?php

namespace Lioy\Constella;

use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Lioy\Constella\Console\GenerateModelColumns;
use Lioy\Constella\Console\GenerateModelRelations;
use Lioy\Constella\Tests\Unit\Console\GenerateModelRelationsTest;
use Symfony\Component\Console\Output\ConsoleOutput;

class ConstellaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'constella');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateModelColumns::class,
                GenerateModelRelations::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('constella.php'),
            ], 'config');
        }

        Event::listen(MigrationsEnded::class, function () {
            if (config('app.env') != 'local') {
                return;
            }

            Artisan::call(
                command: GenerateModelColumns::class,
                outputBuffer: new ConsoleOutput
            );
        });
    }
}
