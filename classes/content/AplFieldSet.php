<?php

class AplFieldSet extends AplInputAttributeType
{
	var $fieldset;
	function validateInput($http)
    {      	
   		$input = $http->variable($this->getIdentifier());   		 
     	if( $this->isRequired() == false && $input == '' )
    	{
   			return true;
    	}      

    	$this->buildFieldSet($http);    	
    	$validationFlag = 1;
    	foreach($this->getValue() as $fields)
    	{
    		foreach($fields['attributes'] as $field)
    		{
    			$validation =  $field->validateInput($fields['inputdata']); 
    			if(!$validation)
    			{
    				$validationFlag = 0;
    			}
    			else
    			{
    				$field->setValue($fields['inputdata']->variable($field->getIdentifier()));
    			}
    		}
    	}   
    	$this->cleanCachedInputs();
   		if($validationFlag == 1)
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
	}		
	
	
	function buildFieldSet($http)
	{
		$input = $http->variable($this->getIdentifier());
    	$fieldAttributes = array();
	    foreach ($input as $fieldset)
    	{
    		$groupElement = array();
    		$fieldsetKeys = array_keys($fieldset);
    		$subInput = new AplArrayGetter($fieldset);    	 
    		$structureCheck = true;
    		foreach($this->data as $baseField)
    		{
    			$identifier = $baseField->getIdentifier();
    			if(in_array($identifier, $fieldsetKeys ))
    			{
    				$groupElement[$identifier] = clone $baseField;
    			}
    			else
    			{
    				if($baseField->getTypeIdentifier() != 'checkbox')
    				{
	    				$structureCheck = false;	
    				}    	
    				else
    				{
    					$groupElement[$identifier] = clone $baseField;
    				}		
    			}	   	
    		}
    		if($structureCheck)
    		{
    			$fieldAttributes[] = array("attributes" => $groupElement, "inputdata" => $subInput);	
    		}    		
    	}
    	$this->setValue($fieldAttributes);
	}
	
	function cleanCachedInputs()
	{
		foreach($this->value as $key =>$item)
		{			
			unset($this->value[$key]["inputdata"]);
			$this->value[$key]  = $this->value[$key]["attributes"];
		}
	}
	
	
	function arrayValue()
	{
		$fieldData = array();
		if(!$this->value)
			return $fieldData;
    	foreach($this->value as $fieldset)
    	{
    		foreach($fieldset as $attribute)
    		{
	    		$attrId = $attribute->getIdentifier();
	    		$validationError =  $attribute->hasValidationError()?1:0;
	    		$set[$attrId] = array('name'=> $attribute->getName(), 'identifier'=> $attribute->getIdentifier(), 'data' => $attribute->getData(),
				                      'value' => $attribute->getValue(), 'type' => $attribute->getTypeIdentifier(),
	    							  'required' => $attribute->isRequired(), 'validation_error' => $validationError );	    		
    		} 
    		$fieldData[] = $set; 	    		
		}		
		return $fieldData;	
	}
}


?>
