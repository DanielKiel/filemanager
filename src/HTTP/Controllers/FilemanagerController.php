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

    /**
     * FilemanagerController constructor.
     * @param FilemanagerAPI $api
     */
    public function __construct(FilemanagerAPI $api)
    {
        $this->api = $api;
    }

    /**
     * @param Request $request
     * @return mixed
     */
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

        $dir = $request->input('dir');
        if (! empty($dir)) {
            $query = $query->where(function($query) use($dir) {
                $query->orWhere('dir', 'LIKE',  $dir . '%')
                    ->orWhere('dir', $dir);
            });
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
    public function upload(Request $request, string $uploadParam, string $directory = '')
    {
        if (empty($directory)) {
            $directory = $request->input('directory', 'uploads');
        }

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
     * @param $fileId
     * @return string
     */
    public function preview($fileId)
    {
        $file = File::find($fileId);

        return $this->api->preview($file);
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