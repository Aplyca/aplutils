<?php

$module = $Params['Module'];
$orderID = $Params['OrderID'];
$statusID = $Params['StatusID'];
require_once( "kernel/common/template.php" );


$ini = eZINI::instance('utils.ini');	
$statusHandlerClass =  $ini->variable('Settings','OrderStatusManagerClass');

$result = call_user_func(array($statusHandlerClass, "modifyOrderStatus"), $orderID, $statusID);

$notification = 'orderstatus';
$resultNotification = call_user_func(array($statusHandlerClass, "sendNotificationOrderStatus"), $result, $notification);

/*
if ( $result['error'] == 'Invalid-data' || $result['error'] == 'no-available' )
	echo '<span class="error">' . $result['result'] . '</span>';
else
	print $result['result'];
*/

echo json_encode($result);
	
$Result = array();
$Result['pagelayout'] = false;
eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));


?>
