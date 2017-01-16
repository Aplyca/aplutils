{if $block_style|not()}
{def $block_style = 'aplinputblock'}
{/if}
<div class="{$block_style}">		
	{if eq($attribute.type, 'cc_number')}
		{include uri="design:attributeinput/types/cc_number.tpl" attribute=$attribute}			
	{elseif eq($attribute.type, 'integer')}
		{include uri="design:attributeinput/types/integer.tpl" attribute=$attribute}		
	{elseif eq($attribute.type, 'password')}
		{include uri="design:attributeinput/types/password.tpl" attribute=$attribute}
	{elseif eq($attribute.type, 'cc_year')}
		{include uri="design:attributeinput/types/cc_year.tpl" attribute=$attribute}
	{elseif eq($attribute.type, 'checkbox')}
		{include uri="design:attributeinput/types/checkbox.tpl" attribute=$attribute}
	{elseif eq($attribute.type, 'month')}
		{include uri="design:attributeinput/types/month.tpl" attribute=$attribute}
	{elseif eq($attribute.type, 'usastate')}
		{include uri="design:attributeinput/types/usa_state.tpl" attribute=$attribute}
	{elseif eq($attribute.type, 'country')}
		{include uri="design:attributeinput/types/country.tpl" attribute=$attribute}		
	{elseif eq($attribute.type, 'country_code')}
		{include uri="design:attributeinput/types/country_code.tpl" attribute=$attribute}
	{elseif eq($attribute.type, 'hour')}
		{include uri="design:attributeinput/types/hour.tpl" attribute=$attribute}		
	{elseif eq($attribute.type, 'select')}
		{include uri="design:attributeinput/types/select.tpl" attribute=$attribute}
	{elseif eq($attribute.type, 'fieldset')}
		{include uri='design:attributeinput/types/fieldsetgroup.tpl' attribute=$attribute res=$response}
	{else}					
		{include uri="design:attributeinput/types/text.tpl" attribute=$attribute iniName=$iniName blockName=$blockName groupName=$groupName}		
	{/if}
</div>