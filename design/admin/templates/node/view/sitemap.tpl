{def $children_limit=10
     $print_sitemap='layout/set/print'
     $path_copydeck='export/word'
     $items=fetch('content','list',hash('parent_node_id', $node.node_id,
                                           'limit', $page_limit,
                                           'class_filter_type', 'include',
                                           'class_filter_array', ezini("IndexInclude", "ClassIdentifierList", "ezfind.ini"),
                                           'offset', $view_parameters.offset,
                                           'sort_by', $node.sort_array))}

<a href={$item.url_alias|ezurl}>{$node.name}</a> <a href={concat($path_copydeck, "/", $node.node_id)|ezurl()}>[<strong>{$node.class_name}</strong>]</a>
<p>{"Current Version:"|d18n("design/geb/view/sitemap")} {currentdate()|l10n( 'shortdate' )}  <a href={concat($print_sitemap, '/', $node.url_alias)|ezurl()}>{"Print view"|d18n("design/geb/view/sitemap")}</a></p>
<p><i style="font-size:10px;">{"Version %version, modified: %modified by %modifier"|d18n('design/geb/view/sitemap', '', hash( '%version', $node.contentobject_version, '%modified', $node.object.modified|l10n( 'shortdatetime' ), '%modifier', $node.contentobject_version_object.creator.name))}</i></p>
{if is_unset($depth)}
	{def $depth=10}
{/if}  
{if le($node.depth, $depth) }
<ul>
{foreach $items as $item}
	<li>{node_view_gui view="sitemap" content_node=$item depth=$depth}</li>
{/foreach}
</ul>
{/if}