/**
* jQuery Expand Plugin 1.0
*
* http://www.aplyca.com/
*
* Copyright (c) 2011 Aplyca SAS 
*/

(function($){
	$.fn.expand = function(options){
		var defaults = {
				callback: function(){return true;},
				easing: 'linear',
				duration: 0,
				content: '.content',
				trigger: '.trigger',
				label: '.label',
				event: 'click'
		}
		var options = $.extend(defaults, options);		
		return this.each(function(){
			var obj=$(this);
			var toggle=function(event) {
				event.preventDefault();
				obj.find(options.content).toggle(options.duration, options.easing, options.callback(obj));
				obj.find(options.label).toggle();
			};
			obj.find(options.trigger).bind(options.event, toggle);
		});
	};
})(jQuery);