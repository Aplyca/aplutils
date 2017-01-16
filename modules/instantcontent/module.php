<?php

$Module = array( 'name' => 'instantcontent', 'variable_params' => false );
$ViewList = array();	

$search_ini = eZINI::instance('search.ini'); 

$rootNode = $search_ini -> variable('SearchSettings', 'RootNode');
$rootPap = array($rootNode => $rootNode);

$attributes_filter = $search_ini -> variable('SearchSettings', 'FieldsFilter');
$filterPap = array_combine($attributes_filter, $attributes_filter);
if (!is_array($filterPap))
{
	$filterPap = array();
}

$query_field = $search_ini -> variable('SearchSettings', 'FieldQuery');
$queryPap =array($query_field => $query_field);

$sortParams = $search_ini -> variable('SearchSettings', 'SortParams');
$sortPap = array_combine($sortParams, $sortParams);
if (!is_array($sortPap))
{
	$sortPap = array();
}

$ViewList['search'] = array(	'functions' => array( 'search' ),
							 	'script' => 'search.php',
							 	'params' => array (),
								'unordered_params' => array('offset' => 'offset'),
								'single_post_actions' => array( 'SearchButton' => 'Search'),
							    'post_action_parameters' => array( 'Search' =>  array_merge($filterPap, $sortPap, $queryPap, $rootPap)));

$SiteAccess = array(
    'name'=> 'SiteAccess',
    'values'=> array(),
    'path' => 'classes/',
    'file' => 'ezsiteaccess.php',
    'class' => 'eZSiteAccess',
    'function' => 'siteAccessList',
    'parameter' => array()
    );
	
$FunctionList['search'] = array( 'SiteAccess' => $SiteAccess );

?>