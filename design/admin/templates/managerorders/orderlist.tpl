{ezcss_require( '/stylesheets/jquery/jquery-ui-1.8.11.custom.css')}
{ezscript_require( '/javascript/jquery/jquery-1.5.1.min.js')}
{ezscript_require( '/javascript/jquery/jquery-ui-1.8.11.custom.js')}

{let can_apply=false()}
{def $view_url = '/managerorders/vieworders'}
{if module_params().function_name|eq('viewcreditorders')}
	{set $view_url = '/managerorders/viewcreditorders'}
{elseif module_params().function_name|eq('vieworderswithcredit')}
	{set $view_url = '/managerorders/vieworderswithcredit'}
{/if}
<div class="context-block">

{* FORM SEARCH *}
{include uri='design:managerorders/ordersearchbar.tpl'}	
	
{if module_params().function_name|eq('vieworders')}
<form name="orderlist" method="post" action={concat( '/managerorders/vieworders' )|ezurl}>
{elseif module_params().function_name|eq('vieworderswithcredit')}
<form name="orderlist" method="post" action={concat( '/managerorders/vieworderswithcredit' )|ezurl}>
{else}
<form name="orderlist" method="post" action={concat( '/managerorders/viewcreditorders' )|ezurl}>
{/if}
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">Orders [{$order_list_count}]</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{section show=$order_list}
<div class="context-toolbar">
<div class="block">
<div class="left">
<p>
{if module_params().function_name|eq('vieworders')}
	{section show=eq( ezpreference( 'admin_orderlist_sortfield' ), 'user_name' )}
	    <a href={'/user/preferences/set/admin_orderlist_sortfield/time/managerorders/vieworders/orderlist/'|ezurl}>{'Time'|i18n( 'design/admin/shop/orderlist' )}</a>
	    <span class="current">{'Customer'|i18n( 'design/admin/shop/orderlist' )}</span>
	{section-else}
	    <span class="current">{'Time'|i18n( 'design/admin/shop/orderlist' )}</span>
	    <a href={'/user/preferences/set/admin_orderlist_sortfield/user_name/managerorders/vieworders/orderlist/'|ezurl}>{'Customer'|i18n( 'design/admin/shop/orderlist' )}</a>
	{/section}
{elseif module_params().function_name|eq('vieworderswithcredit')}
	{section show=eq( ezpreference( 'admin_orderlist_sortfield' ), 'user_name' )}
	    <a href={'/user/preferences/set/admin_orderlist_sortfield/time/managerorders/vieworderswithcredit/orderlist/'|ezurl}>{'Time'|i18n( 'design/admin/shop/orderlist' )}</a>
	    <span class="current">{'Customer'|i18n( 'design/admin/shop/orderlist' )}</span>
	{section-else}
	    <span class="current">{'Time'|i18n( 'design/admin/shop/orderlist' )}</span>
	    <a href={'/user/preferences/set/admin_orderlist_sortfield/user_name/managerorders/vieworderswithcredit/orderlist/'|ezurl}>{'Customer'|i18n( 'design/admin/shop/orderlist' )}</a>
	{/section}
{else}
	{section show=eq( ezpreference( 'admin_orderlist_sortfield' ), 'user_name' )}
	    <a href={'/user/preferences/set/admin_orderlist_sortfield/time/managerorders/viewcreditorders/orderlist/'|ezurl}>{'Time'|i18n( 'design/admin/shop/orderlist' )}</a>
	    <span class="current">{'Customer'|i18n( 'design/admin/shop/orderlist' )}</span>
	{section-else}
	    <span class="current">{'Time'|i18n( 'design/admin/shop/orderlist' )}</span>
	    <a href={'/user/preferences/set/admin_orderlist_sortfield/user_name/managerorders/viewcreditorders/orderlist/'|ezurl}>{'Customer'|i18n( 'design/admin/shop/orderlist' )}</a>
	{/section}
{/if}
</p>
</div>
<div class="right">
<p>
{if module_params().function_name|eq('vieworders')}
	{section show=eq( ezpreference( 'admin_orderlist_sortorder' ), 'desc' )}
	    <a href={'/user/preferences/set/admin_orderlist_sortorder/asc/managerorders/vieworders/orderlist/'|ezurl}>{'Ascending'|i18n( 'design/admin/shop/orderlist' )}</a>
	    <span class="current">{'Descending'|i18n( 'design/admin/shop/orderlist' )}</span>
	{section-else}
	    <span class="current">{'Ascending'|i18n( 'design/admin/shop/orderlist' )}</span>
	    <a href={'/user/preferences/set/admin_orderlist_sortorder/desc/managerorders/vieworders/orderlist/'|ezurl}>{'Descending'|i18n( 'design/admin/shop/orderlist' )}</a>
	{/section}
{elseif module_params().function_name|eq('vieworderswithcredit')}
	{section show=eq( ezpreference( 'admin_orderlist_sortorder' ), 'desc' )}
	    <a href={'/user/preferences/set/admin_orderlist_sortorder/asc/managerorders/vieworderswithcredit/orderlist/'|ezurl}>{'Ascending'|i18n( 'design/admin/shop/orderlist' )}</a>
	    <span class="current">{'Descending'|i18n( 'design/admin/shop/orderlist' )}</span>
	{section-else}
	    <span class="current">{'Ascending'|i18n( 'design/admin/shop/orderlist' )}</span>
	    <a href={'/user/preferences/set/admin_orderlist_sortorder/desc/managerorders/vieworderswithcredit/orderlist/'|ezurl}>{'Descending'|i18n( 'design/admin/shop/orderlist' )}</a>
	{/section}
{else}
	{section show=eq( ezpreference( 'admin_orderlist_sortorder' ), 'desc' )}
	    <a href={'/user/preferences/set/admin_orderlist_sortorder/asc/managerorders/viewcreditorders/orderlist/'|ezurl}>{'Ascending'|i18n( 'design/admin/shop/orderlist' )}</a>
	    <span class="current">{'Descending'|i18n( 'design/admin/shop/orderlist' )}</span>
	{section-else}
	    <span class="current">{'Ascending'|i18n( 'design/admin/shop/orderlist' )}</span>
	    <a href={'/user/preferences/set/admin_orderlist_sortorder/desc/managerorders/viewcreditorders/orderlist/'|ezurl}>{'Descending'|i18n( 'design/admin/shop/orderlist' )}</a>
	{/section}
{/if}

</p>
</div>

<div class="break"></div>

</div>
</div>

{def $currency = false()
     $locale = false()
     $symbol = false()}
	
	{include uri='design:managerorders/orderlisttable.tpl'}	
	
{undef $currency $locale $symbol}
{section-else}
<div class="block">
<p>{'The order list is empty.'|i18n( 'design/admin/shop/orderlist' )}</p>
</div>
{/section}

<div class="context-toolbar">

{if module_params().function_name|eq('vieworders')}

{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/managerorders/vieworders'
         item_count=$order_list_count
         view_parameters=$view_parameters
         item_limit=$limit} 

{elseif module_params().function_name|eq('vieworderswithcredit')}

{include name=navigator
         uri='design:navigator/order.tpl'
         page_uri=concat('/managerorders/vieworderswithcredit')
         item_count=$order_list_count
         view_parameters=$view_parameters
         item_limit=$limit}

{else}

{include name=navigator
         uri='design:navigator/order.tpl'
         page_uri=concat('/managerorders/viewcreditorders')
         item_count=$order_list_count
         view_parameters=$view_parameters
         item_limit=$limit} 
{/if}
</div>
{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

<div class="block">
{*<div class="button-left">
{section show=$order_list}
    <input class="button" type="submit" name="ArchiveButton" value="{'Archive selected'|i18n( 'design/admin/shop/orderlist' )}" title="{'Archive selected orders.'|i18n( 'design/admin/shop/orderlist' )}" />
{section-else}
    <input class="button-disabled" type="submit" name="ArchiveButton" value="{'Archive selected'|i18n( 'design/admin/shop/orderlist' )}" disabled="disabled" />
{/section}
</div>*}
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

</div>


</form>
{/let}
