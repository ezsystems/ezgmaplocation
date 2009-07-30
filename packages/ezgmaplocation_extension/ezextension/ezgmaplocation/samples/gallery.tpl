{* Gallery - Full view *}
{* 
This is an overriden version of the full template for the 
'Gallery class. This will include a map that contains location-tagged
images. Add a 'location' attribute to your images to use
*}

<div class="content-view-full">
    <div class="class-gallery">

        <h1>{$node.name|wash()}</h1>

    {section show=$node.data_map.image.content}
        <div class="attribute-image">
            {attribute_view_gui alignment=right image_class=medium attribute=$node.data_map.image.content.data_map.image}
        </div>
    {/section}

        <div class="attribute-short">
           {attribute_view_gui attribute=$node.data_map.short_description}
        </div>

	{def $locations = fetch(content,list,hash(
	  parent_node_id, $node.node_id,
	  class_filter_type, include,
	  class_filter_array, array('image')))

       $locations_count=fetch_alias( children_count, hash( parent_node_id, $node.node_id, 
         class_filter_type, include,
         class_filter_array, array('image') ) )	  
	  }
	{if $locations}
	
<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key={ezini('SiteSettings','GMapsKey')}" type="text/javascript"></script>
<script type="text/javascript">
    {literal}
    var mapid = 'map';    
    var map = null;
    var geocoder = null;
    var gmapExistingOnload = null;
    var marker = null;

    if (window.onload)
    {
            //Hang on to any existing onload function.
            gmapsExistingOnload = window.onload;
    }
    
    function createMarker( lat, lng, info, bounds, icon)
    {
      var point = new GLatLng(lat, lng)
      var marker = new GMarker( point, icon );
      GEvent.addListener(marker, "click", function() {
        marker.openInfoWindowHtml(info);
      });
      bounds.extend(point);
      return marker;      
    }

    window.onload=function(ev){
        //Run any onload that we found.
        if (gmapExistingOnload)
        {
                gmapExistingOnload(ev);
        }
        if (GBrowserIsCompatible()) {
          map = new GMap2(document.getElementById(mapid));
                    
          map.addControl(new GSmallMapControl());
          map.setCenter(new GLatLng(0,0), 0);
          var bounds = new GLatLngBounds();
    {/literal}
{foreach $locations as $index=>$location}
          var popupwindow_{$index}=$('location_{$index}').innerHTML;
          map.addOverlay(createMarker({$location.data_map.location.content.latitude},{$location.data_map.location.content.longitude},popupwindow_{$index}, bounds));

{/foreach}
var polyline = new GPolyline([
{foreach $locations as $index=>$location}
  new GLatLng({$location.data_map.location.content.latitude}, {$location.data_map.location.content.longitude}),
{/foreach}
], "#CC6600", 6, 0.7);
map.addOverlay(polyline);
    {literal}

          map.setMapType(G_SATELLITE_MAP);
          map.setCenter(bounds.getCenter(), (map.getBoundsZoomLevel(bounds) - 1));
       
        }
    };

    {/literal}
</script>

<div id="map" style="width: 370px; height: 370px"></div>
<div style="display: none">        
{foreach $locations as $index=>$location}
  <div class="location_line" id="location_{$index}">
    <h3>{$location.data_map.name.content|wash}</h3>
    {attribute_view_gui attribute=$location.data_map.image image_class=medium}
    {attribute_view_gui attribute=$location.data_map.caption}
  </div>
{/foreach}
</div>

        <div class="attribute-long">
           {attribute_view_gui attribute=$node.data_map.description}
        </div>

        {let page_limit=10
             children=fetch_alias( children, hash( parent_node_id, $node.node_id,
                                                   offset, $view_parameters.offset,
                                                   limit, $page_limit,
                                                   sort_by, $node.sort_array ) )
             list_count=fetch_alias( children_count, hash( parent_node_id, $node.node_id ) )}

        {section show=$children}
            <div class="attribute-link">
                <p>
                <a href={$children[0].url_alias|ezurl}>{'View as slideshow'|i18n( 'design/base' )}</a>
                </p>
            </div>

           <div class="content-view-children">
               <table>
               <tr>
               {section var=child loop=$children sequence=array( bglight, bgdark )}
                   <td>
                      {node_view_gui view=galleryline content_node=$child}
                   </td>
                   {delimiter modulo=4}
                   </tr>
                   <tr>
                   {/delimiter}
               {/section}
               </tr>
               </table>
           </div>
        {/section}

        {include name=navigator
                 uri='design:navigator/google.tpl'
                 page_uri=concat( '/content/view', '/full/', $node.node_id )
                 item_count=$list_count
                 view_parameters=$view_parameters
                 item_limit=$page_limit}
        {/let}

    </div>
</div>
