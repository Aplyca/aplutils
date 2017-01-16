/**
* jQuery PasswordMeter Plugin 1.0
*
* http://www.aplyca.com/
*
* Copyright (c) 2012 Aplyca SAS 
*/

(function($){
	$.fn.passwordmeter = function(options){
		var defaults = {
				callback: function(){return true;},
				length: [
				         {size: 6, points: 6},
				         {size: 0, points: 3},
				         {size: 8, points: 6},
				         {size: 16, points: 6}
				],
                event: 'keyup',
                levels: [
                    {level: 'veryweak', score: [0, 16], label: 'Very weak'},
                    {level: 'weak', score: [16, 25], label: 'Weak'},
                    {level: 'mediocre', score: [25, 35], label: 'Mediocre'},
                    {level: 'strong', score: [35, 45], label: 'Strong'},
                    {level: 'verystrong', score: [45, 60], label: 'Very strong'}
                ],
                submit: {level: 'strong', exclude: ['DiscardButton']},
                rules: [
                	{regex: '[a-z]', points: 1},
                	{regex: '[A-Z]', points: 5},
                	{regex: '\d+', points: 5},
                	{regex: '(.*[0-9].*[0-9].*[0-9])', points: 5},
                	{regex: '.[!,@,#,$,%,^,&,*,?,_,~]', points: 5},
                	{regex: '(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])', points: 5},
                	{regex: '([a-z].*[A-Z])|([A-Z].*[a-z])', points: 2},
                	{regex: '([a-zA-Z]).*([0-9])|([0-9]).*([a-zA-Z])', points: 2},   
                	{regex: '([a-zA-Z0-9].*[!,@,#,$,%,^,&,*,?,_,~])|([!,@,#,$,%,^,&,*,?,_,~].*[a-zA-Z0-9])', points: 2}              	
                ]
		};
		var options = $.extend(defaults, options);		
		return this.each(function(){
			var obj=$(this);
			var meter=function(event) {
				//obj.find(options.content).toggle(options.duration, options.easing, options.callback(obj));
                var result = $.fn.meter(obj.val(), options);                
                if (obj.parent().find(".passwordmeter").length < 1)
                {
                    $('<div class="passwordmeter"></div>').insertAfter(obj);  
                }
                var gauge = obj.parent().find(".passwordmeter");
                gauge.removeClass().addClass('passwordmeter '+result.level);                
                gauge.html('<div class="label">'+result.label+'</div>');  
			};
			obj.bind(options.event, meter);
			
			var form=obj.closest('form');
            var submitbutton = undefined;
            
            form.find(':submit').bind('click keyup',function(){
            	submitbutton = $(this);
            });
            
        	var stopsubmit={};           	
            for(var j in options.levels)	
        	{
        		if(options.submit.level == options.levels[j].level)
        		{
        			stopsubmit = options.levels[j];
        			break;
        		}
            }
            
			form.submit(function(){
				if ($.inArray(submitbutton.attr('name'), options.submit.exclude) <= -1)               
                {
	            	var check=$.fn.meter(obj.val(), options);
	            	if(check.score[0] < stopsubmit.score[0])
	            	{
	            		alert('Please write a strong password');
	            		return false;
	            	}
                }	
            });            
		});
	};
    $.fn.meter = function(passwd, options){
		var score=0;
		var verdict = {};
				
        for(var i in options.length)
        {
        	if(passwd.length >= options.length[i].size)
    		{
        		score = (score+options.length[i].points);
    		}
        }
		               
        for(var i in options.rules)
        {
    		if (passwd.match(options.rules[i].regex))
    		{
    			score = (score+options.rules[i].points);
    		}
        }		        

        for(var j in options.levels)	
    	{
    		if(score >= options.levels[j].score[0] && score < options.levels[j].score[1])
    		{
    			verdict = options.levels[j];
    			break;
    		}
        }
        
        return verdict;
    };    
})(jQuery);