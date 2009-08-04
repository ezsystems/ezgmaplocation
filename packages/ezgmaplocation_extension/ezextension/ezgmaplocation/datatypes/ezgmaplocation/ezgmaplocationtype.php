<?php
//
// Definition of eZGmapLocationType class
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
 * File containing the eZGmapLocationType class.
 *
 * @package eZDatatype
 */

/**
 * Class providing the eZGmapLocationType datatype.
 *
 * @package eZDatatype
 * @see eZGmapLocation
 */

class eZGmapLocationType extends eZDataType
{
    const DATA_TYPE_STRING = 'ezgmaplocation';

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezi18n( 'extension/ezgmaplocation/datatypes', "GMaps Location", 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
    }

    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_data_gmaplocation_latitude_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_data_gmaplocation_longitude_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {

            $latitude = $http->postVariable( $base . '_data_gmaplocation_latitude_' . $contentObjectAttribute->attribute( 'id' ) );
            $longitude = $http->postVariable( $base . '_data_gmaplocation_longitude_' . $contentObjectAttribute->attribute( 'id' ) );
            $classAttribute = $contentObjectAttribute->contentClassAttribute();
            if ( $latitude == '' or
                 $longitude == '' )
            {
                if ( !$classAttribute->attribute( 'is_information_collector' ) && $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'extension/ezgmaplocation/datatypes',
                                                                         'Missing Latitude/Longitude input.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
                else
                {
                    return eZInputValidator::STATE_ACCEPTED;
                }
            }
        }
        else if ( !$classAttribute->attribute( 'is_information_collector' ) && $contentObjectAttribute->validateIsRequired() )
        {
            return eZInputValidator::STATE_INVALID;

        }
        else
        {
            return eZInputValidator::STATE_ACCEPTED;
        }
    }

    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {

        $latitude = $http->postVariable( $base . '_data_gmaplocation_latitude_' . $contentObjectAttribute->attribute( 'id' ) );
        $longitude = $http->postVariable( $base . '_data_gmaplocation_longitude_' . $contentObjectAttribute->attribute( 'id' ) );


        $location = new eZGmapLocation( $latitude, $longitude );

        $contentObjectAttribute->setContent( $location );
        return true;
    }

    function storeObjectAttribute( $contentObjectAttribute )
    {
        $location = $contentObjectAttribute->content();
        $contentObjectAttribute->setAttribute( "data_text", $location->xmlString() );
    }

    function objectAttributeContent( $contentObjectAttribute )
    {
        $location = new eZGmapLocation( '', '', '' );
        $location->decodeXML( $contentObjectAttribute->attribute( "data_text" ) );
        return $location;
    }

    function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( "data_text" );
    }

    function title( $contentObjectAttribute, $name = null )
    {
        $location = new eZGmapLocation( '', '', '' );
        $location->decodeXML( $contentObjectAttribute->attribute( "data_text" ) );
        return $location->attribute('latitude') . ', ' . $location->attribute('longitude');
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return true;
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion == false )
        {
            $location = $contentObjectAttribute->content();
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            if ( !$location )
            {
                $location = new eZGmapLocation( $contentClassAttribute->attribute( 'data_text1' ), '', '' );
            }
            else
            {
                $location->setLatitude('');
                $location->setLongitude('');
            }
            $contentObjectAttribute->setAttribute( "data_text", $location->xmlString() );
            $contentObjectAttribute->setContent( $location );
        }
    }

    /**
     * @see eZDataType::serializeContentObjectAttribute
     */
    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $success = $dom->loadXML( $objectAttribute->attribute( 'data_text' ) );
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );

        if ( $success )
        {
            $importedNode = $node->ownerDocument->importNode( $dom->documentElement, true );
            $node->appendChild( $importedNode );
        }
        else
        {
            eZDebug::writeError( 'Error parsing XML from data_text', __METHOD__ );
        }

        return $node;
    }

    /**
     * @see eZDataType::unserializeContentObjectAttribute
     */
    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        $locationNode = $attributeNode->getElementsByTagName( 'ezgmaplocation' )->item( 0 );

        eZDebug::writeDebug( $locationNode->ownerDocument->saveXML( $locationNode ) );

        if ( $locationNode )
        {
            $objectAttribute->setAttribute( 'data_text', $locationNode->ownerDocument->saveXML( $locationNode ) );
        }
    }
}

eZDataType::register( eZGmapLocationType::DATA_TYPE_STRING, "eZGmapLocationType" );

?>
