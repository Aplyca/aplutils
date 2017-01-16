{if $type|eq('usastates')}
<div class="block"> 
<span><strong>Choose State / Province </strong></span>
	<select id="usastates_select" class="location_select" name="location_state" onchange="handlestateselect();">
    	<option value="">Select State</option>
    	{foreach $data as $state}
    		<option value="{$state.code}">{$state.name}</option>
    	{/foreach}

     </select>
     
</div>          
{/if}