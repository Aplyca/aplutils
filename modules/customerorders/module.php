<?php

$Module = array( 'name' => 'customerorders', 'variable_params' => false );

$ViewList = array();

$ViewList['vieworder'] = array(
    'functions' => array( 'vieworder' ),
    'script' => 'vieworder.php',
	"default_navigation_part" => 'ezordersvnavigationpart',
    'params' => array ( 'OrderID', 'Type', 'ViewFull' ) );	
	
$SiteAccess = array('name'=> 'SiteAccess',
					'values'=> array(),
					'path' => 'classes/',
					'file' => 'ezsiteaccess.php',
					'class' => 'eZSiteAccess',
					'function' => 'siteAccessList',
					'parameter' => array());

$FunctionList['vieworder'] = array( 'SiteAccess' => $SiteAccess );

?>

