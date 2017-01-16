<?php

class CreditOrderManager extends OrderManager
{
	var $creditObject;
	var $creditObjectId;

    
    /* Pseudoconstructor used when user wants to create an order from zero
	 * @param  userId  The user id whose the order will be related
	 * @param  items an array of items that will be storeds as products in the order
	 * 		   the format of an item is  array(name, contentobject_id, item_count, price)
	 */
    function __constructFromZero($userId, $creditAmount)
    {    	
    	$this->loadSettings();
    	$user = eZUser::fetch($userId);
    	$email = $user->attribute( 'email' );
    	if($user instanceof eZUser)
    	{
    		$collectionId = $this->setOrderCollectionFromObject($creditAmount);    		    		
    		$time = time();
        	$this->order = new AplCreditOrder( array( 'productcollection_id' => $collectionId,
                                     'user_id' => $userId,
                                     'is_temporary' => 1,
                                     'created' => $time,
                                     'status_id' => eZOrderStatus::PENDING,
                                     'status_modified' => $time,
                                     'status_modifier_id' => $userId,
        							 'email' => $email
                                     ) );    		
			$this->order->setAttribute( 'account_identifier', "ez" );
	        $db = eZDB::instance();
	        $db->begin(); 
	        $this->order->store();
	        $db->commit();   			
    	}    	
    }
    
    
    
	/* Pseudoconstructor used when user wants set an existing eZOrder in the ProductOrderManager 
	 * @param  orderId  The id of an existing order
	 */    
    function __constructFromExisting($orderId)
    {    	
    	$this->order =  AplCreditOrder::fetch($orderId);
    }   
    
    
    function loadSettings()
    {
    	$ini = eZINI::instance("ticopayment.ini");
    	$settings = $ini->group("CreditSettings");     	        	
    	$this->creditObjectId = $settings['CreditObjectId']; 
    	$this->creditObject = eZContentObject::fetch($this->creditObjectId);
    }
    
    function setOrderCollectionFromObject($creditAmount)
    {
    	$db = eZDB::instance();
    	$db->begin();
		$collection = AplCreditCollection::create();		
		$currency = self::getProductCurrency($this->creditObject);	
		$price	= self::getProductPrice($this->creditObject);
		$collection->setAttribute( 'currency_code', $currency );                
		$collection->store();
		$collectionID = $collection->attribute("id");    			
		$item = AplCreditCollectionItem::create( $collectionID );
		$item->setAttribute( 'name', $this->creditObject->attribute( 'name' ) );
		$item->setAttribute( "contentobject_id", $this->creditObjectId );
		$item->setAttribute( "item_count", $creditAmount );
		$item->setAttribute( "price", $price );			
       	$item->setAttribute( "is_vat_inc", '1' );
		$item->store();		    
        $db->commit();        	
        return $collectionID;   
    }
     	    
    static function getCreditPrice()
    {
    	//TODO: Change the hardcoded id for a ini setting
    	$creditObj = eZContentObject::fetch( 361 );
		$creditAttr = $creditObj->contentObjectAttributes();
		$creditprice = $creditAttr[1]->DataFloat;
		return $creditprice;
    }
    
   static function getCreditObject()
    {
    	//TODO: Change the hardcoded id for a ini setting
    	$creditObj = eZContentObject::fetch( 361 );
    	return $creditObj;

    }
    
    static function getUserCredits($user_id)
    {
    	$credits_facade = new aplFacadeCredits();
		$response = $credits_facade->getUserCreditNumber($user_id);					
		$numcredits = -1;		
		
		if($response->response_code == 3)
		{
			$response = $credits_facade->registerUser($user_id, 0);
			$numcredits = 0;	
		}
		else
		{
			$numcredits = $response->dataArray['total_credits'];
		}		
		return $numcredits;
    }    	 
} 
?>