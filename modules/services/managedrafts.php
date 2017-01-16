<?php

$Module = $Params['Module'];
$http = eZHTTPTool::instance();



$mainNode = eZContentObjectTreeNode::fetch(12);
$registeredUsers = $mainNode->subTree( array( 'Depth' => 1, 'ClassFilterType' => 'include', 'ClassFilterArray' => array('user') ));
$drafts = array();
foreach($registeredUsers as $userNode)
{
	$currentUserDrafts = eZContentObjectVersion::fetchForUser( $userNode->ContentObjectID );
	if($currentUserDrafts)
	{
		$drafts[$userNode->ContentObjectID] = array();	
		$drafts[$userNode->ContentObjectID]['name'] = $userNode->Name;
		$drafts[$userNode->ContentObjectID]['drafts'] = $currentUserDrafts;
	}
}


if ( $http->hasPostVariable( 'RemoveButton' )  )
{
    if ( $http->hasPostVariable( 'DeleteIDArray' ) )
    {
        $deleteIDArray = $http->postVariable( 'DeleteIDArray' );
        $db = eZDB::instance();
        $db->begin();
        foreach ( $deleteIDArray as $deleteID )
        {
            $version = eZContentObjectVersion::fetch( $deleteID );
            if ( $version instanceof eZContentObjectVersion )
            {
                eZDebug::writeNotice( $deleteID, "deleteID" );
                $version->removeThis();
            }
        }
        $db->commit();
    }
}

if ( $http->hasPostVariable( 'EmptyButton' )  )
{
    $db = eZDB::instance();
    $db->begin();
    foreach ( $drafts as $key=>$userDrafts )
    {
    	foreach($userDrafts['drafts'] as $draft)
    	{
    		$draft->removeThis();
    	}            	
    }
    $db->commit();
    $drafts = array();
	foreach($registeredUsers as $userNode)
	{
		$currentUserDrafts = eZContentObjectVersion::fetchForUser( $userNode->ContentObjectID );
		if($currentUserDrafts)
		{
			$drafts[$userNode->ContentObjectID] = array();	
			$drafts[$userNode->ContentObjectID]['name'] = $userNode->Name;
			$drafts[$userNode->ContentObjectID]['drafts'] = $currentUserDrafts;
		}
	}
    
}





$tpl = eZTemplate::factory();
$tpl->setVariable('draft_data', $drafts );
$Result = array();
$Result['content'] = $tpl->fetch( 'design:services/managedrafts.tpl' );
$Result['path'] = array( array( 'text' => ezpI18n::tr( 'kernel/content', 'Drafts admin' ),
                                'url' => false ) );
?>