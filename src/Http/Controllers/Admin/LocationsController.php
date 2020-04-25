<?php
namespace Newelement\Locations\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\Locations\Models\Location;
use Newelement\Locations\Models\LocationLevel;
use Newelement\Locations\Models\LocationStat;
use Newelement\Neutrino\Models\ObjectMedia;
use Newelement\Neutrino\Models\CfObjectData;
use Newelement\Neutrino\Traits\CustomFields;
use Newelement\Neutrino\Models\ActivityLog;

class LocationsController extends Controller
{

    use CustomFields;

    public function index(Request $request)
    {
        $locations = Location::orderBy('title', 'asc')->orderBy('state', 'asc')->get();

        if( $request->ajax() ){
            return response()->json($location);
        } else {
            return view('locations::admin.index', ['locations' => $locations]);
        }
    }

    public function indexLevels(Request $request)
    {
        $levels = LocationLevel::orderBy('title', 'asc')->get();
        $level = collect();
        $level->id = false;
        $level->title = '';

        if( $request->ajax() ){
            return response()->json($levels);
        } else {
            return view('locations::admin.index-levels', ['levels' => $levels, 'level' => $level]);
        }
    }

    public function get(Request $request, Location $location)
    {
        $levels = LocationLevel::orderBy('title', 'asc')->get();

        if( $request->ajax() ){
            return response()->json($levels);
        } else {
            return view('locations::admin.edit', ['location' => $location, 'location_levels' => $levels]);
        }
    }

    public function show()
    {
        $levels = LocationLevel::orderBy('title', 'asc')->get();
        return view('locations::admin.create', ['location_levels' => $levels]);
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
        ]);

        $location = new Location;
        $location->title = $request->title;
        $location->slug = toSlug($request->slug, 'locations');
        $location->short_description = $request->short_description;
        $location->description = htmlentities($request->description);
        $location->street = $request->street;
        $location->street2 = $request->street2;
        $location->city = $request->city;
        $location->state = $request->state;
        $location->postal = $request->postal;
        $location->country = $request->country;
        $location->email = $request->email;
        $location->website = $request->website;
        $location->location_level_id = $request->location_level_id;

        $geocodeKey = env('GOOGLE_MAPS_API_KEY');

        if( $geocodeKey ){
            $location = urlencode($request->street.' '.$request->city.' '.$request->state.' '.$request->postal);
            $geoData = file_get_contents('https://api.geocod.io/v1.4/geocode?api_key='.$geocodeKey.'&q='.$location);

            $json = json_decode($geoData);
            dd($json);
            if( $json->error ){
                return redirect()->back()->with('error', $json->error);
            }

            $lat = $json->results[0]->location->lat;
            $lng = $json->results[0]->location->lng;

            $location->lat = $lat;
            $location->lng = $lng;

        } else {
            $location->lat = $request->lat;
            $location->lng = $request->lng;
        }

        $location->save();

        if( $request->featured_image ){
            $path = $request->featured_image;
            $media = new ObjectMedia;
            $media->object_id = $location->id;
            $media->object_type = 'location';
            $media->featured = 1;
            $media->file_path = $path;
            $media->save();
        }

        // Custom Fields
        $customFields = $request->cfs;
        if( $customFields ){
            $this->parseCustomFields($customFields, $location->id, 'location');
        }

        ActivityLog::insert([
            'activity_package' => 'locations',
            'activity_group' => 'location.create',
            'object_type' => 'location',
            'object_id' => $location->id,
            'content' => 'location created',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

        return redirect('/admin/locations/'.$location->id)->with('success', 'Location created.');
    }

    public function update(Request $request, Location $location)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
        ]);

        $location->title = $request->title;
        $location->slug = $location->slug === $request->slug? $request->slug : toSlug($request->slug, 'locations');
        $location->short_description = $request->short_description;
        $location->description = htmlentities($request->description);
        $location->street = $request->street;
        $location->street2 = $request->street2;
        $location->city = $request->city;
        $location->state = $request->state;
        $location->postal = $request->postal;
        $location->country = $request->country;
        $location->email = $request->email;
        $location->website = $request->website;
        $location->location_level_id = $request->location_level_id;

        $geocodeKey = env('GOOGLE_MAPS_API_KEY');

        if( $geocodeKey ){
            $location = urlencode($request->street.' '.$request->city.' '.$request->state.' '.$request->postal);
            $geoData = file_get_contents('https://api.geocod.io/v1.4/geocode?api_key='.$geocodeKey.'&q='.$location);

            $json = json_decode($geoData);

            $lat = $json->results[0]->location->lat;
            $lng = $json->results[0]->location->lng;

            $location->lat = $lat;
            $location->lng = $lng;

        } else {
            $location->lat = $request->lat;
            $location->lng = $request->lng;
        }

        $location->save();

        if( $request->featured_image ){
            $path = $request->featured_image;
            ObjectMedia::updateOrCreate([
                'object_id' => $location->id,
                'object_type' => 'location',
                'featured' => 1
            ], [ 'file_path' => $path ]);
        } else {
            ObjectMedia::where([
                'object_id' => $location->id,
                'object_type' => 'location',
                'featured' => 1
                ])->delete();
        }

        // Custom Fields
        $customFields = $request->cfs;
        if( $customFields ){
            $this->parseCustomFields($customFields, $location->id, 'location');
        }

        ActivityLog::insert([
            'activity_package' => 'locations',
            'activity_group' => 'location.updated',
            'object_type' => 'location',
            'object_id' => $location->id,
            'content' => 'location updated',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

        return redirect('/admin/locations/'.$location->id)->with('success', 'Location updated.');
    }

    public function delete(Location $location)
    {
        $location->delete();

        return redirect('/admin/locations')->with('success', 'Location deleted.');
    }

    public function getLevel(Request $request, LocationLevel $level)
    {
        $levels = LocationLevel::orderBy('title', 'asc')->get();

        if( $request->ajax() ){
            return response()->json($levels);
        } else {
            return view('locations::admin.index-levels', ['level' => $level, 'levels' => $levels]);
        }
    }

    public function createLevel(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
        ]);

        $level = new LocationLevel;
        $level->title = $request->title;
        $level->save();

        if( $request->ajax() ){
            return response()->json($level);
        } else {
            return redirect('/admin/locations/levels')->with('success', 'Location level created.');
        }
    }

    public function updateLevel(Request $request, LocationLevel $level)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
        ]);

        $level->title = $request->title;
        $level->save();

        if( $request->ajax() ){
            return response()->json($level);
        } else {
            return redirect('/admin/locations/levels')->with('success', 'Location level updated.');
        }
    }

    public function deleteLevel(LocationLevel $level)
    {
        $level->delete();

        return redirect('/admin/locations/levels')->with('success', 'Location level deleted.');
    }
}
