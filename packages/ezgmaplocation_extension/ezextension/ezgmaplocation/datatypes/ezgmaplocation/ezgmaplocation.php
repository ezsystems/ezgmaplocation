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
class eZGmapLocation extends eZPersistentObject
{
    /*!
     Initializes a new object.
    */
    function __construct( $row )
    {
        $this->eZPersistentObject( $row );
    }

    /*!
     \reimp
    */
    public static function definition()
    {
        static $def = array( "fields" => array( 'contentobject_attribute_id' => array(
                                                        'name' => 'contentobject_attribute_id',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'contentobject_attribute_version' => array( 
                                                        'name' => 'contentobject_attribute_version',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'latitude' => array( 
                                                        'name' => 'latitude',
                                                        'datatype' => 'float',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'longitude' => array( 'name' => "longitude",
                                                        'datatype' => 'float',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'address' => array( 'name' => 'street',
                                                        'datatype' => 'string',
                                                        'default' => '',
                                                        'required' => false )
                                                ),
                      'keys' => array( 'contentobject_attribute_id', 'contentobject_attribute_version' ),
                      'function_attributes' => array(  ),
                      'class_name' => 'eZGmapLocation',
                      'name' => 'ezgmaplocation' );
        return $def;
    }

    /**
     * Fetch map location by attribute id and version
     *
     * @param int $attributeId
     * @param int $version
     * @param bool $asObject
     * @return eZGmapLocation|null
     */
    public static function fetch( $attributeId, $version, $asObject = true )
    {
        $list = eZPersistentObject::fetchObjectList( 
                                        self::definition(),
                                        null,
                                        array( 'contentobject_attribute_id' => $attributeId, 'contentobject_attribute_version' => $version  ), 
                                        null,
                                        null,
                                        $asObject
                );
        if ( isset( $list[0] ) )
            return $list[0];
        return null;
    }

    /**
     * Create a eZGmapLocation (but do not store it, thats up to you!)
     * Make sure you don't create if one with same id / version exists
     * Use fetch instead if that is the case.
     * 
     * @param int $attributeId
     * @param int $version
     * @return eZGmapLocation
     */
    static function create( $attributeId, $version, $latitude = 0.0, $longitude = 0.0, $address = ''  )
    {
        $object = new self( array( 'contentobject_attribute_id' => $attributeId,
                                   'contentobject_attribute_version' => $version,
                                   'latitude' => $latitude,
                                   'longitude' => $longitude,
                                   'address' => $address
        ) );
        return $object;
    }

    /**
     * Remove a eZGmapLocation object form database.
     * 
     * @param int $attributeId
     * @param int|null $version (Optional, deletes all versions if null or ommited)
     */
    static function removeById( $attributeId, $version = null )
    {
        if ( $version !== null )
    	   $conds = array( 'contentobject_attribute_id' => $attributeId, 'contentobject_attribute_version' => $version  );
    	else
    	    $conds = array( 'contentobject_attribute_id' => $attributeId );

        eZPersistentObject::removeObject( self::definition(), $conds, null );
    }

    /**
     * Fetch main nodes by radius distance (as specified by currently published ezgmaps data)
     * NOTE: Private/Internal api, as in: might change in later versions!
     * 
     * @param float $latitude
     * @param float $longitude
     * @param float $distance Radius distance, {@see http://en.wikipedia.org/wiki/Wikipedia:WikiProject_Geographical_coordinates} under "Precision"
     * @param int|null $limit
     * @param int $offset
     * @param bool $asObject
     * @return array
     */
    public static function fetchMainNodesByDistance( $latitude, $longitude, $distance = 0.5, $limit = null, $offset = 0, $asObject = true )
    {
        $minLatitude = $latitude - $distance;
        $maxLatitude = $latitude + $distance;
        $minLongitude = $longitude - $distance;
        $maxLongitude = $longitude + $distance;
        
        if ( $limit !== null )
        {
            $limit = array( 'offset' => $offset, 'limit' => $limit );
        }
        
        $limitation = false;
        $limitationList = eZContentObjectTreeNode::getLimitationList( $limitation );
        $sqlPermissionChecking = eZContentObjectTreeNode::createPermissionCheckingSQL( $limitationList );

        $languageFilter = ' AND ' . eZContentLanguage::languagesSQLFilter( 'ezcontentobject' );
        
        $useVersionName     = true;
        $versionNameTables  = eZContentObjectTreeNode::createVersionNameTablesSQLString ( $useVersionName );
        $versionNameTargets = eZContentObjectTreeNode::createVersionNameTargetsSQLString( $useVersionName );
        $versionNameJoins   = eZContentObjectTreeNode::createVersionNameJoinsSQLString( $useVersionName );

        $showInvisibleNodesCond = eZContentObjectTreeNode::createShowInvisibleSQLString( true );
        
        $db = eZDB::instance();
        $sql = "SELECT
                         ezcontentobject.*,
                         ezcontentobject_tree.*,
                         ezcontentclass.serialized_name_list as class_serialized_name_list,
                         ezcontentclass.identifier as class_identifier,
                         ezcontentclass.is_container as is_container
                         $versionNameTargets
                    FROM
                         ezgmaplocation,
                         ezcontentobject_attribute,
                         ezcontentobject,
                         ezcontentobject_tree,
                         ezcontentclass
                         $versionNameTables
                         $sqlPermissionChecking[from]
                    WHERE
                         ezgmaplocation.latitude > $minLatitude AND ezgmaplocation.latitude < $maxLatitude AND
                         ezgmaplocation.longitude > $minLongitude AND ezgmaplocation.longitude < $maxLongitude AND
                         ezcontentobject_attribute.id = ezgmaplocation.contentobject_attribute_id AND
                         ezcontentobject_attribute.version = ezgmaplocation.contentobject_attribute_version AND
                         ezcontentobject.id = ezcontentobject_attribute.contentobject_id AND
                         ezcontentobject.current_version = ezcontentobject_attribute.version AND
                         ezcontentobject_tree.contentobject_id = ezcontentobject.id AND
                         ezcontentobject_tree.node_id = ezcontentobject_tree.main_node_id AND
                         ezcontentclass.id = ezcontentobject.contentclass_id AND
                         ezcontentclass.version=0
                         $versionNameJoins
                         $showInvisibleNodesCond
                         $sqlPermissionChecking[where]
                         $languageFilter
                    ORDER BY ezcontentobject.published ASC;";

        $server = isset( $sqlPermissionChecking['temp_tables'][0] ) ? eZDBInterface::SERVER_SLAVE : false;

        $ret = $db->arrayQuery( $sql, $limit, $server );

        $db->dropTempTableList( $sqlPermissionChecking['temp_tables'] );

        unset($db);

        if ( isset( $ret[0] ) && is_array( $ret ) )
        {
            if ( $asObject )
            {
                $ret = eZContentObjectTreeNode::makeObjectsArray( $ret );
            }
            
        }
        else if ( $ret === false )
        {
            eZDebug::writeError( 'The ezgmaplocation table seems to be missing,
                          contact your administrator', __METHOD__ );
            $ret = array();
        }
        else
        {
            $ret = array();
        }
        return $ret;
    }
}
?>