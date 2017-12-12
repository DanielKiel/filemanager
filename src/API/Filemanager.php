<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.11.17
 * Time: 16:09
 */

namespace Dionyseos\Filemanager\API;


use Dionyseos\Filemanager\API\Filemanager as FilemanagerAPI;
use Dionyseos\Filemanager\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

interface Filemanager
{
    /**
     * @param $file
     * @param string $directory
     * @return File
     */
    public function upload($file, string $directory): File;

    /**
     * @param File $file
     * @return string
     */
    public function getRaw(File  $file): string;

    /**
     * @param File $file
     * @return string
     */
    public function getThumb(File $file): string;

    /**
     * @param File $file
     * @return string
     */
    public function preview(File $file): string;
}