{if is_unset( $width )}
    {def $width=500}
{/if}
{if is_unset( $height )}
    {def $height=280}
{/if}

{* Make sure to normalize floats from db  *}
{def $latitude  = $attribute.content.latitude|explode(',')|implode('.')
     $longitude = $attribute.content.longitude|explode(',')|implode('.')}
{run-once}
{def $coordinateSystem=ezini('GMapSettings', 'System', 'ezgmaplocation.ini')}
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor={ezini('GMapSettings', 'UseSensor', 'ezgmaplocation.ini')}"></script>
<script type="text/javascript">
{literal}
function eZGmapLocation_MapView( attributeId, latitude, longitude )
{
	var zoommax = 13;
	
	if( latitude && longitude )
	{
		var startPoint = new google.maps.LatLng( latitude, longitude );
		var zoom = zoommax;
		
	}
  else
  {
      var startPoint = new google.maps.LatLng( 0, 0 );
      var zoom = 0;
  }

  var map = new google.maps.Map( document.getElementById( 'ezgml-map-' + attributeId ), { center: startPoint, zoom : zoom, mapTypeId: google.maps.MapTypeId.ROADMAP } );
	var marker = new google.maps.Marker({ map: map, position: startPoint });
}
{/literal}
</script>
{/run-once}

{if $attribute.has_content}
    {def $lat=$attribute.content.latitude $latG=0 $latM=0 $latS=0 $latHemisphere="S"
         $lon=$attribute.content.longitude $lonG=0 $lonM=0 $lonS=0 $lonMeridian="W"}

    {if gt($lat, 0)}
        {set $latHemisphere="N"}
    {/if}
    {set $lat  = abs($lat)}
    {set $latG = floor($lat)}
    {set $lat  = sub($lat, floor($lat))}
    {set $latM = floor(mul($lat, 60))}
    {set $lat  = sub($lat, div(floor(mul($lat, 60)),60))}
    {set $latS = div(floor(mul($lat, 360000)),100)}

    {if gt($lon, 0)}
        {set $lonMeridian="E"}
    {/if}
    {set $lon  = abs($lon)}
    {set $lonG = floor($lon)}
    {set $lon  = sub($lon, floor($lon))}
    {set $lonM = floor(mul($lon, 60))}
    {set $lon  = sub($lon, div(floor(mul($lon, 60)),60))}
    {set $lonS = div(floor(mul($lon, 360000)),100)}
<script type="text/javascript">
if ( window.addEventListener )
    window.addEventListener('load', function(){ldelim} eZGmapLocation_MapView( {$attribute.id}, {first_set( $latitude, '0.0')}, {first_set( $longitude, '0.0')} ) {rdelim}, false);
else if ( window.attachEvent )
    window.attachEvent('onload', function(){ldelim} eZGmapLocation_MapView( {$attribute.id}, {first_set( $latitude, '0.0')}, {first_set( $longitude, '0.0')} ) {rdelim} );
</script>

{if eq($coordinateSystem,"degrees")}
<div class="block">
<label>{'Latitude'|i18n('extension/ezgmaplocation/datatype')}:</label>  {$latG}º {$latM}' {$latS}'' {$latHemisphere}
<label>{'Longitude'|i18n('extension/ezgmaplocation/datatype')}:</label> {$lonG}º {$lonM}' {$lonS}'' {$lonMeridian}
  {if $attribute.content.address}
    <label>{'Address'|i18n('extension/ezgmaplocation/datatype')}:</label> {$attribute.content.address}
  {/if}
</div>
{else}
<div class="block">
<label>{'Latitude'|i18n('extension/ezgmaplocation/datatype')}:</label> {$latitude}
<label>{'Longitude'|i18n('extension/ezgmaplocation/datatype')}:</label> {$longitude}
  {if $attribute.content.address}
    <label>{'Address'|i18n('extension/ezgmaplocation/datatype')}:</label> {$attribute.content.address}
  {/if}
</div>
{/if}

<label>{'Map'|i18n('extension/ezgmaplocation/datatype')}:</label>
<div id="ezgml-map-{$attribute.id}" style="width: {$width}px; height: {$height}px;"></div>
{/if}
{undef $coordinateSystem}