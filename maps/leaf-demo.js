// See post: http://asmaloney.com/2014/01/code/creating-an-interactive-map-with-leaflet-and-openstreetmap/

var map = L.map( 'map', {
  minZoom: 0,
  zoom: 13
})

console.log("voy...");

L.tileLayer( 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
  subdomains: ['a', 'b', 'c']
}).addTo( map )

var myURL = jQuery( 'script[src$="leaf-demo.js"]' ).attr( 'src' ).replace( 'leaf-demo.js', '' )

var myIcon = L.icon({
  iconUrl: myURL + 'images/pin24-blue.png',
  iconRetinaUrl: myURL + 'images/pin48-blue.png',
  iconSize: [39, 34],
  iconAnchor: [9, 21],
  popupAnchor: [0, -14]
})

map.locate({
  setView: true,
  // maxZoom: 120
}).on("locationfound", e => {

  L.marker( [e.latitude, e.longitude], {icon: myIcon} )
      .bindPopup( 'You are here!' )
      .addTo( map );

}).on("locationerror", error => {
  // error
  console.log("error");
});

for ( var i=0; i < markers.length; ++i )
{
  L.marker( [markers[i].lat, markers[i].lng], {icon: myIcon} )
      .bindPopup( '<a href="' + markers[i].url + '" target="_blank">' + markers[i].name + '</a>' )
      .addTo( map );
}

/* se puede borrar


// navigator.geolocation.getCurrentPosition(function () {}, function () {}, {});

//The working next statement.
navigator.geolocation.getCurrentPosition(function (position) {
  milatitude = position['coords']['latitude'];
  milongitude = position['coords']['longitude'];

  // map.options["center"][0] = milatitude;
  // map.options["center"][1] = milongitude;
  // console.log(map.options);

}, function (e) {
  //Your error handling here
}, {
  enableHighAccuracy: true
});

var map = L.map( 'map', {
  center: [9.894424966764568, -84.03757952960271],
  minZoom: 3,
  zoom: 10
})

// map.options[];
// alert("jajaja: "+map);

L.tileLayer( 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
  subdomains: ['a', 'b', 'c']
}).addTo( map )

var myURL = jQuery( 'script[src$="leaf-demo.js"]' ).attr( 'src' ).replace( 'leaf-demo.js', '' )

var myIcon = L.icon({
  iconUrl: myURL + 'images/pin24-blue.png',
  iconRetinaUrl: myURL + 'images/pin48-blue.png',
  iconSize: [29, 24],
  iconAnchor: [9, 21],
  popupAnchor: [0, -14]
})

for ( var i=0; i < markers.length; ++i )
{
 L.marker( [markers[i].lat, markers[i].lng], {icon: myIcon} )
  .bindPopup( '<a href="' + markers[i].url + '" target="_blank">' + markers[i].name + '</a>' )
  .addTo( map );
}
*/