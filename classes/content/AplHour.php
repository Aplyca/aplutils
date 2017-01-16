<?php

class AplHour extends AplInputAttributeType
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
		
    	if(!is_array($input))
    	{
    		$this->setValidationError();
    		return false;  		
    	}
    	else
    	{    		    		
    		$longCheck = AplTypeValidator::validateText($input['hours']) &&  AplTypeValidator::validateText($input['mins']);    		    		    	
    		if(!$longCheck)
    		{
    			$this->setValidationError();
    			return false;
    		}    		 

    		if(ctype_digit($input['hours']) && ctype_digit($input['mins']))
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
	
	function fetchInput($http)
    {
        if($http->hasVariable($this->getIdentifier()))
    	{
    		$this->setValue($http->variable($this->getIdentifier())); 
    	}
	}
	
	
}

?>
