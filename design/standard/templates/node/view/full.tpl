{* Default object admin view template *}
{default with_children=true()
         is_editable=true()
	 is_standalone=true()}
	{let page_limit=15
		 list_count=and($with_children,fetch('content','list_count',hash(parent_node_id,$node.node_id,depth_operator,eq)))}
		{default content_object=$node.object
				 content_version=$node.contentobject_version_object
				 node_name=$node.name|wash}
			{if and( $with_children, $list_count )}
				{let name=Child
					 children=fetch('content','list',hash(parent_node_id,$node.node_id,sort_by,$node.sort_array,limit,$page_limit,offset,$view_parameters.offset,depth_operator,eq))
					 can_remove=false() can_edit=false() can_create=false() can_copy=false()}
				<div class="table_default">
					<table class="list" width="100%" cellspacing="0" cellpadding="0" border="0">
						{section loop=$:children}
						<tr>
							<td>
								<a href={$:item.url_alias|ezurl}>{node_view_gui view=line content_node=$:item}</a>
							</td>
						</tr>
						{/section}
					</table>
				</div>
				{/let}
				{include name=navigator
						 uri='design:navigator/google.tpl'
						 page_uri=concat('/content/view','/full/',$node.node_id)
						 item_count=$list_count
						 view_parameters=$view_parameters
						 item_limit=$page_limit}
				{/if}
		{/default}
	{/let}
{/default}