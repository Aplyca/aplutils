<?php

class AplInputAttributeType
{
	
/*	private $name;
	private $identifier;
	private $group;
	private $required;
	private $value;
	private $validationError;
	private $typeIdentifier;*/
	
	var $name;
	var $identifier;
	var $group;
	var $required;
	var $value;
	var $validationError;
	var $typeIdentifier;
	var $data;
	
	function __construct()
    {
   		$this->validationError = 0;	   
    }

	
	function fetchInput($http)
    {    	
        if($http->hasVariable($this->getIdentifier()))
    	{
    		$this->setValue($http->variable($this->getIdentifier()));
    	}
	}
	
	function fetchFromArray($data)
	{		
		$identifier = $this->getIdentifier();
	    if(isset($data[$identifier]))
    	{
    		$this->setValue($data[$identifier]);
    	}		
	}
	
	
	public function validateInput($params)
	{
		
	}	
		
	function setName($name)
	{
		$this->name = $name;
	}
	
	function setValue($value)
	{
		$this->value = $value;
	}	
	
	function setGroup($group)
	{
		$this->group = $group;
	}
	
	function setIdentifier($identifier)
	{
		$this->identifier = $identifier;
	}
	
	function setTypeIdentifier($typeIdentifier)
	{
		$this->typeIdentifier = $typeIdentifier;
	}

	function setData($data)
	{
		$this->data = $data;
	}		
	
	function setRequired()
	{
		$this->required = true;
	}
		
	function setOptional()
	{
		$this->required = false;		
	}	
	
	function getName()
	{
		return $this->name;
	}
	
	function getValue()
	{
		return $this->value;
	}
	
	function getGroup()
	{
		return $this->group;
	}
	
	function getTypeIdentifier()
	{
		return $this->typeIdentifier;
	}

	function getIdentifier()
	{
		return $this->identifier;
	}
	
	function getData()
	{
		return $this->data;
	}		
	
	function isRequired()
	{
		return $this->required;
	}		
	
	function setValidationError()
	{
		$this->validationError = 1;
	}
	
	function hasValidationError()
	{
		return $this->validationError;
	}
	
	function checkRequired($http)
	{
	    if( ! $http->hasVariable($this->getIdentifier()) )
    	{
    		$this->setValidationError();
    		return false;
    	}
    		
    	$input = $http->variable($this->getIdentifier());
    	
    	
    	
    	if( $this->isRequired() && $input == '' )
    	{
    		$this->setValidationError();
   			return false;
    	}
    	
    	return true;
	}
	

}

?>
