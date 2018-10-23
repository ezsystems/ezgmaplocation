{def $myrandomid = rand(0,9999999)}

{run-once}
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor={ezini('GMapSettings', 'UseSensor', 'ezgmaplocation.ini')}"></script>
<script type="text/javascript">
{literal}

function eZGmapLocation_MapView( mapdata, attributeId, latitude, longitude, zoom )
{
	mapdata.directionsDisplay = new google.maps.DirectionsRenderer();
	if( latitude && longitude )
	{
		var startPoint = new google.maps.LatLng( latitude, longitude );		
	} else {
		var startPoint = new google.maps.LatLng( 0, 0 );
	}
	
	mapdata.map = new google.maps.Map(	document.getElementById( 'ezgml-map-' + attributeId ),
				{	center: startPoint,
					zoom : zoom,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				} );

	var marker = new google.maps.Marker({ map: mapdata.map, position: startPoint });
	mapdata.directionsDisplay.setMap(mapdata.map);
	mapdata.stepDisplay = new google.maps.InfoWindow();
}

  function calcRoute(mapdata, attributeId, latitude, longitude) {
    for (i = 0; i < mapdata.markerArray.length; i++) {
      mapdata.markerArray[i].setMap(null);
    }
    mapdata.markerArray = [];

    var start = $('#fromaddress-' + attributeId).val();
    var latlng = new google.maps.LatLng(latitude,longitude);

    var request = {
        origin:start, 
        destination:latlng,
        travelMode: google.maps.DirectionsTravelMode.DRIVING,
        unitSystem: google.maps.UnitSystem.METRIC
    };
    mapdata.directionsService.route(request, function(response, status) {
      if (status == google.maps.DirectionsStatus.OK) {
      var warnings = document.getElementById("warnings_panel");
	    warnings.innerHTML = "<b>" + response.routes[0].warnings + "</b>";
        mapdata.directionsDisplay.setDirections(response);
        showSteps(mapdata, response);
      }
    });
  }
  
  function showSteps(mapdata, directionResult) {
    var myRoute = directionResult.routes[0].legs[0];
    for (var i = 0; i < myRoute.steps.length; i++) {
      var marker = new google.maps.Marker({
        position: myRoute.steps[i].start_point, 
        map: mapdata.map,
        zIndex:5
      });
      attachInstructionText(mapdata.map, marker, myRoute.steps[i].instructions);
      mapdata.markerArray[i] = marker;
    }
  }

  function attachInstructionText(map, marker, text) {
    google.maps.event.addListener(marker, 'click', function() {
      stepDisplay.setContent(text);
      stepDisplay.open(map, marker);
    });
  }  

</script>
{/literal}

{/run-once}

<script type="text/javascript">
	var mapdata_{$myrandomid} = {ldelim}
		map: null,
		directionDisplay: null,
	 	directionsService: new google.maps.DirectionsService(),
	 	stepDisplay: null,
	 	markerArray: []
	{rdelim}
{literal}
$('document').ready(function() {
{/literal}
	eZGmapLocation_MapView( mapdata_{$myrandomid}, {$myrandomid}, {first_set( $latitude, '0.0')}, {first_set( $longitude, '0.0')}, {$zoomlevel} );
	$('#mapdirections-{$myrandomid}').submit(function(){ldelim}
	calcRoute(mapdata_{$myrandomid}, "{$myrandomid}", {$latitude}, {$longitude}); return false;
{literal}
	});
});
{/literal}
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
		<form id="mapdirections-{$myrandomid}">
			<label>{'Get directions from:'|i18n('extension/ezgmaplocation/datatype')}</label>
			<input id="fromaddress-{$myrandomid}" type="text" name="fromaddress" class="search-box-map" value="" placeholder="{'From address'|i18n('extension/ezgmaplocation/datatype')}">
			<input type="submit" class="button calculate" value="{'Get Directions'|i18n('extension/ezgmaplocation/datatype')}" />
		</form>
		<div class="clearfix"></div>
	</div>
</div>