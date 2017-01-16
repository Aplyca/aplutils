<?php

class AplAttributeInputFactory
{
	private $settings;
	private $ignoreGroups;
	
	function __construct($iniFile, $ignoreGroups)
    {
   		$this->settings = eZINI::instance($iniFile);
   		$this->ignoreGroups = $ignoreGroups;	   
    }
    
    function getAttributeInstance($type)
    {
        if($type=="text")
    	{
    		return new AplText();
    	}
    	elseif($type=="textfield")
    	{
			return new AplTextField();
    	}
    	elseif($type=="email")
    	{
			return new AplEmail();
    	}
    	elseif($type=="cc_number")
    	{
    		return new AplCreditCardNumber();
    	}    
    	elseif($type=="float")
    	{
    	 	return new AplFloat();
    	}
    	elseif($type=="month")
    	{
    	 	return new AplMonth();
    	}    
    	elseif($type=="integer")
    	{
    	 	return new AplInteger();
    	}      	
 		elseif($type=="year")
    	{
    	 	return new AplYear();
    	}    
 		elseif($type=="country")
    	{
    	 	return new AplCountry();
    	}      	  
		elseif($type=="password")
    	{
    	 	return new AplPassword();
    	}       	  
		elseif($type=="select")
    	{
    	 	return new AplSelect();
    	}       	  
		elseif($type=="checkbox")
    	{
    	 	return new AplCheckbox();
    	}      
 		elseif($type=="usastate")
    	{
    	 	return new AplUsaState();
    	}
		elseif($type=="country_code")
    	{
    	 	return new AplCountry();
    	} 
		elseif($type=="hour")
    	{
    	 	return new AplHour();
    	}     	
		elseif($type=="fieldset")
    	{
    	 	return new AplFieldSet();
    	}  	    	 		
    	else return false;
    }
	
    function buildAttributesOLD()
    {
    	$attributes = array();
		$map = self::getAttributesMap();
		foreach($map as $group => $inputAttributeGroup)
		{
			foreach($inputAttributeGroup as $inputAttribute)
			{
				$attributeIdentifier = $inputAttribute['identifier'];
				$attr = $this->getAttributeInstance($inputAttribute['type']);
				if(! is_object($attr) )
				{
					eZDebug::writeError($inputAttribute['identifier'] . ' : input attribute type -' . $inputAttribute['type']  . '- not found', 'Apl Input Error');
					continue;
				}
				$attr->setName($inputAttribute['name']);
				$attr->setIdentifier($inputAttribute['identifier']);
				$attr->setTypeIdentifier($inputAttribute['type']);
				if(isset($inputAttribute['data']))
				{
					$attr->setData($inputAttribute['data']);
				}
				$attr->setGroup($group);
				if($inputAttribute['required'] == 'yes')
					$attr->setRequired();				
				else
					$attr->setOptional();
				/* special construction for fieldset type */
				if($inputAttribute['type'] == 'fieldset')
				{
					
					$attr->setData($inputAttribute['fields'] );
				}					
				$attributes[$attributeIdentifier] = $attr;
			}
		}
		return $attributes;			
    }

    function buildAttributes()
    {
    	$attributes = array();
		$map = self::getAttributesMap();
		foreach($map as $group => $inputAttributeGroup)
		{
			foreach($inputAttributeGroup as $inputAttribute)
			{
				$attributeIdentifier = $inputAttribute['identifier'];
				$attr = $this->buildAttribute($inputAttribute, $group);
				if(is_object($attr))
				{
					$attributes[$attributeIdentifier] = $attr;
				}						
			}
		}
		return $attributes;			
    }
    
    function buildAttribute($inputAttribute, $group)
    {    	
		$attr = $this->getAttributeInstance($inputAttribute['type']);
		if(! is_object($attr) )
		{
			eZDebug::writeError($inputAttribute['identifier'] . ' : input attribute type -' . $inputAttribute['type']  . '- not found', 'Apl Input Error');
			return false;
		}
		$attr->setName($inputAttribute['name']);
		$attr->setIdentifier($inputAttribute['identifier']);
		$attr->setTypeIdentifier($inputAttribute['type']);
		if(isset($inputAttribute['data']))
		{
			$attr->setData($inputAttribute['data']);
		}
		$attr->setGroup($group);
		if($inputAttribute['required'] == 'yes')
			$attr->setRequired();				
		else
			$attr->setOptional();
		/* special construction for fieldset type */
		if($inputAttribute['type'] == 'fieldset')
		{
			$fieldAttributes = array();
			foreach($inputAttribute['fields'] as $field)
			{
				$fieldAttribute = $this->buildAttribute($field,$inputAttribute['identifier']);
				if(is_object($fieldAttribute))
				{
					$fieldAttributes[] = $fieldAttribute;
				}
			}
			$attr->setData($fieldAttributes);
		}					
		return  $attr;		
    }
    
    function buildStaticSettings()
    {
    	if($this->settings->hasGroup('StaticSettings'))
    	{
    		return $this->settings->group('StaticSettings');	
    	}
    	else
    		return null;
    	    	
    }    
    
   function getAttributesMap()
    {
    	$attributeGroupsSetting = $this->settings->group('InputAttributes');	
		$inputAttributeGroups = array();	
		foreach($attributeGroupsSetting as $key => $attributeGroupSetting)
		{
			if(! in_array($key, $this->ignoreGroups) )
			{
				$inputAttributeGroups[$key] = array();
			  	foreach($attributeGroupSetting as $attrSettingName)
	    		{ 
	    			$inputAttributeGroups[$key][$attrSettingName]  = $this->settings->group($attrSettingName);    				
	    			if($inputAttributeGroups[$key][$attrSettingName]['type'] == 'fieldset')
	    			{
	    				$fieldArrray = array();
	    				foreach ($inputAttributeGroups[$key][$attrSettingName]['fieldlist'] as $fieldName)
	    				{
	    					$fieldArrray[] = $this->settings->group($fieldName);
	    				}	
	    				$inputAttributeGroups[$key][$attrSettingName]['fields']  =  $fieldArrray;  				
	    			}	    			    		
	    		}	
			}
		}
		return $inputAttributeGroups;     	
    }
	
	
}




?>