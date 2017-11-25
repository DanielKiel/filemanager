<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.11.17
 * Time: 16:04
 */

namespace Dionyseos\Filemanager\Models;


use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';

    protected $fillable = [
        'path', 'name', 'extension', 'data'
    ];

    protected $casts = [
        'data' => 'object'
    ];
}