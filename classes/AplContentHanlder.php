<?php


/**
 * This file contains the definition for AplContentHandler class
 *  
 */

/**
 *  @author David Sánchez Escobar
 *  @desc Provides a simple interface for using eZ Publish php api, content operations  
 *   
 */

class AplContentHandler
{
	
 	 /**
	 * Creates a draft, related to the current user
	 * @param string $classIdentifier the class identifier for the object to be created
	 * @param int $sectionID the section which the object will be related to
	 * @return eZContentObject $object an instance of the object
	 */   
 	static function createContentObjectDraft($classIdentifier, $sectionID=1, $omitPermissions=false)
	{
		$language_code = false;
		$class = eZContentClass::fetchByIdentifier($classIdentifier);
		if ( $class instanceof eZContentClass )
		{
			if(!$class->canInstantiateClasses())
			{
				eZDebug::writeError ('Not allowed to instantiate class', 'Draft Creation @ AplContentHandler');
				return false;
			}									
			$object = $class->instantiate(false, $sectionID);
			if(!$omitPermissions)
			{
				if(!$object->canCreate() )
				{
					eZDebug::writeError ('Object creation is not allowed', 'Draft Creation @ AplContentHandler');
					return false;	
				}							
			}				
			$name = "apl_draft" . time();
			$object->setName($name);		
			$db = eZDB::instance();
			$db->begin();			
			$object->store();		
			$db->commit();		
			$objectVersion = $object->currentVersion();
			$objectVersion->setAttribute('status', eZContentObjectVersion::STATUS_DRAFT );
			$objectVersion->store();
						
		}				
		return $object;		
	}
	
	
	/**
	* Deletes and object and its nodes, locations, versions etc. Quick deletion for drafts
	* Since this method is focused  on drafts works quickly avoiding cache cleanings
	* WARNING: It doesn't remove subtrees ( since is used for drafts) 
	* if the object has related subrees the operation will be rejected 
	* if you want to delete everything including subtrees use eZContentObjectOperations::remove( $objectId, true );
	* @param string $classIdentifier the class identifier for the object to be created
	* @param int $sectionID the section which the object will be related to
	* @return eZContentObject $object an instance of the object
	*/  
    static function removeDraftContentObject($objectID)
    {
    	$object = eZContentObject::fetch($objectID);
    	if(!$object instanceof eZContentObject)
    	{
    		eZDebug::writeError ('Object not found', 'Draft Deletion @ AplContentHandler');
    		return false; 
    	}
    	$objectVersion = $object->currentVersion();	   	
    	if($objectVersion->attribute('status') != eZContentObjectVersion::STATUS_DRAFT)
    	{
    		eZDebug::writeError ('The object is not a draft, operation cancelled', 'Draft Deletion @ AplContentHandler');
    		return false;
    	}     		   
    	$assignedNodes = $object->attribute( 'assigned_nodes' );
    	if ( count( $assignedNodes ) == 0 )
    	{
	    	$db = eZDB::instance();
	    	$db->begin();	
			$object->purge();
			$db->commit();	
			return true;
    	}   
    	else
    	{
    		eZDebug::writeError ('The object contains related subtrees and cannot be treated as a draft', 'Draft Deletion @ AplContentHandler');
    		return false;	
    	} 	    	    	
    }		
    
 
	/**
	* Publishes a the $objectId draft on the desired $parentNode
	* @param int $objectId whose is a draft and is wanted to be published 
	* @param eZContentObjectTreeNode $parentNode the parent node where the draft will be published	
	*/     
    static function publishDraft($objectID, $parentNode)
    {
    	$object = eZContentObject::fetch($objectID);
    	if(!$object instanceof eZContentObject)
    	{
    		eZDebug::writeError ('Object not found', 'Draf Publication @ AplContentHandler');
    		return false; 
    	}
    	if(! $parentNode instanceof eZContentObjectTreeNode)
    	{
    		eZDebug::writeError ('Invalid Node', 'Draf Publication @ AplContentHandler');
    		return false;
    	}
    	$objectVersion = $object->currentVersion();	   	
    	if($objectVersion->attribute('status') != eZContentObjectVersion::STATUS_DRAFT)
    	{
    		eZDebug::writeError ('The object is not a draft, operation cancelled', 'Draf Publication @ AplContentHandler');
    		return false;
    	}    
    	$versionNum = $objectVersion->attribute( 'version' );
	    $db = eZDB::instance();
		$db->begin();	   
	    $object->createNodeAssignment( $parentNode->NodeID, true );	    	    	   		   
		$object->store();
		$db->commit();		
    	eZOperationHandler::execute('content','publish',array('object_id' => $objectID, 'version'=>$versionNum));
    	return true;
    }

 	 /**
	 * Sets content for an attribute
	 * fromString reference : http://ezpedia.org/ez/simple_fromstring_and_tostring_interface_for_attributes
	 * @param string $attributeIdentifier the eZContentObjectAttribute identifier
	 * @param int $objectID the id of the object that the attribute belongs to
	 * @param string $content the content to be set, must follows the datatype format 
	 * @return eZContentObject $object an instance of the object
	 */   
	static function setAttributeContentFromString($attributeIdentifier, $objectID, $content)
    {
    	$attribute = self::getAttribute($attributeIdentifier, $objectID);  	    	  
    	if ($attribute instanceof eZContentObjectAttribute and $content!=='')
		{			
			// method fromString() can't be validated directly since some datatypes return true in all cases		
			$attribute->fromString($content);			
			$db = eZDB::instance();
			$db->begin();
			$attribute->store();	
			$db->commit();		
			return 	$attribute;
		}    	    	    
		else return false;
    }	    
    
    
 	 /**
	 * Gets an attribute from its identifier and the id of the object related
	 * @param string $attributeIdentifier the eZContentObjectAttribute identifier
	 * @param int $objectID the id of the object that the attribute belongs to
	 * @return eZContentObjectAttribute $attribute the attribute
	 */       
    static function getAttribute($attributeIdentifier ,$objectID)
    {
    	$object = eZContentObject::fetch($objectID);    	
    	if($object instanceof eZContentObject)
    	{
    		$attributes = $object->dataMap();	  
			$attribute = $attributes[$attributeIdentifier];
			if($attribute instanceof eZContentObjectAttribute)
				return $attribute;
			else
			{
				eZDebug::writeError ('Invalid Attribute Identifier', 'getAttribute @ AplContentHandler');
			}
    	}		
    	return false;		
    }    
    
    
 	 /**
	 * Gets an array of attributes from an array of attribute identifiers
	 * unlike the eZContentObject::fetchAttributesByIdentifier method, this one fetches the attributes
	 * with the attribute identifiers as key instead of the attribute ids
	 * @param int $objectID the id of the object that the attributes belong to
	 * @param array $identifiers array of eZContentObjectAttribute identifiers	  
	 * @return array $filteredAttributes an array of eZContentObjectAttribute objects
	 */      
    static function getAttributesFromIdentifiers($objectID, $identifiers)
    {   
    	if(! is_array($identifiers))
    	{
    		eZDebug::writeError ('$identifiers invalid, expecting array of identifiers', 'Attribute retrieving @ AplContentHandler');
    		return false; 
    	}     	
    	$object = eZContentObject::fetch($objectID); 
    	if(! $object instanceof eZContentObject)
    	{
    		eZDebug::writeError ('Object not found', 'Attribute retrieving @ AplContentHandler');
    		return false; 
    	}
    	$attributes = $object->dataMap();  
    	$filteredAttributes = array();   
    	foreach($identifiers as $identifier)
    	{
    		if(isset($attributes[$identifier]))
    		{
    			$filteredAttributes[$identifier]= $attributes[$identifier];
    		}
    	}
    	return $filteredAttributes;    	    
    }    
    
  	 /**
	 * Sets the owner for the last version of the object
	 * @param int $objectID the id of the object to be modified
     * @param int $userID the id of the user that will be the new owner of the last version
	 * @return true if owner could be set, false if not
	 */        
	static function setObjectOwner($objectID, $userID)
	{    	
    	$object = eZContentObject::fetch($objectID);
    	if(! $object instanceof eZContentObject)
    	{
    		eZDebug::writeError ('Object not found', 'Set object owner @ AplContentHandler');
    		return false;
    	}    		
    	if(!$object->canEdit())
    	{
    		eZDebug::writeError ('Not allowed to edit object', 'Set object owner @ AplContentHandler');
    		return false;  
    	}    		  		
    	if(! eZUser::fetch($userID) instanceof eZUser)
    	{
    		eZDebug::writeError ('Invalid UserID', 'Set object owner @ AplContentHandler');
    		return false;
    	}    		       	
    	$object->setAttribute('owner_id', $userID );
		$objectVersion = $object->currentVersion();
		$objectVersion->setAttribute('creator_id', $userID);    	
		$db = eZDB::instance();
		$db->begin();			
		$object->store();	
		$objectVersion->store();
		$db->commit();	    
		return true;	
	}    
	
	 /**
	 * Moves a node from another place
	 * @param eZContentObjectTreeNode $node the node to move
     * @param eZContentObjectTreeNode $newParentNode the new parent node
	 * @return true $node could be moved, false if not
	 */  
  	static function moveNode($node, $newParentNode)
    {
 	   	$object = $node->object(); 
		$class = $object->contentClass();
		$classID = $class->attribute( 'id' );		 
		if ( !in_array( $node->attribute( 'node_id' ), $newParentNode->pathArray() ) )
		{
			$moveFromMessage =  $node->canMoveFrom()?' can be moved from':' can NOT be moved from';
			$moveToMessage =  $newParentNode->canMoveTo( $classID )?' can be moved to':' can NOT be moved to';
			$debugMessage = "Node with ID = " . $node->attribute( 'node_id' ) . $moveFromMessage . " and " . $moveToMessage;
		    if ( $node->canMoveFrom() && $newParentNode->canMoveTo( $classID ) )
		    {
		        include_once( 'kernel/classes/ezcontentobjecttreenodeoperations.php' );
		        $moveResult = eZContentObjectTreeNodeOperations::move( $node->attribute( 'node_id' ), $newParentNode->attribute( 'node_id' ) );
		        $debugMessage = $moveResult?'move operation successful':'move operation failed';
		        eZDebug::writeNotice ($debugMessage, 'Node moving');
		        return $moveResult;		        				        
		    }
		}
		else
		{
			$debugMessage = 'unexpected error';	
		}
        eZDebug::writeNotice ($debugMessage, 'Node moving');
        return false;
    }	
    
 	/**
	* Adds a node assigment
	* @param int $nodeID the id of node to move
    * @param int $objectID the id of the object related to the node to move
    * @param array $selectedNodeIDArray a set of ids of the desired new parent nodes
	*/     
	static function addNodeAssignment( $nodeID, $objectID, $selectedNodeIDArray, $omitPermissions = false )
	{	
		if ( !is_array( $selectedNodeIDArray ) )
            $selectedNodeIDArray = array();
        if($omitPermissions)
        {
        	self::forceAddNodeAssignment($nodeID, $objectID, $selectedNodeIDArray);
        	return true;
        }
            
        if ( eZOperationHandler::operationIsAvailable( 'content_addlocation' ) )
        {
		    $operationResult = eZOperationHandler::execute( 'content',
                                                            'addlocation', array( 'node_id'              => $nodeID,
                                                                                  'object_id'            => $objectID,
                                                                                  'select_node_id_array' => $selectedNodeIDArray ),
                                                            null,
                                                            true );
        }
        else
        {
			eZContentOperationCollection::addAssignment( $nodeID, $objectID, $selectedNodeIDArray );
        }
		
		return true;
	
	}    
	
	static function forceAddNodeAssignment($nodeID, $objectID, $selectedNodeIDArray)
	{

		$userClassIDArray = eZUser::contentClassIDs();
        $object = eZContentObject::fetch( $objectID );
        $class = $object->contentClass();

        $nodeAssignmentList = eZNodeAssignment::fetchForObject( $objectID, $object->attribute( 'current_version' ), 0, false );
        $assignedNodes = $object->assignedNodes();

        $parentNodeIDArray = array();

        foreach ( $assignedNodes as $assignedNode )
        {
            $append = false;
            foreach ( $nodeAssignmentList as $nodeAssignment )
            {
                if ( $nodeAssignment['parent_node'] == $assignedNode->attribute( 'parent_node_id' ) )
                {
                    $append = true;
                    break;
                }
            }
            if ( $append )
            {
                $parentNodeIDArray[] = $assignedNode->attribute( 'parent_node_id' );
            }
        }

        $db = eZDB::instance();
        $db->begin();
        $locationAdded = false;
        $node = eZContentObjectTreeNode::fetch( $nodeID );
        foreach ( $selectedNodeIDArray as $selectedNodeID )
        {
            if ( !in_array( $selectedNodeID, $parentNodeIDArray ) )
            {
                $parentNode = eZContentObjectTreeNode::fetch( $selectedNodeID );
                $parentNodeObject = $parentNode->attribute( 'object' );

                $canCreate = ( ( $parentNode->checkAccess( 'create', $class->attribute( 'id' ), $parentNodeObject->attribute( 'contentclass_id' ) ) == 1 ) ||
                                ( $parentNode->canAddLocation() && $node->canRead() ) );
				// skipping permissions
                if ( true )
                {
                    $insertedNode = $object->addLocation( $selectedNodeID, true );

                    // Now set is as published and fix main_node_id
                    $insertedNode->setAttribute( 'contentobject_is_published', 1 );
                    $insertedNode->setAttribute( 'main_node_id', $node->attribute( 'main_node_id' ) );
                    $insertedNode->setAttribute( 'contentobject_version', $node->attribute( 'contentobject_version' ) );
                    // Make sure the url alias is set updated.
                    $insertedNode->updateSubTreePath();
                    $insertedNode->sync();

                    $locationAdded = true;
                }
            }
        }
        if ( $locationAdded )
        {

            //call appropriate method from search engine
            eZSearch::addNodeAssignment( $nodeID, $objectID, $selectedNodeIDArray );

            // clear user policy cache if this was a user object
            if ( in_array( $object->attribute( 'contentclass_id' ), $userClassIDArray ) )
            {
                eZUser::purgeUserCacheByUserId( $object->attribute( 'id' ) );
            }


        }
        $db->commit();

        eZContentCacheManager::clearContentCacheIfNeeded( $objectID );

        return array( 'status' => true );
	}
	
	
 	/**
	* Removes a node assigment of an object, doesn't remove it if the node is the main node
	* @param int $nodeID the id of node to remove
	* @return true if removeAssignment could be called, false if not
	*/ 
	static function removeLocation($nodeID)
	{
		$node = eZContentObjectTreeNode::fetch($nodeID);
		if(! $node instanceof eZContentObjectTreeNode)
		{
			eZDebug::writeError ('Node not found', 'Remove location @ AplContentHandler');
			return false;
		}
		$ContentObjectID = $node->ContentObjectID;
		$mainNodeID =  $node->MainNodeID;		
		// Preventing the sending of the main node in the remove list
		if($nodeID != $mainNodeID)
		{			
			eZContentOperationCollection::removeAssignment( $mainNodeID, $ContentObjectID, array($node), false );				
			return true;
		}
		else
		{
			eZDebug::writeError ('The main node cannot be removed', 'Remove location @ AplContentHandler');
			return false;
		}						
	}	
	
	/**
	* Remove a node assigment of an objet if exist below a specific parentNode doesn't work if  the node below $parentNode is the main node
	* @param eZContentObjectTreeNode $parentNode the parent node where the desired node is supposed to be
	* @param eZContentObject $childContentObject the object 
	* @return the result of the state change
	*/ 	
	static function removeNodeAssigmentIfExists($parentNode, $childContentObject)
	{	
		if( ($parentNode instanceof eZContentObjectTreeNode) && ($childContentObject instanceof eZContentObject) )
		{
			$nodeArray = $parentNode->subTree( array( 'Depth' => 1, ));			
			foreach ( $nodeArray as $node )
	    	{
	    		if($node->ContentObjectID == $childContentObject->ID )
	    		{
	    			self::removeLocation($node->NodeID);	
	    		}    	   		    		
	    	}	
	    	return true;			
		}		
		else
		{
			eZDebug::writeError ('Invalid parent node or child object', 'removeNodeAssigmentIfExists @ AplContentHandler');
			return false;
		}
	}		

	/**
	*  Sets a new state for the object  
	* @param eZContentObject $object the object to be set
	* @param int $stateID the new state id for the object
	* @return the result of the state change
	*/ 
	static function setState($object, $stateID)
	{
		if(! $object instanceof eZContentObject)
		{
			eZDebug::writeError ('Object not found', 'Remove location @ AplContentHandler');
			return false;
		}
		$result = eZContentOperationCollection::updateObjectState( $object->ID, array($stateID) );
		$object->expireAllCache();
		return $result;
	}	
	
	
	/**
	* Delete the children of a node, if they are not the main nodes of the related objects  
	* @param int $parentNodeID the node whose it children will be removed
	* @return true if parent node is valid 
	*/ 	
	static function deleteChildrenNodeAssociation($parentNodeID)
	{
		//TODO check permisions
		$parentNode = eZContentObjectTreeNode::fetch($parentNodeID);	
		if(! $parentNode instanceof eZContentObjectTreeNode)
		{
			eZDebug::writeError ('Node not found', 'Remove children location @ AplContentHandler');
			return false;
		}
		$nodeArray = $parentNode->subTree( array( 'Depth' => 1, ));			
		foreach ( $nodeArray as $node )
    	{    	   		
    		// removeLocation checks if the node is not the main node, preventing main nodes to be deleted
    		self::removeLocation($node->NodeID);
    	}		
    	return true;
	}	

	/**
	* Removes the children of an specific node, childrens will be removed with all its associated data
	* related (related object, assigned nodes, versions, etc)  
	* @param int $parentNodeID the node whose it children will be removed
	* @return true if parent node is valid, false if not 
	*/ 	
	static function deleteChildren($parentNodeID)
	{
		$parentNode = eZContentObjectTreeNode::fetch($parentNodeID);
		if(! $parentNode instanceof eZContentObjectTreeNode)
		{
			eZDebug::writeError ('Node not found', 'Delete children @ AplContentHandler');
			return false;
		}		
		$nodeArray = $parentNode->subTree( array( 'Depth' => 1, ));		
	
		foreach ( $nodeArray as $node )
    	{    	   		
			eZContentObjectOperations::remove( $node->object()->ID,  true );
    	}
    	return true;
	}
	
	/**
	* Instructive more than functional method, since its better to manage $params directly instead of separated vars
	* @return true if success 
	*/ 		
	/*setting attribute values
		example format for the $attributesData variable		
		$attributesData = array () ;
		$attributesData['name'] = 'My Article' ; 
		$attributesData ['price'] = '23.3|1|0' ; 		
		attribute format reference:
		http://ezpedia.org/ez/simple_fromstring_and_tostring_interface_for_attributes
	*/	
	static function createAndPublish($classIdentifier, $creatorID, $parentNodeID, $sectionID, $attributesData, $remoteID=false)
	{
		$params = array();
		$params ['class_identifier'] = $classIdentifier; 
		$params['creator_id'] = $creatorID;
		$params['parent_node_id']= $parentNodeID;
		$params['section_id'] = $sectionID; //not required					
		$params['attributes'] = $attributesData;
		if($remoteID)
		{
			$params['remote_id'] = $remoteID;
		}
		// the other optional params :  remote_id, storage_dir
		$contentObject = eZContentFunctions::createAndPublishObject($params);
		return $contentObject;				
	}
	
	
	static function assignSection($selectedSectionID, $object, $omitPermissions=false)
	{		
		$currentUser = eZUser::currentUser();   
        if ( $currentUser->canAssignSectionToObject( $selectedSectionID, $object ) || $omitPermissions)        
        {
        	$db = eZDB::instance();
            $db->begin();
            $assignedNodes = $object->attribute( 'assigned_nodes' );
            if ( count( $assignedNodes ) > 0 )
            {
            	foreach ( $assignedNodes as $node )
                {
                	if ( eZOperationHandler::operationIsAvailable( 'content_updatesection' ) )
                	{                    		         	
                		$operationResult = eZOperationHandler::execute( 'content',
                        												'updatesection',
                                                                        array( 'node_id' => $node->attribute( 'node_id' ),
                                                                        'selected_section_id' => $selectedSectionID ),
                                                                        null,
                                                                        true );                                                                                             	
					}
                    else
                    {                                        	
                    	eZContentOperationCollection::updateSection( $node->attribute( 'node_id' ), $selectedSectionID );
					}
				}
			}
            else
            {
            	// If there are no assigned nodes we should update db for the current object.
                $objectID = $object->attribute( 'id' );
                $db->query( "UPDATE ezcontentobject SET section_id='$selectedSectionID' WHERE id = '$objectID'" );
                $db->query( "UPDATE ezsearch_object_word_link SET section_id='$selectedSectionID' WHERE  contentobject_id = '$objectID'" );
			}
        	$object->expireAllViewCache();
        	$db->commit();
        	
        	return true;
		}
		
		return false;
	}	
	
	static function getAttributePostValues($attributes)
	{		
		$attributeIds = array_keys($attributes);		 
		$base = 'ContentObjectAttribute';
		$attributeValues = array();
		foreach($_POST as $postvarName => $postvar)
		{
			$postvarParts = explode('_', $postvarName);
			
			if(count($postvarParts) > 2)
			{
				if(reset($postvarParts) == 'ContentObjectAttribute')
				{
					$attributeID = end($postvarParts);
					if(ctype_digit($attributeID))
					{
						if(in_array($attributeID, $attributeIds))
						{
							
							$identifier = $attributes[$attributeID]->contentClassAttributeIdentifier();
							$attributeValues[$identifier] = $postvar;
						}													
					}					
				}
			}
		}
		return $attributeValues;	
	}	
        

}

?>