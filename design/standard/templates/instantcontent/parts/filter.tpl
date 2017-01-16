{def    $filter_root_node = fetch('instantcontent', 'getrootnodes', hash('type', 'get', 'data', $view_parameters)) 
        $selected_root_node=false()
        $root_nodes=fetch( 'content', 'list', hash( 'parent_node_id', '273',
                                                    'class_filter_type', 'include',
                                                    'class_filter_array', array('category'),
                                                    'sort_by', array('name', false())))
        $default_root_node = ezini(ezini('SearchSettings', 'RootNode', 'search.ini'), 'Default', 'search.ini')
}
<div class="row">
    <div class="three columns">
	   <label for="search_query">{'Search by keyword/s'|i18n('aplutils/general')}</label>
	</div>
	<div class="six columns end">
	   <input name="Search" class="instant" type="text" value="{if ezhttp_hasvariable('Search', 'get')}{ezhttp('Search', 'get')}{/if}" id="search_query"/>
    </div>      
</div>
<div class="row">
	<div class="three columns">
		<label for="root_node">{'Choose root node'|i18n('aplutils/general')}</label>
	</div>
    <div class="six columns end">
		<select id="root_node" class="instant" name="{ezini('SearchSettings', 'RootNode', 'search.ini')}">
		    <option value="{$default_root_node}" id="{$default_root_node}">{"All"|i18n("aplutils/general")}</option>
		{foreach $root_nodes as $root_node}
		    {set $selected_root_node=false()}
		    {if $filter_root_node|contains($root_node.node_id)}
		        {set $selected_root_node=true()}                        
		    {/if}
		    <option value="{$root_node.node_id}" {if $selected_root_node} selected{/if} id="{$root_node.node_id}">{$root_node.name}</option>
		{/foreach}
		</select> 
    </div>    
</div>