<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WsmData extends Model
{
    protected $table = 'wsm_data';

    protected $fillable = ['user_id', 'criteria_data'];

    protected $casts = [
        'criteria_data' => 'array'
    ];
}
