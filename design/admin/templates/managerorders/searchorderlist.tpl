{ezcss_require( '/stylesheets/jquery/jquery-ui-1.8.11.custom.css')}
{ezscript_require( '/javascript/jquery/jquery-1.5.1.min.js')}
{ezscript_require( '/javascript/jquery/jquery-ui-1.8.11.custom.js')}

{let can_apply=false()}
{def $view_url = '/managerorders/searchorders'}
<div class="context-block">

	{* FORM SEARCH *}
	{include uri='design:managerorders/ordersearchbar.tpl'}	
	
	<form name="orderlist" method="post" action={concat( '/managerorders/searchorders' )|ezurl}>

		{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

		<h1 class="context-title">Orders [{$order_list_count}]</h1>

		{* DESIGN: Mainline *}<div class="header-mainline"></div>

		{* DESIGN: Header END *}</div></div></div></div></div></div>

		{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">
				
		<div class="block">
		</div>

		{section show=$order_list}
			{def $currency = false()
				 $locale = false()
				 $symbol = false()}
			
			{include uri='design:managerorders/orderlisttable.tpl'}	
			
			{undef $currency $locale $symbol}	
		{/section}

		<div class="context-toolbar">
			{include name=navigator
					 uri='design:navigator/google.tpl'
					 page_uri=concat('/managerorders/searchorders/',$customer,'/',$product,'/',$status,'/',$type,'/',$from_date,'/',$to_date)
					 item_count=$order_list_count
					 view_parameters=$view_parameters
					 item_limit=$limit} 
		</div>
		{* DESIGN: Content END *}</div></div></div>

		<div class="controlbar">
		{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

		<div class="block">
			<div class="button-right">
				{section show=and( $order_list|count|gt( 0 ), $can_apply )}
				<input class="button" type="submit" name="SaveOrderStatusButton" value="{'Apply changes'|i18n( 'design/admin/shop/orderlist' )}" title="{'Click this button to store changes if you have modified any of the fields above.'|i18n( 'design/admin/shop/orderlist' )}" />
				{section-else}
				<input class="button-disabled" type="submit" name="SaveOrderStatusButton" value="{'Apply changes'|i18n( 'design/admin/shop/orderlist' )}" disabled="disabled" />
				{/section}
			</div>
			<div class="break"></div>
		</div>

		{* DESIGN: Control bar END *}</div></div></div></div></div></div>
		</div>

	</form>

</div>
{/let}
