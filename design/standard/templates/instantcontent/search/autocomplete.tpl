{literal}
<script type="text/javascript">	
$(function(){
	{/literal}{if eq(ezini('AutoCompleteSettings', 'AutoComplete', 'ezfind.ini'), 'enabled')}{literal}
    $("#search_form input[name=Search]").autocomplete({
        source: function( request, response ) {
            var limit = {/literal}{ezini('AutoCompleteSettings', 'Limit', 'ezfind.ini')}{literal}
			$.ajax({
                url: "{/literal}{'ezjscore/call'|ezurl(no)}{/literal}"+"/ezfind::autocomplete::"+request.term+"::"+limit,
                dataType: "json",
                data: {
                    ContentType: "json"
                },
                success: function( data ) {
                    response( $.map( data.content, function( item ) {
                        return {
                            label: item[0]+" ("+item[1]+")",
                            value: item[0]
                        }
                    }));
                }
            });
        },
        delay: {/literal}{ezini('AutoCompleteSettings', 'Delay', 'ezfind.ini')}{literal},
        minLength: {/literal}{ezini('AutoCompleteSettings', 'MinQueryLength', 'ezfind.ini')}{literal}
    });
	{/literal}{/if}{literal}
    $("#search_form input[name=Search]").focus();								
});				
</script>
{/literal}	