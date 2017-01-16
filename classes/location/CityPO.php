<?php
/**
 * This file contains the definition for ConsultaPO class
 *  
 */

/**
 * @author Javier Díaz Garzón
 * @desc The class allow to fetching, listing, moving, storing and deleting information of the persistent object ConsultaPO
 */
class CityPO extends eZPersistentObjectRemote
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
        static $definition = array( 'fields' => array( 'country' => array( 'name' => 'Country',
																				'datatype' => 'string',
																				'default' => '',
																				'required' => true ),
				        								 'city' => array( 'name' => 'City',
																			   		 'datatype' => 'string',
																			   		 'default' => '',
																			   		 'required' => true ),
				        								 'latitude' => array( 'name' => 'Latitude',
																			     'datatype' => 'float',
																			     'default' => 0,
																			     'required' => true ),
				        								 'longitude' => array( 'name' => 'Longitude',
																			     'datatype' => 'float',
																			     'default' => 0,
																			     'required' => true ) ),
                      'keys' => array( 'city' ),
                      'sort' => array( 'country' => 'asc' ),
                      'class_name' => 'CityPO',
                      'name' => 'city' );
        return $definition;
		
    }
	
	/**
	 * Create the persistent object
	 * @param objectData
	 * @return eZPersistentObject
	 */
    static function create( $objectData )
    {
		$row = array( 'country' => isset($objectData['Country']) ? $objectData['Country'] : '',
					  'city' => isset($objectData['City']) ? $objectData['City'] : '',
					  'latitude' => isset($objectData['Latitude']) ? $objectData['Latitude'] : 0,
					  'longitude' => isset($objectData['Longitude']) ? $objectData['Longitude'] : 0
					);
		return new CityPO( $row );  	
    }
    
    
    static function fetch( $cityName, $asObject = true )
    {
        return self::fetchObject( self::definition(),
                                  null,
                                  array( 'City' => $cityName ),
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
	    		
    static function fetchCitiesByCountry( $countryName )
    {
		return self::fetchObjectList( self::definition(),
                                  null,
                                  array('country' => $countryName),
                                  true );		
    }
    
    static function cityExistsOnCountry($countryName, $cityName)
    {
    	$result =  self::fetchObject( self::definition(),
                                  null,
                                  array('country' => $countryName, 'city' => $cityName),
                                  true );
        return ($result instanceof CityPO);
    }
	    		
    static function fetchCitiesByCountryLike( $value, $state, $country )
    {
		if ($country == 'US')
		{
	    	$select = "SELECT DISTINCT zipcodes_usa.city AS label, 
							  zipcodes_usa.city AS value ";
			$from   = "FROM   zipcodes_usa ";
			$where  = "WHERE  zipcodes_usa.city like '" . $value . "%' ";
			$where .= "AND    zipcodes_usa.state  = '". $state ."' ";		
			$order  = "ORDER BY zipcodes_usa.city ";
			$limit  = "LIMIT  10 ";		
		}
		else
		{
	    	$select = "SELECT DISTINCT city.city AS label, 
							  city.city AS value ";
			$from   = "FROM   city ";
			$where  = "WHERE  city.city like '" . $value . "%' ";
			$where .= "AND    city.country  = '". $country ."' ";		
			$order  = "ORDER BY city.city ";
			$limit  = "LIMIT  10 ";
		}
		
		$query = $select . $from . $where . $order. $limit;
		
		$db = eZPersistentObjectRemote::setDB();
				
		$rows = $db->arrayQuery( $query );
		
		return $rows;	
    }
  
	
}

?>
