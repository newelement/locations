@extends('neutrino::admin.template.header-footer')
@section('title', 'Locations Stats | ')
@section('content')
    <div class="container">
        <div class="content full">
            <div class="title-search">
                <h2>Locations Stats</h2>
                <div class="object-search extra-search">
                    <form class="search-form" action="{{url()->full()}}" method="get">
                        <select class="select-2-dropdown" id="locations-dropdown" name="location_id">
                            <option value=""></option>
                            @foreach($locations as $location)
                            <option value="{{$location->id}}" {{ request('location_id') == $location->id? 'selected="selected"' : '' }}>{{ $location->title }}</option>
                            @endforeach
                        </select>
                        <div class="form-date-col">
                            <input type="text" class="start-date" name="start_date" readonly value="{{ old('start_date', request('start_date') ) }}">
                        </div>
                        <div class="form-to-from">
                        from &rarr; to
                        </div>
                        <div class="form-date-col">
                            <input type="text" class="end-date" name="end_date" readonly value="{{ old('end_date', request('end_date') ) }}" >
                        </div>
                        <input type="hidden" name="s" value="1">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>

        @if( !request('s') )

            <h3>Top Locations Clicked <small>(Top 10)</small></h3>

            <table class="table" cellpadding="0" cellspacing="0" style="margin-bottom: 48px;">
                <thead>
                    <tr>
                        <th class="text-left">Location Name</th>
                        <th class="text-left">Location Address</th>
                        <th width="100">Click Count</th>
                    </tr>
                </thead>
                <tbody>
                @foreach( $stats['top_clicked'] as $clicked )
                    <tr>
                        <td><a href="/admin/locations/{{ $clicked->id }}">{{ $clicked->title }}</a></td>
                        <td>{{ $clicked->street }} {{ $clicked->city }} {{ $clicked->state }}</td>
                        <td class="text-center">{{ $clicked->location_count }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <h3>Top Location Search Impressions (Top 10 avg.)</h3>

            <table class="table" cellpadding="0" cellspacing="0" style="margin-bottom: 48px;">
                <thead>
                    <tr>
                        <th class="text-left">Location Name</th>
                        <th class="text-left">Location Address</th>
                        <th width="140">Average Position</th>
                    </tr>
                </thead>
                <tbody>
                @foreach( $stats['top_impressions'] as $imp )
                    <tr>
                        <td><a href="/admin/locations/{{ $imp->id }}">{{ $imp->title }}</a></td>
                        <td>{{ $imp->street }} {{ $imp->city }} {{ $imp->state }}</td>
                        <td class="text-center">{{ $imp->avg_pos }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        @endif

        @if( request('s') )

            <h3>Clicks</h3>

            @if( request('s') )
                <form action="{{url()->full()}}&type=clicks" method="post" style="text-align: right; margin-top: -32px; margin-bottom: 12px">
                    @csrf
                    <button class="btn small" type="submit">Export Results to CSV</button>
                </form>
            @endif

            <table class="table" cellpadding="0" cellspacing="0" style="margin-bottom: 48px;">
                <thead>
                    <tr>
                        <th class="text-left">Location Name</th>
                        <th class="text-left">Location Address</th>
                        <th width="140">Clicks</th>
                        <th>Created On</th>
                    </tr>
                </thead>
                <tbody>
                @foreach( $stats['clicks'] as $click )
                    <tr>
                        <td><a href="/admin/locations/{{ $click->id }}">{{ $click->title }}</a></td>
                        <td>{{ $click->street }} {{ $click->city }} {{ $click->state }}</td>
                        <td class="text-center">{{ $click->location_count }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::create($click->created_at)->timezone( config('neutrino.timezone') )->format('m-j-y g:i a') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <h3>Impressions</h3>

            @if( request('s') )
                <form action="{{url()->full()}}&type=impressions" method="post" style="text-align: right; margin-top: -32px; margin-bottom: 12px">
                    @csrf
                    <button class="btn small" type="submit">Export Results to CSV</button>
                </form>
            @endif

            <table class="table" cellpadding="0" cellspacing="0" style="margin-bottom: 48px;">
                <thead>
                    <tr>
                        <th class="text-left">Location Name</th>
                        <th class="text-left">Location Address</th>
                        <th width="140">Average Position</th>
                        <th>Created On</th>
                    </tr>
                </thead>
                <tbody>
                @foreach( $stats['impressions'] as $imp )
                    <tr>
                        <td><a href="/admin/locations/{{ $imp->id }}">{{ $imp->title }}</a></td>
                        <td>{{ $imp->street }} {{ $imp->city }} {{ $imp->state }}</td>
                        <td class="text-center">{{ $imp->avg_pos }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::create($imp->created_at)->timezone( config('neutrino.timezone') )->format('m-j-y g:i a') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        @endif

        </div>
    </div>
@endsection
