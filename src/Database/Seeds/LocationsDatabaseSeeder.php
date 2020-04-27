<?php
namespace Newelement\Locations\Database\Seeds;

use Illuminate\Database\Seeder;
use Newelement\Locations\Traits\Seedable;
class LocationsDatabaseSeeder extends Seeder
{
    use Seedable;
    protected $seedersPath = __DIR__.'/';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seed('LocationsSettingsSeeder');
        $this->seed('LocationPageSeeder');
    }
}
