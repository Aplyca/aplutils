<?php

$module = $Params['Module'];
$mode = $Params['Mode'];


$hotels = HapdManagerPublish::fetchObjects( 2183, 'hotel', false );

$columns = array('Aplyca AccountID', 'username', 'Title', 'First Name', 'Last Name', 'email', 'Telephone', 'Address Number', 'Street Name', 'City', 'Zip', 'State', 'Country',
 'Hotel Name', 'FEMA No', 'Hotel Address Number', 'Hotel Street Name', 'City', 'Zip', 'State', 'Country',
 'Aplyca OrderID', 'Order Date', 'Start Sate', 'Expire Date', 'Product', 'Price Net', 'Price Tax', 'Price Total', 'Currency',
 'Bill me Later Selected', 'Status', 'TokenID', 'Card Type', 'Last4', 'Expire Date', 'ResponseID', 'Auth Code', 'Amount', 'Currency', 'Status');


//$columns = array('objectID', 'Name', 'URL', 'Message');

$logData = array();

$csv = implode(',', $columns);
$csv .= "\n";

$db = eZDB::instance();

foreach ($hotels as $hotel)
{
	$object = $hotel->object();
	$orders = getRelatedOrders($object->ID, $db, $logData);		
	$authors =  $object->authorArray();  // TODO get a better way
	if(count($authors) == 0)
	{
		$logData[] = array($hotel->urlAlias(),  'no user set');
		continue; 
	}		
	foreach($orders as $order)
	{
		if($order instanceof eZOrder)
		{	
			$hotelInfo = getHotelInformation($object, $order);					
				if($hotelInfo)
				{
					$csv .= implode(',', $hotelInfo);
					$csv .= "\n";
					
				}	
				else
				{
					$logData[] = array($hotel->urlAlias(),  'no user set');
				}							
		}
		else
		{
			$logData[] = array($hotel->urlAlias(), 'no order set');
		}
	}
}


$db->close();

if($mode == 'data')
{
	$filename = 'hotels.csv';	
	header("Content-type:text/csv; charset=utf-8");
	header("Content-Disposition: attachment; filename=" . $filename);
	echo $csv;
}
else
{
	$text = "";
	foreach($logData as $logItem)
	{
		$text .= $logItem[0] . "\r\n" . $logItem[1] . "\r\n \r\n"; 
	}
	$filename = 'hotelslog.txt';
	header("Content-type:text/plain; charset=utf-8");
	header("Content-Disposition: attachment; filename=" . $filename);
	echo $text;
}



function getHotelInformation($object, $order)
{	
	$authors =  $object->authorArray();  // TODO get a better way
	if(count($authors) == 0)
	{
		return false; 
	}		
	$user = eZContentObject::fetch($authors[0]->ContentObjectID);
	$hotelInfo = array();

	if($user instanceof eZContentObject)
	{
		$userData = $user->dataMap();		
		$hotelData = $object->dataMap();
		$hotelInfo['accountID'] =  $authors[0]->ContentObjectID;
		$hotelInfo['username'] =  $authors[0]->Login;
		$hotelInfo['title'] =  $userData['title']->toString();		
		$hotelInfo['firstname'] =  $userData['first_name']->content();
		$hotelInfo['lastname'] =  $userData['last_name']->content();
		$hotelInfo['email'] =  $authors[0]->Email;		
		$hotelInfo['phonenumber'] =  $userData['personal_phone_number']->content();
		$hotelInfo['addressnumber'] =  $userData['personal_address_number']->content();
		$hotelInfo['addressname'] =  $userData['personal_street_name']->content();
		$hotelInfo['city'] =  $userData['personal_city']->content();
		$hotelInfo['zipcode'] =  $userData['personal_zip_code']->content();
		$hotelInfo['state'] =  $userData['personal_state_province']->content();
		$hotelInfo['country'] =  $userData['personal_country']->toString();

		$hotelInfo['hotel_name'] =  $hotelData['hotel_name']->content();	
		$hotelInfo['fema_number'] =  $hotelData['fema_no']->content();
		$hotelInfo['hotel_address_number'] =  $hotelData['street_number']->content();
		$hotelInfo['hotel_street_name'] =  $hotelData['street_name']->content();	
		$hotelInfo['hotel_city'] =  $hotelData['city_name']->content();
		$hotelInfo['hotel_zip'] =  $hotelData['zip_code']->content();
		$hotelInfo['hotel_state'] =  $hotelData['state_province']->content();	
		$hotelInfo['hotel_country'] =  $hotelData['country_name']->toString();
		$hotelInfo['ez_order_id'] =  $order->ID;//$order->OrderNr;		
		$hotelInfo['order_date'] =  date("m/d/Y" ,$order->Created);    
		$hotelInfo['start_date'] =  date("m/d/Y" ,$hotelData['start_date']->DataInt);
		$hotelInfo['expire_date'] = date("m/d/Y" ,$hotelData['end_date']->DataInt);
		$hotelInfo['package'] = $hotelData['package']->content()->Name;		
		$hotelInfo['price_net'] =  $order->totalExVat();
		$hotelInfo['price_tax'] =  $order->totalVat()?$order->totalVat():0;
		$hotelInfo['price_total'] = $order->totalIncVat();
		$hotelInfo['currency'] = $order->currencyCode();
		$statusObj = eZOrderStatus::fetchByStatus($order->status());
		$hotelInfo['order_status'] = $statusObj->Name;				
		if($order->AccountIdentifier == 'Cybersource')
		{
			$hotelInfo['bill_me_later'] = "N";
			$orderManager = new ProductOrderManager($order->ID);
			$orderData = $orderManager->getOrderInformation();
			$billingData = $orderData['billing_data'];
			$csp = CyberSourceProfile::instance($authors[0]->ContentObjectID);
			if($csp)
			{
				if($csp->token())
				{
					$data = $csp->getData();
					$hotelInfo['token_id'] = $data['cybersource_id'];									
				}
				else
				{
					$hotelInfo['token_id'] = '';
				}
			}												
			$hotelInfo['card_type'] = ctype_digit($billingData['cc_type_id'])?CyberSourceProfile::creditCardTypeNameByID($billingData['cc_type_id']):$billingData['cc_type_id'];
			$hotelInfo['lastfour'] = $billingData['cc_last_four_digits'];
			$hotelInfo['cc_expire_date'] = $billingData['cc_expire_date'];
			$hotelInfo['response_id'] = 100;			
		}
		else
		{
			//print_r($hotelInfo); die();	
			$hotelInfo['bill_me_later'] = "Y";
			$hotelInfo['token_id'] = '';
			$hotelInfo['card_type'] = '';
			$hotelInfo['lastfour'] = '';
			$hotelInfo['cc_expire_date'] = '';
			$hotelInfo['response_id'] = '';	
		}					
		$hotelInfo['auth_code'] = '';
		$hotelInfo['amount'] = '';							
	}
	else
	{			
		return false;		
	}	

	foreach($hotelInfo as $key => $item)
	{
		$hotelInfo[$key] = str_replace(",", ";", $item);
	}
	
	return $hotelInfo;
}

function getRelatedOrders($objectID, $db)
{
	$query = "SELECT ezorder.* 
			  FROM ezorder, ezproductcollection_item 
			  WHERE ezorder.id = ezproductcollection_item.id  
			  AND ezproductcollection_item.contentobject_id = " .$objectID;
	
	$orders = $db->arrayQuery( $query); 	
 	$eZOrderArray = false;
 	if (count($orders) > 0)
 	{
 		$eZOrderArray = array();
 		foreach($orders as $orderData)
 		{
 			$eZOrderArray[] = new eZOrder($orderData);	
 		} 		
 	}
 	else
 	{
 		return false;
 	} 	
 	return $eZOrderArray; 	 	
}

$Result = array();
$Result['pagelayout'] = false;
eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));


?>
