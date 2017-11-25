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

        $this->publishes([
            __DIR__.'/config/filemanager.php' => config_path('filemanager.php')
        ], 'config');

        $this->app->bind(FilemanagerAPI::class, Filemanager::class);
    }

    public function register()
    {

    }
}