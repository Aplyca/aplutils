<label {if $attribute.validation_error} class="message-error"{/if}>
	{$attribute.name} {if $attribute.required}<span class="required_field">*</span>{/if}
</label>

{if $attribute.data}
	<select id="{$attribute.identifier}" name="{$attribute.identifier}">
		{foreach $attribute.data as $key => $option}
			<option value="{$key}">{$option}</option>
		{/foreach}
	</select>
{/if}
