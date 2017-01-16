<?php

$module = $Params['Module'];
$offset = $Params['offset'];

$searchIni = eZINI::instance('search.ini'); 

$query = false;
$filters = array();

$rootNodeField = $searchIni -> variable('SearchSettings', 'RootNode');
if ( $module->hasActionParameter( $rootNodeField ))
{
	$rootNodes = $module->actionParameter( $rootNodeField );
}

$queryField = $searchIni -> variable('SearchSettings', 'FieldQuery');	
if ( $module->hasActionParameter( $queryField ))
{
	$query = $module->actionParameter( $queryField );
}	

$attributes_filter = $searchIni -> variable('SearchSettings', 'FieldsFilter');
foreach ($attributes_filter as $input)
{
    if ( $module->hasActionParameter( $input ))
    {
        $filters[$input] = $module->actionParameter( $input );
    }   
}

$sortArray = array();
foreach ($searchIni -> variable('SearchSettings', 'SortParams') as $sortParam)
{
	if ($module -> hasActionParameter( $sortParam))
	{
		$sortArray[$sortParam] = $module -> actionParameter($sortParam);
	}
}

$searchView = $searchIni -> variable('SearchSettings', 'DefaultSearchView');
if ( $module->hasActionParameter( 'SearchView' ))
{
	$searchView = $module->actionParameter( 'SearchView' );
}

$tpl = eZTemplate::factory();
$tpl->setVariable( 'root_nodes', $rootNodes);
$tpl->setVariable( 'query', $query);
$tpl->setVariable( 'sort_array', $sortArray );
$tpl->setVariable( 'view_parameters', array('offset' => $offset, 'search_view' => $searchView));
$tpl->setVariable( 'page_uri', 'instantcontent/search');
$tpl->setVariable( 'filters', $filters);

$Result = array();
$Result['content'] = $tpl->fetch( "design:instantcontent/search.tpl" );
$Result['path'] = array ( array ('url' => 'instantcontent/search', 'text' => "full") );

$Result['pagelayout'] = false;
eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));

$response['m']=$Result['content'];
$response['total_search']=$tpl->variable('total_search');
$response['s']='s';
print_r(json_encode($response));

eZDB::checkTransactionCounter();
eZExecution::cleanExit();

?>