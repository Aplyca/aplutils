<input name="{$nombre}" id="{$nombre}" class="{$tipo}"/> 

<script type="text/javascript"> 
var type = "{$tipo}"
{literal}
	$('.' + type).datePicker();

{/literal}
</script> 