@extends('neutrino::admin.template.header-footer')
@section('title', 'Locations | ')
@section('content')
    <div class="container dashboard">
        <div class="content full">
            <div class="title-search">
                <h2>Locations <a class="headline-btn" href="/admin/locations/create" role="button">Create New Location</a></h2>
                <div class="object-search">
                    <form class="search-form" action="{{url()->full()}}" method="get">
                        <input type="text" name="s" value="{{ request('s') }}" placeholder="Search locations" autocomplete="off">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>

            <div class="responsive-table">
                <table cellpadding="0" cellspacing="0" class="table">
                    <thead>
                        <tr>
                            <th class="text-left">Title</th>
                            <th class="text-left">Street</th>
                            <th>State</th>
                            <th width="80">Edit</th>
                            <th width="60"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach( $locations as $location )
                        <tr>
                            <td><a href="/admin/locations/{{$location->id}}">{{$location->title}}</a></td>
                            <td>{{$location->street}}</td>
                            <td class="text-center">{{$location->state}}</td>
                            <td class="text-center">
                                <a href="/admin/locations/{{$location->id}}">Edit</a>
                            </td>
                            <td>
                                <form action="/admin/locations/{{$location->id}}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="delete-btn">&times;</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-links">
                {{ $locations->appends($_GET)->links() }}
            </div>

        </div>
    </div>
@endsection
