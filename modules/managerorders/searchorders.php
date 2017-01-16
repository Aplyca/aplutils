<?php

require_once( 'kernel/common/template.php' );
$module = $Params['Module'];
$tpl = templateInit();
$http = eZHTTPTool::instance();

$offset = $Params['UserParameters']['offset'];
$limit = 15;

$fromDate = 0;
$toDate = 0;
$toDateFixed = false;

$viewParameters = array();

if ( $http->hasPostVariable( 'SearchButton' ) )
{
		if ( $http->hasPostVariable( 'inputCustomer' ) )
		{
			$customer = $http->postVariable( 'inputCustomer' );		
		}
		
		if ( $http->hasPostVariable( 'inputProduct' ) )
		{
			$product = $http->postVariable( 'inputProduct' );		
		}				
		if ($http->hasPostVariable('inputstatus') )
		{
			$status = $http->postVariable( 'inputstatus' );	
		}	
		if ($http->hasPostVariable('inputtype') )
		{
			$type = $http->postVariable( 'inputtype' );	
		}						
		if ( $http->hasPostVariable( 'datepicker_from' ) and $http->postVariable( 'datepicker_from' ) != "" and 
			 $http->hasPostVariable( 'datepicker_to' ) and $http->postVariable( 'datepicker_to' ) != "")
		{	
			$fromDateTime = new DateTime($http->postVariable( 'datepicker_from' ));	//$fromDate = $fromDateTime->getTimestamp();
			$fromDate = $fromDateTime->format('U');
			$toDateTime = new DateTime($http->postVariable( 'datepicker_to' )); 	//$toDate = $toDateTime->getTimestamp() + 86400;
			$toDate = $toDateTime->format('U') + 86400;	
			$toDateFixed = true;							
			if ($fromDate > $toDate)
			{
				$viewParameters['dateError'] = 1;
			}	
		}
		if ( $http->hasPostVariable( 'expiration_from' ) and $http->postVariable( 'expiration_from' ) != "" and 
			 $http->hasPostVariable( 'expiration_to' ) and $http->postVariable( 'expiration_to' ) != "")
		{	
			$expfromDate = $http->postVariable( 'expiration_from' );
			$exptoDate = $http->postVariable( 'expiration_to' );
		}		
}
else if ( $http->hasPostVariable( 'SaveOrderStatusButton' ) )
{
    if ( $http->hasPostVariable( 'StatusList' ) )
    {
        foreach ( $http->postVariable( 'StatusList' ) as $orderID => $statusID )
        {
            $order = eZOrder::fetch( $orderID );
            $access = $order->canModifyStatus( $statusID );
            if ( $access and $order->attribute( 'status_id' ) != $statusID )
            {
                $order->modifyStatus( $statusID );
            }
        }
    }
}
else
{
	if ( $Params['customer'] )
	{
		$customer = $Params['customer'];
	}
	if ( $Params['product'] )
	{
		$product = $Params['product'];
	}
	if ( $Params['fromDate'] > 0 and $Params['toDate'] > 0)
	{
		$fromDate = $Params['fromDate'];
		$toDate = $Params['toDate'];
	}
	if ( $Params['status'] )
	{
		$status = $Params['status'];
	}	
	if ( $Params['type'] )
	{
		$type = $Params['type'];
	}		
}
//$resultQueryParams = array('customer' => $customer, 'product' => $product, 'status' => $status, 'type' => $type, 'fromDate' => $fromDate, 'toDate' => $toDate);
$resultQueryParams = "customer=$customer&product=$product&status=$status&type=$type&fromDate=$fromDate&toDate=$toDate&expfromDate=$expfromDate&exptoDate=$exptoDate";

$query = AplManageOrders::buildQuery( $customer, $product, $status, $type, $fromDate, $toDate);
$orderArray = AplManageOrders::searchOrders($query, $offset, $limit);
$orderCount = AplManageOrders::countSearchOrders($query);	

if($toDateFixed)
{
	$toDate -= 86400;
}


$statusList = eZOrderStatus::fetchList();

$tpl->setVariable( 'customer', $customer );
$tpl->setVariable( 'product', $product );
$tpl->setVariable( 'status', $status );
$tpl->setVariable( 'type', $type );
$tpl->setVariable( 'from_date', $fromDate );
$tpl->setVariable( 'to_date', $toDate );
$tpl->setVariable( 'order_list', $orderArray );
$tpl->setVariable( 'order_list_count', $orderCount );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'typec', 'D' );

$viewParameters['offset'] = $offset;
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'sort_field', $sortField );
$tpl->setVariable( 'sort_order', $sortOrder );
$tpl->setVariable( 'status_list', $statusList );
$tpl->setVariable( 'query_params', $resultQueryParams );


$Result = array();
$Result['content'] = $tpl->fetch( "design:managerorders/searchorderlist.tpl" );
$Result['path'] = array ( array ('url' => 'managerorders/searchorders', 
								 'text' => "Order List" ) );	         

$Result['content'] = $tpl->fetch( 'design:managerorders/searchorderlist.tpl' );
?>
