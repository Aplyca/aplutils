<?php


/**
 * This file contains the definition for ApleZTools class
 * @deprecated since revision 599, use AplContentHandler instead
*/

/**
 *  @author David S�nchez Escobar
 *  @desc Provides a simple interface for using eZ Publish php api, specially for content operations this is a legacy class
 *   
 */

class ApleZTools
{
	
	function __construct($settingsFile, $settingBlock) 
    {      	
 	
    }   
    
    static function validateModuleRequiredParams($params)
    {    	  	      
    	$view = $params['FunctionName'];  	
    	$viewParams = $params['Module']->Functions[$view]['post_action_parameters'];
    	foreach($paramNames as $key => $item)
    	{
    		if( ! $module->hasActionParameter($key) )
    		{
    			return false;
    		}
    	}
    	return true;    	
    }
    
 	static function createContentObject($class_id)
	{				
		$classIdentifier = eZContentClass::classIdentifierByID($class_id);		
		return AplContentHandler::createContentObjectDraft($classIdentifier);		
	}
	
	
	// TODO deprecate unpublishObject() by checking where is used, seems to be unsafe
	static function unpublishObject($object_id)
	{
		$obj = eZContentObject::fetch($object_id); 
		$objectVersion = $Object->currentVersion();
		$objectVersion->setAttribute('status', eZContentObjectVersion::ARCHIVED );
		$db = eZDB::instance();
		$objectVersion->store();		
		$db->commit();		
	}
	
	
    static function setContent($attribute_identifier, $object_id, $content)
    {    	
    	return AplContentHandler::setAttributeContentFromString($attribute_identifier, $object_id, $content);   	    	    
    }	
    
    static function getAttribute($identifier,$object_id)
    {    	
    	return AplContentHandler::getAttribute($identifier, $object_id);      					
    }
    
    
    static function getAttributesFromIdentifiers($object_id, $identifiers)
    {    
    	return AplContentHandler::getAttributesFromIdentifiers($object_id, $identifiers);  	    
    }
    
    
    static function validateAttributesInput($attributes, $saveIfOk=true)
    {
		$validation = array();
		$validation['result'] = 1;
		$http= eZHTTPTool::instance();
		
    	foreach($attributes as $attribute)
    	{
    		$inputParameters = $attribute->inputParameters();
    		$temp_validation = $attribute->validateInput($http, "ContentObjectAttribute", $inputParameters);
    		$identifier = $attribute->ContentClassAttributeIdentifier;
    		if($temp_validation != 1)
    		{
    			$validation['result'] = 0;
    		}
    		else 
    		{
    			if($saveIfOk)
    			{
    				$attribute->fetchInput($http, "ContentObjectAttribute");
					$attribute -> store();	
    			}    			   
    		}
    		$validation['attributes'][$identifier] = $temp_validation; 
    	}
    	return $validation;
    }
    
    
    static function setObjectOwner($object_id, $user_id)
    {   
    	return AplContentHandler::setObjectOwner($object_id, $user_id); 	
    }
    
    
    static function setAttributesFromImput($attributes)
    {
    	$http= eZHTTPTool::instance();
    	$db = eZDB::instance();
    	$db->begin();								
      	foreach($attributes as $attribute)
    	{
    		$attribute->fetchInput($http, "ContentObjectAttribute");
    		$attribute -> store();    		
    	}
    	$db->commit();
    }
    
    
    static function moveNode($node, $newParentNode)
    {    	
    	return AplContentHandler::moveNode($node, $newParentNode);
    }
           
	
	static function AddNodeAssignment( $nodeID, $objectID, $selectedNodeIDArray )
	{	
		return AplContentHandler::addNodeAssignment( $nodeID, $objectID, $selectedNodeIDArray );	
	}
	
	
	static function removeLocation($nodeID)
	{				
		return AplContentHandler::removeLocation($nodeID);	
	}

	
    static function removeContentObject($obj_id)
    {
	   	return AplContentHandler::removeDraftContentObject($obj_id);
    }	
	
	
	static function updateState($node, $stateID)
	{
		$object = $node->object();
		return AplContentHandler::setState($object, $stateID);
	}
	
	/* warning : deletes the object and all its node asociatiosns */
	static function deleteNodeChildren($parentNodeID)
	{
		return AplContentHandler::deleteChildren($parentNodeID);
	}
	
	static function deleteChildrens($parentNodeID)
	{
		return AplContentHandler::deleteChildrenNodeAssociation($parentNodeID);
	}
	
	function removeNodeAssigmentIfExists($parentNode, $childContentObject)
	{	
		return AplContentHandler::removeNodeAssigmentIfExists($parentNode, $childContentObject);	
	}	
	
	
	static function aplCreateAndPublishObject($params)
	{
		return $contentObject = eZContentFunctions::createAndPublishObject($params);		 		
	}	      

	static function fetchSubTree($iniFile)
	{
	    $ini = eZINI::instance( $iniFile );	    
	    if ($ini instanceof eZINI)
	    {	
    	    $parentNodeID = $ini->variable( 'FetchSettings', 'ParentNodeID' );
    	    if ($parentNodeID > 2)
    	    {
    	        $sortBy = array(name, true);
        	    $limit = $ini->variable( 'FetchSettings', 'Limit' );
        	    $offset = $ini->variable( 'FetchSettings', 'Offset' );
        	    $depth = $ini->variable( 'FetchSettings', 'Depth' );
        	    $depthOperator = $ini->variable( 'FetchSettings', 'DepthOperator' );
        	    $ignoreVisibility = ($ini->variable( 'FetchSettings', 'IgnoreVisibility' ) == 'true')?true:false;
        	    $classID = $ini->variable( 'FetchSettings', 'ClassID' );
        	    $attribute_filter = self::getAttributeFilter($ini);
        	    
        	    $class_filter_type = 'include';
        	    if ($ini->hasVariable( 'FetchSettings', 'ClassFilterType' ))
        	    {
        	        $class_filter_type = $ini->variable( 'FetchSettings', 'ClassFilterType' );
        	    }    
        	    $class_filter_array = $ini->variable( 'FetchSettings', 'ClassFilterArray' );
        	    
        	    $fetch = eZContentFunctionCollection::fetchObjectTree(    $parentNodeID,
                                                                	    $sortBy,
                                                                	    false,
                                                                	    false,
                                                                	    $offset,
                                                                	    $limit,
                                                                	    $depth,
                                                                	    $depthOperator,
                                                                	    $classID,
                                                                	    $attribute_filter,
                                                                	    false,
                                                                	    $class_filter_type,
                                                                	    $class_filter_array,
                                                                	    false,
                                                                	    true,
                                                                	    $ignoreVisibility,
                                                                	    false,
                                                                	    true,
                                                                	    false,
                                                                	    false);
        	    return $fetch['result'];
    	    }
	    }
	    
	    return array();
	}

	static function getAttributeFilter($ini)
	{
	    $apl_attribute_filters = $ini->variable( 'FetchSettings', 'AttributeFilter' );
	    $attribute_filter = array();
	    foreach ($apl_attribute_filters as $apl_attribute_filter)
	    {
	        $attribute_filter_block = 'AttributeFilter_' . $apl_attribute_filter;
	        	
	        $class_identifier = $ini->variable( $attribute_filter_block, 'ClassIdentirier' );
	        $attribute_identifier = $ini->variable( $attribute_filter_block, 'AttributeIdentirier' );
	        $attribute_filter_operator = $ini->variable( $attribute_filter_block, 'Operator' );
	        $attribute_filter_value = $ini->variable( $attribute_filter_block, 'Value' );
	
	        $apl_attribute_filter = array(  $class_identifier . '/' . $attribute_identifier,
                        	                $attribute_filter_operator,
                                	        $attribute_filter_value);
	
	        $attribute_filter[] = $apl_attribute_filter;
	    }
	
	    if ($attribute_filter)
	    {
	        return $attribute_filter;
	    }
	
	    return false;	
	}
	
	static function getCycles($options, $name, $ini_cycles = false, $fallback = true)
	{
	    $arguments = $options['arguments'];
	    $cycles = array();
	    $is_cli_cycles = false;
	    foreach ($arguments as $argument)
	    {
	        $argument_split = explode('=', $argument);
	        if ($argument_split[0] == $name)
	        {
	            $is_cli_cycles = true;
	            $cycles=explode(',', $argument_split[1]);
	            if (is_array($ini_cycles))
	            {
	                $cycles = array_intersect($cycles, $ini_cycles);
	            }
	            break;
	        }
	    }
	     
	    if (!$is_cli_cycles and $fallback and is_array($ini_cycles))
	    {
	        return $ini_cycles;
	    }
	     
	    return $cycles;
	}	

	static function CronjobloginUser($userName, $cli)
	{
	    eZUser::logoutCurrent();
	    if ($userName)
	    {
            $user = eZUser::fetchByName( $userName );
            if ($user instanceof eZUser AND $user -> isEnabled() AND !($user -> isLocked()))
            {
                $userContentObjectID = $user->attribute( 'contentobject_id' );
                eZUser::setCurrentlyLoggedInUser( $user, $userContentObjectID );
                $cli->output( "Cronjob will be performed by user $userName" );
                return true;
            }
	    }
        $cli->output( "User $userName doesn't exist or is disabled, cronjob will be performed by Anonymous user" );
        return false;
	}	
	
}

?>