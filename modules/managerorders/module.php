<?php

$Module = array( 'name' => 'managerorders', 'variable_params' => false );

$ViewList = array();

$ViewList['vieworders'] = array(
    'functions' => array( 'vieworders' ),
    'script' => 'vieworders.php',
	"default_navigation_part" => 'ezordersvnavigationpart',
    'params' => array ( ) );

$ViewList['customerorderview'] = array(
    'functions' => array( 'customerorderview' ),
    'script' => 'customerorderview.php',
	"default_navigation_part" => 'ezordersvnavigationpart',
	"params" => array( "CustomerID","Offset","Email") );

$ViewList['orderview'] = array(
    'functions' => array( 'orderview' ),
    'script' => 'orderview.php',
	"default_navigation_part" => 'ezordersvnavigationpart',
    "params" => array( "OrderID", "Type" ) );

$ViewList['searchorders'] = array(
    'functions' => array( 'searchorders' ),
    'script' => 'searchorders.php',
	"default_navigation_part" => 'ezordersvnavigationpart',
    'params' => array ( "customer","product", "status", "type", "fromDate","toDate" ) );


$ViewList['editorder'] = array(
    'functions' => array( 'editorder' ),
    'script' => 'editorder.php',
	"default_navigation_part" => 'ezordersvnavigationpart',
    "params" => array( ) );

$ViewList['getorderinfo'] = array(
    'functions' => array( 'getorderinfo' ),
    'script' => 'getorderinfo.php',
	"default_navigation_part" => 'ezordersvnavigationpart',
    "params" => array('Type', 'OrderID'));

$ViewList['exportorders'] = array(
    'functions' => array( 'exportorders' ),
    'script' => 'exportorders.php',
	"default_navigation_part" => 'ezordersvnavigationpart',
    "params" => array('Mode'));

$ViewList['modifystatus'] = array(
    'functions' => array( 'modifystatus' ),
    'script' => 'modifystatus.php',
	"default_navigation_part" => 'ezordersvnavigationpart',
    "params" => array('OrderID','StatusID'));

	
$SiteAccess = array('name'=> 'SiteAccess',
					'values'=> array(),
					'path' => 'classes/',
					'file' => 'ezsiteaccess.php',
					'class' => 'eZSiteAccess',
					'function' => 'siteAccessList',
					'parameter' => array());	

$FunctionList['vieworders'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['orderview'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['customerorderview'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['searchorders'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['editorder'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['getorderinfo'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['exportorders'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['modifystatus'] = array( 'SiteAccess' => $SiteAccess );

?>


