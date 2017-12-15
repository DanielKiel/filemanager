<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.11.17
 * Time: 16:04
 */

namespace Dionyseos\Filemanager\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class File extends Model
{
    protected $table = 'files';

    protected $fillable = [
        'path', 'name', 'extension', 'data', 'published', 'dir', 'disk'
    ];

    protected $casts = [
        'data' => 'object',
        'published' => 'boolean'
    ];

    public function scopePublished(Builder $query, $published = true)
    {
        return $query->where('published', $published);
    }
}