jQuery(document).ready(function( $ ) {
    $('body').on('click', '#location-icon', function(e){
        e.preventDefault();
        // var gps = Promise.resolve(getLocation());
        // gps.then(function(value){
        var lat = 0;
        var lon = 0;
            if(navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                lat = position.coords.latitude;
                lon = position.coords.longitude;
                });
            }
        setTimeout(function(){
            $.ajax({
                url:indppl_ajax.ajaxurl,
                dataType: 'text',
                method: 'POST',
                data: {
                    action: 'indppl_planting_pal_home_ajax',
                    lat: lat,
                    lon: lon,
                },
                type: 'POST',
                success: function(e){
                    $('.store-list-container').replaceWith(e);
                }
            })
        }, 200);
    })
});

function getLocation() {
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
        var lat = position.coords.latitude;
        var lon = position.coords.longitude;
        gps = [lat, lon];
        return gps;
        });
    }
}