<?php

namespace Newelement\Locations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    public function featuredImage()
    {
        return $this->hasOne('\Newelement\Neutrino\Models\ObjectMedia', 'object_id', 'id')->where(['object_type' => 'location', 'featured' => 1]);
    }

    public function level()
    {
        return $this->hasOne('\Newelement\locations\Models\LocationLevel');
    }

}
