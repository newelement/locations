<?php
namespace Newelement\Locations\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\Locations\Models\Location;
use Newelement\Locations\Models\LocationLevel;
use Newelement\Locations\Models\LocationSetting;
use Newelement\Locations\Models\LocationRequest;
use Newelement\Locations\Models\LocationImpression;
use Newelement\Neutrino\Models\ObjectMedia;
use Newelement\Neutrino\Models\CfObjectData;
use Newelement\Neutrino\Traits\CustomFields;
use Newelement\Neutrino\Models\ActivityLog;

class LocationsController extends Controller
{

    use CustomFields;

    private $geoCodeKey;

    function __construct()
    {
        $this->geoCodeKey = env('GOOGLE_MAPS_API_KEY_SERVER');
    }

    public function index(Request $request)
    {
        $locations = Location::orderBy('title', 'asc')->orderBy('state', 'asc')->paginate(40);

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
        $location->phone = $request->phone;
        $location->website = $request->website;
        $location->location_level_id = $request->location_level_id;

        $location->keywords = $request->keywords;
        $location->meta_description = $request->meta_description;
        $location->social_image_1 = $request->social_image_1;
        $location->social_image_2 = $request->social_image_2;
        $location->sitemap_change = $request->sitemap_change? $request->sitemap_change : 'weekly';
        $location->sitemap_priority = $request->sitemap_priority? $request->sitemap_priority : 0.5;

        if( $this->geocodeKey ){
            $address = urlencode($request->street.' '.$request->city.' '.$request->state.' '.$request->postal);

            $ch = curl_init('https://maps.googleapis.com/maps/api/geocode/json?key='.$this->geocodeKey.'&address='.$address);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $geoData = '';
                if( ($geoData = curl_exec($ch) ) === false){
                    $err = curl_error($ch);
                    return redirect()->back()->with('error', $err);
                }

                $info = curl_getinfo($ch);
                if( $info['http_code'] === 403 ){
                    return redirect()->back()->with('error', 'Geocoding API error. Invalid key.');
                }

                curl_close($ch);

                $json = json_decode($geoData);

                if( $json->status !== 'OK' ){
                    return redirect()->back()->with('error', 'Geocoding API error. '.$json->status. ' - '.$json->error_message);
                }

            $lat = $json->results[0]->geometry->location->lat;
            $lng = $json->results[0]->geometry->location->lng;

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
        $location->phone = $request->phone;
        $location->website = $request->website;
        $location->location_level_id = $request->location_level_id;

        $location->keywords = $request->keywords;
        $location->meta_description = $request->meta_description;
        $location->social_image_1 = $request->social_image_1;
        $location->social_image_2 = $request->social_image_2;
        $location->sitemap_change = $request->sitemap_change? $request->sitemap_change : 'weekly';
        $location->sitemap_priority = $request->sitemap_priority? $request->sitemap_priority : 0.5;

        if( $this->geocodeKey ){

            if( $location->street !== $request->street ||
                $location->city !== $request->city ||
                $location->state !== $request->state ||
                $location->postal !== $request->postal ){

                $address = urlencode($request->street.' '.$request->city.' '.$request->state.' '.$request->postal);

                $ch = curl_init('https://maps.googleapis.com/maps/api/geocode/json?key='.$this->geocodeKey.'&address='.$address);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $geoData = '';
                if( ($geoData = curl_exec($ch) ) === false){
                    $err = curl_error($ch);
                    return redirect()->back()->with('error', $err);
                }

                $info = curl_getinfo($ch);
                if( $info['http_code'] === 403 ){
                    return redirect()->back()->with('error', 'Geocoding API error. Invalid key.');
                }

                curl_close($ch);

                $json = json_decode($geoData);

                if( $json->status !== 'OK' ){
                    return redirect()->back()->with('error', 'Geocoding API error. '.$json->status. ' - '.$json->error_message);
                }

                $lat = $json->results[0]->geometry->location->lat;
                $lng = $json->results[0]->geometry->location->lng;

                $location->lat = $lat;
                $location->lng = $lng;
            }

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

    public function getSettings()
    {
        $settingsObj = LocationSetting::all();
        $settings = [];
        foreach( $settingsObj as $setting ){
            $settings[$setting->name] = $setting->value_string? $setting->value_string : (int) $setting->value_bool;
        }
        return view('locations::admin.settings', ['settings' => $settings]);
    }

    public function updateSettings(Request $request)
    {
        if ($request->hasFile('pin_image')) {
            $path = $request->pin_image->store( 'images', 'public');
            LocationSetting::where(['name' => 'pin_image'])->update([
                'value_string' => $path
            ]);
        }

        LocationSetting::where(['name' => 'default_radius'])->update([
            'value_string' => $request->default_radius
        ]);

        LocationSetting::where(['name' => 'pin_color'])->update([
            'value_string' => $request->pin_color
        ]);

        LocationSetting::where(['name' => 'pin_label_color'])->update([
            'value_string' => $request->pin_label_color
        ]);

        LocationSetting::where(['name' => 'show_level'])->update([
            'value_bool' => $request->boolean('show_level')
        ]);

        LocationSetting::where(['name' => 'marker_size_width'])->update([
            'value_string' => $request->marker_size_width
        ]);

        LocationSetting::where(['name' => 'marker_size_height'])->update([
            'value_string' => $request->marker_size_height
        ]);

        LocationSetting::where(['name' => 'marker_label_x'])->update([
            'value_string' => $request->marker_label_x
        ]);

        LocationSetting::where(['name' => 'marker_label_y'])->update([
            'value_string' => $request->marker_label_y
        ]);

        LocationSetting::where(['name' => 'marker_anchor_x'])->update([
            'value_string' => $request->marker_anchor_x
        ]);

        LocationSetting::where(['name' => 'marker_anchor_y'])->update([
            'value_string' => $request->marker_anchor_y
        ]);

        LocationSetting::where(['name' => 'marker_origin_x'])->update([
            'value_string' => $request->marker_origin_x
        ]);

        LocationSetting::where(['name' => 'marker_origin_y'])->update([
            'value_string' => $request->marker_origin_y
        ]);

        LocationSetting::where(['name' => 'locations_not_found'])->update([
            'value_string' => $request->locations_not_found
        ]);

        LocationSetting::where(['name' => 'init_load_locations'])->update([
            'value_bool' => $request->boolean('init_load_locations')
        ]);

        return redirect('/admin/locations/settings')->with('success', 'Settings updated.');
    }

    public function getStats(Request $request)
    {
        $stats = [];

        if( $request->s ){

            $validatedData = $request->validate([
                'start_date' => 'required',
                'end_date' => 'required',
            ]);

        }

        if( !$request->s ){

            $sqlClicked = '
                SELECT COUNT( location_id ) AS location_count, l.*, l.id AS id
                FROM location_requests AS lr
                JOIN locations AS l ON l.id = lr.location_id ';

            $sqlClicked .= '
                GROUP BY location_id, l.id
                ORDER BY location_count DESC
                LIMIT 10
                ';

            $stats['top_clicked'] = \DB::select($sqlClicked);

            $sqlImp = '
                SELECT
                    ROUND(AVG(DISTINCT location_pos)) AS avg_pos,
                    l.*,
                    l.id AS id
                FROM location_impressions AS li
                JOIN locations AS l ON l.id = li.location_id ';

            $sqlImp .= '
                GROUP BY li.location_id, l.id
                ORDER BY avg_pos DESC, l.title ASC
                LIMIT 10
                ';

            $stats['top_impressions'] = \DB::select($sqlImp);

        }

        if( $request->s ){

            // CLICKS


            $clicks = $this->getClicks($request);
            $stats['clicks'] = $clicks;


            // IMPRESSIONS
            $impressions = $this->getImpressions($request);
            $stats['impressions'] = $impressions;

        }

        $locations = Location::orderBy('title', 'asc')->orderBy('state', 'asc')->get();

        return view('locations::admin.stats', ['stats' => $stats, 'locations' => $locations]);
    }

    private function getClicks($request)
    {
        $startDate = \Carbon\Carbon::create($request->start_date)->startOfDay();
        $endDate = \Carbon\Carbon::create($request->end_date)->endOfDay();

        $query = \DB::table('location_requests');
            $query->join('locations as l', 'l.id', '=', 'location_requests.location_id')
            ->whereBetween('location_requests.created_at', [$startDate->timezone('UTC')->toDateTimeString(), $endDate->timezone('UTC')->toDateTimeString()]);

        if( $request->location_id ){
            $query->where('l.id', $request->location_id);
        }

        $query->groupBy('location_requests.location_id', 'l.id', 'location_requests.created_at')
                ->selectRaw('COUNT( location_id ) AS location_count, l.*, l.id AS id, location_requests.created_at AS created_at')
                ->orderByRaw('COUNT( location_id ) desc')
                ->orderby('l.title', 'asc');

        return $query->get();
    }

    private function getImpressions($request)
    {
        $startDate = \Carbon\Carbon::create($request->start_date)->startOfDay();
        $endDate = \Carbon\Carbon::create($request->end_date)->endOfDay();

        $query2 = \DB::table('location_impressions');

        $query2->join('locations as l', 'l.id', '=', 'location_impressions.location_id')
                    ->whereBetween('location_impressions.created_at', [$startDate->timezone('UTC')->toDateTimeString(), $endDate->timezone('UTC')->toDateTimeString()]);

        if( $request->location_id ){
                $query2->where('l.id', $request->location_id);
        }

        $query2->groupBy('location_impressions.location_id', 'l.id', 'location_impressions.created_at')
                                ->selectRaw('ROUND(AVG(DISTINCT location_pos)) AS avg_pos, l.*, l.id AS id, location_impressions.created_at AS created_at')
                                ->orderByRaw('ROUND(AVG(DISTINCT location_pos)) desc')
                                ->orderby('l.title', 'asc');

        return $query2->get();
    }

    public function exportStats(Request $request)
    {
        $type = $request->type === 'clicks'? 'clicks' : 'impressions';
        $date = date('Y-m-d');

        $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename='.$type.'-'.$date.'.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'no-cache'
        ];

        $list = $type === 'clicks'? $this->getClicks($request) : $this->getImpressions($request);
        $items = [];
        $count = $type === 'clicks'? 'clicks' : 'avg_position';
        $columns = ['ID', 'Location', 'Street', 'City', 'State', 'Postal', $count];

        foreach($list as $item){
            $items[] = [
               'id' => $item->id,
               'title' => $item->title,
               'street' => $item->street,
               'street2' => $item->street2,
               'city' => $item->city,
               'state' => $item->state,
               'postal' => $item->postal,
               $count => $type === 'clicks'? $item->location_count : $item->avg_pos
            ];
        }

        $filePath = \Storage::disk('local')->put($type.'-'.$date.'.csv', '');

        $file = fopen(storage_path('app').'/'.$type.'-'.$date.'.csv', 'w');

        fputcsv($file, $columns);

        foreach($items as $item) {
            fputcsv($file, [$item['id'], $item['title'], $item['street'], $item['city'], $item['state'], $item['postal'], $item[$count] ] );
        }

        fclose($file);

        return response()->download(storage_path('app').'/'.$type.'-'.$date.'.csv')->deleteFileAfterSend();
    }

}
