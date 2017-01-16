<?php

/**
 * This file contains the definition for AplMatrixAdapter class
 *  
 */

/**
 *  @author David Sánchez Escobar
 *  @desc Provides an interface to use eZ Matrix attribute rows as a set of sub attributes managed through apl input type abstraction
 *  Each cell on a eZ Matrix row will have a related type, mapped with the order of the aplfieldset are defined on the .ini file  
 */

class AplMatrixAdapter
{
	
	var $aplInputObject;
	var $fieldsetIdentifier; 
	var $eZCOAMatrix;
	
	private function __construct($aplInputObject, $fieldsetIdentifier, $eZCOAMatrix) 
    {      	
 		$this->aplInputObject = $aplInputObject;
 		$this->fieldsetIdentifier = $fieldsetIdentifier;
 		$this->eZCOAMatrix = $eZCOAMatrix;		
    }   
    
    static function instance($aplInputObject, $fieldsetIdentifier, $eZCOAMatrix)
    {    	
    	if( !($aplInputObject instanceof AplInputObject && $eZCOAMatrix instanceof eZContentObjectAttribute) )
    	{
    		return false;
    	}        	
    	if(! $aplInputObject->attributes[$fieldsetIdentifier] instanceof AplFieldSet )
    		return false;    		    			    		
    	if( $eZCOAMatrix->DataTypeString != 'ezmatrix' )
    		return false;
    	return new AplMatrixAdapter($aplInputObject, $fieldsetIdentifier, $eZCOAMatrix);    	
    }
    
    public function getAttributeInputArray()
    {
	   	$columnIdentifiers = $this->columnIdentifiersSequence();    	    	    	
		$serializedContent = $this->eZCOAMatrix->toString();		
		$data = array();
		if($serializedContent != '')
    	{	
    		$serializedContent = str_replace("\&", "_AMPERSAND_", $serializedContent);
    		$rows = explode('&', $serializedContent);    		    		
    		foreach($rows as $row)
	    	{	
	    		$row = str_replace("_AMPERSAND_", "&", $row);
	    		$fields = explode('|', $row);
	    		$tempRow = array();
	    		foreach($fields as $key => $field)
	    		{	
	    			$columnIdentifier = $columnIdentifiers[$key];
	    			if(strstr($columnIdentifier, '-'))
	    			{
	    				$identifierParts = explode ('-', $columnIdentifier);
	    				if(empty($tempRow[$identifierParts[0]]))
	    				{
	    					$tempRow[$identifierParts[0]] = array();
	    				}
	    				$tempRow[$identifierParts[0]][$identifierParts[1]]=$field;	    				 
	    			}
	    			else
	    			{
	    				$tempRow[$columnIdentifier] = $field;	
	    			}	    			
	    			
	    		}   
	    		$data[]=$tempRow;	    		
	    	}	    	
    	}   	     
    	$_POST[$this->fieldsetIdentifier] = $data;
    	$this->aplInputObject->validateAttributesInput(); 
    	$attributes = $this->aplInputObject->attributesToArray(); 
    	$aplInputGroup = $this->aplInputObject->attributes[$this->fieldsetIdentifier]->group;
		$attributeInput = $attributes[$aplInputGroup][$this->fieldsetIdentifier];
		return $attributeInput;	
    }
    
    public function processMatrixInput()
    {
    	$validAttributes = $this->aplInputObject->validateAttributesInput();
		$attributes = $this->aplInputObject->attributesToArray(); 
		$aplInputGroup = $this->aplInputObject->attributes[$this->fieldsetIdentifier]->group;
		$attributeInput = $attributes[$aplInputGroup][$this->fieldsetIdentifier];
						
		if(empty($attributeInput['value']))
		{
			// Warning: eZMatrixType (ezmatrix datatype class) doesn't not clear the matrix when fromStrinh recieves an empty param
			// instead of  $this->eZCOAMatrix->fromString("") we delete the rows from the matrix
			// the db data consistency for this alternative was checked for eZ Publish 4.5
			$serializedFieldset = "";
			$relatedMatrix = & $this->eZCOAMatrix->content();	

			
			$rowsIndex = array_keys($relatedMatrix->Matrix['rows']['sequential']);
			foreach($rowsIndex as $rowIndex)	
			{		
				$relatedMatrix->removeRow($rowIndex);
			}
			$relatedMatrix->Matrix['cells'] = array();
			$relatedMatrix->Cells = array();			
			$db = eZDB::instance();
	    	$db->begin();		
	    	$this->eZCOAMatrix->store(); 
	    	$db->commit();	
		}
		else
		{
			$fieldsetData = array();
			$count = 0;
	      	foreach($attributeInput['value'] as $fieldset)
	    	{  
	    		$tempData = array();
	    		if($this->fieldSetValidation($fieldset))
	    		{
	    			foreach($fieldset as $fieldsetItem)
	    			{
	    				if(is_array($fieldsetItem['value']))
	    				{
	    					foreach($fieldsetItem['value'] as $fvalue)
	    					{
	    						$tempData[] = $fvalue;	
	    					}    					
	    				}
	    				else
	    				{
	    					$tempData[] = $fieldsetItem['value'];
	    				}
	    			}	
	    			$count++;
	    			// TODO escape |  with \| , check if works
	    			$fieldsetData[$count] =  implode('|',$tempData);
	    		}    		
	    	}	    		
	    	$fieldsetData = str_replace("&", "\&", $fieldsetData);   	
	    	$serializedFieldset = implode('&',$fieldsetData);
   	    	$res = $this->eZCOAMatrix->fromString($serializedFieldset);	
	    	$db = eZDB::instance();
	    	$db->begin();		
	    	$this->eZCOAMatrix->store(); 
	    	$db->commit();	    	
		}		
    	return array('result' => $validAttributes, 'attribute' =>  $attributeInput);
    }
    
    private function fieldSetValidation($fieldset)
    {
    	foreach($fieldset as $fieldsedItem)
    	{
			if($fieldsedItem['validation_error'] == 1)
				return false;    		
    	}  	
    	return true;
    }
    
    public function columnIdentifiersSequence()
    {
    	$matrix = $this->eZCOAMatrix->content();
    	$columnIdentifiers = array();
    	foreach($matrix->Matrix['columns']['sequential'] as $column)
    	{
    		$columnIdentifiers[] = $column['identifier'];
    	}
    	return $columnIdentifiers;
    }
    
    public function fieldsetItemSequence()
    {
    	$fieldsetItemsData = $this->aplInputObject->attributes[$this->fieldsetIdentifier]->data;
    	$fieldsetItemIdentifiers = array();
    	foreach($fieldsetItemsData as $fieldsetItemData)
    	{
	   		$fieldsetItemIdentifiers[]=	$fieldsetItemData->identifier;
    	}    	
    }
            

 	    
}

?>