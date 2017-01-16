<?php

$module = $Params['Module'];
$tpl = eZTemplate::factory();
$http = eZHTTPTool::instance();


if ( $http->hasPostVariable( "CustomActionButton" ) )
{
    $customActionArray = $http->postVariable( "CustomActionButton" );
    foreach ( $customActionArray as $customActionKey => $customActionValue )
    {
        $customActionString = $customActionKey;

        if ( preg_match( "#^([0-9]+)_(.*)$#", $customActionString, $matchArray ) )
        {
            $customActionAttributeID = $matchArray[1];
            $customAction = $matchArray[2];
            $customActionAttributeArray[$customActionAttributeID] = array( 'id' => $customActionAttributeID,
                                                                           'value' => $customAction );
        }
    }
}


?>
