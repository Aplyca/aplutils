{* Included in pagelayout *}
<br/>




<div class="block"> 
   <fieldset> 
   <legend> Static Cache </legend>  
		{* def $mirrorExecTime = 35  *}
		<button id="generatestatic">Generate Static Site</button> 		
		<table width="40%">
		  <tr>
		    <td width="90%">
			    <div id="pbarcont">
			    	<div id="loading"></div>
					<div id="progressbar"></div>
				</div>
		    </td>  		
		    <td width="10%">
		    	<div id="percentaje"></div>
		    </td>
		  </tr>
		</table>		
		<div id="loadMessage"></div>				
		<div id="changescontent"></div>

   </fieldset>
</div>

<script type="text/javascript">
var loadTime = {$mirrorExecTime};
var nodeID = {$node.node_id};
/*var loadTime = 10;*/
var flag = false;
{literal}
$(document).ready(function() {

	$("#pbarcont").hide();
	$("#progressbar" ).progressbar();
	$("#progressbar").hide();
    
  });


	$('#generatestatic').click(function() {		
		beginLoad();
		$.get('/deployment/dashboardactions',{action:'generatestaticpath',nodeID:nodeID},function(data){
			$( "#progressbar" ).progressbar( "option", "value", 100 );
        	$('#percentaje').html("100%");        	
			$("#progressbar").fadeOut();
			$('#percentaje').html("");
			$('#loading').html("");
			$( "#progressbar" ).progressbar( "option", "value", 0 );
			/*$("#loadMessage").html(data);*/  	
			flag = true;
	    });
	    
		//window.location = "/deployment/dashboardactions?action=generatestatic";
	});


	function beginLoad() {
		flag = false;
		$("#progressbar" ).progressbar();		  
		$("#pbarcont").show();
	    $("#progressbar").fadeIn();
	    $('#loading').html("Loading...");
		var secs = 0;
		var progressUnit =  parseInt(100/loadTime);
		var progress = 0;
	
	    var i = setInterval(function() { 	      	 
              	progress = progress + progressUnit;               
	            secs++;
	            if(secs >= loadTime || flag == true)
	            {	            		
	            	flag = false;            	            
	                clearInterval(i);	                
	                return;		            
	            }
	            $( "#progressbar" ).progressbar( "option", "value", progress );
	            $('#percentaje').html(progress + "%");	            
	       
	    }, 1000);
	}		
</script>
{/literal} 
