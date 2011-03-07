{* Make sure to normalize floats from db  *}
{def $latitude  = $attribute.content.latitude|explode(',')|implode('.')
     $longitude = $attribute.content.longitude|explode(',')|implode('.')}
{run-once}
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={ezini('SiteSettings','GMapsKey')}" type="text/javascript"></script>
<script type="text/javascript">
{literal}
function eZGmapLocation_MapView( attributeId, latitude, longitude )
{
    if (GBrowserIsCompatible()) 
    {
        if( latitude && longitude )
            var startPoint = new GLatLng( latitude, longitude ), zoom = 13;
        else
            var startPoint = new GLatLng( 0, 0 ), zoom = 0;

        var map = new GMap2( document.getElementById( 'ezgml-map-' + attributeId ) );
        map.addControl( new GSmallMapControl() );
        map.setCenter( startPoint, zoom );
        map.addOverlay( new GMarker(startPoint) );
    }
}
{/literal}
</script>
{/run-once}

{if $attribute.has_content}
<script type="text/javascript">
<!--

if ( window.addEventListener )
    window.addEventListener('load', function(){ldelim} eZGmapLocation_MapView( {$attribute.id}, {first_set( $latitude, '0.0')}, {first_set( $longitude, '0.0')} ) {rdelim}, false);
else if ( window.attachEvent )
    window.attachEvent('onload', function(){ldelim} eZGmapLocation_MapView( {$attribute.id}, {first_set( $latitude, '0.0')}, {first_set( $longitude, '0.0')} ) {rdelim} );

-->
</script>

<div class="block">
<label>{'Latitude'|i18n('extension/ezgmaplocation/datatype')}:</label> {$latitude}
<label>{'Longitude'|i18n('extension/ezgmaplocation/datatype')}:</label> {$longitude}
  {if $attribute.content.address}
    <label>{'Address'|i18n('extension/ezgmaplocation/datatype')}:</label> {$attribute.content.address}
  {/if}
</div>

<label>{'Map'|i18n('extension/ezgmaplocation/datatype')}:</label>
<div id="ezgml-map-{$attribute.id}" style="width: 500px; height: 280px;"></div>
{/if}