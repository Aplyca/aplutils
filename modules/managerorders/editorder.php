<?php

$module = $Params['Module'];
require_once( "kernel/common/template.php" );
$tpl = templateInit();
$http = eZHTTPTool::instance();


if ( $http->hasPostVariable( 'ChangeStatusButton' ) )
{
	

	
	
}
elseif( $http->hasPostVariable( 'EditCommentsButton' ) )
{
	$comments = $http->postVariable('comments');
	$orderID = $http->postVariable('orderId');
	$orderManager = new ProductOrderManager($orderID);
	$username  = eZUser::CurrentUser()->Login;
	$dateObj = new eZDateTime();
	$date = $dateObj->toString();
	$commentData = array("comment" => array('user' => $username, 'date' => $date, 'comment_text' => $comments));
	$orderManager->setAdditionalInformation('comments', $commentData, false);			
}
else
{
	
			

	
}

$Result = array();
$Result['pagelayout'] = false;
eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));


?>
