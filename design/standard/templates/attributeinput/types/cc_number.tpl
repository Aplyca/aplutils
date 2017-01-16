<label {if $attribute.validation_error} class="message-error"{/if}>
	{$attribute.name} {if $attribute.required}<span class="required_field">*</span>{/if}
</label>
<input type="text" name="{$attribute.identifier}" value="{$attribute.value}" id="{$attribute.identifier}" size="20"/>