<?php
/**
 * This file contains the definition for ConsultaPO class
 *  
 */

/**
 * @author Javier Díaz Garzón
 * @desc The class allow to fetching, listing, moving, storing and deleting information of the persistent object ConsultaPO
 */
class CountryPO extends eZPersistentObjectRemote
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
        static $definition = array( 'fields' => array( 'iso' => array( 'name' => 'Iso',
																		'datatype' => 'string',
																		'default' => '',
																		'required' => true ),
				        								 'name' => array( 'name' => 'Name',
																		  'datatype' => 'string',
																		  'default' => '',
																		  'required' => true ),
				        								 'printable_name' => array( 'name' => 'PrintableName',
																		  'datatype' => 'string',
																		  'default' => '',
																		  'required' => true ),
				        								 'iso3' => array( 'name' => 'Iso3',
																		  'datatype' => 'string',
																		  'default' => '',
																		  'required' => true ),
				        								 'numcode' => array( 'name' => 'NumCode',
																		  'datatype' => 'string',
																		  'default' => '',
																		  'required' => true ) ),
                      'keys' => array( 'iso' ),
                      'sort' => array( 'iso' => 'asc' ),
                      'increment_key' => 'iso',
                      'class_name' => 'CountryPO',
                      'name' => 'country' );
        return $definition;
		
    }
	
	/**
	 * Create the persistent object
	 * @param objectData
	 * @return eZPersistentObject
	 */
    static function create( $objectData )
    {
		$row = array( 'iso' => isset($objectData['ZipCode']) ? $objectData['ZipCode'] : '',
					  'name' => isset($objectData['Name']) ? $objectData['Name'] : '',
					  'printable_name' => isset($objectData['PrintableName']) ? $objectData['PrintableName'] : '',
					  'iso3' => isset($objectData['Iso3']) ? $objectData['Iso3'] : '',
					  'numcode' => isset($objectData['NumCode']) ? $objectData['NumCode'] : ''
					);
		return new CountryPO( $row );  	
    }
       	    		
	/**
	 * Fetch the persistent object for Iso
	 * @param $iso
	 * @return the persistent object 
	 */ 
    static function fetch( $iso, $asObject = true )
    {
        return self::fetchObject( self::definition(),
                                  null,
                                  array( 'Iso' => $iso ),
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
    	
}

?>
