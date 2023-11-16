var map;
var markers = [];
var infoWindow;
var locationSelect;

function initMap() {
    var singapore = { lat: 1.3521, lng: 103.8198 };
    map = new google.maps.Map(document.getElementById('map'), {
        center: singapore,
        zoom: 12,
        mapTypeId: 'roadmap',
    });
    showStoreMarkers();
};

function showStoreMarkers() {
    ocbcBranches.forEach(function(branch, index) {
        var latlng = new google.maps.LatLng(
            branch.latitude,
            branch.longitude
        );
        var name = branch.name;
        var address = branch.address;
        createMarker(latlng, name, address);
    });
};

function createMarker(latlng, name, address) {
    var tolocation = "../Project/Calendar.php";
    var html = "<b>" + name + "</b> <br/>" + address + "<br/> <button value='"+name+"' onclick=\"window.location.href='" + tolocation + '?branch='+name+"'\">book</button>";
    var marker = new google.maps.Marker({
        map: map,
        position: latlng
    });
    google.maps.event.addListener(marker, 'click', function () {
        if (!infoWindow) {
            infoWindow = new google.maps.InfoWindow();
        }
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
    });
    markers.push(marker);
};
