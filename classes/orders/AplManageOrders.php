<?php

/** 
 * @author David Sanchez Escobar
 * 
 * This class provides search features for ez Publish
 * 
 */ 
 

class AplManageOrders
{
	
	function __construct() 
    {      	
 	
    }   
    
    
    /**
	 * Get eZ Publish shop orders by filtering and keyword parameters 
	 * supports asc and desc order by name, and date range, only fetches active orders
	 * @param string $customer a keyword for the customer name, it works for similar matches
	 * @param string $product a keyword for the product name, it works for similar matches
	 * @return eZOrder $orderArray an array of eZOrder objects
	 */
    
    static function searchOrders($query, $offset, $limit, $withOffset=true)
    { 		
		$db = eZDB::instance();
		$sortOrder = $db->escapeString( $sortOrder );				
		if($withOffset)
		{
			$db_params = array();
			$db_params["offset"] =(int) $offset;
			$db_params["limit"] =(int) $limit;	
			$orders = $db->arrayQuery( $query, $db_params );	
		}	
		else
		{
			$orders = $db->arrayQuery($query);
		}  				
								
		$orderArray = array();		
		foreach ( $orders as $order )
		{
			$order = new eZOrder( $order );
			$orderArray[] = $order;
		}		
        return $orderArray;													
    }
	
    
        /**
	 * Get eZ Publish shop orders COUNT by filtering and keyword parameters 
	 * the purpose of this method is to be used with  searchOrders for provide pagination features
	 * supports asc and desc order by name, and date range, only fetches active orders
	 * @param string $customer a keyword for the customer name, it works for similar matches
	 * @param string $product a keyword for the product name, it works for similar matches
	 * @return the number of matches
	 */
	static function countSearchOrders( $query )
    {
        $db = eZDB::instance();       		       
		$countArray = $db->arrayQuery( $query );		        
		return count($countArray);
    }
    
    
    static function buildQuery($customer, $product, $status, $type, $fromDate, $toDate, $sortOrder = "asc", $show = eZOrder::SHOW_NORMAL)
    {
    	$ini = eZINI::instance('aplpayment.ini');	
		$queryClass =  $ini->variable('Orders','OrderQueryClass');
		$queryHandler = new $queryClass();
		$customQuery = $queryHandler->getCustomQuery();
		if($customQuery)
		{
			$select = $customQuery['select'];
			$from = $customQuery['from'];
			$where = $customQuery['where'];
		}
		else
		{
			$select = "SELECT ezorder.*";		
			$from = " FROM ezorder, ezcontentobject";
			
			if ( $product != "" ) 
			{
				$from .= ", ezproductcollection_item";
			}
			
			$where = " WHERE ".eZOrder::getShowOrdersQuery( $show, "ezorder" )." 
					   AND   ezorder.is_temporary = '0' 
					   AND   ezcontentobject.id = ezorder.user_id";
	    			
			if ( $customer != "" ) 
			{
				$where .= " AND ezcontentobject.name LIKE '%". $customer ."%'";
			}
		}
		
		if ( $product != "" ) 
		{
			$where .= " AND ezorder.id = ezproductcollection_item.id 
						AND ezproductcollection_item.name LIKE '%". $product ."%'";
		}
		
		if( !($status == 0 || $status == "") )
		{
			$where .= " AND ezorder.status_id = ". $status;
		}
		
    	if( !($type == "Any" || $type == "") )
		{
			$where .= " AND ezorder.account_identifier = " . "'" . $type . "'";
		}			
		if ( $fromDate > 0 and $toDate > 0 ) 
		{
			$where .= " AND ezorder.created BETWEEN ". $fromDate ." AND ". $toDate;
		}			
		if($customQuery)
		{
			$orderBy = $queryHandler->getSortType();			  
		}
		else
		{
			$orderBy = " ORDER BY ezcontentobject.name ".$sortOrder;	
		}							
		$query = $select . $from . $where . $orderBy;
		return $query;			
    }
    
}

?>