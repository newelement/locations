@extends('neutrino::layouts.header-footer')
@section('title', $data->title.' | ')
@section('meta_keywords', $data->keywords)
@section('meta_description', $data->meta_description)
@section('og')
<meta property="og:title" content="{{ $data->title }}" />
<meta property="og:description" content="{{ $data->meta_description }}" />
@if( isset($data->social_image) && strlen($data->social_image) )
@php
$socialImages = getImageSizes($data->social_image);
@endphp
<meta property="og:image" content="{{ env('APP_URL') }}{{ $socialImages['original'] }}"/>
@endif
@endsection

@section('content')

    <div class="container pt-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <a href="/{{ config('locations.locations_slug') }}">&larr; Back to {{ config('locations.landing_page_title') }}</a>
            </div>
        </div>
        <div class="row">
        @if($data->featuredImage)
            <div class="col-md-4 location-image mb-4">
            @php
            $images = getImageSizes($data->featuredImage->file_path);
            @endphp
            <img src="{{ $images['medium'] }}" alt="{{ $data->title }}">
            </div>
        @endif
            <div class="col-md-4 pl-4">
                <h2>{{ $data->title }}</h2>

                @if( $data->level )
                <h5>{{ $data->level->title }}</h5>
                @endif

                <p class="location-address">
                    {{ $data->street }}<br>
                    @if($data->street2){{ $data->street2 }}<br>@endif
                    {{ $data->city }} @if($data->state){{ $data->state }},@endif {{ $data->postal }}<br>
                    {{ $data->country }}
                </p>

                <p class="location-attributes">
                    @if($data->website) <a href="{{ $data->website }}" target="_blank">{{ $data->website }}</a><br>@endif
                    @if($data->phone) {{ $data->phone }}<br>@endif
                    @if($data->email) {{ $data->email }}<br>@endif
                </p>

            </div>
            <div class="col-md-4 pl-4">
                <p>
                {!! nl2br($data->short_description) !!}
                </p>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-12">
                <div id="location-single-map"></div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-8">
                 {!! getContent([], $data->description) !!}
            </div>
        </div>
    </div>

@endsection

@prepend('footerscripts')
@if( env('GOOGLE_MAPS_API_KEY_FRONTEND') )
<script src="https://maps.google.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY_FRONTEND')}}"></script>
@endif
<script>
    window.locationsSettings = {!! json_encode($settings) !!};
    window.singleLocation = {!! json_encode($data) !!};
</script>
@endprepend
