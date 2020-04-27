@extends('neutrino::admin.template.header-footer')
@section('title', 'Location Settings | ')
@section('content')
<form action="/admin/locations/settings" method="post" enctype="multipart/form-data">
@csrf
    <div class="container">
        <div class="content">
            <div class="title-search">
                <h2>Location Settings</h2>
            </div>

            <div class="form-row">
                <label class="label-col" for="pin-image">Marker Image</label>
                <div class="input-col has-checkbox">
                    @if( $settings['pin_image'] )
                    <img src="/{{ $settings['pin_image'] }}" alt="marker image" style="margin: 0 0 12px 0">
                    @endif
                    <input id="pin-image" type="file" name="pin_image">
                </div>
                <div class="input-notes">
                    <span class="note">PNG is highly recommended. Keep width and height below 72px.</span>
                </div>
            </div>

            <div class="form-row">
                <div class="label-col">Marker Image Size</div>
                <div class="input-col input-col-group">
                    <div>
                        <input type="number" name="marker_size_width" value="{{ $settings['marker_size_width'] }}"> width
                    </div>
                    <div>
                        <input type="number" name="marker_size_height" value="{{ $settings['marker_size_height'] }}"> height
                    </div>
                </div>
                <div class="input-notes">
                    <span class="note">For use with marker image. In pixels.</span>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="pin-color">Marker Color</label>
                <div class="input-col has-checkbox">
                    <input id="pin-color" type="color" name="pin_color" value="{{ $settings['pin_color'] }}">
                </div>
                <div class="input-notes">
                    <span class="note">Use only if not using custom marker image.</span>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="pin-label-color">Marker Label Color</label>
                <div class="input-col has-checkbox">
                    <input id="pin-label-color" type="color" name="pin_label_color" value="{{ $settings['pin_label_color'] }}">
                </div>
            </div>

            <div class="form-row">
                <div class="label-col">Marker Label Position</div>
                <div class="input-col input-col-group">
                    <div>
                        <input type="number" name="marker_label_x" value="{{ $settings['marker_label_x'] }}"> X
                    </div>
                    <div>
                        <input type="number" name="marker_label_y" value="{{ $settings['marker_label_y'] }}"> Y
                    </div>
                </div>
                <div class="input-notes">
                    <span class="note">X & Y coordinates of the marker label.</span>
                </div>
            </div>

            <div class="form-row">
                <div class="label-col">Marker Anchor Position</div>
                <div class="input-col input-col-group">
                    <div>
                        <input type="number" name="marker_anchor_x" value="{{ $settings['marker_anchor_x'] }}"> X
                    </div>
                    <div>
                        <input type="number" name="marker_anchor_y" value="{{ $settings['marker_anchor_y'] }}"> Y
                    </div>
                </div>
                <div class="input-notes">
                    <span class="note">X & Y coordinates of the marker anchor. The offset from the marker's position to the tip of an InfoWindow that has been opened with the marker as anchor.</span>
                </div>
            </div>

            <div class="form-row">
                <div class="label-col">Marker Origin Position</div>
                <div class="input-col input-col-group">
                    <div>
                        <input type="number" name="marker_orgin_x" value="{{ $settings['marker_origin_x'] }}"> X
                    </div>
                    <div>
                        <input type="number" name="marker_origin_y" value="{{ $settings['marker_origin_y'] }}"> Y
                    </div>
                </div>
                <div class="input-notes">
                    <span class="note">X & Y coordinates of the marker orgin. The position of the image within a sprite, if any. By default, the origin is located at the top left corner of the image (0, 0).</span>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="default-radius">Default Radius</label>
                <div class="input-col">
                    <div class="select-wrapper">
                        <select name="default_radius" id="default-radius">
                            <option value="10" {{ $settings['default_radius'] === '10'? 'selected="selected"' : '' }}>10 miles</option>
                            <option value="25" {{ $settings['default_radius'] === '25'? 'selected="selected"' : '' }}>25 miles</option>
                            <option value="50" {{ $settings['default_radius'] === '50'? 'selected="selected"' : '' }}>50 miles</option>
                            <option value="100 {{ $settings['default_radius'] === '100'? 'selected="selected"' : '' }}">100 miles</option>
                            <option value="150" {{ $settings['default_radius'] === '150'? 'selected="selected"' : '' }}>150 miles</option>
                            <option value="200" {{ $settings['default_radius'] === '200'? 'selected="selected"' : '' }}>200 miles</option>
                        </select>
                    </div>
                </div>
                <div class="input-notes">
                    <span class="note">Default search radius in miles.</span>
                </div>
            </div>

            <div class="form-row">
                <div class="label-col" for="show-level">Show Location Level</div>
                <div class="input-col has-checkbox">
                    <label><input id="show-level" type="checkbox" name="show_level" value="1" {{ $settings['show_level'] ? 'checked="checked"' : '' }}> <span>Yes</label></label>
                </div>
                <div class="input-notes">
                    <span class="note">Show the location level dropdown in filter search.</span>
                </div>
            </div>

        </div>
        <aside class="sidebar">
            <div class="side-fields">
                <button type="submit" class="btn full">Save Settings</button>
            </div>
        </aside>
    </div>
</form>`
@endsection
