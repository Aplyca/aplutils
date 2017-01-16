{def $children_limit=10
     $items=fetch('content','list',hash('parent_node_id', $node.node_id,
                                           'limit', $page_limit,
                                           'class_filter_type', 'include',
                                           'class_filter_array', ezini("IndexInclude", "ClassIdentifierList", "ezfind.ini"),
                                           'offset', $view_parameters.offset,
                                           'sort_by', $node.sort_array))}

<h2><a href={$item.url_alias|ezurl}>{$node.name}</a></h2>
{if is_unset($depth)}
	{def $depth=1}
{/if}  
{if le($node.depth, $depth) }
<ul>
{foreach $items as $item}
	<li>{node_view_gui view="sitemap" content_node=$item depth=$depth}</li>
{/foreach}
</ul>
{/if}