@extends('neutrino::admin.template.header-footer')
@section('title', 'Edit Location | ')
@section('content')
<form action="/admin/locations/{{$location->id}}" method="post">
    @csrf
    @method('put')
    <div class="container">
        <div class="content">
            <h2>Edit Location <a class="headline-btn" href="/admin/locations/create" role="button">Create New Location</a></h2>

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
                <label class="label-col" for="phone">Phone</label>
                <div class="input-col">
                    <input id="phone" type="text" name="phone" value="{{ old('phone', $location->phone) }}" autocomplete="off">
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

            <h3 class="cf-group-title">SEO</h3>

                    <div class="form-row">
                        <label class="label-col" for="keywords">Keywords</label>
                        <div class="input-col">
                            <input id="keywords" type="text" name="keywords" value="{{ $location->keywords }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="label-col" for="meta-desc">Meta Description</label>
                        <div class="input-col">
                            <input id="meta-desc" type="text" name="meta_description" value="{{ $location->meta_description }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="label-col full-width">Sitemap</div>
                        <div class="input-cols">
                            <div class="input-col">
                                <label for="sitemap-change">Change Frequency</label>
                                <div class="select-wrapper">
                                    <select id="sitemap-change" name="sitemap_change">
                                        <option value=""></option>
                                        <option value="always" {{ $location->sitemap_change === 'always'? 'selected="selected"' : '' }}>Always</option>
                                        <option value="hourly" {{ $location->sitemap_change === 'hourly'? 'selected="selected"' : '' }}>Hourly</option>
                                        <option value="daily" {{ $location->sitemap_change === 'daily'? 'selected="selected"' : '' }}>Daily</option>
                                        <option value="weekly" {{ $location->sitemap_change === 'weekly'? 'selected="selected"' : '' }}>Weekly</option>
                                        <option value="monthly" {{ $location->sitemap_change === 'monthly'? 'selected="selected"' : '' }}>Monthly</option>
                                        <option value="yearly" {{ $location->sitemap_change === 'yearly'? 'selected="selected"' : '' }}>Yearly</option>
                                        <option value="never" {{ $location->sitemap_change === 'never'? 'selected="selected"' : '' }}>Never</option>
                                    </select>
                                </div>
                            </div>
                            <div class="input-col">
                                <label for="sitemap-priority">Priority (0.1 - 1.0)</label>
                                <input id="sitemap-priority" type="number" name="sitemap_priority" value="{{ $location->sitemap_priority }}">
                            </div>
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

                <div class="form-row">
                        <label class="label-col">Social Image
                            <a class="lfm-social-image" data-input="social-image" data-preview="social-image-preview">
                                <i class="fas fa-image"></i> Choose
                            </a>
                        </label>
                        <div class="input-col">
                            <input id="social-image" class="file-list-input" value="{{ $location->social_image_1? $location->social_image_1 : '' }}" type="text" name="social_image_1">
                            <div id="social-image-preview" class="featured-image-preview">
                                @if($location->social_image_1)
                                <img class="lfm-preview-image" src="{{ $location->social_image_1? $location->social_image_1 : '' }}" style="height: 160px;">
                                <a class="clear-social-image" href="/">&times;</a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="label-col">Twitter Image
                            <a class="lfm-social-image" data-input="social-image1" data-preview="social-image-preview1">
                                <i class="fas fa-image"></i> Choose
                            </a>
                        </label>
                        <div class="input-col">
                            <input id="social-image1" class="file-list-input" value="{{ $location->social_image_2? $location->social_image_2 : '' }}" type="text" name="social_image_2">
                            <div id="social-image-preview1" class="featured-image-preview">
                                @if($location->social_image_2)
                                <img class="lfm-preview-image" src="{{ $location->social_image_2? $location->social_image_2 : '' }}" style="height: 160px;">
                                <a class="clear-lfm-image" data-input="social-image1" data-preview="social-image-preview1" href="/">&times;</a>
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
