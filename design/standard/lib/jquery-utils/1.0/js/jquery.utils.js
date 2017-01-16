(function($){
	$.fn.ajaxjson = function(options){
		var defaults = {
				type: 'GET',
				url: '/',
				params: {},
				callback:{
					success: function(){return true;},
					before: function(){return true;},
					error: function(){return false;},
					complete: function(){return true;}
				} 
		};
		options.callback = $.extend(defaults.callback, options.callback);
		var options = $.extend(defaults, options);			
		
		$.ajax({
				type: options.type,
				url: options.url,
				data: options.params,
			   beforeSend: function(msg){
			        if(jQuery.isFunction(options.callback.before))
			        {
			        	(options.callback.before)();
			        }
			    },
				success: function(msg){
					var res=jQuery.parseJSON(msg);
					if (jQuery.isFunction(options.callback.success))
					{
						(options.callback.success)(res);
					}		
				},
				error: function() {
			        if (jQuery.isFunction(options.callback.error))
			        {
			        	(options.callback.error)();
			        }
			  	},
			    complete: function() {
			        if (jQuery.isFunction(options.callback.complete))
			        {
			        	(options.callback.complete)();
			        }
			    }
		});
	};
})(jQuery);

(function($){
	$.fn.sendform = function(options){	
		
		var defaults = {
				url: $(this).attr("action"),
				type: $(this).attr("method"),
				onsubmit: {
					start: function (res){return true},
				    success: function (res){return true},
					error: function (res){return false},
					finish: function (res){return true}
				} 
		};
		
		options.onsubmit = $.extend(defaults.onsubmit, options.onsubmit);
		options.data = $.extend(defaults.data, options.data);
		var options = $.extend(defaults, options);	
		
		$.fn.ajaxjson({
			type: options.type,
			url: options.url,
			params: $(this).serialize()+'&'+$(this).find("input[type='submit']").attr("name")+'='+$(this).find("input[type='submit']").attr("name"),
			callback: {
				before: function (res){
					(options.onsubmit.start)(res);
				},
			    success: function (res){	
			    	(options.onsubmit.success)(res);
				},
				error: function (res){
					(options.onsubmit.error)(res);
			    },
			    complete: function (res){
					(options.onsubmit.finish)(res);
			    }
			} 
		});
	};
})(jQuery);

function reloadTab(event, tabs, url, data, type)
{   	
	event.preventDefault();
    var index=$(tabs).tabs( "option", "selected" );
    var aO = $(tabs).tabs( "option", "ajaxOptions" );
    if (data)
    {	
	    aO['data']=data;
    }
    if (type)
    {	    
	    aO['type']=type;
	}
    $(tabs).tabs( "option", "ajaxOptions", aO);
    if (url)
	{
        $(tabs).tabs( "url", index, url );
	}
    $(tabs).tabs( "load", index );
    $(tabs).tabs( "option", "ajaxOptions", {dataFilter: function(result){
									            return $.parseJSON(result).m;
									        },
									        error: function( xhr, status, index, anchor ) {
									            $( anchor.hash ).html($(tabs).children("#tabs_error").html());
									        }}
    );
}

function ajaxTabs(tabs)
{   
	$(tabs).tabs({
        ajaxOptions: {
        	dataFilter: function(result){
                return $.parseJSON(result).m;
        	},
        error: function( xhr, status, index, anchor ) {
                $( anchor.hash ).html($(tabs).children("#tabs_error").html());
        		}
        },
        cookie: {
            expires: 1
        },
        spinner: function() {
            var index=$(tabs).tabs( "option", "selected" );
            $(tabs+" .label-"+index).show();
        },
        cache: true
    });
}

function refreshTab(tabs, url, form)
{
	if ($(tabs).length > 0)
	{
	    var index=$(tabs).tabs( "option", "selected" );
	    var idPrefix=$(tabs).tabs( "option", "idPrefix" );
	    var tab_id = '#'+idPrefix+(index+1);
	    
	    $(tab_id+" .pagenavigator a[href]").each(function(){
	        $(this).click(function(event) {
	        	gform=getForm(form, url);
	            reloadTab(event, tabs, $(this).attr('href'), gform.data, gform.type);
	        });
	    });
	    
	    $(tab_id+" .refresh-click").each(function() {
	        $(this).click(function(event) {
	        	gform=getForm(form, url);
		        reloadTab(event, tabs, gform.url, gform.data, gform.type);
	        });
	    });
	    
	    $(tab_id+" "+form+" .refresh-change").each(function() {
	        $(this).change(function(event) {
	        	gform=getForm(form, url);
		        reloadTab(event, tabs, gform.url, gform.data, gform.type);
	        });
	    });
	    
		$(tab_id+" "+form+" input[type=submit]").click(function(event){
        	gform=getForm(form, url);
	        reloadTab(event, tabs, gform.url, gform.data, gform.type);
	    });
	}
}