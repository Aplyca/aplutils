<?php

$Module = array( 'name' => 'attributecontentactions', 'variable_params' => false );

$ViewList = array();

$ViewList['attributecontentactions'] = array(
    'functions' => array( 'attributecontentactions' ),
    'script' => 'attributecontentactions.php',
	"params" => array( ) );


$SiteAccess = array('name'=> 'SiteAccess',
					'values'=> array(),
					'path' => 'classes/',
					'file' => 'ezsiteaccess.php',
					'class' => 'eZSiteAccess',
					'function' => 'siteAccessList',
					'parameter' => array());	

$FunctionList['attributecontentactions'] = array( 'SiteAccess' => $SiteAccess );


?>

