<?php
/**
 * This file contains the definition for UsaZIPCodePO class
 *  
 */

/**
 * @author Javier Díaz Garzón
 * @desc The class allow to fetching, listing, moving, storing and deleting information of the persistent object UsaZIPCodePO
 */
class UsaZIPCodePO extends eZPersistentObjectRemote
{
    function __construct( $row )
    {
        $this->eZPersistentObject( $row );
    }
	
	/**
	 * Defines the structure of the persistent object
	 * @return definition of the persistent object
	 */
    static function definition()
    {
        static $definition = array( 'fields' => array( 'zipcode' => array( 'name' => 'ZipCode',
																		'datatype' => 'string',
																		'default' => '',
																		'required' => true ),
				        								 'city' => array( 'name' => 'City',
																		  'datatype' => 'string',
																		  'default' => '',
																		  'required' => true ),
				        								 'state' => array( 'name' => 'State',
																		  'datatype' => 'string',
																		  'default' => '',
																		  'required' => true ),
				        								 'county' => array( 'name' => 'County',
																		  'datatype' => 'string',
																		  'default' => '',
																		  'required' => true ),
														'cityaliasname' => array( 'name' => 'CityAliasName',
																		  'datatype' => 'string',
																		  'default' => '',
																		  'required' => true ),        
				        								 'latitude' => array( 'name' => 'Latitude',
																		  'datatype' => 'string',
																		  'default' => '',
																		  'required' => true ),
				        								 'longitude' => array( 'name' => 'Longitude',
																		  'datatype' => 'string',
																		  'default' => '',
																		  'required' => true ) ),
                      'keys' => array( 'zipcode' ),
                      'sort' => array( 'zipcode' => 'asc' ),
                      'increment_key' => 'zipcode',
                      'class_name' => 'UsaZIPCodePO',
                      'name' => 'zipcodes_usa' );
        return $definition;
		
    }
	
	/**
	 * Create the persistent object
	 * @param objectData
	 * @return eZPersistentObject
	 */
    static function create( $objectData )
    {
		$row = array( 'zipcode' => isset($objectData['ZipCode']) ? $objectData['ZipCode'] : '',
					  'city' => isset($objectData['City']) ? $objectData['City'] : '',
					  'state' => isset($objectData['State']) ? $objectData['State'] : '',
					  'county' => isset($objectData['County']) ? $objectData['County'] : '',
					  'cityaliasname' => isset($objectData['CityAliasName']) ? $objectData['CityAliasName'] : '',
					  'latitude' => isset($objectData['Latitude']) ? $objectData['Latitude'] : '',
					  'longitude' => isset($objectData['Longitude']) ? $objectData['Longitude'] : ''
					);
		return new UsaZIPCodePO( $row );  	
    }
       	    		
	/**
	 * Fetch the persistent object for ZipCode
	 * @param $zipCode
	 * @return the persistent object 
	 */ 
    static function fetch( $zipCode, $asObject = true )
    {
        return self::fetchObject( self::definition(),
                                  null,
                                  array( 'zipcode' => $zipCode ),
                                  $asObject );
    }	
	    		
	/**
	 * Fetch the persistent object for several attributes
	 * @param attributes
	 * @return the persistent object 
	 */
    static function fetchByAttributes( $attributes, $asObject = true )
    {
		return self::fetchObject( self::definition(),
                                  null,
                                  $attributes,
                                  $asObject );		
    }
    
    
    static function fetchListByZIP($zip, $state=false, $asObject = true)
    {
    	if($state)
    	{
	    	return self::fetchObjectList( self::definition(),
	                                  null,
	                                  array('zipcode' => $zip, 'state' => $state),
	                                  $asObject );	
    	}
    	else
    	{
    		return self::fetchObjectList( self::definition(),
	                                  null,
	                                  array('zipcode' => $zip),
	                                  $asObject );
    	}
    }
    
    static function isCityOnState($stateCode, $city)
    {
    	$result =  self::fetchObject( self::definition(),
                                  null,
                                  array('state' => $stateCode, 'city' => $city),
                                  true );
    	return ($result instanceof UsaZIPCodePO);
    }
    
    static function isZipOnCity($city, $zip)
    {
    	$result =  self::fetchObject( self::definition(),
                                  null,
                                  array('city' => $city, 'zipcode' => $zip),
                                  true );
    	return ($result instanceof UsaZIPCodePO);
    }    
    
    static function getZipCodesByRange( $minLat, $maxLat, $minLon, $maxLon )
    {
    	$select = "SELECT DISTINCT zipcodes_usa.zipcode ";
		$from   = "FROM   zipcodes_usa ";
		$where  = "WHERE  zipcodes_usa.latitude BETWEEN ". $minLat ." AND " . $maxLat ." ";
		$where .= "AND    zipcodes_usa.longitude BETWEEN ". $minLon ." AND " . $maxLon ." ";
		$order  = "ORDER BY zipcodes_usa.zipcode ";
		
		$query = $select . $from . $where . $order;
		
		$db = eZPersistentObjectRemote::setDB();
				
		$rows = $db->arrayQuery( $query );
		
		return $rows;
    }  
      
    static function fetchListByAttributes( $attributes, $asObject = true )
    {
        return self::fetchObjectList( self::definition(),
        null,
        $attributes );
    }
      
    static function fetchListByState( $state )
    {
        $select = "SELECT DISTINCT zipcodes_usa.cityaliasname ";
		$from   = "FROM   zipcodes_usa ";
		$where  = "WHERE  zipcodes_usa.state = '". $state ."'";
		$order  = "ORDER BY zipcodes_usa.cityaliasname ";
		
		$query = $select . $from . $where . $order;
		
		$db = eZPersistentObjectRemote::setDB();
				
		$rows = $db->arrayQuery( $query );
		
		return $rows;
    }
 
    	
}

?>
