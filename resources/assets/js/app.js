import axios from 'axios';

const HTTP = axios.create(axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : app.csrfToken,
    'Content-Type': 'multipart/form-data'
});

let map, markers = [], locations = [], infoWindow;
let $locationsMap = document.getElementById('locations-map');
let $locationMap = document.getElementById('location-map');
let $locationsList = document.querySelector('.locations-list');
let $listingFilters = document.querySelectorAll('.listing-filter');
let $locationsSearchBtn = document.querySelector('.locations-search-btn');
let $locationsLoader = document.getElementById('locations-loader');
let locationLabels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
let locationsLabelIndex = 0;
let position;

function scrollToLocationItem( i ){
    let item = document.querySelector('[data-locations-item="'+i+'"]');
    var topPos = item.offsetTop;
    document.querySelector('.locations-list').scrollTop = topPos-38;
}

function createMarker( latlng, listing, label, i ) {

    let directions = '<a class="get-directions-marker" href="https://maps.google.com?daddr='+listing.street+' '+listing.city+' '+listing.state+' '+listing.postal+'" target="_blank">Get directions</a>';
    let html = '';
    if( listing.featured_image !== null ){
        html += '<div class="locations-marker-image" style="background-image: url(\''+listing.featured_image.file_path+'\')"></div>';
    }
      html += '<div class="marker-cols">';
        html += '<div class="marker-col">';
          html += '<div class="marker-listing-title">' + listing.title + '</strong></div><br>';
          html += listing.street+'<br>';
          html += listing.city+', '+listing.state+' '+listing.postal;
        html += '</div>';
        html += '<div class="marker-col">';
          html += '<div class="marker-info"></div>';
        html += '</div>';
      html += '</div>';

    html += directions;

    let icon = {};

    if( locationsSettings.pin_image ){

        icon = {
            url: '/'+locationsSettings.pin_image,
            scaledSize: new google.maps.Size(parseInt(locationsSettings.marker_size_width), parseInt(locationsSettings.marker_size_height)),
            origin: new google.maps.Point(parseInt(locationsSettings.marker_origin_x),parseInt(locationsSettings.marker_origin_y)),
            anchor: new google.maps.Point(parseInt(locationsSettings.marker_anchor_x), parseInt(locationsSettings.marker_anchor_y)),
            labelOrigin: new google.maps.Point(parseInt(locationsSettings.marker_label_x), parseInt(locationsSettings.marker_label_y))
        };

    } else {
        icon = {
            path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z',
            fillColor: locationsSettings.pin_color,
            fillOpacity: 1,
            strokeWeight: 0,
            scale: 1.1,
            origin: new google.maps.Point(0,0),
            anchor: new google.maps.Point(10, 34),
            labelOrigin: new google.maps.Point(0, -30)
        };
    }

    let markerData = {
        map: map,
        position: latlng,
        label:{ text: label, color: locationsSettings.pin_label_color },
    }

    markerData.icon = icon;

    let marker = new google.maps.Marker(markerData);

    google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
        scrollToLocationItem( i );
    });

    markers.push(marker);
}

function createListItem(listing, label, i){

    let html = '<li data-locations-item="'+i+'">';
    html += '<div class="locations-item-inner">';
        html += '<div class="locations-item-label">';
            html += '<a href="#" data-locations-marker="'+i+'">'+label+'</a>';
        html += '</div>';

        if( listing.featured_image !== null ){
        html += '<div class="locations-item-image">';
            html += '<a href="/'+locationsSettings.locations_slug+'/'+listing.slug+'"><img src="'+listing.featured_image.file_path+'" alt="'+listing.title+'"></a>';
        html += '</div>';
        }

        html += '<div class="locations-item-info">';
              html += '<h3><a href="/'+locationsSettings.locations_slug+'/'+listing.slug+'">'+listing.title+'</a></h3>';
                  if( listing.website ){
                      html += '<h5><a href="'+listing.website+'">'+listing.website+'</a></h5>';
                }
              html += '<div class="locations-item-address"><p data-locations-marker="'+i+'">'+listing.street+'<br> '+listing.city+', '+listing.state+' '+listing.postal+'</p></div>';
              html += '<div class="locations-item-contact"><p>';
                  if( listing.email){
                    html += listing.email+'<br>';
                  }
                  if(listing.phone){
                    html += listing.phone;
                  }
              html += '</p></div>';

              if( listing.distance ){
                  html += '<div class="locations-item-distance">';
                      html += '<span>Distance:</span> '+listing.distance.toFixed(2)+' mi';
                  html += '</div>';
              }
        html += '</div>';
     html += '</div>';
    html += '</li>';

    $locationsList.innerHTML =  $locationsList.innerHTML + html;

    let $listItems = document.querySelectorAll('[data-locations-marker]');
    if( $listItems.length ){
        $listItems.forEach( (v) => {
            v.addEventListener('click', (el) => {
                el.preventDefault();
                let markerNum = el.target.getAttribute('data-locations-marker');
                if(markerNum){
                    google.maps.event.trigger(markers[markerNum], 'click');
                }
            });
        });
    }
}


function clearLocations() {
    $locationsList.innerHTML = '';
    markers.forEach( (v,i) => {
        markers[i].setMap(null);
    });
    markers = [];
}


function filterLocations(){
    clearLocations();

    var bounds = new google.maps.LatLngBounds();

    locations.forEach(function(v, i) {
        let latlng = new google.maps.LatLng(
          parseFloat(v.lat),
          parseFloat(v.lng)
        );
        let label = locationLabels[locationsLabelIndex++ % locationLabels.length];
        createMarker( latlng, v, label, i );
        createListItem(v, label, i)
        bounds.extend( latlng );
    });

    map.fitBounds(bounds);
    let center = map.getCenter();

    google.maps.event.addDomListener(window, 'resize', function() {
        map.setCenter(center);
    });
}

// LOCATIONS
function loadLocationsMap(){
    map = new google.maps.Map($locationsMap, {
        center: new google.maps.LatLng(40, -100),
        zoom: 4,
        mapTypeId: 'roadmap',
        scrollwheel: false,
        mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU },
        //styles: mapTheme
    });

    infoWindow = new google.maps.InfoWindow();
}

// LOCATION SINGLE
function loadLocationMap(lat,lng){
    map = new google.maps.Map(listingMap, {
        center: new google.maps.LatLng(lat, lng),
        zoom: 12,
        mapTypeId: 'roadmap',
        scrollwheel: false,
        mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU },
        styles: mapTheme
    });

    infoWindow = new google.maps.InfoWindow();
}

function loadListingMarker(){

    let latlng = new google.maps.LatLng(
      parseFloat(lat),
      parseFloat(lng)
    );

    let pinColor = '000000';

    let directions = '<a class="get-directions-marker" href="'+listing.link+'" >View Property</a>';

    var html = '<div class="marker-image"><img src="'+listing.image+'" alt="'+listing.title +'" width="340" height="180"></div>';
      html += '<div class="marker-cols">';
        html += '<div class="marker-col" style="margin-bottom: 12px;">';
          html += '<div class="marker-listing-title">' + listing.title + '</strong></div><span class="marker-listing-status">'+listing.status+'</span><br>';
          html += listing.address+'<br>';
          html += listing.city+', '+listing.state+' '+listing.zip_code;
        html += '</div>';
        html += '<div class="marker-col">';
          html += '<div class="marker-listing-price">'+listing.price+'</div>';
            if(listing.building_size){
                html += 'Building size: '+listing.building_size+'<br>';
            }
            if(listing.available_space){
                html += 'Available space: '+listing.available_space;
            }
        html += '</div>';
    html += '</div>';

    html += directions;

    let icon = {
        url: '',
        scaledSize: new google.maps.Size(30, 37),
        origin: new google.maps.Point(0,0),
        anchor: new google.maps.Point(10, 34),
        labelOrigin: new google.maps.Point(15, 15)
    };

    let marker = new google.maps.Marker({
        map: map,
        position: latlng,
        label: 'A',
        //icon: icon
    });

    google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
    });

    markers.push(marker);

    let center = map.getCenter();

    google.maps.event.addDomListener(window, 'resize', function() {
        map.setCenter(center);
    });
}

function getUserLocation(){
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition( (position) => {
            console.log(position);
            position = position;
        });
    }
}

function getMarkers(){
    locationsLabelIndex = 0;
    let formData = new FormData;
    let $zipcode = document.getElementById('locations-zip');
    let $radius = document.getElementById('locations-radius');
    formData.append('zipcode', $zipcode.value );
    formData.append('radius', $radius.value );
    $locationsLoader.classList.remove('hide');

    HTTP.post('/locations-markers', formData)
        .then(response => {
            locations = response.data.markers;
            filterLocations();
            $locationsLoader.classList.add('hide');
    })
    .catch(e => {
        console.log('Error loading locations. '+e.message);
        $locationsLoader.classList.add('hide');
    });
}

document.addEventListener('DOMContentLoaded', () => {

    // LOCATION SINGLE
    if($locationMap){
        loadLocationMap(lat,lng);
        loadLocationMarker();
    }

    if( $locationsMap ){

        loadLocationsMap();
        getMarkers();

        if( $listingFilters.length ){
            $listingFilters.forEach(function (el){
                el.addEventListener('change', function(){
                    filterListings();
                });
            });
        }
    }

    if($locationsSearchBtn){
        $locationsSearchBtn.addEventListener('click', (e) => {
            e.preventDefault();
            getMarkers()
        });
    }

});
