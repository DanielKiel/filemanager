<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.11.17
 * Time: 15:56
 */

namespace Dionyseos\Filemanager;


use Illuminate\Support\ServiceProvider as BaseProvider;
use Dionyseos\Filemanager\API\Filemanager as FilemanagerAPI;
use Dionyseos\Filemanager\Repositories\Filemanager;

class ServiceProvider extends BaseProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'filemanager');

        $this->publishes([
            __DIR__.'/config/filemanager.php' => config_path('filemanager.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/database/migrations/' => database_path('migrations')
        ], 'migrations');

        $this->app->bind(FilemanagerAPI::class, Filemanager::class);

        $this->publishes([
            __DIR__.'/resources/views' => base_path('resources/views/vendor/dionyseos/filemanager'),
        ], 'filemanager-views');

        $this->publishes([
            __DIR__.'/resources/assets/js/components' => base_path('resources/assets/js/components/filemanager'),
        ], 'filemanager-components');
    }

    public function register()
    {

    }
}