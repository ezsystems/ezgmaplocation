{* Make sure to normalize floats from db  *}
{def $latitude  = $attribute.content.latitude|explode(',')|implode('.')
     $longitude = $attribute.content.longitude|explode(',')|implode('.')}
{run-once}
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
{/run-once}
{run-once}
<script type="text/javascript">
{literal}
function eZGmapLocation_MapView( attributeId, latitude, longitude ){
        if( latitude && longitude ){
            var myLatlng = new google.maps.LatLng(latitude,longitude);
            var myOptions = {
              zoom: 13,
              center: myLatlng,
              mapTypeId: google.maps.MapTypeId.ROADMAP
            }
        }else{
            var myLatlng = new google.maps.LatLng(0,0);
            var myOptions = {
              zoom: 0,
              center: myLatlng,
              mapTypeId: google.maps.MapTypeId.ROADMAP
            }
        }
        var map = new google.maps.Map(document.getElementById( 'ezgml-map-' + attributeId ), myOptions);
        var marker = new google.maps.Marker({
            position: myLatlng, 
            map: map
        });   
}
</script>
{/literal}
{/run-once}
carte
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