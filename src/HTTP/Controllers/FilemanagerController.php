<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.11.17
 * Time: 16:08
 */

namespace Dionyseos\Filemanager\HTTP\Controllers;


use App\Http\Controllers\Controller;
use Dionyseos\Filemanager\API\Filemanager as FilemanagerAPI;
use Dionyseos\Filemanager\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class FilemanagerController extends Controller
{
    public $api;

    public function __construct(FilemanagerAPI $api)
    {
        $this->api = $api;
    }

    public function list(Request $request)
    {
        $query = new File();

        $orderBy = explode(' ',$request->input('orderBy', 'id DESC'));
        $query = call_user_func_array([$query, 'orderBy'], $orderBy);

        $status = $request->input('published');

        if (! empty($status)) {
            $statement = explode(' ', $status);
            array_unshift($statement, 'published');
            $query = call_user_func_array([$query, 'where'], $statement);
        }


        return $query
            ->paginate($request->input('per_page', 25));
    }

    /**
     * @param Request $request
     * @param string $uploadParam
     * @param string $directory
     * @return mixed
     */
    public function upload(Request $request, string $uploadParam, string $directory)
    {
        return $this->api->upload($request->file($uploadParam), $directory);
    }

    /**
     * @param $fileId
     * @param int $thumb
     * @return mixed
     */
    public function show( $fileId, $thumb = 1)
    {
        $file = File::find($fileId);

        if ( (bool) $thumb === false ) {
            return $this->api->getRaw($file);
        }
        else {
            return $this->api->getThumb($file);

        }
    }

    public function preview( $fileId)
    {
        $file = File::find($fileId);

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

    /**
     * @param Request $request
     * @param $fileId
     * @return mixed
     */
    public function update(Request $request, $fileId)
    {
        $file = File::find($fileId);

        if (! $file instanceof File) {
            return response('something went wrong', 422);
        }

        $result = $file->update($request->all());

        if ( (bool) $result === true ) {
            return $file->fresh();
        }

        return response('something went wrong', 422);
    }

}