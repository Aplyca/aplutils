<?php

$user = eZUser::currentUser();
$itemID = false;
$http = eZHTTPTool::instance();

if ( $http->hasPostVariable( 'itemID' ) )
{
	$itemID = $http->postVariable( 'itemID' );
	$node = eZContentObjectTreeNode::fetch( $itemID );
	$name = $node->attribute( 'name' );
	$bookmark = eZContentBrowseBookmark::createNew( $user->id(), $itemID, $node->attribute( 'name' ) );
	echo "Added: '$name'";
}

$Result['pagelayout'] = false;
eZExecution::cleanExit(); 	

?>
