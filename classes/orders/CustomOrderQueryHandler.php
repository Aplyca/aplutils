<?php

/** 
 * @author David Sanchez Escobar
 * 
 * This class sets a custom query for searching orders
 * 
 */ 
 

class CustomOrderQueryHandler
{
	
	function __construct() 
    {      	
 	
    }   
    
    public function getCustomQuery( )
    { 		
		$customQuery = "SELECT ezorder.* 
						FROM ezorder, ezcontentobject 
						WHERE ezorder.is_archived = '0' 
						AND ezorder.is_temporary = '0' 
						AND ezcontentobject.id = ezorder.user_id";	
		return false;										
    }
    

}

?>