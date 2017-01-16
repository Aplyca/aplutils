<table class="list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} alt="{'Invert selection.'|i18n( 'design/admin/shop/orderlist' )}" title="{'Invert selection.'|i18n( 'design/admin/shop/orderlist' )}" onclick="ezjs_toggleCheckboxes( document.orderlist, 'OrderIDArray[]' ); return false;" /></th>
	<th class="wide">{'ID'|i18n( 'design/admin/shop/orderlist' )}</th>
	<th class="wide">{'Type'|i18n( 'design/admin/shop/orderlist' )}</th>
	<th class="wide">{'Customer'|i18n( 'design/admin/shop/orderlist' )}</th>
	<th class="tight">{'Total'|i18n( 'design/admin/shop/orderlist' )}</th>
	<th class="wide">{'Time'|i18n( 'design/admin/shop/orderlist' )}</th>
	<th class="wide">{'Exernal Order ID'|i18n( 'design/admin/shop/orderlist' )}</th>
	<th class="wide">{'Status'|i18n( 'design/admin/shop/orderlist' )}</th>
</tr>
{section var=Orders loop=$order_list sequence=array( bglight, bgdark )}

{set $currency = fetch( 'shop', 'currency', hash( 'code', $Orders.item.productcollection.currency_code ) )}
{if $currency}
    {set locale = $currency.locale
         symbol = $currency.symbol}
{else}
    {set locale = false()
         symbol = false()}
{/if}

<tr class="{$Orders.sequence}">
    <td><input type="checkbox" name="OrderIDArray[]" value="{$Orders.item.id}" title="{'Select order for removal.'|i18n( 'design/admin/shop/orderlist' )}" /></td>
		
	<td>
	<a href={concat( '/managerorders/orderview/', $Orders.item.id, '/' , $typec  )|ezurl}>
			{if $typec|eq('C')}
				{$Orders.item.created |datetime( 'custom', '%Y%m%d' )}-{$Orders.item.id}C
			{elseif $typec|eq('P')}
				{$Orders.item.created |datetime( 'custom', '%Y%m%d' )}-{$Orders.item.id}P
			{else}
				{$Orders.item.created |datetime( 'custom', '%Y%m%d' )}-{$Orders.item.id}
			{/if}
			
		</a>
	</td>
	
	<td>
		{$Orders.item.account_identifier}
	</td>
	
	<td>
	{if is_null($Orders.item.account_name)}
	    <s><i>{'( removed )'|i18n( 'design/admin/shop/orderlist' )}</i></s>
	{else}
	    <a href={concat( '/managerorders/customerorderview/', $Orders.item.user_id, '/', $Orders.item.account_email|wash )|ezurl}>{$Orders.item.user.contentobject.name|wash}</a>
	{/if}
	</td>
	
    {* NOTE: These two attribute calls are slow, they cause the system to generate lots of SQLs.
             The reason is that their values are not cached in the order tables *}
	<td class="number" align="right">{$Orders.item.total_inc_vat|l10n( 'currency', $locale, $symbol )}</td>

	<td>{$Orders.item.created|l10n( shortdatetime )}</td>
	<td>{$Orders.item.order_nr}</td>
	<td>
    {let order_status_list=$Orders.status_modification_list}

    {section show=$order_status_list|count|gt( 0 )}
        {set can_apply=true()}
        <select name="StatusList[{$Orders.item.id}]">
        {section var=Status loop=$order_status_list}
            <option value="{$Status.item.status_id}"
                {section show=eq( $Status.item.status_id, $Orders.item.status_id )}selected="selected"{/section}>
                {$Status.item.name|wash}</option>
        {/section}
        </select>
    {section-else}
        {* Lets just show the name if we don't have access to change the status *}
        {$Orders.status_name|wash}
    {/section}

    {/let}
	</td>
</tr>
{/section}
</table>