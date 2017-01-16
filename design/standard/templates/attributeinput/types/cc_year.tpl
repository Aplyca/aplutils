<label {if $attribute.validation_error} class="message-error"{/if}>
	{$attribute.name} {if $attribute.required}<span class="required_field">*</span>{/if}
</label>

{def $currYear=currentdate()|datetime(custom, '%Y')}
<select id="{$attribute.identifier}" name="{$attribute.identifier}">
{for 0 to 10 as $counter}
	{if eq($attribute.value, $currYear)}
		<option value={$currYear} selected="selected">{$currYear}</option>			
	{else}
		<option value={$currYear}>{$currYear}</option>
	{/if}				
	{set $currYear = $currYear|inc(1)}		
{/for}	
</select>