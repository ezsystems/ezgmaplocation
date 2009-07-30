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

/*! \file ezgmaplocation.php
*/

/*!
  \class eZGmapLocation ezgmaplocation.php
  \ingroup eZDatatype
  \brief The class eZGmapLocation provides a datatype for storing
  \latitude & longitude values.

*/

class eZGmapLocation
{
    /*!
     Constructor
    */
    function __construct( $latitude, $longitude )
    {
        $this->Latitude = $latitude;
        $this->Longitude = $longitude;
    }

    /*!
     \return list of supported attributes
    */
    function attributes()
    {
        return array( 'latitude',
                      'longitude' );
    }

    function hasAttribute( $name )
    {
        return in_array( $name, $this->attributes() );
    }

    function attribute( $name )
    {
        switch ( $name )
        {
            case "latitude" :
            {
                return $this->Latitude;
            }break;
            case "longitude" :
            {
                return $this->Longitude;
            }break;
            default:
            {
                eZDebug::writeError( "Attribute '$name' does not exist", __METHOD__ );
                $retValue = null;
                return $retValue;
            }break;
        }
    }


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


    function xmlString()
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );

        $root = $doc->createElement( 'ezgmaplocation' );
        $root->setAttribute( 'latitude', $this->Latitude );
        $root->setAttribute( 'longitude', $this->Longitude );
        $doc->appendChild( $root );

        return $doc->saveXML();
    }

    function setLatitude( $value )
    {
        $this->Latitude = $value;
    }

    function setLongitude( $value )
    {
        $this->Longitude = $value;
    }


    private $Latitude;
    private $Longitude;
}

?>
