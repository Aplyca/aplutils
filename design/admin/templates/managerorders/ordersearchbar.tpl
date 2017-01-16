{* FORM SEARCH *}
<form id="form-search-orders" method="post" action={concat( '/managerorders/searchorders' )|ezurl}>	
	<div class="context-block">
		<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
			<h2 class="context-title">Search orders</h2>
			<div class="header-subline"></div>
		</div></div></div></div></div></div>
		<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">
			<div class="block">
				<fieldset>
					<legend>By Customer and/or Date</legend>
					<div class="block">
						<div class="button-left">
							<label style="display: inline; font-weight: normal;" >{'Customer Name'|i18n( 'design/admin/shop/orderlist' )}:
							<input type="text" title="" value="{$customer}" size="15" name="inputCustomer" id="inputCustomer">
							</label>
						</div>
						<div class="button-left">
							<label style="display: inline; font-weight: normal;" >{'Product Name'|i18n( 'design/admin/shop/orderlist' )}:
							<input type="text" title="" value="{$product}" size="15" name="inputProduct" id="inputProduct">
							</label>
						</div>
						<div class="button-left">
							<label style="display: inline; font-weight: normal;" >{'Date From'|i18n( 'design/admin/shop/orderlist' )} (mm/dd/yyyy):</label>
							<input type="text" id="datepicker_from" name="datepicker_from" value="{if $from_date|gt(0)}{$from_date|l10n( 'shortdate' )}{/if}">
						</div>
						<div class="button-left">
							<label style="display: inline; font-weight: normal;" >{'To'|i18n( 'design/admin/shop/orderlist' )} (mm/dd/yyyy):</label>
							<input type="text" id="datepicker_to" name="datepicker_to" value="{if $to_date|gt(0)}{$to_date|l10n( 'shortdate' )}{/if}">
						</div>
						<div class="button-left">
							<label style="display: inline; font-weight: normal;" >{'Status'|i18n( 'design/admin/shop/orderlist' )}:</label>
					       <select name="inputstatus">						       
					        <option value="0" {if or($status|eq(''), $status|eq('0'))}selected="selected"{/if}>Any</option>						        
					        {section var=Status loop=$status_list}						        						        
					            <option value="{$Status.item.status_id}" {if $status|eq($Status.item.status_id)}selected="selected"{/if}>{$Status.item.name|wash}</option>
					        {/section}
					        </select>
						</div>						
						<div class="button-left">
							<input type="submit" title="Search Orders" value="Search" name="SearchButton" class="button">
						</div>
					</div>
				</fieldset>
			</div>
		</div></div></div></div></div></div>
	</div>
</form>

{literal}
<script type="text/javascript">

	jQuery(document).ready(function(){

		$(function() {
			$( "#datepicker_from" ).datepicker({
				showOn: "both",
				buttonImage: "/extension/aplutils/design/standard/images/calendar.gif",
				buttonImageOnly: true
			});
			$( "#datepicker_to" ).datepicker({
				showOn: "both",
				buttonImage: "/extension/aplutils/design/standard/images/calendar.gif",
				buttonImageOnly: true
			});
		});

	});			
	
</script>
{/literal}