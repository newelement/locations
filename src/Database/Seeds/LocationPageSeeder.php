<?php

use Illuminate\Database\Seeder;
use Newelement\Neutrino\Models\Page;

class LocationPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Page::firstOrCreate(
            [ 'slug' => 'locations-landing-page' ],
            [
                'title' => 'Locations Landing Page',
                'status' => 'P',
                'parent_id' => 0,
                'content' => 'DO NOT DELETE',
                'system_page' => 1
            ]
        );
    }

}
