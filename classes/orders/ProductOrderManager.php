<?php

/**
 * 
 * @author David Sanchez Escobar
 * 
 * This class works as a simplified interface of the eZOrder Class, getting it loosely coupled regards the 
 * eZPublish generic shop feature
 * 
 */
class ProductOrderManager extends OrderManager
{
	
	var $order;	
	    
    /* Pseudoconstructor used when user wants to create an order from zero
	 * @param  userId  The user id whose the order will be related
	 * @param  items an array of items that will be storeds as products in the order
	 * 		   the format of an item is  array(name, contentobject_id, item_count, price)
	 */
    function __constructFromZero($userId, $items, $accountIdentifier = 'ez', $startingStatus = eZOrderStatus::PENDING)
    {
    	$user = eZUser::fetch($userId);
    	if($user instanceof eZUser)
    	{
    		$collectionId = self::createProductCollection($items);
    		$email = $user->attribute( 'email' );
    		$time = time();
        	$this->order = new eZOrder( array( 'productcollection_id' => $collectionId,
                                     'user_id' => $userId,
                                     'is_temporary' => 1,
                                     'created' => $time,
                                     'status_id' => $startingStatus,
                                     'status_modified' => $time,
                                     'status_modifier_id' => $userId,
        							 'email' => $email
                                     ) );    		
			$this->order->setAttribute( 'account_identifier', $accountIdentifier);			
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
    	$this->order =  eZOrder::fetch($orderId);
    } 

    /* Pseudoconstructor used when user wants set an existing eZOrder in the ProductOrderManager 
	 * @param  order  The order itself
	 * @param  check  a dummy param for adding one argument, differentiating the method to __constructFromExisting
	 */    
    function __constructFromExistingOrder($order, $check)
    {    	    	
    	$this->order =  $order;
    }  
    

    
  	/* Sets an array of item as the product collection of the related eZOrder 
	 * @param products	an array of items that will be storeds as products in the order
	 * 		   			the format of an item is  array(name, contentobject_id, item_count, price)
	 * @return      	the id of the collection created
	 */   
    static function createProductCollection($products)
    {  
    	//TODO Set the currency value
        //$currency = $priceObj->attribute( 'currency' );                                                                      
		$collection = eZProductCollection::create();
		$collection->store();		
		$collectionId = $collection->attribute("id");
		
		foreach($products as $product)
		{				
			$item = eZProductCollectionItem::create( $collectionId );
			$item->setAttribute( 'name', $product['name'] );
			$item->setAttribute( "contentobject_id",  $product['contentobject_id'] );
			$item->setAttribute( "item_count", $product['item_count'] );
			$item->setAttribute( "price", $product['price'] );
			
			if ( array_key_exists ('is_vat_included', $product) )
			{
				if ( $product['is_vat_included'] )
		        {
		        	$item->setAttribute( "is_vat_inc", '1' );
		        }	
			    else
		        {
		        	$item->setAttribute( "is_vat_inc", '0' );
		        }				
			}
			else
			{
				$item->setAttribute( "is_vat_inc", '0' );				
			}
	
        	$item->store(); 						
		}		
		return $collectionId;									            				       	    
    }
               
}
 