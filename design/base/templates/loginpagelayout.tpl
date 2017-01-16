<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
{cache-block}
    <head>      
        {include uri="design:head/headers.tpl"}
        {include uri="design:head/style.tpl"}
        {include uri="design:head/script.tpl"}
        {include uri="design:head/link.tpl"}
        {include uri="design:head/metrics.tpl"}
    </head>
    <body>
{/cache-block}      
        {$module_result.content} 
{cache-block}           
        {include uri="design:footer/script.tpl"}
{/cache-block}  
    </body>
</html>