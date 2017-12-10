<?php

use Illuminate\Support\Facades\Route;
use Dionyseos\Filemanager\HTTP\Controllers\FilemanagerController;

Route::middleware(config('filemanager.middleware.auth', 'auth'))->prefix('/services/filemanager/v1/')->group(function() {
    Route::post('{uploadParam}/{directory}', FilemanagerController::class.'@upload')
        ->name('filemanager.upload');

    Route::put('{fileId}', FilemanagerController::class.'@update')
        ->name('filemanager.edit');

    Route::get('', FilemanagerController::class.'@list')
        ->name('filemanager.list');

    Route::get('upload', function() {
        return view('filemanager::upload');
    })
        ->name('filemanager.view');
});


Route::get('/services/filemanager/v1/{fileId}/{thumb?}', FilemanagerController::class.'@show')
    ->name('filemanager.get')
    ->where(['id' => '[0-9]+']);

Route::get('/services/filemanager/preview/v1/{fileId}/', FilemanagerController::class.'@preview')
    ->name('filemanager.preview');