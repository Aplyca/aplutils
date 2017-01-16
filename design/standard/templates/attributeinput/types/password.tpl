<label {if $attribute.validation_error} class="message-error"{/if}>
	{$attribute.name} {if $attribute.required}<span class="required_field">*</span>{/if}
</label>
<input type=password name="{$attribute.identifier}" value="{$attribute.value}" id="{$attribute.identifier}" size="20"/>

<label {if $attribute.validation_error} class="message-error"{/if}>
	Confirm {$attribute.name} {if $attribute.required}<span class="required_field">*</span>{/if}
</label>
<input type=password name="{$attribute.identifier}_confirm" value="{$attribute.value}" id="{$attribute.identifier}_confirm" size="20"/>	

<script type="text/javascript">
	var id = '#{$attribute.identifier}';
{literal}
	jQuery(document).ready(function(){	
		$(id).pstrength();
	});
</script>
{/literal}