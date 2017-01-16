<?php

$Module = array( 'name' => 'aplinput', 'variable_params' => false );

$ViewList = array();

$ViewList['getfieldset'] = array(
    'functions' => array( 'getfieldset' ),
    'script' => 'getfieldset.php',
    'params' => array ( ) );

$SiteAccess = array('name'=> 'SiteAccess',
					'values'=> array(),
					'path' => 'classes/',
					'file' => 'ezsiteaccess.php',
					'class' => 'eZSiteAccess',
					'function' => 'siteAccessList',
					'parameter' => array());		
	
$FunctionList['getfieldset'] = array( 'SiteAccess' => $SiteAccess );

?>