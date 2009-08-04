<?php
//
// Definition of eZGmapLocation class
//
// SOFTWARE NAME: Blend Gmap Location Class
// SOFTWARE RELEASE: 0.5
// COPYRIGHT NOTICE: Copyright (C) 2006-2009 Blend Interactive
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

/**
 * File containing the eZGmapLocation class.
 *
 * @package eZDatatype
 */

/**
 * Helper class used along with the eZGmapLocationType datatype.
 * Provides a datatype for storing latitude & longitude values.
 *
 * @package eZDatatype
 * @see eZGmapLocationType
 */
class eZGmapLocation
{
    /**
     * Constructor.
     *
     * @param $latitude
     * @param $longitude
     */
    function __construct( $latitude, $longitude )
    {
        $this->Latitude = $latitude;
        $this->Longitude = $longitude;
    }

    /**
     * Returns the list of supported attributes
     *
     * @return array
     */
    function attributes()
    {
        static $atr = array( 'latitude',
                             'longitude' );
        return $atr;
    }

    /**
     * Returns true if attribute is set.
     *
     * @param $name Attribute name
     * @return boolean
     */
    function hasAttribute( $name )
    {
        return in_array( $name, $this->attributes() );
    }

    /**
     * Reads an attribute's value.
     *
     * @param $name Attribute name
     * @return mixed
     */
    function attribute( $name )
    {
        switch ( $name )
        {
            case 'latitude' :
            {
                return $this->Latitude;
            }break;
            case 'longitude' :
            {
                return $this->Longitude;
            }break;
            default:
            {
                eZDebug::writeError( "Attribute '$name' does not exist", __METHOD__ );
                return null;
            }break;
        }
    }

    /**
     * Populates the local properties from decoding an XML string.
     *
     * @param $xmlString
     * @return void
     */
    function decodeXML( $xmlString )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );

        if ( $xmlString != "" )
        {
            $success = $dom->loadXML( $xmlString );
            if ( !$success )
            {
                eZDebug::writeError( 'Failed loading XML', __METHOD__ );
                return false;
            }

            $locationElement = $dom->documentElement;

            $this->Latitude = $locationElement->getAttribute( 'latitude' );
            $this->Longitude = $locationElement->getAttribute( 'longitude' );
        }
        else
        {
            $this->Latitude = 0;
            $this->Longitude = 0;
        }
    }

    /**
     * Creates and return a well-formed XML string representing the coordinates.
     * @return string XML string
     */
    function xmlString()
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );

        $root = $doc->createElement( 'ezgmaplocation' );
        $root->setAttribute( 'latitude', $this->Latitude );
        $root->setAttribute( 'longitude', $this->Longitude );
        $doc->appendChild( $root );

        return $doc->saveXML();
    }

    /**
     * Sets Latitude value
     *
     * @param $value Latitude value
     * @return void
     */
    function setLatitude( $value )
    {
        $this->Latitude = $value;
    }

    /**
     * Sets Longitude value
     *
     * @param $value Longitude value
     * @return void
     */
    function setLongitude( $value )
    {
        $this->Longitude = $value;
    }

    /**
     * Strore the Latitude value
     * @var numeric
     */
    private $Latitude;

    /**
     * Strore the Longitude value
     * @var numeric
     */
    private $Longitude;
}

?>
