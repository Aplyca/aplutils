<select id="usacities_select" class="location_select" name="location_city">
  	{foreach $cities as $city}    		    		
 		{if $city.city|eq($city.cityaliasname)}
 			<option value="{$city.cityaliasname}">{$city.city}</option>
 		{else}
 			<option value="{$city.city}-{$city.cityaliasname}">{$city.city}-{$city.cityaliasname}</option>
 		{/if}
  	{/foreach}
</select>