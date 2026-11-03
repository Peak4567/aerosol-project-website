<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    protected $table = 'sensor';
    const CREATED_AT = 'create_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'temperature',
        'humidity',
        'tds_value',
        'ec_value',
        'ph_value',
        'create_at',
    ];

    protected function casts(): array
    {
        return [
            'temperature' => 'float',
            'humidity' => 'float',
            'tds_value' => 'float',
            'ec_value' => 'float',
            'ph_value' => 'float',
            'create_at' => 'datetime',
        ];
    }
}

