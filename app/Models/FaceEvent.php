<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaceEvent extends Model
{
    protected $fillable = [
        'device_id',
        'nim',
        'predicted_label',
        'confidence',
        'image_path',
        'raw_payload',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'confidence'  => 'float',
    ];
}
