gmapsLocation Datatype Extension
Version 0.5
Developed by Blend Interactive
http://blendinteractive.com
----------------------
The GmapsLocation datatype extension provides a handy way to store
latitude/longitude points (as decimal degrees) on an object by using
Google Maps to identify and mark positions using their address.

Installation
---------------

1.) Obtain a Google Maps Key for all domains you'll be using by registering
your domains with Google at http://www.google.com/apis/maps/

2.) Upload the gmapslocation folder to the extensions folder in your
eZ Publish installation.

3.) Activate the extension from the 'Extensions' portion of the
'Setup' tab in the eZ publish admin interface.

4.) Add your GmapsKey to the site.ini under [SiteSettings] like so:
GMapsKey=<Long string of characters from Google>

Use
---------------
To use the extension, add the 'GMaps Location' datatype to your classes using
the class editor.

The most common use of the extension is to also Google Maps data on a public-
facing web site. The included gmap.tpl template will provide a basic gmap
with mapped points. See the instructions in design/standard/templates/gmap.tpl
for the full documentation of parameters.

A few examples:

List all office objects under node 57 and display their 'location'
attributes on a 600x400 map. Also list the offices on the page:

{def $offices = fetch('content','list', hash(
                'parent_node_id', 57,
                'class_filter_type', 'include',
                'class_filter_array', array('office')))}

{include uri='design:gmaps.tpl'
    locations=$offices
    size=array(600,400)
    show_popups_on_page=true()
}


Recursively get all image objects under node 243 and display their
'gps_point' attributes on a 400x400 map. Center the map on Yellowstone
National Park at a reasonable zoom level, and use the object's 'galleryline'
view for the map popups:

{def $pictures = fetch('content','tree', hash(
                'parent_node_id', 243,
                'class_filter_type', 'include',
                'class_filter_array', array('image')))}

{include uri='design:gmaps.tpl'
    locations=$pictures
    location_attribute='gps_point'
    center=array(44.62566, -110.5389)
    zoom=8
    popup_view='galleryline'
}


Pull the 'location' attribute from the object at node 415, and display a
small map centered on that point. Don't display any markers.
{def $center = fetch('content','node', hash(
                'node_id', 415))}


{include uri='design:gmaps.tpl'
    center=$center.object.data_map.location.content
    zoom=8
    size=array(150,150)
}



See the samples folder for samples of customized maps.


