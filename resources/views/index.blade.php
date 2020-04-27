@extends('neutrino::templates.header-footer')
@section('title', $data->title.' | ')

@section('meta_keywords', $data->keywords)
@section('meta_description', $data->meta_description)
@section('og')
<meta property="og:title" content="{{ $data->title }}" />
<meta property="og:description" content="{{ $data->meta_description }}" />
@if( isset($data->social_image_1) && strlen($data->social_image_1) )
@php
$socialImages = getImageSizes($data->social_image_1);
@endphp
<meta property="og:image" content="{{ env('APP_URL') }}{{ $socialImages['original'] }}"/>
@endif
@endsection

@section('content')

<main class="main">
    <div class="container">
        <h2>Locations</h2>

        <div id="locations-template">
            <div id="locations-loader" class="hide">Loading ...</div>
            <header class="locations-filter-bar">
                <input id="locations-zip" type="text" name="postal">
                <select id="locations-radius">
                    <option value="10">10 miles</option>
                    <option value="25">25 miles</option>
                    <option value="50">50 miles</option>
                    <option value="100">100 miles</option>
                    <option value="150">150 miles</option>
                    <option value="200">200 miles</option>
                </select>
                <button type="button" class="btn btn-primary locations-search-btn">Search</button>
            </header>
            <div class="locations-cols">
                <div class="locations-list-col">
                    <ul class="locations-list">
                    </ul>
                </div>
                <div class="locations-map-col">
                    <div id="locations-map"></div>
                </div>
            </div>
        </div>

    </div>
</main>

@endsection

@prepend('footerscripts')
@if( env('GOOGLE_MAPS_API_KEY') )
<script src="https://maps.google.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}"></script>
@endif
<script>
    window.locationsSettings = {!! json_encode($settings) !!};
</script>
@endprepend
