<?php

$Module = array( 'name' => 'Apl Services', 'variable_params' => false );

$ViewList = array();

$ViewList['coordinatesbyaddress'] = array(
    'functions' => array( 'coordinatesbyaddress' ),
    'script' => 'coordinatesbyaddress.php',
    'params' => array ( ) );

$ViewList['testview'] = array(
    'functions' => array( 'testview' ),
    'script' => 'testview.php',
    'params' => array ( ) );

$ViewList['managedrafts'] = array(
    'functions' => array( 'managedrafts' ),
    'script' => 'managedrafts.php',
    'params' => array ( ) );

$ViewList['storeattributes'] = array(
    'functions' => array( 'storeattributes' ),
    'script' => 'storeattributes.php',
    'params' => array ( ) );

$ViewList['sublocationlist'] = array(
    'functions' => array( 'sublocationlist' ),										
    'script' => 'sublocationlist.php',
    'params' => array ('Type', 'Value' ) );

$ViewList['getlocation'] = array(
    'functions' => array( 'getlocation' ),										
    'script' => 'getlocation.php',
    'params' => array ('Country', 'State', 'Value') );

$ViewList['usacitiesbyzip'] = array(
    'functions' => array( 'usacitiesbyzip' ),										
    'script' => 'usacitiesbyzip.php',
    'params' => array ('Zip', 'State'));

$ViewList['customlogout'] = array(
    'functions' => array( 'customlogout' ),										
    'script' => 'customlogout.php',
    'params' => array ());

	 
$SiteAccess = array('name'=> 'SiteAccess',
					'values'=> array(),
					'path' => 'classes/',
					'file' => 'ezsiteaccess.php',
					'class' => 'eZSiteAccess',
					'function' => 'siteAccessList',
					'parameter' => array());		
	
$FunctionList['coordinatesbyaddress'] = array( 'SiteAccess' => $SiteAccess );    
$FunctionList['testview'] = array( 'SiteAccess' => $SiteAccess );  
$FunctionList['storeattributes'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['sublocationlist'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['getlocation'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['usacitiesbyzip'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['managedrafts'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['customlogout'] = array( 'SiteAccess' => $SiteAccess );


?>