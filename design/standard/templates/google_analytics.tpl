{def $tracker_id = ezini('TrackerSettings','TrackerID', 'googleanalytics.ini')
}
{if $tracker_id}
<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '{$tracker_id}']);
	_gaq.push(['_trackPageview']);
	{if ezini('TrackerSettings','ECommerce', 'googleanalytics.ini')|eq('true')}
		{include uri="design:ecommerce_analytics.tpl"}
	{/if}
	(function() {ldelim}
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	{rdelim})();
</script>
{/if}