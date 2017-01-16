<?php

/**
 * 
 * @author David Sanchez Escobar
 * 
 * This class works as a simplified interface of the eZOrder Class, getting it loosely coupled regards the 
 * eZPublish generic shop feature
 * 
 */
class OrderManager
{
	
	var $order;

	const CREATED = 1;
	const SUCCESS = 2;
	const PENDING_DELIEVERING = 3;
	const DECLINED_TRANSACTION = 1000;
	const TRANSACTION_ERROR = 1001;
	
	
	/* The invoked constructor of the class, it calls two differen pseudo-constructor methods depending 
	* of the number of params when user invokes it
	*/
	function __construct()
    {    	
        $argv = func_get_args();	
    	switch( func_num_args() )
    	{
    		default:
    		case 1:
	    		$this->__constructFromExisting($argv[0]);	
	    		break;
    		case 2:
    			$this->__constructFromExistingOrder($argv[0], $argv[1]);	
	    		break; 
    		case 4:
				$this->__constructFromZero( $argv[0], $argv[1], $argv[2], $argv[3]);
				break;    		
    	}    		          	    	
    }
    
    
	function setOrderInformation($blockName, $data, $replace=true)
    {
    	$doc = $this->initializeOrderXML('order_data'); 
    	$doc = $this->setDataBlock($doc, 'order_data', $blockName, $data, $replace);
    	$xmlString = $this->saveOrderXMLAttribute('data_text_1', $doc);    	
    	return $xmlString;
    }        
    
    
    function setAdditionalInformation($blockName, $data, $replace=true)
    {
    	$doc = $this->initializeOrderXML('additional_information');
    	$doc = $this->setDataBlock($doc, 'additional_information', $blockName, $data, $replace);
    	$xmlString = $this->saveOrderXMLAttribute('data_text_2', $doc);
   		return $xmlString;
    }
    
    function getOrderInformation()
    {
    	$xmlString = $this->order->attribute( 'data_text_1' );    	   
    	if ( $xmlString != null )
        {        	        
       		return AplArrayGetter::XMLToArray($xmlString, 'order_data');
        }    
        return false;
    }
    
    function getAdditionalInformation()
    {
    	$xmlString = $this->order->attribute( 'data_text_2' );    	   
    	if ( $xmlString != null )
        {        	        
       		return AplArrayGetter::XMLToArray($xmlString, 'additional_information');
        }    
        return false;
    }
     	    
    
    
    function setDataBlock($doc, $type, $blockName, $dataArray, $replace=true)
    {    
    	$root = $doc->getElementsByTagName($type)->item(0);    	
    	// getting block is is already set
    	if($root->getElementsByTagName($blockName)->length == 0)
    	{
    		$block = $doc->createElement($blockName);  
       		$root->appendChild( $block );	
    	}
    	else
    	{
   			$block = $root->getElementsByTagName($blockName)->item(0);
	    	if($replace)
	    	{
	    		$block->parentNode->removeChild($block);    
	    		$block = $doc->createElement($blockName);  
	       		$root->appendChild( $block );			    		
	    	}
    	}    	
    	    	
    	 
        foreach($dataArray as $itemName => $item)
        {
        	if(is_array($item))
        	{
        		$tempNode = $doc->createElement( $itemName ); 
	        	$block->appendChild( $tempNode );
		        foreach($item as $key => $attribute)
		        {
		        	$subNode = $doc->createElement( $key, $attribute );
		        	$tempNode->appendChild( $subNode );
		        }	
        	}
        	else
        	{
        		$tempNode = $doc->createElement( $itemName, $item );
        		$block->appendChild( $tempNode );
        	}        
        }
        return $doc;          
    }    
    
    function saveOrderXMLAttribute($attributeField, $doc)
    {
    	$xmlString = $doc->saveXML();
    	$this->order->setAttribute( $attributeField, $xmlString );
        $db = eZDB::instance();
        $db->begin(); 
        $this->order->store();
        $db->commit();
    	return $xmlString;
    }

    function initializeOrderXML($type, $saveInitialize=false)
    {    
    	if($type == 'order_data')
    	{
    		$ezcolumn = 'data_text_1';
    	}	
    	elseif($type == 'additional_information')
    	{
    		$ezcolumn = 'data_text_2';
    	}
    	else return false;
    	$xmlString = $this->order->attribute( $ezcolumn );
    	$doc = new DOMDocument( '1.0', 'utf-8' );
    	if($xmlString)
    	{
	    	$success = $doc->loadXML( $xmlString );
	    	$data = $doc->getElementsByTagName($type);
	    	if($data->length == 0)
	    	{    	
	    		$root = $doc->createElement($type);
	        	$doc->appendChild( $root );      
	    	}	
    	}    
    	else
    	{
    		$root = $doc->createElement($type);
	        $doc->appendChild( $root );
    	}	   
    	if($saveInitialize)
    	{
    		$xmlString = $this->saveOrderXMLAttribute($ezcolumn, $doc);    	
    	}    	    	      	
    	return $doc;
    }
     	
	/*
	 * Fetches the related user of the order and shows its basic data 
	 * @return	the related user information
	 */           
	function accountInformation()
    {
        
	    $order = $this->order;

	    if (is_object($order))
	    {
	       $user = $order->user();
	       if (is_object($user))
	       {
	           $userObject = $user->attribute( "contentobject" );
	           $dataMap = $userObject->dataMap();
	           return array( 'first_name' => $dataMap['first_name']->content(),
	                      'last_name' => $dataMap['last_name']->content(),
	                      'email' => $user->attribute( "email" ) );
	        }              
	     }

        return false;
              
  	}
    
 	/*
	 * Gets the billing data in an array  
	 * @return	the related user information into an array with this format: array('item1' => 'value1', 'item2' => 'value2') 
	 */      

    function getBillingData()
    {     	
    	$xmlString = $this->order->attribute( 'data_text_1' );
    	if ( $xmlString != null )
        {
        	$dom = new DOMDocument( '1.0', 'utf-8' );
  	    	$billingData = array();   
        	$success = $dom->loadXML( $xmlString );
        	$billingNodes = $dom->getElementsByTagName('billing_data');             	     	
        	$billingDataNode =  $billingNodes->item(0);
        	$XMLElements =  $billingDataNode->childNodes;
        	for($i=0; $i < $XMLElements->length; $i++)
        	{
        		$itemName = $XMLElements->item($i)->nodeName;
        		$item = $XMLElements->item($i)->textContent;
        		if($itemName != 'shop_account')
        		{
        			$billingData[$itemName] = $item;	
        		}       			       			 
        	}
        	return $billingData;    	
        }        
    }  	
  	
 	/*
	 * Gets the shipping data in an array  
	 * @return	the related user information into an array with this format: array('item1' => 'value1', 'item2' => 'value2') 
	 */              
    function getShippingData()
    {
    	$xmlString = $this->order->attribute( 'data_text_1' );
    	if ( $xmlString != null )
        {
        	$dom = new DOMDocument( '1.0', 'utf-8' );
  	    	$billingData = array();   
        	$success = $dom->loadXML( $xmlString );
        	$billingNodes = $dom->getElementsByTagName('shipping_data');             	     	
        	$billingDataNode =  $billingNodes->item(0);
        	$XMLElements =  $billingDataNode->childNodes;
        	for($i=0; $i < $XMLElements->length; $i++)
        	{
        		$itemName = $XMLElements->item($i)->nodeName;
        		$item = $XMLElements->item($i)->textContent;
        		if($itemName != 'shop_account')
        		{
        			$billingData[$itemName] = $item;	
        		}       			       			 
        	}
        	return $billingData;    	
        }    
    }    
    
    
    function getOrderResponseData()
    {    
    	$xmlString = $this->order->attribute( 'data_text_2' );
    	if ( $xmlString != null )
        {
        	$dom = new DOMDocument( '1.0', 'utf-8' );
  	    	$billingData = array();   
        	$success = $dom->loadXML( $xmlString );
        	$billingNodes = $dom->getElementsByTagName('responseData');             	     	
        	$billingDataNode =  $billingNodes->item(0);
        	$XMLElements =  $billingDataNode->childNodes;
        	for($i=0; $i < $XMLElements->length; $i++)
        	{
        		$itemName = $XMLElements->item($i)->nodeName;
        		$item = $XMLElements->item($i)->textContent;
        		if($itemName != 'shop_account')
        		{
        			$billingData[$itemName] = $item;	
        		}       			       			 
        	}
        	return $billingData;    	
        }   	
    }      

    
 	function setOrderComments($comments)
    {
    	
    	
    	$xmlString = $this->order->attribute( 'data_text_1' );
    	if ( $xmlString == null )
        {
        	$xmlString = self::buildXML('order_data', array());
        	$this->order->setAttribute( 'data_text_1', $xmlString );
        	$db = eZDB::instance();
	        $db->begin(); 
	        $this->order->store();
	        $db->commit();  
        	$xmlString = $this->order->attribute( 'data_text_1' );
        }
 		//TODO Complete
    }
    
    
    function getOrderComments()
    {
    	$xmlString = $this->order->attribute( 'data_text_2' );
    	if ( $xmlString != null )
        {
        	$dom = new DOMDocument( '1.0', 'utf-8' );
  	    	$commentData = array();   
        	$success = $dom->loadXML( $xmlString );
        	$commentNodes = $dom->getElementsByTagName('comments');             	     	
        	$commentDataNode =  $commentNodes->item(0);
        	$XMLElements =  $commentDataNode->childNodes;
        	for($i=0; $i < $XMLElements->length; $i++)
        	{
        		$commentData[$i] = array();
	   			$itemAttributes = $XMLElements->item($i)->childNodes;
	   			for($j=0; $j < $itemAttributes->length; $j++)
	        	{
	        		$attributeName = $itemAttributes->item($j)->nodeName;
	        		$attributeContent = $itemAttributes->item($j)->textContent;
	        		$commentData[$i][$attributeName] = $attributeContent;
	        	}       			       			 
        	}
        	return $commentData;    	
        }  
    }
        
  	/* Gets the total order price	 
	 * @return      the order price
	 */       
    function getOrderPrice()
    {
    	return $this->orderTotalIncVAT();
    }
    
    
     /* Gets the order status
	 * @return      the order status
	 */   
    function getOrderStatus()
    {
    	return $this->order->status();
    }

    //TODO Implement me
     /* Sets an array of item as the product collection of the related eZOrder 
	 * @param		the desired status for the order
	 * @return      the image at the specified URL
	 */       
    function setOrderStatus($status)
    {
    	$this->order->setStatus($status);
    	$this->order->store();
    }
    
    function setCurrency($currencyCode)
    {
    	$collection = $this->order->productCollection();   	                      
        $collection->setAttribute( 'currency_code', $currencyCode );
        $collection->store();	
    }
    
	function activate()
	{
		$this->order->activate();
	}    
    /* eZ Order Creation in one action */ 
    //TODO  Implement me 
	static function createOrder($object, $quantity, $formData, $priceAttr=null)
	{   		
	}    
		

	public function getRelatedProduct()
	{
		
	}
    
   	/* Gets the product price searching in the attributes with the eZPrice datatype, if the attributeIdentifier
   	 * parameter is specified, the method gets the price of it, if the attributeIdentifier refers to a non eZPrice related
   	 * datatype, the method gets the generic value of the attribute as the price returned 
	 * @param object	the eZContentObject which the method will fetches the price 
	 * @param atributeIdentifier	a valid identifier of a eZContentObjectAttribute in the object
	 * @return      	the total price of the product
	 */      
    static function getProductPriceData($object, $attributeIdentifier='')
    {        	
        $attributes = $object->dataMap();
        $price = 0;
        $priceFound = false;
        if(isset($attributeIdentifier) && $attributeIdentifier!='')
        {
        	if($attributes[$attributeIdentifier] instanceof eZContentObjectAttribute)
        	{
        		$attribute = $attributes[$attributeIdentifier];
	            $dataType = $attribute->dataType();
	            if ( eZShopFunctions::isProductDatatype( $dataType->isA() ) )
	            {
	                $priceObj = $attribute->content();
	                $price += $priceObj->attribute( 'price' );	          
	                $priceFound = true;
	            }  	  	        	        	
        		else
        		{
        			$price = $attributes[$attributeIdentifier]->value();	
					$priceFound = true;
        		}        		
        	}
        }
        else
        {        
	        foreach ( $attributes as $attribute )
	        {
	            $dataType = $attribute->dataType();	            	          	           
	           // if ( eZShopFunctions::isProductDatatype( $dataType->isA() ) )
	            if ( ( $dataType instanceof eZPriceType ) ||   ($dataType instanceof eZMultiPriceType) )
	            {	            
	                $priceObj = $attribute->content();
	                // ensuring that we have getting the price even when this was partialy set (only accessible from DataFloat) before
	                if($priceObj->attribute( 'price' ) == 0)
	                {
	                	if($attribute->DataFloat == $priceObj->attribute( 'price' ))
	                	{
	                		$price += $priceObj->attribute( 'price' );	
	                	}
	                	else
	                	{
	                		$price += $attribute->DataFloat;	
	                	}
	                }
	                else
	                {
	                	$price += $priceObj->attribute( 'price' );	
	                }	                	          
	                $priceFound = true;
	            }  	  	        	        	
	        }    	                 	
        }
        if(!$priceFound)
        {
       		eZDebug::writeError ("Price not found in the object", "getProductPriceData@OrderManager");
        }
        return $price;
    }
    
    
    static function getProductPriceInfo($productId)
    {
    	$obj = eZContentObject::fetch($productId);
    	if( $obj->ClassIdentifier == 'subscription_package' )
    	{
   			return array('obj' => $obj, 'identifier' => 'subscription_price');
    	}
    	else
    	{
    		return array('obj' => $obj, 'identifier' => '');
    	}
    }
    
    static function getProductCurrency($object, $attributeIdentifier)
    {    		    	    	
        $attributes = $object->dataMap();
        if(isset($attributeIdentifier))
        {
        	if($attributes[$attributeIdentifier] instanceof eZContentObjectAttribute)
        	{
	            $dataType = $attribute->dataType();
	            if ( eZShopFunctions::isProductDatatype( $dataType->isA() ) )
	            {
	            	 $priceObj = $attribute->content();
	            	 $currency = $priceObj->attribute( 'currency' );    
	                 return $currency; 
	            }  	  	        	        	
        		else
        		{
        			return;
        		}        		
        	}
        }
        else
        {        	
	        foreach ( $attributes as $attribute )
	        {
	            $dataType = $attribute->dataType();
	            if ( eZShopFunctions::isProductDatatype( $dataType->isA() ) )
	            {
	            	 $priceObj = $attribute->content();
      				 $currency = $priceObj->attribute( 'currency' );
      				 return $currency;       
	                  
	            }  	  	        	        	
	        }	         	             
	        return;    
        }
    }    
    
    
   	/* Builds an array of product data from a eZContentObject and its related product information
	 * @param object	the eZContentObject for gets the info 
	 * @param quantity	the quantity of items for the product
  	 * @param price0	the price of the product 
	 * @return      	the array with the data
	 */          
    static function productArrayFromObject($object, $quantity, $price)
    {      	
    	$product = array('name' => $object->name(), 'contentobject_id' => $object->ID, 'item_count' => $quantity, 'price' => $price );
    	return $product;        
    }        
    
    static function productArrayFromName($name, $object, $quantity, $price)
    {
    	$product = array('name' => $name, 'contentobject_id' => $object->ID, 'item_count' => $quantity, 'price' => $price );
    	return $product;   
    }
    
    
	static function isShippableProduct($product_id)
	{
		if( eZContentObject::exists( $product_id) )
		{
			$creditsIni = eZINI::instance("ticoproducttypes.ini.append.php");
    		$types = $creditsIni -> variable('ProductTypes', 'DigitalProducts');
    		$product = eZContentObject::fetch( $product_id );     		
    		$identifier = $product->contentClassIdentifier();    		
    		if(in_array($identifier, $types))
    		{
    			return false;
    		}
    		return true;
		}
		return true;
	}   

	static function isShippable($product_id)
	{
		$productObj =  eZContentObject::fetch($product_id);
		$productAttributes = $productObj->dataMap();
		
		if($productObj->contentClassIdentifier() == "guidebook")
		{
			return true;
		}
		else
		{
			return false;
		}		
	}
        
    static function getProductPrice($object, $attributeIdentifier)
    {    		    	    	
        $attributes = $object->dataMap();
        if(isset($attributeIdentifier))
        {
        	if($attributes[$attributeIdentifier] instanceof eZContentObjectAttribute)
        	{
	            $dataType = $attribute->dataType();
	            if ( eZShopFunctions::isProductDatatype( $dataType->isA() ) )
	            {
	                $priceObj = $attribute->content();
	                $price += $priceObj->attribute( 'price' );	          
	                $priceFound = true;
	            }  	  	        	        	
        		else
        		{
        			$price = $attributes[$attributeIdentifier]->value();	
					$priceFound = true;
        		}        		
        	}
        }
        else
        {        	
	        foreach ( $attributes as $attribute )
	        {
	            $dataType = $attribute->dataType();
	            if ( eZShopFunctions::isProductDatatype( $dataType->isA() ) )
	            {
	            
	                $priceObj = $attribute->content();
	                $price += $priceObj->attribute( 'price' );	          
	                $priceFound = true;
	            }  	  	        	        	
	        }    	                 	
        }
        return $price;
    }    	
}
 