<?php

class AplFloat extends AplInputAttributeType
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
    		
    	if(AplTypeValidator::validateFloat($input))
    	{ 
    		return true;
    	}
    	else
    	{    				
    		$this->setValidationError();
    		return false;
    	}          
	}	
	
	function fetchInput($http)
    {
        if($http->hasVariable($this->getIdentifier()))
    	{
    		$this->setValue($http->variable($this->getIdentifier())); 
    	}
	}
	
	
}

?>
