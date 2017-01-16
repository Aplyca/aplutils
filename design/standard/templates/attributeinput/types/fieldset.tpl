<div class="fieldsetbox">
		{foreach $fieldset as $fieldattibute}	
			{def $fixedFieldAttribute = hash('name',$fieldattibute.name, 
											 'identifier', concat($fieldset_identifier, '[',$count,']', '[', $fieldattibute.identifier, ']'),
											 'data',$fieldattibute.data,
											 'type',$fieldattibute.type,
											 'value', $fieldattibute.value,
											 'validation_error', $fieldattibute.validation_error,
											 'required',$fieldattibute.required )}		
	 		{include uri='design:attributeinput/attribute_view.tpl' attribute=$fixedFieldAttribute block_style='fieldset_block'}	 	
		{/foreach}		
		<div class="removefields">
			<a onclick="removeFields(this, '{$removeText}');return false;" href="#">Remove</a>
		</div>
</div>