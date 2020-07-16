<?php

namespace Newelement\Locations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Newelement\Searchable\SearchableTrait;

class Location extends Model
{
    use SoftDeletes, SearchableTrait;

    protected $searchable = [
        'columns' => [
            'title' => 7,
            'street' => 5,
            'city' => 5,
            'state' => 5,
            'postal' => 5,
            'phone' => 3,
            'email' => 3
        ],
    ];

    public function featuredImage()
    {
        return $this->hasOne('\Newelement\Neutrino\Models\ObjectMedia', 'object_id', 'id')->where(['object_type' => 'location', 'featured' => 1]);
    }

    public function level()
    {
        return $this->hasOne('\Newelement\Locations\Models\LocationLevel', 'id', 'location_level_id');
    }

    public function url()
    {
        return '/'.config('locations.locations_slug', 'locations').'/'.$this->slug;
    }

}
