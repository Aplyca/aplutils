<?php

class AplYear extends AplInputAttributeType
{

	function validateInput($http)
    {    	
    	if( ! $this->checkRequired($http) )
    		return false;    		
    	
   		$input = $http->variable($this->getIdentifier());
     	if( $this->isRequired() == false && $input == '' )
    	{
   			return true;
    	}    		
    		
    	if(AplTypeValidator::validateInteger($input))
    	{ 
    		return true;
    	}
    	else
    	{    				
    		$this->setValidationError();
    		return false;
    	}     
	}
	
}

?>
