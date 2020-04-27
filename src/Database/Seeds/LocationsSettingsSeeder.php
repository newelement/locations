<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Newelement\Locations\Models\LocationSetting;

class LocationsSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LocationSetting::firstOrCreate(
            [ 'name' => 'pin_image' ],
            [ 'value_bool' => 0 ]
        );
        LocationSetting::firstOrCreate(
            [ 'name' => 'pin_color' ],
            [ 'value_string' => '#000000' ]
        );
        LocationSetting::firstOrCreate(
            [ 'name' => 'pin_label_color' ],
            [ 'value_string' => '#ffffff' ]
        );
        LocationSetting::firstOrCreate(
            [ 'name' => 'default_radius' ],
            [ 'value_string' => '25' ]
        );
        LocationSetting::firstOrCreate(
            [ 'name' => 'show_level' ],
            [ 'value_bool' => 0 ]
        );

        LocationSetting::firstOrCreate(
            [ 'name' => 'marker_size_width' ],
            [ 'value_string' => '30' ]
        );

        LocationSetting::firstOrCreate(
            [ 'name' => 'marker_size_height' ],
            [ 'value_string' => '40' ]
        );

        LocationSetting::firstOrCreate(
            [ 'name' => 'marker_origin_x' ],
            [ 'value_string' => '0' ]
        );

        LocationSetting::firstOrCreate(
            [ 'name' => 'marker_origin_y' ],
            [ 'value_string' => '0' ]
        );

        LocationSetting::firstOrCreate(
            [ 'name' => 'marker_anchor_x' ],
            [ 'value_string' => '10' ]
        );

        LocationSetting::firstOrCreate(
            [ 'name' => 'marker_anchor_y' ],
            [ 'value_string' => '34' ]
        );

        LocationSetting::firstOrCreate(
            [ 'name' => 'marker_label_x' ],
            [ 'value_string' => '15' ]
        );

        LocationSetting::firstOrCreate(
            [ 'name' => 'marker_label_y' ],
            [ 'value_string' => '15' ]
        );

        /*LocationSetting::firstOrCreate(
            [
                'name' => 'pin_image',
                'value_string' => '',
                'value_bool' => 0,
            ]
        );*/

    }
}
