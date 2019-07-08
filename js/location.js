(function () {
    'use strict';
    var add = document.getElementById("location");
    var loc = document.getElementById("loc");

    //Assign position received
    function onPositionReceived(position){
        console.log(position);
        $.get( "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + position.coords.latitude + "," + position.coords.longitude +"&key=AIzaSyC1L4ozX5tsNlqWnPuJ0QoEf_CH7jDPlJI",
            function(location) {
                console.log(location);
            add.value = location.results[6].formatted_address;
                loc.removeAttribute('required');
         })
    }

    function locationNotReceived(positionError){
        loc.style.visibility = "visible";
    }

    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(onPositionReceived, locationNotReceived, {timeout:3000});
    }
}());
