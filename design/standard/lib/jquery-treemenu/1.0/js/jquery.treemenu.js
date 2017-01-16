/**
* jQuery Treemenu Plugin 1.0
*
* http://www.aplyca.com/
*
* Copyright (c) 2011 Aplyca SAS 
*/

(function($){
	$.fn.treemenu = function(options){
		var defaults = {
				deploy: 'false',
				depth: 1,
				easing: 'linear',
				duration: 0,
				event: 'click',
				callback: function(){return true;},
				bullet:{
					children: 'children',
					open: 'open',
					pointer: 'pointer'
				} 
		};
	
		options.bullet = $.extend(defaults.bullet, options.bullet);
		var options = $.extend(defaults, options);	
		
		return this.each(function(){
			var obj=$(this);
			
			obj.find("li a").click(function(e){
	        	e.stopPropagation();
	        });	
	        obj.find("li ul").click(function(e){
	        	e.stopPropagation();
	        });
	        
	        var root_depth=obj.parents().length;
	        obj.find("li").each(function(){	        	
	        	var current_depth=$(this).parents().length;	        	
	        	if($(this).children("ul").length > 0)
	        	{	
	        		if (options.deploy == 'true' && (((options.depth-1)*2) > (current_depth - root_depth)))
	        		{	        			
	        			$(this).addClass(options.bullet.children);
		        		$(this).addClass(options.bullet.open);
	        		}
	        		else
	        		{	
		        		if (((options.depth-1)*2) > (current_depth - root_depth))
		        		{	
		        			$(this).addClass(options.bullet.children);
		        		}
		        		else
		        		{
			        		$(this).removeClass(options.bullet.children);
			        		$(this).removeClass(options.bullet.open);
		        		}
	        		}
	        		
        		}
	        	
	        	if ($(this).hasClass(options.bullet.children))
	        	{
	        		$(this).addClass(options.bullet.pointer);
	        	}	
	        	
	        });

			obj.find("ul").each(function(){
				var current_depth=$(this).parent().parents().length;
				if (options.deploy == 'true')
				{
					if (((options.depth-1)*2) > (current_depth - root_depth))
					{
						$(this).show();
					}						
				}
				else
				{
					if ($(this).parent().hasClass(options.bullet.open))
					{
						$(this).show();
					}
				}
	        });
				        
			var toggle=function(event) {
				$(this).toggleClass(options.bullet.open);
	        	$(this).children("ul").slideToggle(options.duration, options.easing, options.callback );
			};
			obj.find("."+options.bullet.children).on(options.event, toggle);
		});
	};
})(jQuery);