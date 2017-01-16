<?php

class AplInputObject
{
	var $attributes;
	var $eZContentObjectAttributes;
	private $internalAttributes;
	private $staticSettings;	
	private $attributeFactory;
	
	function __construct($iniFile, $ignoreGroups = array(), $objectId=false) 
    {
    	$this->settings = eZINI::instance($iniFile);
    	$this->attributeFactory = new AplAttributeInputFactory($iniFile, $ignoreGroups);    	
    	$eZAttributeFactory =  AplInputEZAttributeFactory::instance($iniFile, $objectId);
    	if($eZAttributeFactory)
    	{
    		$this->eZContentObjectAttributes = $eZAttributeFactory->buildAttributes();
    	}     	    	
    	$this->attributes = $this->attributeFactory->buildAttributes();
    	$this->staticSettings = $this->attributeFactory->buildStaticSettings();
    }
    
   
	function validateAttributesInput()
    {    	
		$validation = array();
		$validation = 1;
		$http= eZHTTPTool::instance();
    	foreach($this->attributes as $attribute)
    	{    		    		
    		$temp_validation = $attribute->validateInput($http);
    		if(!$temp_validation)
    		{  			
    			$validation = 0;
    		}
    		else 
    		{
    			$attribute->fetchInput($http);    			   
    		}    		 
    	}    	
    	if(!empty($this->eZContentObjectAttributes))
    	{
    		$eZAttrvalidation = ApleZTools::validateAttributesInput($this->eZContentObjectAttributes);    	
    		return $validation && $eZAttrvalidation['result'];	
    	}
    	else
    	{
    		return $validation;
    	}    	
    }
    
    function getAttributes()
    {
    	return $this->attributes;
    }
	 
	function getStaticAttributes(){
		return $this->staticSettings;	 
	}
    
       
    function setAttributesFromImput()
    {
    	$http= eZHTTPTool::instance();   					
      	foreach($this->attributes as $attribute)
    	{
    		$attribute->fetchInput($http);    		
    	}
    }
    
    function setInternalAttributes($internalAttributes)
    {
    	$this->internalAttributes = $internalAttributes;
    }
    
    function valuesToArray()
    {
    	$arrayAttributes = array();
    	foreach($this->attributes as $attribute)
    	{
    		$attrId = $attribute->getIdentifier();
    		$atrValue = $attribute->getValue();
    		$arrayAttributes[$attrId]=$atrValue;
    	}    	
    }
    
    function attributesToArray($index=true)
    {
    	$arrayAttributes = array();
    	if($index)
    	{   	    	
	    	foreach($this->attributes as $attribute)
	    	{    	
	    		$group = $attribute->getGroup();
	    		if(!isset($group))
	    		{
	    			$arrayAttributes[$group] = array();
	    		}    		
	    		$attrId = $attribute->getIdentifier();
	    		$validationError =  $attribute->hasValidationError()?1:0;
	    		$arrayAttributes[$group][$attrId] = array('name'=> $attribute->getName(), 'value'=> $attribute->getValue(), 'data' => $attribute->getData(),
																		'identifier'=> $attribute->getIdentifier(), 'type' => $attribute->getTypeIdentifier(),
																		'required' => $attribute->isRequired(), 'validation_error' => $validationError);
	    		if($arrayAttributes[$group][$attrId]['type'] == 'fieldset')
    			{
    				$arrayAttributes[$group][$attrId]['data'] = $this->fieldDataToArray($arrayAttributes[$group][$attrId]['data']);
    				$arrayAttributes[$group][$attrId]['value'] = $attribute->arrayValue();
    			}
	    		
	    	}   
  			$arrayAttributes['eZAttributes'] = array(); 	
  			if(!$this->eZContentObjectAttributes )
  				return $arrayAttributes;
    		foreach($this->eZContentObjectAttributes as $eZContentObjectAttribute)
    		{
    			$arrayAttributes['eZAttributes'][$eZContentObjectAttribute->ContentClassAttributeIdentifier] = $eZContentObjectAttribute;
    		}    		    	 	
	    	return $arrayAttributes;    	
    	}	
    	else
    	{
			foreach($this->attributes as $attribute)
	    	{    	    		
	    		$attrId = $attribute->getIdentifier();
				$arrayAttributes[$attrId] =  $attribute->getValue();
	    	}  
	    	return $arrayAttributes;    	            		
    	}
    }
    
    function getAttributesGroup($group, $index=true)
    {
    	$arrayAttributes = array();
    	if($index)
    	{
	    	foreach($this->attributes as $attribute)
	    	{    	
	    		if($attribute->getGroup() == $group)
	    		{
	    			$attrId = $attribute->getIdentifier();
	    			$validationError =  $attribute->hasValidationError()?1:0;
	    			$arrayAttributes[$attrId] = array('name'=> $attribute->getName(), 'value'=> $attribute->getValue(), 'identifier'=> $attribute->getIdentifier(), 'type' => $attribute->getTypeIdentifier(), 'required' => $attribute->isRequired(), 'validation_error' => $validationError);    			    			
	    		}			
	    	}    	
	    	return $arrayAttributes;    		
    	}
    	else
    	{
			foreach($this->attributes as $attribute)
	    	{   
	    		if($attribute->getGroup() == $group)
    			{	    		 	    		
		    		$attrId = $attribute->getIdentifier();
					$arrayAttributes[$attrId] =  $attribute->getValue();
    			}
	    	}  
	    	return $arrayAttributes;
    	}      	    	
    }
    
    
    function toArray()
    {    	      	
    	//TODO Verificar los if de los merges
    	$arrayAttributes = array();
    	foreach($this->attributes as $attribute)
    	{    	    		
    		$attrId = $attribute->getIdentifier();
			$arrayAttributes[$attrId] =  $attribute->getValue();
    	}    	
    	$serialized = array();
    	if( !isset($this->internalAttributes) &&  !isset($this->staticSettings)   )
    	{
    		$serialized = $arrayAttributes;
    	}
    	elseif( ! isset($this->internalAttributes)   )
    	{
    		$serialized = array_merge($arrayAttributes, $this->staticSettings);
    	}
    	elseif( ! isset($this->staticSettings)   )
    	{
    		$serialized = array_merge($arrayAttributes, $this->internalAttributes);
    	}
    	else
    	{
    		$serialized = array_merge($arrayAttributes, $this->staticSettings, $this->internalAttributes);	
    	}    	
		$eZAttrsArray = $this->eZAttributesToArray();		
    	if(!empty($this->eZContentObjectAttributes))
    	{
    		$serialized = array_merge($serialized, $eZAttrsArray);
    	}    	    	
    	return $serialized;    		
    }
    
    
    function eZAttributesToArray()
    {
    	$data = array();
    	foreach($this->eZContentObjectAttributes as $attr)
    	{
    		if($attr->DataTypeString == 'ezcountry')
    		{
    			$tempVal = $attr->value();
    			$tempKey = reset($tempVal['value']);
    			$data[$attr->ContentClassAttributeIdentifier] =$tempKey['Alpha3'];	
    		}
    		else
    		{
    			$data[$attr->ContentClassAttributeIdentifier] = $attr->value();	
    		}    		 
    		
    	}
    	return $data;
    }
    
    function attributeMetadataArray()
    {
    	$arrayAttributes = array();
    	foreach($this->attributes as $attribute)
    	{    	
    		$group = $attribute->getGroup();
    		if(!isset($group))
    		{
    			$arrayAttributes[$group] = array();
    		}    		
    		$attrId = $attribute->getIdentifier();
    		$arrayAttributes[$group][$attrId] = array('name'=> $attribute->getName(), 'identifier'=> $attribute->getIdentifier(), 'data' => $attribute->getData(),
			                                          'type' => $attribute->getTypeIdentifier(), 'required' => $attribute->isRequired());
    		if($arrayAttributes[$group][$attrId]['type'] == 'fieldset')
    		{
    			$arrayAttributes[$group][$attrId]['data'] = $this->fieldDataToArray($arrayAttributes[$group][$attrId]['data']);
    			
	    		$arrayAttributes[$group][$attrId]['value'] =  $this->valuesToArray();
    		}
			//$arrayAttributes[$attrId] = array('type' => $attribute->getTypeIdentifier(), 'required' => $attribute->isRequired());
    	}   
    	if(! isset($this->eZContentObjectAttributes) )
    		return $arrayAttributes;
    	$arrayAttributes['eZAttributes'] = array(); 	
    	foreach($this->eZContentObjectAttributes as $eZContentObjectAttribute)
    	{
    		$arrayAttributes['eZAttributes'][$eZContentObjectAttribute->ContentClassAttributeIdentifier] = $eZContentObjectAttribute;
    	}
    	return $arrayAttributes;
    }
    
    function fieldDataToArray($fieldData)
    {
   		$arrayAttributes = array();
    	foreach($fieldData as $attribute)
    	{
    		$group = $attribute->getGroup();
    		if(!isset($group))
    		{
    			$arrayAttributes[$group] = array();
    		}    		
    		$attrId = $attribute->getIdentifier();
    		$arrayAttributes[$attrId] = array('name'=> $attribute->getName(), 'identifier'=> $attribute->getIdentifier(), 'data' => $attribute->getData(),
			                                          'type' => $attribute->getTypeIdentifier(), 'required' => $attribute->isRequired());
    	}
    	return $arrayAttributes;
    }
    
    
    /*
     * 
     * 
     *@param $order_id = 
     */
    function ImportFromOrder($order_id)
    {    	
    	$orderManager = new ProductOrderManager($order_id);
    	$billingData = $orderManager->getBillingData();
    	$shippingData = $orderManager->getShippingData(); 	    	  	
		//$data = array_merge($billingData,$shippingData);
      	foreach($this->attributes as $attribute)
    	{
    		$attribute->fetchFromArray($shippingData);    		
    	}   	    	    
    }
	
	
}

?>