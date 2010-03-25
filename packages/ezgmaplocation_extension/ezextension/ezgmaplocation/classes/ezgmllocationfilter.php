<?php
//
// Definition of ezgmlLocationFilter class
//
// Created on: <10-Aug-2009 12:42:08 ar>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Google Maps Location
// SOFTWARE RELEASE: 1.x
// COPYRIGHT NOTICE: Copyright (C) 2009-2010 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

class ezgmlLocationFilter
{
    function ezgmlLocationFilter()
    {
    }

    function createSqlParts( $params )
    {
        /*
         * Filtering nodes within a area relative to supplied coordinates.
         * 
         * Params can be either hash or array, in case of array this is the order:
         * param 1: latitude
         * param 2: longitude
         * param 3: distance (Optional radius length, default is 0.5)
         * param 4: arccosine (Optional, calculate accurate distance, default is false())
         * param 5: distance_as_circle (Optional, see 'NOTE on distance accuracy', default is false())
         * param 6: arccosine_distance (Optional, see 'NOTE on arccosine_distance', default is 0.005)
         * 
         * Note: All params needs to be numeric (incl numeric string values, lik '5.5')
         * 
         * Full example for fetching nodes that are within a certain area:
         * 
         * {def $users_in_oslo = fetch( 'content', 'tree', hash(
                                      'parent_node_id', 1503,
                                      'limit', 3,
                                      'sort_by', array( 'distance', true() ),
                                      'class_filter_type', 'include',
                                      'class_filter_array', array( 'user' ),
                                      'extended_attribute_filter', hash( 'id', 'ezgmlLocationFilter', 'params', hash( 'latitude', 59.917, 'longitude', 10.729, 'distance', 0.35 ) )
                                      ) )}
         *
         * NOTE on distance accuracy:
         * This filter uses a bounding box to make sure the sql is fast, this means distance is actually a square and not circle by default.
         * If you want more accuracy in the filter, use 'distance_as_circle' setting:
         *    false()       : Default, fastest, no additional calculation other then the bounding box (square) distance
         *    'pythagorean' : Use somewhat fast pythagorean calculation, accurate for small distances ( < 60km  )
         *    'arccosine'   : Use full arccosine calculation, in this case provide 'arccosine_distance' as well
         *
         * NOTE on arccosine_distance:
         * A double number, if you have distance in km divide by 6371, if miles divide by 3959
         * For use in addition to 'distance' when 'distance_as_circle' is set to 'arccosine'
         *
         * NOTE on sorting:
         * You can optionally sort on 'distance' when this filter is used.
         *   
         * NOTE on as_object = false:
         * If you fetch content using the 'as_object' parameter set to false(), you'll get additional columns:
         *     latitude
         *     longitude
         *     distance  A simple pythagorean calculated distance, only for use in sorting, as it's not
         *               accurate enough to be used for basis of km and miles.
         *     arccosine An accurate distance calculation, multiply by 6371 for km and 3959 for miles
         *               Is not included by default, include by setting 'arccosine' to true()
         *                 
         *        
         * CREDITS:
         * arccosine SQL based on "Beginning Google Maps Mashups with Mapplets, KML & GeoRSS: From Novice
         * to Professional (Expert's Voice in Web Development)" by Sterling Udell, page 257
         *
         * Also found as "Spherical Law of Cosines" on http://www.movable-type.co.uk/scripts/latlong.html
         * and http://www.movable-type.co.uk/scripts/gis-faq-5.1.html in JavaScript and Excel code
         * 
         */


        $latitude  = isset( $params['latitude'] ) && is_numeric( $params['latitude'] ) ? $params['latitude'] : ( isset( $params[0] ) && is_numeric( $params[0] ) ? $params[0] : 59.917 );
        $longitude = isset( $params['longitude'] ) && is_numeric( $params['longitude'] ) ? $params['longitude'] : ( isset( $params[1] ) && is_numeric( $params[1] ) ? $params[1] : 10.729 );
        $distance  = isset( $params['distance'] ) && is_numeric( $params['distance'] ) ? $params['distance'] : ( isset( $params[2] ) && is_numeric( $params[2] ) ? $params[2] : 0.5 );
        $arccosine = isset( $params['arccosine'] ) ? $params['arccosine'] : ( isset( $params[3] ) ? $params[3] : false );
        $asCircle  = isset( $params['distance_as_circle'] ) ? $params['distance_as_circle'] : ( isset( $params[4] ) ? $params[4] : false );
        $aDistance = isset( $params['arccosine_distance'] ) && is_numeric( $params['arccosine_distance'] ) ? $params['arccosine_distance'] : ( isset( $params[5] ) && is_numeric( $params[5] ) ? $params[5] : 0.005 );

        $minLatitude = $latitude - $distance;
        $maxLatitude = $latitude + $distance;
        $minLongitude = $longitude - $distance;
        $maxLongitude = $longitude + $distance;

        $sqlSelect = ",
            ezgmaplocation.latitude AS latitude,
            ezgmaplocation.longitude AS longitude,
            SQRT(POW(ezgmaplocation.latitude - $latitude,2) + POW( ezgmaplocation.longitude - $longitude,2)) AS distance";

        if ( $arccosine )
        {
            $sqlSelect .= ",
                acos( cos(radians( $latitude )) * cos(radians( ezgmaplocation.latitude )) *
                     cos(radians( ezgmaplocation.longitude  -  $longitude )) +
                     sin(radians( $latitude )) * sin( radians( ezgmaplocation.latitude ))) AS arccosine_distance";
        }

        $sqlWhere = "ezgmaplocation.latitude >= $minLatitude AND ezgmaplocation.latitude <= $maxLatitude AND
                     ezgmaplocation.longitude >= $minLongitude AND ezgmaplocation.longitude <= $maxLongitude AND";

        if ( $asCircle === 'pythagorean' )
        {
            $sqlWhere .= "
                     SQRT(POW(ezgmaplocation.latitude - $latitude,2) + POW(ezgmaplocation.longitude - $longitude,2)) <= $distance AND";
        }
        else if ( $asCircle === 'arccosine' )
        {
            $sqlWhere .= "
                     acos( cos(radians( $latitude )) * cos(radians( ezgmaplocation.latitude )) *
                     cos(radians( ezgmaplocation.longitude  -  $longitude )) +
                     sin(radians( $latitude )) * sin( radians( ezgmaplocation.latitude ))) <= $aDistance AND";
        }

        $sqlWhere .= "
                     ezcontentobject_attribute.id = ezgmaplocation.contentobject_attribute_id AND
                     ezcontentobject_attribute.version = ezgmaplocation.contentobject_version AND
                     ezcontentobject.id = ezcontentobject_attribute.contentobject_id AND
                     ezcontentobject.current_version = ezcontentobject_attribute.version AND";

        return array('tables' => ', ezcontentobject_attribute, ezgmaplocation', 'joins' => $sqlWhere, 'columns' => $sqlSelect );
    }
}
?>