/**
* jQuery FormResults Plugin 1.0
*
* http://www.aplyca.com/
*
* Copyright (c) 2011 Aplyca SAS 
*/

(function($){
	$.fn.formresults = function(options){
		var defaults = {
				instant: {
					keyup: 0
				},
				onsubmit: {
					start: function (res){return true},
				    success: function (res){return true},
					error: function (res){return false},
					finish: function (res){return true}
				} 
		};
	
		options.onsubmit = $.extend(defaults.onsubmit, options.onsubmit);
		options.instant = $.extend(defaults.instant, options.instant);
		var options = $.extend(defaults, options);	
		
		return this.each(function(){

			var form = $(this);		

			form.find("select.instant").on('change', function(event){
				event.preventDefault();	
				form.sendform(options);
			});

			form.find("input[type='checkbox'].instant").on('change', function(event){
				event.preventDefault();	
				form.sendform(options);
			});
			
			form.find("input[type='radio'].instant").on('change', function(event){
				event.preventDefault();	
				form.sendform(options);
			});
			
			form.find("input.instant").on('keyup', function(event){
				if (($(this).val().length >= options.instant.keyup) || ($(this).val().length == 0))
				{
					event.preventDefault();					
					form.sendform(options);
				}
			});

			form.on('submit', function(event){
				event.preventDefault();	
				form.sendform(options);
			});
		    
		});
	};
})(jQuery);