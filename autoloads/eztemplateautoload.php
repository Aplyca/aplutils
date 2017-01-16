<?php

// Operator autoloading

$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array( 'script' => 'extension/aplutils/autoloads/AplUtilsOperators.php',
                                    'class' => 'AplUtilsOperators',
                                    'operator_names' => array(  'explode_by_capital_letter',
                                                                'fetch_classes_by_group',
                                                                'time_ago', 
                                                                'get_orders_by_user',
                                                                'get_total_orders_by_user',
																'get_navigation_node') );
	
									
?>