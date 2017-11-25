<?php

use Illuminate\Support\Facades\Route;
use Dionyseos\Filemanager\HTTP\Controllers\FilemanagerController;

Route::middleware(config('filemanager.middleware.auth', 'auth'))->prefix('/service/filemanager/v1/')->group(function() {
    Route::post('{uploadParam}/{directory}', FilemanagerController::class.'@upload')
        ->name('filemanager.upload');

    Route::put('{fileId}', FilemanagerController::class.'@update')
        ->name('filemanager.edit');
});



Route::get('/service/filemanager/v1/{fileId}/{thumb?}', FilemanagerController::class.'@show')
    ->name('filemanager.get');