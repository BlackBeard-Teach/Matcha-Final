(function () {
    'use strict';
    var add = document.getElementById("location");
    var loc = document.getElementById("loc");

    //Assign position received
    function onPositionReceived(position){
        console.log(position);
        //Removed the API key
        $.get( "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + position.coords.latitude + "," + position.coords.longitude +"",
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
