{def $bookmarks=fetch( 'content', 'bookmarks' )
	$BtnStyle = 'podFavBtn'
}
{foreach $bookmarks as $bookmark}
	{if $node_id|eq($bookmark.node_id)}
		{set $BtnStyle = 'podFavBtnFav'}
	{/if}
{/foreach}
<div class="{$BtnStyle}" id="FavBtn_{$node_id}" title="{'Add the current item to your bookmarks.'|i18n( 'design/admin/pagelayout' )}"></div>
<p class="favorite">{'Favorite'|i18n('content/classifieds')}</a>