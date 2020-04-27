@extends('neutrino::admin.template.header-footer')
@section('title', 'Locations Stats | ')
@section('content')
<form action="/admin/locations/stats" method="post">
@csrf
    <div class="container">
        <div class="content">
            <div class="title-search">
                <h2>Locations Stats</h2>
            </div>



        </div>
        <aside class="sidebar">
            <div class="side-fields">
                <!--<button type="submit" class="btn full">Save Settings</button>-->
            </div>
        </aside>
    </div>
</form>
@endsection
