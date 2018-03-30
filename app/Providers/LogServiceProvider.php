<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Configure logging on boot.
     *
     * @return void
     */
    public function boot() {
        $maxFiles = env('LOG_RETENTION', 7);

        // Allow the log path to be configurable
        $logPath = env('LOG_PATH', storage_path("logs"));

        $handlers[] = (new RotatingFileHandler($logPath . "/debug.log", $maxFiles))
            ->setFormatter(new LineFormatter(null, null, true, true));

        $this->app['log']->setHandlers($handlers);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
    }
}
