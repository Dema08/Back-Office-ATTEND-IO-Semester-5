<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfidScan extends Model
{
    protected $table = 'rfid_scans';

    protected $fillable = [
        'uid_hex',
    ];
}
