<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.11.17
 * Time: 16:09
 */

namespace Dionyseos\Filemanager\Repositories;


use Dionyseos\Filemanager\API\Filemanager as FilemanagerAPI;
use Dionyseos\Filemanager\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Filemanager implements FilemanagerAPI
{

    public function upload($file, string $directory)
    {
        $path =  Storage::putFile( $directory, $file);

        if ($file instanceof UploadedFile) {
            $name = $file->getClientOriginalName();
        }
        else {
            $name = $file->name;
        }

        $fileObj = new \Illuminate\Http\File($file->getRealPath());

        $extension = $file->clientExtension();

        return File::create([
            'path' => $path,
            'name' => $name,
            'extension' => $extension,
            'data' => [
                'mimeType' => $fileObj->getMimeType(),
                'ctime' => $fileObj->getCTime(),
                'hashName' => $fileObj->hashName(),
                'atime' => $fileObj->getATime(),
                'size' => $fileObj->getSize()
            ]
        ]);
    }

    public function getRaw(File  $file)
    {
        return Cache::rememberForever('filemanager_raw_' . $file->id, function() use($file) {
            return base64_encode(Storage::get($file->path));
        });
    }

    public function getThumb(File $file)
    {
        return Cache::rememberForever('filemanager_thumb_' . $file->id, function() use($file) {
            return base64_encode(Image::make(Storage::get($file->path))
                ->widen(config('filemanager.widen', 180))->stream());
        });
    }

}