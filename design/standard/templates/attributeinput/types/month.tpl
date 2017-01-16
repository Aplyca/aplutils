{*
<apldoc>
Sets month text format as default
</apldoc>
*}

<label {if $attribute.validation_error} class="message-error"{/if}>
	{$attribute.name} {if $attribute.required}<span class="required_field">*</span>{/if}
</label>

{if $month_format|eq('number')}
	<select id="{$attribute.identifier}" class="month_number" name="{$attribute.identifier}">
		<option value=01>01</option>
		<option value=02>02</option>
		<option value=03>03</option>
		<option value=04>04</option>
		<option value=05>05</option>
		<option value=06>06</option>
		<option value=07>07</option>
		<option value=08>08</option>
		<option value=09>09</option>
		<option value=10>10</option>
		<option value=11>11</option>
		<option value=12>12</option>
	</select>
{else}
	<select class="month_string" name="{$attribute.identifier}">
		<option value=01>{"January"|i18n('design/standard/view')}</option>
		<option value=02>{"February"|i18n('design/standard/view')}</option>
		<option value=03>{"March"|i18n('design/standard/view')}</option>
		<option value=04>{"April"|i18n('design/standard/view')}</option>
		<option value=05>{"May"|i18n('design/standard/view')}</option>
		<option value=06>{"June"|i18n('design/standard/view')}</option>
		<option value=07>{"July"|i18n('design/standard/view')}</option>
		<option value=08>{"August"|i18n('design/standard/view')}</option>
		<option value=09>{"September"|i18n('design/standard/view')}</option>
		<option value=10>{"October"|i18n('design/standard/view')}</option>
		<option value=11>{"November"|i18n('design/standard/view')}</option>
		<option value=12>{"December"|i18n('design/standard/view')}</option>
	</select>
{/if}