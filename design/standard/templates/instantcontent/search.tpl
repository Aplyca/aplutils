{def    $limit = ezini('SearchSettings', 'PageLimit', 'search.ini')
        $search_view = ezini('SearchSettings', 'SearchViewDefault', 'search.ini') 
        $sortby = fetch('instantcontent', 'getsortby', hash('sortby', $sort_array)) 
        $keywords = fetch('instantcontent', 'getsearchquery', hash('query', $query)) 
        $filter = fetch('instantcontent', 'getsearchfilter', hash('filters', $filters))     
        $root = fetch('instantcontent', 'root', hash('nodes', $root_nodes))       
		$find = fetch( 'ezfind', 'search', hash('query', $keywords,
										     	'sort_by', $sortby,
										  	   	'offset', $view_parameters.offset,
												'limit', $limit,
												'section_id', ezini('SearchSettings', 'Section', 'search.ini'),
												'class_id', ezini('SearchSettings', 'ClassIdentifierFilter', 'search.ini'),
												'subtree_array', $root,
												'filter', $filter,
												'spell_check', array( true() ) 
												))										
}

{set-block variable=total_search scope=global}{$find.SearchCount}{/set-block}
{if gt($find.SearchCount, 0)}
    {if $view_parameters.search_view}
        {set $search_view = $view_parameters.search_view}
    {/if}
<div class="list">			 
{foreach $find.SearchResult as $item}		
	{node_view_gui content_node=$item view=$search_view}
{/foreach}
</div>
<div class="pagenavigator">
    {include    name=navigator
                uri='design:navigator/google.tpl'
                page_uri=$page_uri
                item_count=$find.SearchCount
                view_parameters=$view_parameters
                item_limit=$limit}
</div>
{/if}			