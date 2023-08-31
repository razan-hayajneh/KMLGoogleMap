<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KmlFile extends Model
{
    public $table = 'kml_files';

    public $fillable = [
        'file_path',
        'user_id'
    ];

    protected $casts = [
        'file_path' => 'string'
    ];

    public static array $rules = [
        'file_path' => 'required'
    ];
    /**
     * Get the user that owns the KMLFile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
