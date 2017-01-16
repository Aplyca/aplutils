<?php

$module = $Params['Module'];
$OrderID = $Params['OrderID'];
$Type = $Params['Type'];
$viewFull = $Params['ViewFull'];

$ini = eZINI::instance();
$user = eZUser::currentUser();
$access = false;
$order = null;
$orderManager = null;

if($Type == 'C')
{
	$order = AplCreditOrder::fetch( $OrderID );		
	$orderManager = new CreditOrderManager($OrderID);
}
elseif($Type == 'D')
{
	$order = eZOrder::fetch( $OrderID );
	$orderManager = new ProductOrderManager($OrderID);	
}
else{
	
}

if ( !$order )
{
    return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

if ($order->UserID == $user->id())
{
	$access = true;
}

$shippingData = $orderManager->getShippingData();
$accountInfo = $orderManager->accountInformation();
$orderResponse =  $orderManager->getOrderResponseData();

$tpl = eZTemplate::factory();
$tpl->setVariable( "access", $access );
$tpl->setVariable( "view_full", $viewFull );
$tpl->setVariable( "order", $order );
$tpl->setVariable( "shippingInfo", $shippingData );
$tpl->setVariable( "accountInfo", $accountInfo );
$tpl->setVariable( "responseText", $orderResponse );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:customerorders/vieworder.tpl' );
$Result['path'] = array( array( 'text' => ezpI18n::tr( 'customerorders/vieworder', 'Order' ),
                                'url' => false ) );
if ($viewFull)
{
	$Result['pagelayout'] = true;
}
else
{
    $Result['pagelayout'] = false;
    eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));
    $response['m']=$Result['content'];
    $response['s']='s';
    print_r(json_encode($response));
    eZDB::checkTransactionCounter();
    eZExecution::cleanExit();
}

?>
