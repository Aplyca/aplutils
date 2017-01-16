{def    $filters = fetch('instantcontent', 'getinputfilters', hash('type', 'get', 'data', $view_parameters)) 
        $query=false()
		$action_url='instantcontent/search'
        $method_name='post'                               
}

{if is_null($action)|not()}
    {set $action_url=$action}
{/if}
{if is_null($method)|not()}
    {set $method_name=$method}
{/if}

{if ezhttp_hasvariable('Search', 'get')
}    {set $query=ezhttp('Search', 'get')}    
{/if}

<form action={$action_url|ezurl()} method="{$method_name}" name="SearchForm">
	<div class="block">
		{include uri='design:instantcontent/parts/filter.tpl' filters=$filters}	
		<div class="row">
            <div class="three columns offset-by-nine">
                <input type="submit" value="{'Search'|i18n('aplutils/general')}" name="SearchButton" class="button" />
            </div>
        </div>
	</div>
	<div class="block sortby">
		{include uri='design:instantcontent/parts/sortby.tpl'}
	</div>
	<div class="dialog">
		{include uri='design:instantcontent/parts/dialog.tpl'}
	</div>
	<div class="results">
		{include 	uri='design:instantcontent/search.tpl' 
					page_uri=$action_url
					sort_array=array()
					query=$query
					root_nodes=fetch('instantcontent', 'getrootnodes', hash('type', 'get', 'data', $view_parameters)) 
					filters=$filters}										
	</div>		
</form>	

{literal}
<script type="text/javascript">	
	$(document).ready(function() {	
		var form = $('form[name="SearchForm"]');		
	    function showResults(results){
	    	if (results > 0)
	    	{
	    		form.find(".dialog .found").show();
	    	}
	        else
	        {
	        	form.find(".dialog .notfound").show();
	        }	
	        form.find(".number").html(results);       
	    }
	    showResults({/literal}{$total_search}{literal});
		var actions = {
            start: function (res){
                form.find(".results").fadeTo('fast', 0.5);
                form.find(".dialog .warning").hide();
                form.find(".dialog .searching").show();
            },
            success: function (res){
                form.find(".dialog .warning").hide();
                showResults(res.total_search);
                form.find(".results").html(res.m);
            },
            error: function (res){
                form.find(".dialog .warning").hide();
                form.find(".dialog .error").show();
                showResults(0);
            },
            finish: function (res){
                form.find(".results").fadeTo('fast', 1);
                $('html, body').animate({scrollTop: form.offset().top}, 500);
            }
        };
		form.formresults({
			instant:{
				keyup: {/literal}{ezini('SearchSettings', 'InstantWordLength', 'search.ini')}{literal}
			},
			onsubmit: actions
	    }); 
		
        form.find(".pagenavigator a[href]").live('click', function(event){
            event.preventDefault(); 
            form.sendform({
                url: $(this).attr("href"),
                onsubmit: actions
            });            
        });        		
	});
</script>
{/literal}


{ezscript_require(array('/lib/jquery-utils/1.0/js/jquery.utils.min.js', '/lib/jquery-formresults/1.0/js/jquery.formresults.js'))}