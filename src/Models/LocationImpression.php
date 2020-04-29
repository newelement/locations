<?php

namespace Newelement\Locations\Models;

use Illuminate\Database\Eloquent\Model;

class LocationImpression extends Model
{

    public function location()
    {
        return $this->belongsTo('\Newelement\Locations\Models\Location');
    }
}
