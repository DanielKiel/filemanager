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

class FilemanagerController extends Controller
{
    public $api;

    public function __construct(FilemanagerAPI $api)
    {
        $this->api = $api;
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