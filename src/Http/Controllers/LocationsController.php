<?php
namespace Newelement\Locations\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\Locations\Models\Location;
use Newelement\Locations\Models\LocationSetting;
use Newelement\Neutrino\Models\Page;
use Newelement\Neutrino\Models\ObjectMedia;

class LocationsController extends Controller
{

    public function index()
    {
        $data = collect();
        $settings = [];

        $data = Page::where('slug', 'locations-landing-page')->first();
        $data->title = config('locations.landing_page_title', 'Locations');

        $settingsObj = LocationSetting::all();

        foreach( $settingsObj as $setting ){
            $settings[$setting->name] = $setting->value_string? $setting->value_string : (int) $setting->value_bool;
        }

        $settings['locations_slug'] = config('locations.locations_slug');

        return view('locations::index', ['settings' => $settings, 'data' => $data]);
    }

    public function get($slug)
    {
        $data = Location::where('slug', $slug)->first();
        if( !$data ){
            abort(404);
        }

        $settings = [];
        $settingsObj = LocationSetting::all();
        foreach( $settingsObj as $setting ){
            $settings[$setting->name] = $setting->value_string? $setting->value_string : (int) $setting->value_bool;
        }

        return view('locations::single', ['settings' => $settings, 'data' => $data]);
    }

    public function getMarkers(Request $request)
    {
        if( $request->zipcode ){

            $zipcode = $request->zipcode;
            $radius = (int) $request->radius? $request->radius : 50;


            $latLng = $this->getLatLng($zipcode);
            if( !$latLng['success'] ){
                return response()->json(['error' => $latLng['message']], 500);
            }

            $latitude = $latLng['lat'];
            $longitude = $latLng['lng'];

            $markers = Location::selectRaw("
                        id, title, slug, street, street2, short_description, city, state, postal, country, website, phone, email, lat, lng,
                        ( 3956 * acos( cos( radians( ? ) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( lat ) ) ) ) AS distance
                        ", [$latitude, $longitude, $latitude])
            ->having('distance', '<', $radius)
            ->orderBy("distance", 'asc')
            ->get();

            foreach( $markers as $key => $marker ){
                $markers[$key]->featured_image = ObjectMedia::where(['object_type' => 'location', 'featured' => 1])->first();
            }

        } else {
            $markers = Location::orderBy('title', 'asc')->with('featuredImage')->get();
        }

        return response()->json(['markers' => $markers]);
    }

    public function filterMarkers(Request $request)
    {

    }

    private function getLatLng($address, $position = false)
    {
        $geocodeKey = env('GOOGLE_MAPS_API_KEY');

        if( !strlen($geocodeKey) ){
            return ['success' => false, 'message' => 'No Google Maps API key.'];
        }

        $ch = curl_init('https://maps.googleapis.com/maps/api/geocode/json?key='.$geocodeKey.'&address='.$address);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $geoData = '';
        if( ($geoData = curl_exec($ch) ) === false){
            $err = curl_error($ch);
            return ['success' => false, 'message' => $err];
        }

        $info = curl_getinfo($ch);
        if( $info['http_code'] === 403 ){
            return ['success' => false, 'message' => 'Geocoding API error. Invalid key.'];
        }

        curl_close($ch);

        $json = json_decode($geoData);

        if( $json->status !== 'OK' ){
            return ['success' => false, 'message' => $json->status. ' - '.$json->error_message];
        }

        $lat = $json->results[0]->geometry->location->lat;
        $lng = $json->results[0]->geometry->location->lng;

        return ['success' => true, 'lat' => $lat, 'lng' => $lng];
    }
}
