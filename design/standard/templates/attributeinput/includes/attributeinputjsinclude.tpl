<script type="text/javascript">
{literal}	
	function removeFields(elm,selOption){
		elm.parentNode.parentNode.innerHTML = "";	
		$('select[name$="TradingPartnerName"] option').each(function() {
			if ($(this).text() == selOption){				
				//$(this).show();
				$(this).removeAttr("disabled");
			}			
		});
		return false;
	}
{/literal} 
</script>	
