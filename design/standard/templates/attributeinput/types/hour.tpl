<label {if $attribute.validation_error} class="message-error"{/if}>
	{$attribute.name} {if $attribute.required}<span class="required_field">*</span>{/if}
</label>

{def $temphourpart = ''
     $hours = ''
     $mins  = ''}

{if $attribute.value.hours}
	{set $hours = $attribute.value.hours}
{else}
	{set $hours = '01'}
{/if}

{if $attribute.value.mins}
	{set $mins = $attribute.value.mins}
{else}
	{set $mins = '00'}
{/if}

<select class="aplinputstyle" name="{$attribute.identifier}[hours]" id="aplinput_hours"> 
	{for 1 to 12 as $i}
		{if le($i,9)}
			{set $temphourpart = concat('0', $i)}
		{else}
			{set $temphourpart = $i}
		{/if}
		
		{if eq($temphourpart,$hours)}
			<option selected="selected">{$temphourpart}</option>
		{else}
			<option>{$temphourpart}</option> 
		{/if}	
	{/for}
</select>

<select class="aplinputstyle" name="{$attribute.identifier}[mins]" id="aplinput_mins"> 
	{for 0 to 59 as $i}
		{if le($i,9)}
			{set $temphourpart = concat('0', $i)}
		{else}
			{set $temphourpart = $i}
		{/if}
		
		{if eq($temphourpart,$mins)}
			<option selected="selected">{$temphourpart}</option>
		{else}
			<option>{$temphourpart}</option> 
		{/if}	
	{/for}
</select> 



