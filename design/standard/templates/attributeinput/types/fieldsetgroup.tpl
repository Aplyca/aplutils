<script type="text/javascript">
	var {$attribute.identifier}Count = {$attribute.value|count()};
</script>


{foreach $attribute.value as  $key => $fieldset}
	{include uri="design:attributeinput/types/fieldset.tpl" fieldset=$fieldset block_style=$block_style fieldset_identifier=$attribute.identifier count=$key}
{/foreach}

<div id="fieldsetcontainer_{$attribute.identifier}"></div>


<div class="addfields" id="addfieldsbutton_{$attribute.identifier}">
	<a href="#" onclick="javascript:return false;">Add</a>
</div>

<script type="text/javascript">
{literal}
	$('#addfieldsbutton_{/literal}{$attribute.identifier}{literal} a').click(function(){
		$.get("/aplinput/getfieldset",
			{  iniName:'{/literal}{$iniName}{literal}', 
			   blockName:'{/literal}{$blockName}{literal}',
			   count:{/literal}{$attribute.identifier}Count{literal},  
			   groupName:'{/literal}{$groupName}{literal}'
			},
			function(data){
				$('#fieldsetcontainer_{/literal}{$attribute.identifier}{literal}').append(data);	
				{/literal} {$attribute.identifier}Count++;  {literal} 		
			});	
	});
{/literal} 
</script>