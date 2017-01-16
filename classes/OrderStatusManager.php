<?php

class OrderStatusManager
{	
	const UNDEFINED = 0;
	const PENDING = 1;
	const PAID_ONLINE = 2;
	const INVOICED = 3;
	const PAID_OFFLINE = 1000;
	const SUSPENDED = 1001;
	const PUBLISH_ACTION = 1;
	const UNPUBLISH_ACTION = 1;
		
	static function modifyOrderStatus( $orderID, $newStatusID )
	{	
		$result = array( 'orderid' => $orderID, 'result' => 'Invalid data please try again later.' ,'error' => 'Invalid-data' );		
		$order = eZOrder::fetch( $orderID );
				
		if ( is_object($order) )
		{
			$items = $order->productItems();
			$contentObject = $items[0]['item_object']->contentObject();	
					
			$currentStatusID = $order->attribute( 'status_id' );
			$status = eZOrderStatus::fetchByStatus($currentStatusID);
			$newStatus = eZOrderStatus::fetchByStatus($newStatusID);
			
			if ( $currentStatusID == $newStatusID )
			{
				$result = array( 'orderid' => $orderID, 'result' => 'Invalid data please select a diferent status.' ,'error' => 'Invalid-data', 'currentstatus' => $currentStatusID );
				return $result;
			}	
			
			if ( !$contentObject instanceof eZContentObject )
			{
				$result = array( 'orderid' => $orderID, 'result' => 'The listing related to this order was deleted.' ,'error' => 'Invalid-data', 'currentstatus' => $currentStatusID );		
				return $result;
			}
			
			$postingObject = PostingObject::instance($contentObject->ID);
			if(! $postingObject instanceof PostingObject)
			{
				$result = array( 'orderid' => $orderID, 'result' => 'The listing is invalid or corrupted.' ,'error' => 'Invalid-data', 'currentstatus' => $currentStatusID );
				return $result;
			}
			
			$access = $order->canModifyStatus( $newStatusID );			
		    if ( $access and $currentStatusID != $newStatusID )
		    {	
		    	$activeNotification = false;
		    	
		    	if ( $newStatusID == self::PENDING )
		    	{
		    		if( $currentStatusID == self::PAID_ONLINE )
					{
						$activeNotification = false;
			    		$result = array( 'orderid' => $orderID, 'result' => 'Can not change the order status '. date("Ymd",$order->Created) . "-" . $order->ID .' from '. $status->Name . ' to ' . $newStatus->Name . '.', 'error' => 'no-available', 'currentstatus' => $currentStatusID );
		
					}
					elseif( $currentStatusID == self::SUSPENDED )
					{
						$order->modifyStatus( $newStatusID );
						$postingObject->publish();
						$activeNotification = false; 
					}
					else
					{
		    			$order->modifyStatus( $newStatusID );
		    			$activeNotification = false;
					}
		    	}
		    	else if ( $newStatusID == self::PAID_ONLINE )
		    	{	
		    		if( $currentStatusID == self::SUSPENDED )
					{
						$activeNotification = false;
						$resultPublish = $postingObject->publish();	
						if($resultPublish)
						{
							$order->modifyStatus( $newStatusID );							
						}
						else
						{
							$result = array( 'orderid' => $orderID, 'result' => 'Related listing cannot be republished '. date("Ymd",$order->Created) . "-" . $order->ID .' from '. $status->Name . ' to ' . $newStatus->Name . '.', 'error' => 'no-available', 'currentstatus' => $currentStatusID );
							return $result;
						}
						 
					}
					else
					{
			    		$activeNotification = false;
			    		$result = array( 'orderid' => $orderID, 'result' => 'Can not change the order status '. date("Ymd",$order->Created) . "-" . $order->ID .' from '. $status->Name . ' to ' . $newStatus->Name . '.', 'error' => 'no-available', 'currentstatus' => $currentStatusID );						
					}

		    	}
		    	else if ( $newStatusID == self::INVOICED )
		    	{
		    		if( $currentStatusID == self::PAID_ONLINE )
					{
						$activeNotification = false;
			    		$result = array( 'orderid' => $orderID, 'result' => 'Can not change the order status '. date("Ymd",$order->Created) . "-" . $order->ID .' from '. $status->Name . ' to ' . $newStatus->Name . '.', 'error' => 'no-available', 'currentstatus' => $currentStatusID );
		
					}
		    		elseif( $currentStatusID == self::SUSPENDED )
					{
						$order->modifyStatus( $newStatusID );
						$postingObject->publish();
						$activeNotification = false; 
					}					
					else
					{
		    			$order->modifyStatus( $newStatusID );
		    			$activeNotification = false;
					}
		    	}
		    	else if ( $newStatusID == self::PAID_OFFLINE )
		    	{	
		    		if( $currentStatusID == self::PAID_ONLINE )
					{
						$activeNotification = false;
			    		$result = array( 'orderid' => $orderID, 'result' => 'Can not change the order status '. date("Ymd",$order->Created) . "-" . $order->ID .' from '. $status->Name . ' to ' . $newStatus->Name . '.', 'error' => 'no-available', 'currentstatus' => $currentStatusID );
		
					}
		    		elseif( $currentStatusID == self::SUSPENDED )
					{
						$order->modifyStatus( $newStatusID );
						$postingObject->publish();
						$activeNotification = false; 
					}					
					else
					{
		    			$order->modifyStatus( $newStatusID );
		    			$activeNotification = false;
					}
		    	}    
		    	else if ( $newStatusID == self::SUSPENDED )
		    	{
			
					if ( !$postingObject instanceof PostingObject )
					{
						$activeNotification = false;
						$result = array( 'orderid' => $orderID, 'result' => 'Operation could not be completed.' ,'error' => 'Invalid-data', 'currentstatus' => $currentStatusID );		
						return $result;
					}
		    			
					$resultPublish = $postingObject->unpublish();
					if($resultPublish)
					{
						$order->modifyStatus( $newStatusID );
			    		$additionalMessage = 'The product '. $contentObject->Name .' has been unpublish.';
			    		$activeNotification = true;
					}
					else
					{
						$activeNotification = false;
						$result = array( 'orderid' => $orderID, 'result' => 'There is an inconsistency with the listing, status could not be changed' ,'error' => 'Invalid-data', 'currentstatus' => $currentStatusID );
					}								    		
		    	}
		    	
		    	$result = array( 'orderid' => $orderID, 'result' => 'Order status  '. date("Ymd",$order->Created) . "-" . $order->ID .' has been changed from '. $status->Name . ' to ' . $newStatus->Name . '. ' . $additionalMessage, 'error' => '', 'newstatus' => $newStatusID, 'activenotification' => $activeNotification );
		    	
		    	return $result;
		    	
		    }
		    else
		    	return $result;
		    
		}
		else
			return $result;		
		
	}
	
	static function sendNotificationOrderStatus( $resultModifyStatus, $notification )
	{
		if ( $notification == 'orderstatus' )
		{
			$order = eZOrder::fetch( $resultModifyStatus['orderid'] );
			$notificationData = array( 'Order' => $order, 'Result' => $resultModifyStatus['result'] );
			AplManageMail::sendNotification( $notification, $notificationData );
		}
		else if ( $notification == 'orderstatusall' )
		{
			$resulData = array();
			foreach ( $resultModifyStatus as $result )
			{
				foreach ( $result as $identifier => $value )
				{
					if ( $identifier == 'orderid' )
						$id = $value;
					
					if ( $identifier == 'result' )
						$content = $value;
					
					if ( $identifier == 'activenotification' and $value )
						$resulData[$id] = $content;
				}
			}
			if ( count($resulData) > 0 )
			{
				$notificationData = array( 'Result' => $resulData );
				AplManageMail::sendNotification( $notification, $notificationData );
			}			
		}
		
	}
	
}


?>
