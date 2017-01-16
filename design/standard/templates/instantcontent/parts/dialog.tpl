{def    $number_results='<span class="number"></span>'                               
}
<span class="warning found hide">{"We've found %total results for you!"|i18n('aplutils/general', '', hash('%total', $number_results))}</span>
<span class="warning notfound hide">{"No results found for your search!"|i18n('aplutils/general', '', hash('%total', $number_results))}</span>	
<span class="warning searching hide"><img src={'spinner.gif'|ezimage()}>{"Searching"|i18n('aplutils/general')} ...</span>
<span class="warning error hide">{"Error"|i18n("aplutils/general")}: {"Could not connect to the server. We will fix this as soon as possible. Please try again later."|i18n("enterate/general")}</span>