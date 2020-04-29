<?php

namespace Newelement\Locations\Models;

use Illuminate\Database\Eloquent\Model;

class LocationRequest extends Model
{

    public function location()
    {
        return $this->belongsTo('\Newelement\Locations\Models\Location');
    }
}
