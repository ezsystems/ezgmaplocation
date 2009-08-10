<?php
//
// Definition of ezgmlTemplateOperators class
//
// SOFTWARE NAME: eZ Google Maps Location
// SOFTWARE RELEASE: 1.0
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

class ezgmlTemplateOperators
{
    function ezgmlTemplateOperators()
    {
    }

    function operatorList()
    {
        return array( 'fetch_by_location' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'fetch_by_location' => array( 'params' => array( 'type' => 'array',
                                              'required' => true,
                                              'default' => array() ))
        );
                                              
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        $ret = false;
    	switch ( $operatorName )
        {
            case 'fetch_by_location':
            {
                $latitude = isset( $namedParameters['params']['latitude'] ) ? $namedParameters['params']['latitude'] : 0;
                $longitude = isset( $namedParameters['params']['longitude'] ) ? $namedParameters['params']['longitude'] : 0;
                $distance = isset( $namedParameters['params']['distance'] ) ? $namedParameters['params']['distance'] : 0.5;
                $limit = isset( $namedParameters['params']['limit'] ) ? $namedParameters['params']['limit'] : null;
                $offset = isset( $namedParameters['params']['offset'] ) ? $namedParameters['params']['offset'] : 0;
                $asObject = isset( $namedParameters['params']['as_object'] ) ? $namedParameters['params']['as_object'] : true;

            	$ret = eZGmapLocation::fetchMainNodesByDistance( $latitude, $longitude, $distance, $limit, $offset, $asObject );
            } break;
        }
        $operatorValue = $ret;
    }
}

?>