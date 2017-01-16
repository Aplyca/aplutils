<?php

class AplMonth extends AplInputAttributeType
{

	function validateInput($http)
    {
		if( ! $this->checkRequired($http) )
    		return false;
    	return true;    		    		   
	}
	

	
	
}


?>
