<?php

class AplInputEZAttributeFactory
{
	private $eZInputSettings;
	private $object;
	
	private function __construct($eZInputSettings, $object)
    {
   		$this->eZInputSettings = $eZInputSettings;
   		$this->object = $object;	   
    }
    
    static function instance($iniFile, $objectId)
    {    	
    	if(!$objectId)
    		return false;
    	$settings = eZINI::instance($iniFile);    		
    	$eZInputSettings = $settings->group('eZInputAttributes');
    	if(!$eZInputSettings)
			return false;
		if(! $eZInputSettings['ClassIdentifier'])
			return false;
		if(empty($eZInputSettings['AttributeCategory']) && empty($eZInputSettings['SingleAttributes'])	)
		{
			return false;
		}				
		$object = eZContentObject::fetch($objectId);
		if(! $object instanceof eZContentObject)
			return false;							
		if($object->ClassIdentifier != $eZInputSettings['ClassIdentifier'])
			return false;	
		return new AplInputEZAttributeFactory($eZInputSettings, $object);			
    }
    

    public function buildAttributes()
    {
    	
    	$dataMap = $this->object->dataMap(); 
    	$selectedAttributeIdentifiers = $this->getAttributesIdentifierMap();
    	$attributes = array();
		foreach($dataMap as $objectAttribute)
		{
			if(in_array($objectAttribute->ContentClassAttributeIdentifier,$selectedAttributeIdentifiers))
			{
				$attributes[] = $objectAttribute;
			}				
		}
		return $attributes;			
    }
    
    
	function getAttributesIdentifierMap()
    {
    	$classId = eZContentClass::classIDByIdentifier($this->eZInputSettings['ClassIdentifier']);
    	$class = eZContentClass::fetch($classId);
    	$classDataMap = $class->dataMap();
    	
    	$attributeMap = array();
    	
    	
    	foreach($classDataMap as $classAttribute)
    	{    		
    		if(array_key_exists('AttributeCategory', $this->eZInputSettings))
    		{
	    		if(in_array($classAttribute->Category, $this->eZInputSettings['AttributeCategory']))
	    		{
	    			$attributeMap[] = $classAttribute->Identifier;
	    		}	
    		}    		    		
    		
    		if(array_key_exists('SingleAttributes', $this->eZInputSettings))
    		{
    			if(in_array($classAttribute->Identifier, $this->eZInputSettings['SingleAttributes']))
	    		{
	    			$attributeMap[] = $classAttribute->Identifier;
	    		}       			
    		}    			    	 	
    	}
    	return $attributeMap;
	}
    
 
	
	
}




?>