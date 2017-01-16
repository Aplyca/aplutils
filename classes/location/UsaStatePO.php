<?php
/**
 * This file contains the definition for ConsultaPO class
 *  
 */

/**
 * @author Javier Díaz Garzón
 * @desc The class allow to fetching, listing, moving, storing and deleting information of the persistent object ConsultaPO
 */
class UsaStatePO extends eZPersistentObjectRemote
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
        static $definition = array( 'fields' => array( 'name' => array( 'name' => 'Name',
																				'datatype' => 'string',
																				'default' => '',
																				'required' => true ),
				        								 'code' => array( 'name' => 'Code',
																			   		 'datatype' => 'string',
																			   		 'default' => '',
																			   		 'required' => true )),
                      'keys' => array( 'code' ),
                      'sort' => array( 'code' => 'asc' ),
                      'class_name' => 'UsaStatePO',
                      'name' => 'states_usa' );
        return $definition;
		
    }
	
	/**
	 * Create the persistent object
	 * @param objectData
	 * @return eZPersistentObject
	 */
    static function create( $objectData )
    {
		$row = array( 'name' => isset($objectData['Name']) ? $objectData['Name'] : '',
					  'code' => isset($objectData['Code']) ? $objectData['Code'] : ''
					);
		return new UsaStatePO( $row );  	
    }
    
    
    static function fetch( $stateCode, $asObject = true )
    {
        return self::fetchObject( self::definition(),
                                  null,
                                  array( 'code' => $stateCode ),
                                  $asObject );
    }	
    
    static function fetchAll()
    {
    	return self::fetchObjectList( self::definition() );
    }
	    		
  
	
}

?>
