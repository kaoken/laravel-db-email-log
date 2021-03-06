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
    protected function getMyBasePath(string $path=''): string
    {
        return __DIR__.'/../'.$path;
    }

    /**
     * The basic path of the library here.
     * @param string $path
     * @return string
     */
    protected function getMyResourcesPath(string $path=''): string
    {
        return $this->getMyBasePath('resources/'.$path);
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
                $this->getMyResourcesPath('views') => resource_path('views/vendor'),
                $this->getMyBasePath('database/migrations') => database_path('migrations'),
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
//        $config = $this->app->make('config');
//        $config->set("logging.channels", array_merge(
//            [
//                'db_log' => [
//                    'driver'  => 'monolog',
//                    'handler' => Kaoken\LaravelDBEmailLog\LaravelDBEmailLogHandler::class,
//                    'level' => env('LOG_LEVEL', 'debug'),
//                ]
//            ],
//            $config->get("logging.channels", [])
//        ));
    }
}
