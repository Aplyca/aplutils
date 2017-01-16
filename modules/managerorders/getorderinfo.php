<?php

$module = $Params['Module'];
$type = $Params['Type'];
$orderID = $Params['OrderID'];
require_once( "kernel/common/template.php" );
$tpl = templateInit();
$http = eZHTTPTool::instance();
$Result = array();



if ( $type == 'comments' )
{	
	$orderManager = new ProductOrderManager($orderID);	
	$comments = array_reverse($orderManager->getOrderComments());
	$tpl->setVariable( "comments", $comments );
	$Result['content'] = $tpl->fetch( "design:managerorders/ordercomments.tpl" );		
}
else
{
	
			

	
}


$Result['pagelayout'] = false;
eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));


?>
