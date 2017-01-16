<?php

class AplPassword extends AplInputAttributeType
{

	function validateInput($http)
    {   
   		$input = $http->variable($this->getIdentifier());
     	if( $this->isRequired() == false && $input == '' )
    	{
   			return true;
    	}    

    	if( ! $this->checkRequired($http) )
    		return false;    
    	
    	    		
    	if(AplTypeValidator::validateText($input))
    	{     		
    		$inputConfirm = $http->variable($this->getIdentifier() . '_confirm');
    		if($input == $inputConfirm)
    		{
    			return true;	
    		}
    		else
    		{
    			$this->setValidationError();
    			return false;
    		}
    		
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
