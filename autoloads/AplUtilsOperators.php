<?php

/*!
  \class AplOperators
  \brief The class AplOperators
*/

class AplUtilsOperators
{
	var $Operators;
	/*!
		Constructor
	*/
	function AplUtilsOperators()
	{
		$this->Operators = array( 'explode_by_capital_letter', 
		                          'fetch_classes_by_group',
		                          'time_ago',
		                          'get_input_filters',
                                  'get_orders_by_user',
                                  'get_total_orders_by_user',
								  'get_navigation_node' ); 
	}
	/*!
		Returns the operators in this class
	*/
	function operatorList()    
	{
		return $this->Operators;
	}
	/*!
		\return true to tell the template engine that the parameter list
		exists per operator type, this is needed for operator classes
		that have multiple operators.
	*/
	function namedParameterPerOperator()
	{
		return true;
	}
	/*!
		See eZTemplateOperator::namedParameterList()
	*/
	function namedParameterList()
	{        
		return array(   'explode_by_capital_letter' => array( 'data' => array( 'type' => 'string', 
																			'required' => true, 
																			'default' => false ) 
																			),
					    'fetch_classes_by_group' => array( 'group_id' => array( 'type' => 'array', 
    																			'required' => true, 
    																			'default' => false ),
												           'return_type' => array( 'type' => 'text', 
																					'required' => false, 
																					'default' => false )
																		),
                        'time_ago' => array( 'timestamp' => array( 'type' => 'string', 
																'required' => true, 
																'default' => '' )
														),
                        'get_input_filters' => array(   'type' => array(  'type' => 'string', 
                                                                        'required' => false,
                                                                        'default' => '' ),
                                                        'data' => array(  'type' => 'array', 
                                                                        'required' => false,
                                                                        'default' => array() )
                                                        ),
                        'get_orders_by_user' => array( 'UserID' => array( 'type' => 'string', 
																   	      'required' => true,
																          'default' => '' ),
                                                        'Offset' => array(  'type' => 'integer', 
                                                                        	'required' => false,
                                                                        	'default' => 0 ),
                                                        'Limit' => array(  'type' => 'integer', 
                                                                        	'required' => false,
                                                                        	'default' => 0 ) 
                                                        ) ,
                        'get_total_orders_by_user' => array( 'UserID' => array( 'type' => 'string', 
																   	      		'required' => true,
																          		'default' => '' )
														),
			    		'get_navigation_node' => array( 'current_node_id' => array( 'type' => 'integer',
																	   				'required' => true, 
																	   				'default' => 0 ),
											    	    'sibling_nodes' => array( 'type' => 'array',
																		  		  'required' => true,
											    	   					  		  'default' => array()),
											    	    'circular' => array( 'type' => 'integer',
																		  	 'required' => true,
											    	   					  	 'default' => 0 )
														)                  								
						);                                       
	}
	/*!
		Executes the needed operator(s).
		Checks operator names, and calls the appropriate functions.
	*/
	function modify( $tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
	{	 
		switch ( $operatorName )
		{
			case 'explode_by_capital_letter':
			{	
				preg_match_all('/[A-Z][^A-Z]*/',ucfirst($namedParameters['data']),$results);
				$resultString = implode(" ",$results[0]);					
				$operatorValue = $resultString;
			} 
			break;   
			case 'fetch_classes_by_group':
			{    	
				$operatorValue = $this->getClassListbyGroupId(  $namedParameters['group_id'], $namedParameters['return_type'] );
			} 
			break;     
			case 'time_ago':
			{    	
				$operatorValue = $this->time_ago( $namedParameters['timestamp'] );
			} 
			break;
            case 'get_input_filters':
            {       
                $operatorValue = self::getInputFilters( $namedParameters );
            } 
            break;   
            case 'get_orders_by_user':
            {					
				$operatorValue = self::getOrdersByUser( $namedParameters['UserID'], $namedParameters['Offset'], $namedParameters['Limit'] );
            } 
            break;  
            case 'get_total_orders_by_user':
            {					
				$operatorValue = self::getTotalOrdersByUser( $namedParameters['UserID'] );
            }      
			case 'get_navigation_node':
		    {
		        $operatorValue = self::getNavigationNode( $namedParameters );
		    }
			break;	
		}
	}
	
	// @param $returntype :  'id_list' , 'object', 'array_info' 
	function getClassListbyGroupId($groupId, $returnType)
	{
		if($returnType == 'object')
		{
			$classes = eZContentClass::fetchAllClasses( true, true, $groupId );	
    		for($i=0; $i < sizeof($classes); $i++)
    		{
    			// avoiding bug with  ContentObjectName attribute 
    			$classes[$i]->ContentObjectName =  $classes[$i]->name();    			
    		}
    	}
    	elseif($returnType == 'id_list')
    	{
    		$classes = array();
    		$rawClasses = eZContentClass::fetchAllClasses( false, true, $groupId );
    		foreach($rawClasses as $rawClass)
    		{
    			$classes[] = $rawClass['id'];
    		}
    	}
    	else
    	{
    		$classes = eZContentClass::fetchAllClasses( false, true, $groupId );
    	}    	
    	return $classes;
	}
	 
	function time_ago($tm,$rcs = 0) {
		$cur_tm = time(); 
		$dif = $cur_tm-$tm;
		$pds = array('second','minute','hour','day','week','month','year','decade');
		$lngh = array(1,60,3600,86400,604800,2630880,31570560,315705600);
		for($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);

		$no = floor($no); if($no <> 1) $pds[$v] .='s'; $x=sprintf("%d %s ",$no,$pds[$v]);
		if(($rcs == 1)&&($v >= 1)&&(($cur_tm-$_tm) > 0)) $x .= time_ago($_tm);
		return $x;
	}
    
    static function getOrdersByUser( $userID, $offset, $limit, $asObject=true )
    {
    	return eZPersistentObject::fetchObjectList( eZOrder::definition(),
												    null,
												    array( "user_id" => $userID, 'is_temporary' => 0 ),
												    array( "created" => "desc" ), 
												    array( 'offset' => $offset, 'length' => $limit ),
												    $asObject );
															   
    }
    
    static function getTotalOrdersByUser( $userID, $asObject=true )
    {
    	$result = eZPersistentObject::fetchObjectList( eZOrder::definition(),
												       null,
												       array( "user_id" => $userID, 'is_temporary' => 0 ),
												       array( "created" => "desc" ),
												       null,
												       $asObject );
												       
		return count($result);												       
															   
    }  
	
	static function getNavigationNode( $params )
	{		
		$node = eZContentObjectTreeNode::fetch($params['current_node_id']);
		
		if ($node instanceof eZContentObjectTreeNode)
		{
			$siblingNodes = $params['sibling_nodes'];
			$nodeNav = array();
			
			foreach ($siblingNodes as $key => $node)
			{
				if ($node->NodeID == $params['current_node_id'])
				{
					if ($key == 0)
					{
						if ($params['circular'])
							$nodeNav['previous'] = end($siblingNodes);
						else
							$nodeNav['previous'] = false;
					}
					else
					{
						$nodeNav['previous'] = $siblingNodes[$key-1];
					}
					
					$nodeNav['current'] = $node;
					
					if (($key+1) == count($siblingNodes))
					{
						if ($params['circular'])
							$nodeNav['next'] = reset($siblingNodes);
						else
							$nodeNav['next'] = false;
					}
					else
					{
						$nodeNav['next'] = $siblingNodes[$key+1];
					}
				}
			}
			
			return $nodeNav;
		}
		else
			return false;
	}
    
}
?>