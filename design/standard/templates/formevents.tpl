{literal}
	<script type="text/javascript">
	
	starteZForm();
	
	$("input[name^='ContentObjectAttribute']").blur(function() {
		validate(this);
	}); 
	
	$("input[name^='ContentObjectAttribute']").focus(function() {
		fieldEvents(this);
	}); 
	
	</script>
{/literal}