/**
* jQuery Accordion Plugin 1.0
*
* http://www.aplyca.com/
*
* Copyright (c) 2011 Aplyca SAS 
*/

(function($){
	$.fn.accordion = function(options){
		var defaults = {
			element: 'li',
			title: '.title',
			content: '.content',	
			active: '.active',
			effect: {
				easing: 'swing',
				duration: 300,
				event: 'click',
				callback: function(e){return true;}
			},
			bullet: {
				item: '.bullet',
				open: 'open'
			}
		};
		options.effect = $.extend(defaults.effect, options.effect);
		options.bullet = $.extend(defaults.bullet, options.bullet);
		var options = $.extend(defaults, options);	
		
		return this.each(function(){
			var obj = $(this);
			var element = $(this).find(options.element);

			element.not(options.active).find(options.content).hide();
			
			obj.find(options.active).find(options.bullet.item).addClass(options.bullet.open);
			
			element.find(options.content).click(function(e){
	        	e.stopPropagation();
	        });
			
 			var toggle=function(event) {
	        	obj.find(options.element).not($(this)).find(options.content).slideUp(options.effect.duration, options.effect.easing);
	        	$(this).find(options.content).slideToggle(options.effect.duration, options.effect.easing, options.effect.callback );
	        	$(this).find(options.bullet.item).toggleClass(options.bullet.open);
	        	obj.find(options.element).not($(this)).find(options.bullet.item).removeClass(options.bullet.open);
			};

			element.on(options.effect.event, toggle);
		});
	};
})(jQuery);