<?php
namespace Kaoken\LaravelDBEmailLog;

use Illuminate\Support\ServiceProvider;

class LaravelDBEmailLogServiceProvider extends ServiceProvider
{
    /**
     * The basic path of the library here.
     * @param string $path
     * @return string
     */
    protected function my_base_path(string $path=''): string
    {
        return __DIR__.'/../'.$path;
    }

    /**
     * The basic path of the library here.
     * @param string $path
     * @return string
     */
    protected function my_resources_path(string $path=''): string
    {
        return $this->my_base_path('resources/'.$path);
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->my_resources_path('views') => resource_path('views/vendor'),
                $this->my_base_path('database/migrations') => database_path('migrations'),
            ], 'db-email-log');
        }
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->configureMonologUsing(function($monolog) {
            $monolog->setHandlers([new LaravelDBEmailLogHandler()]);
            //$monolog->pushHandler(new LaravelDBEmailLogHandler());
        });
    }
}