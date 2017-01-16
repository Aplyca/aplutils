<?php
class ContentHandlerTest
{
	
    function runTests()
    {
   	    $userID = eZUser::currentUser()->ContentObjectID;
		$versions = eZContentObjectVersion::fetchForUser( $userID );
		$db = eZDB::instance();
		$db->begin();
		foreach ( $versions as $version )
		{
			$version->removeThis();
		}
		$db->commit();
		
		$testNode = eZContentObjectTreeNode::fetch(403);
		$secondTestNode = eZContentObjectTreeNode::fetch(405);
		ApleZTools::deleteNodeChildren($testNode->NodeID); // bridge
		ApleZTools::deleteNodeChildren($secondTestNode->NodeID); // bridge
		
		
		$object = ApleZTools::createContentObject(16); // bridge
		ApleZTools::setContent('hotel_name', $object->ID, 'Hotel Deliciosox'); // bridge
		$res = ApleZTools::getAttributesFromIdentifiers($object->ID, array('hotel_name', 'street_name')); // bridge
		//echo "attributes: "; print_r($res);
		//echo "<br/><br/><br/>";
		
		
		AplContentHandler::publishDraft($object->ID,$testNode);
		ApleZTools::setObjectOwner($object->ID, 259); // bridge
		$node = eZContentObject::fetch($object->ID)->mainNode();
		ApleZTools::moveNode($node, $secondTestNode);
		$node = eZContentObject::fetch($object->ID)->mainNode();
		ApleZTools::AddNodeAssignment( $node->NodeID, $object->ID, array($testNode->NodeID) ); // bridge
		$object = eZContentObject::fetch($object->ID);
		$assignedNodes = $object->assignedNodes();
		//ApleZTools::removeLocation($assignedNodes[0]->NodeID);
		ApleZTools::removeLocation($assignedNodes[1]->NodeID);
		ApleZTools::removeNodeAssigmentIfExists($secondTestNode, $object);
		
		
		
		//AplContentHandler::setAttributeContentFromString('hotel_name', 1103, 'hotel maravilla');
		//$object = AplContentHandler::createContentObjectDraft('hotel');	
		// 1103
		
		//ApleZTools::setObjectOwner(953,67);
		
		//AplContentHandler::removeDraftsContentObject($object->ID);	
    }
    
    
}

?>