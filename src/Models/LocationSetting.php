<?php

namespace Newelement\Locations\Models;

use Illuminate\Database\Eloquent\Model;

class LocationSetting extends Model
{

    public $timestamps = false;

    protected $fillable = ['name', 'value_string', 'value_bool'];

}
