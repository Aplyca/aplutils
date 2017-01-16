<?php

$user = eZUser::currentUser();
$itemID = false;
$http = eZHTTPTool::instance();

if ( $http->hasPostVariable( 'itemID' ) )
{
	$itemID = $http->postVariable( 'itemID' );
	$node = eZContentObjectTreeNode::fetch( $itemID );
	$name = $node->attribute( 'name' );
	$bookmark = eZContentBrowseBookmark::removeByNodeID( $itemID );
	echo "Removed: '$name'";
}

$Result['pagelayout'] = false;
eZExecution::cleanExit(); 

?>
