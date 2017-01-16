{if $access}	
	{def $currency = fetch( 'shop', 'currency', hash( 'code', $order.productcollection.currency_code ) )
	     $locale = false()
	     $symbol = false()}
	{if $currency}
	    {set locale = $currency.locale
	         symbol = $currency.symbol}
	{/if}
	{if $view_full}
		<h1>{'Order #%order_id [%order_status]'|i18n( 'design/admin/shop/orderview',,hash( '%order_id', $order.id, '%order_status', $order.status_name ) )}</h1>
	{else}
		<h1 class="title">{'Order #%order_id [%order_status]'|i18n( 'design/admin/shop/orderview',,hash( '%order_id', $order.id, '%order_status', $order.status_name ) )}</h1>
	{/if}
	<div class="order_content">			
		<div class="title">
			{'Product items'|i18n( 'design/admin/shop/orderview' )}
		</div>			
		<div class="order_body" >	
			<div class="order_header">
				<div class="cell product">{'Product'|i18n( 'design/admin/shop/orderview' )}</div>
				<div class="cell count">{'Count'|i18n( 'design/admin/shop/orderview' )}</div>
				<div class="cell price">{'Price'|i18n( 'design/admin/shop/orderview' )}</div>
			</div>
		{foreach $order.product_items as $product_item}
			<div class="user_order">
				<div class="cell product">{$product_item.item_object.name|wash}</div>
				<div class="cell count">{$product_item.item_count}</div>
				<div class="cell price">{$product_item.total_price_inc_vat|l10n( 'currency', $locale, $symbol )}</div>
			</div>		
		{/foreach}
		</div>	
	</div>			
	<div class="order_content">
		<div class="title">
			{'Status History'|i18n( 'design/admin/shop/orderview' )}
		</div>
		<div class="order_body" >	
			<div class="order_header">
				<div class="cell date">{'Date'|i18n( 'design/admin/shop/orderview' )}</div>
				<div class="cell status">{'Status'|i18n( 'design/admin/shop/orderview' )}</div>
				<div class="cell person">{'Person'|i18n( 'design/admin/shop/orderview' )}</div>
			</div>
		{def 	$order_status_history = fetch( shop, order_status_history, hash( 'order_id', $order.order_nr ) )}
		{foreach $order_status_history as $history}
			<div class="user_order">
		    	<div class="cell date">{$history.modified|l10n( shortdatetime )}</div>
				<div class="cell status">{$history.status_name|wash}</div>									    
			    <div class="cell person">{$history.modifier.name|wash}</div>		
			</div>		
		{/foreach}
		</div>	
	</div>
	{if $view_full}
		<div class="button_block">
			<a class="button_a_go_to" href={"myprofile/dashboard"|ezurl}>{"Go to My account"|i18n("design/standard/error/kernel")}</a>
		</div>
	{/if}
{else}
	<div class="content_general">
		<div class="warning">
			<h2>{"Access denied"|i18n("design/standard/error/kernel")}</h2>
			<ul>
				<li>{"Your current user does not have the proper privileges to access this page."|i18n("design/standard/error/kernel")}</li>
			</ul>	
		</div>
		<div class="button_block">
			<a class="button_a_go_to" href={"myprofile/dashboard"|ezurl}>Go to My account</a>
		</div>
	</div>	
{/if}