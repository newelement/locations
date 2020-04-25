@extends('neutrino::admin.template.header-footer')
@section('title', 'Edit Location | ')
@section('content')
<form action="/admin/locations/{{$location->id}}" method="post">
    @csrf
    @method('put')
    <div class="container">
        <div class="content">
            <h2>Edit Location</h2>

            <div class="form-row">
                <label class="label-col" for="name">Title</label>
                <div class="input-col">
                    <input id="title" class="to-slug" type="text" name="title" value="{{ old('title', $location->title) }}" required>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="slug">Slug</label>
                <div class="input-col">
                    <input id="slug" class="slug-input" type="text" name="slug" value="{{ old('slug', $location->slug) }}">
                </div>
            </div>

            <div class="form-row">
                <label class="label-col full-width" for="short-description">Short Description</label>
                <div class="input-col full-width">
                    <textarea id="short-description" name="short_description">{{ old('short_description', $location->short_description) }}</textarea>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col align-top full-width" for="description">Description</label>
                <div class="input-col full-width">
                    <textarea class="editor" id="description" name="description">{!! old('description', $location->description) !!}</textarea>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="street">Address</label>
                <div class="input-col">
                    <input id="street" type="text" name="street" value="{{ old('street', $location->street) }}" required>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="street2">Address 2</label>
                <div class="input-col">
                    <input id="street2" type="text" name="street2" value="{{ old('street2', $location->street2) }}" autocomplete="off">
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="city">City</label>
                <div class="input-col">
                    <input id="city" type="text" name="city" value="{{ old('city', $location->city) }}" required>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="state">State/Province</label>
                <div class="input-col">
                    <div class="select-wrapper">
                        <select id="state" name="state" required>
                            <option value="">Choose ...</option>
                            @foreach( states() as $key => $state )
                            <option value="{{$key}}" {{ old('state', $location->state) === $key? 'selected="selected"' : '' }}>{{$state}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="postal">Zip/Postal Code</label>
                <div class="input-col">
                    <input id="postal" type="text" name="postal" value="{{ old('postal', $location->postal) }}" required>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="country">Country</label>
                <div class="input-col">
                    <div class="select-wrapper">
                        <select id="country" name="country" required>
                            <option value="">Choose ...</option>
                            <option value="CA" {{ old('country', $location->country) === 'CA'? 'selected="selected"' : '' }}>Canada</option>
                            <option value="US" {{ old('country', $location->country) === 'US'? 'selected="selected"' : '' }}>United States</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="email">Email</label>
                <div class="input-col">
                    <input id="email" type="text" name="email" value="{{ old('email', $location->email) }}" autocomplete="off">
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="website">Website</label>
                <div class="input-col">
                    <input id="website" type="text" name="website" value="{{ old('website', $location->website) }}" autocomplete="off">
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="level">Location Level</label>
                <div class="input-col">
                    <div class="select-wrapper">
                        <select id="level" name="location_level_id">
                            <option value="">Choose ...</option>
                            @foreach( $location_levels as $level )
                            <option value="{{$level->id}}" {{ old('location_level_id', $location->location_level_id) === $level->id? 'selected="selected"' : '' }}>{{$level->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="lat">Latitude</label>
                <div class="input-col">
                    <input id="lat" type="text" name="lat" value="{{ old('lat', $location->lat) }}" autocomplete="off">
                </div>
                <div class="input-notes">
                    <span class="note">Do not alter unless you are NOT using Google's geocoding service.</span>
                </div>
            </div>

            <div class="form-row">
                <label class="label-col" for="lon">Longitude</label>
                <div class="input-col">
                    <input id="lon" type="text" name="lng" value="{{ old('lng', $location->lng) }}" autocomplete="off">
                </div>
                <div class="input-notes">
                    <span class="note">Do not alter unless you are NOT using Google's geocoding service.</span>
                </div>
            </div>

        </div>

        <aside class="sidebar">

            <div class="side-fields">
                <div class="form-row">
                    <label class="label-col">Featured Image
                        <a class="lfm-featured-image" data-input="featured-image" data-preview="featured-image-preview">
                            <i class="fas fa-image"></i> Choose
                        </a>
                    </label>
                    <div class="input-col">
                        <input id="featured-image" class="file-list-input" value="{{ $location->featuredImage? $location->featuredImage->file_path : '' }}" type="text" name="featured_image">
                        <div id="featured-image-preview" class="featured-image-preview">
                            @if($location->featuredImage)
                            <img class="lfm-preview-image" src="{{ $location->featuredImage? $location->featuredImage->file_path : '' }}" style="height: 160px;">
                            <a class="clear-featured-image" href="/">&times;</a>
                            @endif
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn full text-center">Save Location</button>
            </div>
        </aside>
    </div>
</form>
@endsection

@section('js')
<script>
window.editorStyles = <?php echo json_encode(config('neutrino.editor_styles')) ?>;
window.editorCss = '<?php echo getEditorCss(); ?>';
</script>
@endsection
