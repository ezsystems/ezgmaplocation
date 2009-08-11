<?php
//
// Definition of ezgmlLocationFilter class
//
// Created on: <10-Aug-2009 12:42:08 ar>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Google Maps Location
// SOFTWARE RELEASE: 1.x
// COPYRIGHT NOTICE: Copyright (C) 2009 eZ Systems AS
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
         * Filtering nodes witin a radius of cordinates.
         * 
         * Params can be either hash or array, in case of array this is the order:
         * param 1: latitude
         * param 2: longitude
         * param 2: distance (Optional radius length, default is 0.5)
         * 
         * Note: All params needs to be numeric (incl numeric string values, lik '5.5')
         * 
         * Full example for fetching nodes that are within a certain area:
         * 
         * {def $users_close_by = fetch( 'content', 'tree', hash(
                                      'parent_node_id', 1503,
                                      'limit', 3,
                                      'sort_by', array( 'published', false() ),
                                      'class_filter_type', 'include',
                                      'class_filter_array', array( 'user' ),
                                      'extended_attribute_filter', hash( 'id', 'ezgmlLocationFilter', 'params', hash( 'latitude', 59.917, 'longitude', 10.729 ) )
                                      ) )}
         * 
         */


        $latitude  = isset( $params['latitude'] ) && is_numeric( $params['latitude'] ) ? $params['latitude'] : ( isset( $params[0] ) && is_numeric( $params[0] ) ? $params[0] : 59.917 );
        $longitude = isset( $params['longitude'] ) && is_numeric( $params['longitude'] ) ? $params['longitude'] : ( isset( $params[1] ) && is_numeric( $params[1] ) ? $params[1] : 10.729 );
        $distance  = isset( $params['distance'] ) && is_numeric( $params['distance'] ) ? $params['distance'] : ( isset( $params[2] ) && is_numeric( $params[2] ) ? $params[2] : 0.5 );

        $minLatitude = $latitude - $distance;
        $maxLatitude = $latitude + $distance;
        $minLongitude = $longitude - $distance;
        $maxLongitude = $longitude + $distance;

        $sqlWhere = "ezgmaplocation.latitude > $minLatitude AND ezgmaplocation.latitude < $maxLatitude AND
                     ezgmaplocation.longitude > $minLongitude AND ezgmaplocation.longitude < $maxLongitude AND
                     ezcontentobject_attribute.id = ezgmaplocation.contentobject_attribute_id AND
                     ezcontentobject_attribute.version = ezgmaplocation.contentobject_attribute_version AND
                     ezcontentobject.id = ezcontentobject_attribute.contentobject_id AND
                     ezcontentobject.current_version = ezcontentobject_attribute.version AND";

        return array('tables' => ', ezcontentobject_attribute, ezgmaplocation', 'joins' => $sqlWhere, 'columns' => '');
    }
}
?>