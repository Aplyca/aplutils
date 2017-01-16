<?php
//
// Definition of Customorderview class
//
// Created on: <01-Mar-2004 15:53:50 wy>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Publish
// SOFTWARE RELEASE: 4.1.x
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
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/*! \file
*/

$CustomerID = $Params['CustomerID'];
$Email = $Params['Email'];
$module = $Params['Module'];
require_once( "kernel/common/template.php" );

$http = eZHTTPTool::instance();

$tpl = templateInit();

$Email = urldecode( $Email );
$productList = eZOrder::productList( $CustomerID, $Email );
$orderList = eZOrder::orderList( $CustomerID, $Email );
$Offset = $Params['Offset'];
$viewParameters = array( 'offset' => $Offset);



$userContent = eZUser::fetch($CustomerID)->contentObject();



$tpl->setVariable( "user_content", $userContent );
$tpl->setVariable( "product_list", $productList );
$tpl->setVariable( "customer_id", $CustomerID );
$tpl->setVariable( "order_list", $orderList );
$tpl->setVariable( "view_parameters", $viewParameters );


$Result = array();
$Result['content'] = $tpl->fetch( "design:managerorders/customerorderview.tpl" );
$path = array();
$path[] = array( 'url' => 'managerorders/customerorderview',
                 'text' => ezpI18n::tr( 'kernel/shop', 'Order list' ) );
$path[] = array( 'url' => false,
                 'text' => ezpI18n::tr( 'kernel/shop', 'Customer order view' ) );
$Result['path'] = $path;

?>
