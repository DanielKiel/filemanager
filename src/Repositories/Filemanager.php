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
    /**
     * @param $file
     * @param string $directory
     * @return File
     */
    public function upload($file, string $directory): File
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

    /**
     * @param File $file
     * @return string
     */
    public function getRaw(File  $file): string
    {
        return Cache::rememberForever('filemanager_raw_' . $file->id, function() use($file) {
            return base64_encode(Storage::get($file->path));
        });
    }

    /**
     * @param File $file
     * @return string
     */
    public function getThumb(File $file): string
    {
        return Cache::rememberForever('filemanager_thumb_' . $file->id, function() use($file) {
            return base64_encode(Image::make(Storage::get($file->path))
                ->widen(config('filemanager.widen', 180))->stream());
        });
    }

    /**
     * @param File $file
     * @return string
     */
    public function preview(File $file): string
    {
        if ($file->extension === 'pdf') {

            $parser = new Parser();

            $pdf = $parser->parseContent(Storage::get($file->path));
            // Retrieve all pages from the pdf file.
            $pages  = $pdf->getPages();

            $text = '<h3>Textversion:</h3>';
            foreach ($pages as $key => $page) {
                $site = $key + 1;
                $text .=  '<br/><strong>Seite ' . $site. '</strong><br/>';
                $text .= $page->getText() . '<br/>';
            }

            return $text;
        }

        if (property_exists($file->data, 'mimeType')) {
            if (str_is('image*', $file->data->mimeType)) {
                return '<img src="data:' . $file->data->mimeType . ';base64,' . base64_encode(Storage::get($file->path)) .'">';
            }

        }

        return Storage::get($file->path);
    }

}