<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key={ezini('SiteSettings','GMapsKey')}" type="text/javascript"></script>
<script type="text/javascript">
function MapViewer_{$attribute.id}()
{literal}
{
{/literal}
    var attribid = {$attribute.id}
    var mapid = 'map_{$attribute.id}';
    {if is_set($attribute.content.latitude)}
    var lat = {$attribute.content.latitude};
    var long = {$attribute.content.longitude};
    {else}
    var lat = 0.0;
    var long = 0.0;
    {/if}
    {literal}
    
    var map = null;
    var geocoder = null;
    var gmapExistingOnload = null;
    var marker = null;
    if (GBrowserIsCompatible()) 
    {
        var startPoint = new GLatLng(0,0);
        var zoom = 0;
        if(lat && long)
        {
          startPoint = new GLatLng(lat,long);
          zoom=13
        }
        map = new GMap2(document.getElementById(mapid));
        map.addControl(new GSmallMapControl());
        map.setCenter(startPoint, zoom);
        map.addOverlay(new GMarker(startPoint));
    }
}

if (window.addEventListener)
{
{/literal}
    window.addEventListener('load', MapViewer_{$attribute.id}, false);
{literal}
}
else if (window.attachEvent)
{
{/literal}
    window.attachEvent('onload', MapViewer_{$attribute.id});
{literal}
}
    {/literal}
</script>

{if and( is_set($attribute.content.latitude), is_set($attribute.content.longitude) )}
<div class="block">
<label>{'Latitude'|i18n('extension/ezgmaplocation/datatype')}:</label> {$attribute.content.latitude}
<label>{'Longitude'|i18n('extension/ezgmaplocation/datatype')}:</label> {$attribute.content.longitude}
</div>
{/if}
<label>{'Map'|i18n('extension/ezgmaplocation/datatype')}:</label>
<div id="map_{$attribute.id}" style="width: 240px; height: 150px"></div>