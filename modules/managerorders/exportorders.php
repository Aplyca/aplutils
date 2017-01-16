<?php

$module = $Params['Module'];
$mode = $Params['Mode'];

if(count($_GET) > 0)
{
	$_POST['expiration_from'] = $_GET['expfromDate'];
	$_POST['expiration_to'] =  $_GET['exptoDate'];
	$query = AplManageOrders::buildQuery( $_GET['customer'], $_GET['product'], $_GET['status'], $_GET['type'], $_GET['fromDate'], $_GET['toDate']);
	$orderArray = AplManageOrders::searchOrders($query, $offset, $limit, false);				
}
else
{
	$orderArray = eZOrder::active( true, 0, 100000);	
}


$columns = array('Aplyca AccountID', 'username', 'Title', 'First Name', 'Last Name', 'email', 'Telephone', 'Address Number', 'Street Name', 'City', 'Zip', 'State', 'Country',
 'Hotel Name', 'FEMA No', 'Hotel Address Number', 'Hotel Street Name', 'City', 'Zip', 'State', 'Country',
 'Aplyca OrderID', 'Order Date', 'Start Sate', 'Expire Date', 'Product', 'Price Net', 'Price Tax', 'Price Total', 'Currency',
 'Bill me Later Selected', 'Status', 'TokenID', 'Card Type', 'Last4', 'Expire Date', 'ResponseID', 'Auth Code', 'Amount', 'Currency', 'Status');


$logData = array();
$csv = implode(',', $columns);
$csv .= "\n";
$db = eZDB::instance();



foreach ($orderArray as $order)
{	
	$relatedOrderData = getRelatedOrderData($order);
	$hotelInfo = getHotelInformation($order, $relatedOrderData);	

	if($hotelInfo)
	{
		$csv .= implode(',', $hotelInfo);
		$csv .= "\n";
		
	}	
	else
	{
		$logData[] = array($order->ID,  'no user set for order of this id');
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



function getHotelInformation($order, $relatedOrderData)
{	
	
	$hotelInfo = array();
	
	if($relatedOrderData['user'] instanceof eZUser)
	{
		$user = eZContentObject::fetch($relatedOrderData['user']->ContentObjectID);
		$userData = $user->dataMap();
		$hotelInfo['accountID'] =  $relatedOrderData['user']->ContentObjectID;
		$hotelInfo['username'] =  $relatedOrderData['user']->Login;
		$hotelInfo['title'] =  $userData['title']->toString();		
		$hotelInfo['firstname'] =  $userData['first_name']->content();
		$hotelInfo['lastname'] =  $userData['last_name']->content();
		$hotelInfo['email'] =  $relatedOrderData['user']->Email;		
		$hotelInfo['phonenumber'] =  $userData['personal_phone_number']->content();
		$hotelInfo['addressnumber'] =  $userData['personal_address_number']->content();
		$hotelInfo['addressname'] =  $userData['personal_street_name']->content();
		$hotelInfo['city'] =  $userData['personal_city']->content();
		$hotelInfo['zipcode'] =  $userData['personal_zip_code']->content();
		$hotelInfo['state'] =  $userData['personal_state_province']->content();
		$hotelInfo['country'] =  $userData['personal_country']->toString();				
		
	}
	else
	{
		$hotelInfo['accountID'] = '';
		$hotelInfo['username'] = '';
		$hotelInfo['title'] = '';		
		$hotelInfo['firstname'] = '';
		$hotelInfo['lastname'] = '';
		$hotelInfo['email'] = $relatedOrderData['user']['email'];		
		$hotelInfo['phonenumber'] = '';
		$hotelInfo['addressnumber'] = '';
		$hotelInfo['addressname'] = '';
		$hotelInfo['city'] =  '';
		$hotelInfo['zipcode'] =  '';
		$hotelInfo['state'] =  '';
		$hotelInfo['country'] =  '';
	}
	
	if($relatedOrderData['hotel'] instanceof eZContentObject)
	{
		$hotelData = $relatedOrderData['hotel']->dataMap();
		$hotelInfo['hotel_name'] =  $hotelData['hotel_name']->content();	
		$hotelInfo['fema_number'] =  $hotelData['fema_no']->content();
		$hotelInfo['hotel_address_number'] =  $hotelData['street_number']->content();
		$hotelInfo['hotel_street_name'] =  $hotelData['street_name']->content();	
		$hotelInfo['hotel_city'] =  $hotelData['city_name']->content();
		$hotelInfo['hotel_zip'] =  $hotelData['zip_code']->content();
		$hotelInfo['hotel_state'] =  $hotelData['state_province']->content();	
		$hotelInfo['hotel_country'] =  $hotelData['country_name']->toString();			
	}
	else
	{
		$hotelInfo['hotel_name'] =  $relatedOrderData['hotel']['name'];	
		$hotelInfo['fema_number'] = '';
		$hotelInfo['hotel_address_number'] =  '';
		$hotelInfo['hotel_street_name'] = '';	
		$hotelInfo['hotel_city'] = '';
		$hotelInfo['hotel_zip'] = '';
		$hotelInfo['hotel_state'] = '';	
		$hotelInfo['hotel_country'] = '';
	}		
	$hotelInfo['ez_order_id'] =  $order->ID;//$order->OrderNr;		
	$hotelInfo['order_date'] =  date("m/d/Y" ,$order->Created);
	if($relatedOrderData['hotel'] instanceof eZContentObject)
	{
		$hotelInfo['start_date'] =  date("m/d/Y" ,$hotelData['start_date']->DataInt);
		$hotelInfo['expire_date'] = date("m/d/Y" ,$hotelData['end_date']->DataInt);
		$hotelInfo['package'] = $hotelData['package']->content()->Name;	
	}
	else
	{
		$hotelInfo['start_date'] = '';
		$hotelInfo['expire_date'] = '';
		$hotelInfo['package'] = '';	
	}    			
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
		if($relatedOrderData['user'] instanceof eZUser)
		{
			$csp = CyberSourceProfile::instance($relatedOrderData['user']->ContentObjectID);
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
			else
			{
				$hotelInfo['token_id'] = '';
			}						
		}
		else
		{
			$hotelInfo['token_id'] = '';
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

	foreach($hotelInfo as $key => $item)
	{
		$hotelInfo[$key] = str_replace(",", ";", $item);
	}
	
	return $hotelInfo;
}

function getRelatedOrderData($order)
{
	$items = $order->productItems();
	$userExists = true;	
	$orderRelatedData = array();
	if(count($items) == 0)
	{
		return false;
	}
	if($items[0]['item_object']->ContentObject instanceof eZContentObject)
	{
		$orderRelatedData['hotel'] = $items[0]['item_object']->ContentObject;
		$authors =  $orderRelatedData['hotel']->authorArray();
		if(count($authors) > 0)
		{
			$orderRelatedData['user'] = $authors[0]; 
		}	
		else
		{
			$userExists = false;
		}
	}
	else
	{
		$user = eZUser::fetch($order->UserID);
		if($user instanceof eZUser)
		{
			$orderRelatedData['user'] = $user;			
		}
		else
		{
			$userExists = false; 
		}
		$orderRelatedData['hotel'] = array();
		$orderRelatedData['hotel']['name'] = $items[0]['item_object']->Name;
		$orderRelatedData['hotel']['price'] = $items[0]['total_price_inc_vat'];		
	}	
	if(!$userExists)
	{
		$orderRelatedData['user'] = array();
		$orderRelatedData['user']['email'] = $order->Email;
	}	
	return $orderRelatedData;
}

$Result = array();
$Result['pagelayout'] = false;
eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));


?>
