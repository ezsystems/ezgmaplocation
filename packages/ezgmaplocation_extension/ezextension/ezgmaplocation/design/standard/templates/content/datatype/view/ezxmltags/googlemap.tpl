{def $myrandomid = rand(0,9999999)}

{run-once}
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor={ezini('GMapSettings', 'UseSensor', 'ezgmaplocation.ini')}"></script>
<script type="text/javascript">
{literal}
 var directionDisplay;
 var directionsService = new google.maps.DirectionsService();
 var map;
 var stepDisplay;
 var markerArray = [];

function eZGmapLocation_MapView( attributeId, latitude, longitude, zoom )
{
	directionsDisplay = new google.maps.DirectionsRenderer();
	if( latitude && longitude )
	{
		var startPoint = new google.maps.LatLng( latitude, longitude );		
	} else {
		var startPoint = new google.maps.LatLng( 0, 0 );
	}
	
	map = new google.maps.Map(	document.getElementById( 'ezgml-map-' + attributeId ),
				{	center: startPoint,
					zoom : zoom,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				} );

	var marker = new google.maps.Marker({ map: map, position: startPoint });
	directionsDisplay.setMap(map);
	stepDisplay = new google.maps.InfoWindow();
}

  function calcRoute() {
    for (i = 0; i < markerArray.length; i++) {
      markerArray[i].setMap(null);
    }
    markerArray = [];
    var start = $("#locationa").val();
{/literal}
    var latlng = new google.maps.LatLng({$latitude},{$longitude});
{literal}
    var request = {
        origin:start, 
        destination:latlng,
        travelMode: google.maps.DirectionsTravelMode.DRIVING,
        unitSystem: google.maps.UnitSystem.METRIC
    };
    directionsService.route(request, function(response, status) {
      if (status == google.maps.DirectionsStatus.OK) {
      var warnings = document.getElementById("warnings_panel");
	    warnings.innerHTML = "<b>" + response.routes[0].warnings + "</b>";
        directionsDisplay.setDirections(response);
        showSteps(response);
      }
    });
  }
  
  function showSteps(directionResult) {
    var myRoute = directionResult.routes[0].legs[0];
    for (var i = 0; i < myRoute.steps.length; i++) {
      var marker = new google.maps.Marker({
        position: myRoute.steps[i].start_point, 
        map: map,
        zIndex:5
      });
      attachInstructionText(marker, myRoute.steps[i].instructions);
      markerArray[i] = marker;
    }
  }

  function attachInstructionText(marker, text) {
    google.maps.event.addListener(marker, 'click', function() {
      stepDisplay.setContent(text);
      stepDisplay.open(map, marker);
    });
  }  

$('document').ready(function() {
{/literal}
	$('#mapdirections').submit(function(){ldelim}
{literal}
		calcRoute(); return false;
	});
});
{/literal}
</script>
{/run-once}

<script type="text/javascript">
<!--

if ( window.addEventListener )
    window.addEventListener('load', function(){ldelim} eZGmapLocation_MapView( {$myrandomid}, {first_set( $latitude, '0.0')}, {first_set( $longitude, '0.0')}, {$zoomlevel} ) {rdelim}, false);
else if ( window.attachEvent )
    window.attachEvent('onload', function(){ldelim} eZGmapLocation_MapView( {$myrandomid}, {first_set( $latitude, '0.0')}, {first_set( $longitude, '0.0')}, {$zoomlevel} ) {rdelim} );

-->
</script>
<div class="googlemap">
	<div class="maplabel block">
		<label>{'Latitude'|i18n('extension/ezgmaplocation/datatype')}:</label> {$latitude}
		<label>{'Longitude'|i18n('extension/ezgmaplocation/datatype')}:</label> {$longitude}
		<label>{'Address'|i18n('extension/ezgmaplocation/datatype')}:</label> {$address}
	</div>
	<div id="ezgml-map-{$myrandomid}" class="mapblock" style="width: {$width}; height: {$height};"></div>
	<div id="warnings_panel" style="text-align:center"></div>
	<div id="ezgml-getdirection-{$myrandomid}">
		<form id="mapdirections">
			<label>{'Get directions from:'|i18n('extension/ezgmaplocation/datatype')}</label>
			<input id="fromaddress" type="text" name="fromaddress" class="search-box-map" value="" placeholder="{'From address'|i18n('extension/ezgmaplocation/datatype')}">
			<input type="submit" class="button calculate" value="{'Get Directions'|i18n('extension/ezgmaplocation/datatype')}" />
		</form>
		<div class="clearfix"></div>
	</div>
</div>