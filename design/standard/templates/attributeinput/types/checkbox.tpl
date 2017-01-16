<label {if $attribute.validation_error} class="message-error"{/if}>
	{$attribute.name} {if $attribute.required}<span class="required_field">*</span>{/if}
</label>

<input type="checkbox" name="{$attribute.identifier}" id="{$attribute.identifier}"  {if $attribute.value|eq('on')}checked="checked"{/if}/> <span id="text_check_{$attribute.identifier}"></span>