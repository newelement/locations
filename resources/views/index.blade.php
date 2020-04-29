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

<main class="main pt-4">
    <div class="container pt-2">
        <h2>Locations</h2>

        <div id="locations-template">
            <div id="locations-search-instructions">{{ $settings['search_instructions'] }}</div>
            <div id="locations-loader" class="hide">Loading ...</div>
            <form action="/locations-markers" method="post">
            @csrf
            <header class="locations-filter-bar">
                <div class="locations-filters">
                    @if( $settings['show_level'] )
                    <label for="locations-levels">{{ $settings['level_label'] }}</label>
                    <select id="locations-levels">
                        <option value="">Choose ...</option>
                        @foreach( $data->levels as $level )
                        <option value="{{ $level->id }}">{{ $level->title }}</option>
                        @endforeach
                    </select>
                    @endif
                </div>
                <div class="locations-search">
                    <input id="locations-zip" type="text" placeholder="Zip code / Address" required>
                    <select id="locations-radius">
                        <option value="10" {{ $settings['default_radius'] === '10'? 'selected="selected"' : '' }}>10 miles</option>
                        <option value="25" {{ $settings['default_radius'] === '25'? 'selected="selected"' : '' }}>25 miles</option>
                        <option value="50" {{ $settings['default_radius'] === '50'? 'selected="selected"' : '' }}>50 miles</option>
                        <option value="100" {{ $settings['default_radius'] === '100'? 'selected="selected"' : '' }}>100 miles</option>
                        <option value="150" {{ $settings['default_radius'] === '150'? 'selected="selected"' : '' }}>150 miles</option>
                        <option value="200" {{ $settings['default_radius'] === '200'? 'selected="selected"' : '' }}>200 miles</option>
                    </select>
                    <button type="submit" class="btn btn-primary locations-search-btn">Search</button>
                </div>
            </header>
            </form>
            <div class="locations-cols">
                <div class="locations-list-col">
                    <div id="locations-not-found" class="hide">{{ $settings['locations_not_found'] }}</div>
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
