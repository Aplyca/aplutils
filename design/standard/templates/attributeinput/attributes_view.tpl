{foreach $attributes as $attribute}
	{if eq($attribute.type, 'text')}
	<div class="block">
		<label>{$attribute.name}</label>
		<input type="text" name="{$attribute.identifier}" size="20" value="" class="text_input" />
	</div>
	{elseif eq($attribute.type, 'email')}
	<div class="block">
		<label>{$attribute.name}</label>
		<input  type="text" name="{$attribute.identifier}" size="20" value="{$user_object.data_map.user_account.content.email}" class="text_input" />	
	</div>
	{else}
	<div class="block">
		<label>{$attribute.name}</label>
		<input type="text" name="{$attribute.identifier}" size="20" value="" class="text_input" />		
	</div>
	{/if}
{/foreach}