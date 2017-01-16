{def    $sort_bytype_options =  ezini('SearchSettings', 'SortByTypeOptions', 'search.ini')
        $sort_bytype_default =  ezini('SearchSettings', 'SortByTypeDefault', 'search.ini')
        $sort_byorder_default =  ezini('SearchSettings', 'SortByOrderDefault', 'search.ini')                             
}
<div class="row">
    <div class="three columns">
		<label for="sort_by_type">{'Sort By'|i18n('aplutils/general')}</label>
    </div>
    <div class="six columns">		
		<select id="sort_by_type" name="SortByType" class="instant">
		{foreach $sort_bytype_options as $option_label => $option}
			<option value="{$option}" {if eq($option, $sort_bytype_default)}selected{/if}>{$option_label|i18n('aplutils/general')}</option>			
		{/foreach}
		</select>
	</div>	
    <div class="three columns">   		
		<label class="sort-order{if eq($sort_byorder_default, 'asc')} hide{/if} bullet triangle-white-s" for="sortbyorder-desc">{'Descending'|i18n('aplutils/general')}</label>
		<input type="radio" value="desc" name="SortByOrder" id="sortbyorder-desc" class="instant hide"/>
		<label class="sort-order{if eq($sort_byorder_default, 'desc')} hide{/if} bullet triangle-white-n" for="sortbyorder-asc">{'Ascending'|i18n('aplutils/general')}</label>					
		<input type="radio" value="asc" name="SortByOrder" id="sortbyorder-asc" class="instant hide"/>
	</div>
</div>
{literal}
<script type="text/javascript">	
	$(document).ready(function() {	
		var form = $('form[name="SearchForm"]');		
        form.find(".sort-order").on('click', function(event){
            $(this).parent().find(".sort-order").toggleClass('hide');
        });        		
	});
</script>
{/literal}