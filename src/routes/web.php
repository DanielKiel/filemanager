<?php

use Illuminate\Support\Facades\Route;
use Dionyseos\Filemanager\HTTP\Controllers\FilemanagerController;

Route::post('/service/filemanager/v1/{uploadParam}/{directory}', FilemanagerController::class.'@upload')
    ->name('filemanager.upload');

Route::get('/service/filemanager/v1/{fileId}/{thumb?}', FilemanagerController::class.'@show')
    ->name('filemanager.get');