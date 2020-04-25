@extends('neutrino::admin.template.header-footer')
@section('title', 'Location Levels | ')
@section('content')
    <div class="container">
        <div class="content">
            <div class="title-search">
                <h2>Location Levels</h2>
            </div>

            <div class="responsive-table">
                <table cellpadding="0" cellspacing="0" class="table">
                    <thead>
                        <tr>
                            <th class="text-left">Title</th>
                            <th width="80">Edit</th>
                            <th width="60"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach( $levels as $lev )
                        <tr>
                            <td><a href="/admin/locations/levels/{{$lev->id}}">{{$lev->title}}</a></td>
                            <td class="text-center">
                                <a href="/admin/locations/levels/{{$lev->id}}">Edit</a>
                            </td>
                            <td>
                                <form action="/admin/locations/levels/{{$lev->id}}" method="post">
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

        </div>
        <aside class="sidebar">
            <div class="side-fields">
                @if( $level->id )
                <form action="/admin/locations/levels/{{ $level->id }}" method="post">
                    <h3>Edit Location Level</h3>
                @else
                <form action="/admin/locations/levels" method="post">
                    <h3>Create Location Level</h3>
                @endif
                    @csrf
                    @if( $level->id )
                    @method('put')
                    @endif

                    <div class="form-row">
                        <label class="label-col">Title</label>
                        <div class="input-col">
                            <input type="text" name="title" value="{{ $level->title }}">
                        </div>
                    </div>

                    <button type="submit" class="btn">{{ $level->id? 'Save' : 'Create' }} Location Level</button>
                </form>
            </div>

        </aside>
    </div>
@endsection
