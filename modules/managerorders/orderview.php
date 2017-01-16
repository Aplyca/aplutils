<?php
//
// Created on: <31-Jul-2002 16:49:13 bf>
//
// SOFTWARE NAME: eZ Publish
// SOFTWARE RELEASE: 4.1.3
// BUILD VERSION: 23650
// COPYRIGHT NOTICE: Copyright (C) 1999-2009 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//

$OrderID = $Params['OrderID'];
$Type = $Params['Type'];
$module = $Params['Module'];
require_once( "kernel/common/template.php" );

if(!$OrderID)
{
	$module->redirectToView('vieworders');
	return false;
}


$ini = eZINI::instance();
$http = eZHTTPTool::instance();
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
elseif($Type == 'P')
{
	$order = TicoCreditProductOrder::fetch( $OrderID );
	$orderManager = new TCProductOrderManager($OrderID);	
}
else{
	
}


if ( !$order )
{
    return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

$orderInformation = $orderManager->getOrderInformation();
$additionalInformation = $orderManager->getAdditionalInformation();
$accountInfo = $orderManager->accountInformation();
unset($additionalInformation['comments']);


$accessToAdministrate = $user->hasAccessTo( 'shop', 'administrate' );
$accessToAdministrateWord = $accessToAdministrate['accessWord'];

$accessToBuy = $user->hasAccessTo( 'shop', 'buy' );
$accessToBuyWord = $accessToBuy['accessWord'];

if ( $accessToAdministrateWord != 'no' )
{
    $access = true;
}
elseif ( $accessToBuyWord != 'no' )
{
    if ( $user->id() == $ini->variable( 'UserSettings', 'AnonymousUserID' ) )
    {
        if( $OrderID != $http->sessionVariable( 'UserOrderID' ) )
        {
            $access = false;
        }
        else
        {
            $access = true;
        }
    }
    else
    {
        if ( $order->attribute( 'user_id' ) == $user->id() )
        {
            $access = true;
        }
        else
        {
            $access = false;
        }
    }
}
if ( !$access )
{
     return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$tpl = templateInit();


$tpl->setVariable( "order", $order );
$tpl->setVariable( "orderInfo", $orderInformation );
$tpl->setVariable( "additionalInfo", $additionalInformation );


$Result = array();
$Result['content'] = $tpl->fetch( "design:managerorders/orderview.tpl" );
$Result['path'] = array ( array ('url' => 'managerorders/orderview', 'text' => "Order #" . $order->attribute( 'id' )  ) );	                         
                         

?>
