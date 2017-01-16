<?php

class AplCheckbox extends AplInputAttributeType
{
	function validateInput($http)
    {
    	
	    if( $this->isRequired()  &&  ( $http->hasVariable($this->getIdentifier()) == false  ) )
    	{
    		$this->setValidationError();
    		return false;
    	}
    	elseif( $this->isRequired()  &&  ( $http->hasVariable($this->getIdentifier()) == true  ) )
    	{
    		$input = $http->variable($this->getIdentifier());
    		if($input == 'on')
    			return true;
    		else
    			return false;
    	}
    	else
    		return true; 		    		   
	}
	
	function fetchInput($http)
    {
        if($http->hasVariable($this->getIdentifier()))
    	{
    		$this->setValue('on'); 
    	}
	}	
}

?>
