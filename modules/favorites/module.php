<?php

$Module = array( 'name' => 'favorites', 'variable_params' => false );

$ViewList = array();

$ViewList['addfavorite'] = array(
    'functions' => array( 'addfavorite' ),
    'script' => 'addFavorite.php',
    'params' => array ( ) );

$ViewList['removeFavorite'] = array(
    'functions' => array( 'removeFavorite' ),
    'script' => 'removeFavorite.php',
    'params' => array ( ) );	
	 
$SiteAccess = array('name'=> 'SiteAccess',
					'values'=> array(),
					'path' => 'classes/',
					'file' => 'ezsiteaccess.php',
					'class' => 'eZSiteAccess',
					'function' => 'siteAccessList',
					'parameter' => array());		
	
$FunctionList['addfavorite'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['removeFavorite'] = array( 'SiteAccess' => $SiteAccess );    

?>