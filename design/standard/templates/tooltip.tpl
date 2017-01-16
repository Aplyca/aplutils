{def $tooltip_type = ezini('TooltipSettings','TooltipMessage', 'tooltip.ini')
	$use_ini = false()
	$use_attribute = false()
}
{if or($tooltip_type|eq(ini), $tooltip_type|eq(both))}
	{set $use_ini=true()}
{/if}
{if or($tooltip_type|eq(attribute), $tooltip_type|eq(both))}
	{set $use_attribute=true()}
{/if}
{if $use_ini}
	{def $tooltip_messages_ids = ezini('TooltipAttributeMessages','TooltipAttributeMessageId', 'tooltip.ini')
		$tooltip_messages_identifiers = ezini('TooltipAttributeMessages','TooltipAttributeMessageIdentifier', 'tooltip.ini')
		$tooltip_messages_custom_identifiers = ezini('TooltipAttributeMessages','TooltipCustomAttributeMessageIdentifier', 'tooltip.ini')
		$tooltip_messages_custom_names = ezini('TooltipAttributeMessages','TooltipCustomAttributeMessageName', 'tooltip.ini')
	}
{literal}	
	<script type="text/javascript">
		$(document).ready(function(){
{/literal}
			{foreach $tooltip_messages_identifiers as $att_identifier => $tip_message}
{literal}
				var id = "[id$='_{/literal}{$att_identifier}{literal}']";
				
				$(id).qtip({
					content: '{/literal}{$tip_message}{literal}',
					position: {
						corner: {
							target: 'bottomRight',
							tooltip: 'bottomLeft'
						}
					},
					style: {
						//name: 'light',
						//width: 200,
						padding: 5,
						background: '#FFFFFF',
						color: 'black',
						textAlign: 'center',
						border: {
							width: 2,
							radius: 5,
							color: '#FFCB32'
						},
						tip: 'leftBottom'
					},
					hide:{delay:500}	
				});
{/literal}	
			{/foreach}
{literal}

{/literal}
			{foreach $tooltip_messages_ids as $att_id => $tip_message}
{literal}
				var id = "[id^='ezcoa-{/literal}{$att_id}{literal}']";
				
				$(id).qtip({
					content: '{/literal}{$tip_message}{literal}',
					position: {
						corner: {
							target: 'bottomRight',
							tooltip: 'bottomLeft'
						}
					},
					style: {
						/*name: 'light',*/
						//width: 200,
						padding: 5,
						background: '#FFFFFF',
						color: 'black',
						textAlign: 'center',
						border: {
							width: 2,
							radius: 5,
							color: '#FFCB32'
						},
						tip: 'leftBottom'
					},
					hide:{delay:500}	
				});
{/literal}	
			{/foreach}
{literal}

{/literal}
			{foreach $tooltip_messages_custom_identifiers as $att_id => $tip_message}
{literal}
				var id = "[id^='{/literal}{$att_id}{literal}']";
				
				$(id).qtip({
					content: '{/literal}{$tip_message}{literal}',
					position: {
						corner: {
							target: 'bottomRight',
							tooltip: 'bottomLeft'
						}
					},
					style: {
						/*name: 'light',*/
						//	width: 200,
						padding: 5,
						background: '#FFFFFF',
						color: 'black',
						textAlign: 'center',
						border: {
							width: 2,
							radius: 5,
							color: '#FFCB32'
						},
						tip: 'leftBottom'
					},
					hide:{delay:500}	
				});
{/literal}	
			{/foreach}
{literal}

{/literal}
			{foreach $tooltip_messages_custom_names as $input_name => $tip_message}
{literal}
				var id = "[name^='{/literal}{$input_name}{literal}']";
				
				$(id).qtip({
					content: '{/literal}{$tip_message}{literal}',
					position: {
						corner: {
							target: 'bottomRight',
							tooltip: 'bottomLeft'
						}
					},
					style: {
						/*name: 'light',*/
						//	width: 200,
						padding: 5,
						background: '#FFFFFF',
						color: 'black',
						textAlign: 'center',
						border: {
							width: 2,
							radius: 5,
							color: '#FFCB32'
						},
						tip: 'leftBottom'
					},
					hide:{delay:500}	
				});
{/literal}	
			{/foreach}
{literal}
		});
	</script>
{/literal}
{/if}
{if $use_attribute}
	{if $node}
		{def $data_map = $node.data_map}
	{else}
		{def $data_map = $object.data_map}
	{/if}
	<script type="text/javascript">
		{foreach $data_map as $attribute}
			{if $attribute.contentclass_attribute.description}
			
				var id = "[name$='{$attribute.id}']";
				{literal}
				$(id).qtip({
					content: '{/literal}{$attribute.contentclass_attribute.description}{literal}',
					position: {
						corner: {
							target: 'bottomRight',
							tooltip: 'bottomLeft'
						}
					},
					style: {
						/*name: 'light',*/
						//	width: 200,
						padding: 5,
						background: '#FFFFFF',
						color: 'black',
						textAlign: 'center',
						border: {
							width: 2,
							radius: 5,
							color: '#FFCB32'
						},
						tip: 'leftBottom'
					},
					hide:{delay:500}	
				});
				{/literal}
			{/if}
		{/foreach}		
	</script>		
{/if}