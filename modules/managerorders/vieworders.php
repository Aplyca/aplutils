<?php

require_once( 'kernel/common/template.php' );
$module = $Params['Module'];

$tpl = templateInit();

$offset = $Params['UserParameters']['offset'];

$limit = 15;

if( eZPreferences::value( 'admin_orderlist_sortfield' ) )
{
    $sortField = eZPreferences::value( 'admin_orderlist_sortfield' );
}

if ( !isset( $sortField ) || ( ( $sortField != 'created' ) && ( $sortField!= 'user_name' ) ) )
{
    $sortField = 'created';
}

if( eZPreferences::value( 'admin_orderlist_sortorder' ) )
{
    $sortOrder = eZPreferences::value( 'admin_orderlist_sortorder' );
}

if ( !isset( $sortOrder ) || ( ( $sortOrder != 'asc' ) && ( $sortOrder!= 'desc' ) ) )
{
    $sortOrder = 'asc';
}

$http = eZHTTPTool::instance();

// The RemoveButton is not present in the orderlist, but is here for backwards
// compatibility. Simply replace the ArchiveButton for the RemoveButton will
// do the trick.
//
// Note that removing order can cause wrong order numbers (order_nr are
// reused).  See eZOrder::activate.
if ( $http->hasPostVariable( 'RemoveButton' ) )
{
    if ( $http->hasPostVariable( 'OrderIDArray' ) )
    {
        $orderIDArray = $http->postVariable( 'OrderIDArray' );
        if ( $orderIDArray !== null )
        {
            $http->setSessionVariable( 'DeleteOrderIDArray', $orderIDArray );
            $module->redirectTo( $module->functionURI( 'removeorder' ) . '/' );
        }
    }
}

// Archive options.
if ( $http->hasPostVariable( 'ArchiveButton' ) )
{
    if ( $http->hasPostVariable( 'OrderIDArray' ) )
    {
        $orderIDArray = $http->postVariable( 'OrderIDArray' );
        if ( $orderIDArray !== null )
        {
        	foreach ( $orderIDArray as $archiveID )
			{
			    eZOrder::archiveOrder( $archiveID );
			    $module->redirectTo( '/managerorders/vieworders' );
			}
        }
    }
}

if ( $http->hasPostVariable( 'SaveOrderStatusButton' ) )
{
    if ( $http->hasPostVariable( 'StatusList' ) )
    {
    	$modifyOrdersStatus = array();
        foreach ( $http->postVariable( 'StatusList' ) as $orderID => $statusID )
        {
            $order = eZOrder::fetch( $orderID );
            $access = $order->canModifyStatus( $statusID );
            if ( $access and $order->attribute( 'status_id' ) != $statusID )
            {
            	$ini = eZINI::instance('utils.ini');	
				$statusHandlerClass =  $ini->variable('Settings','OrderStatusManagerClass');				
				$modifyOrdersStatus[] = call_user_func(array($statusHandlerClass, "modifyOrderStatus"), $orderID, $statusID);
            	//$order->modifyStatus( $statusID );
            }
        }
        
        $notification = 'orderstatusall';
        $notificationOrdersStatus = call_user_func(array($statusHandlerClass, "sendNotificationOrderStatus"), $modifyOrdersStatus, $notification);
        
        $offset = $http->postVariable( 'Offset' );
		$tpl->setVariable( 'modify_orders_status', $modifyOrdersStatus );
    }
}

$statusList = eZOrderStatus::fetchList();

$orderArray = eZOrder::active( true, $offset, $limit, $sortField, $sortOrder );
$orderCount = eZOrder::activeCount();

$tpl->setVariable( 'order_list', $orderArray );
$tpl->setVariable( 'order_list_count', $orderCount );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'typec', 'D' );

$viewParameters = array( 'offset' => $offset );
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'sort_field', $sortField );
$tpl->setVariable( 'sort_order', $sortOrder );
$tpl->setVariable( 'status_list', $statusList );

$Result = array();
$Result['path'] = array( array( 'text' => ezpI18n::tr( 'managerorders/vieworders', 'Order list' ),
                                'url' => false ) );

$Result['content'] = $tpl->fetch( 'design:managerorders/orderlist.tpl' );
?>
