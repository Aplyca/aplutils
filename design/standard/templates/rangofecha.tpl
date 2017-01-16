<table border="0">
	<tr>
		<td>
			Inicio
			<div class="break"></div>
			{include uri="design:selectorFecha.tpl" tipo=$tipo_inicio nombre=$nombre_inicio }
		</td>
		<td>
			Final
			<div class="break"></div>
			{include uri="design:selectorFecha.tpl" tipo=$tipo_final nombre=$nombre_final} 			
		</td>
		<td>
			<button id='clear_range' class="boton"> Limpiar rango </button>
		</td>				
	</tr>
</table>

<script type="text/javascript"> 
{literal}


$(document).ready(function() {

	$('#boton_enviar').click(function() 
	{	
		var start_date_array = $('.inicio').val().split('/');
		var end_date_array = $('.final').val().split('/');
		var startDate = new Date(start_date_array[2],start_date_array[1]-1,start_date_array[0]);
		var endDate = new Date(end_date_array[2],end_date_array[1]-1,end_date_array[0]);
		var startTimestamp = startDate.getTime();		
		var endTimestamp= endDate.getTime();
		if(startTimestamp >= endTimestamp)
		{
			alert("Seleccione un rango válido");
			return false;
		}		
	}); 
	
	$('#clear_range').bind('click', function() {
		//e.preventDefault();
		$('.inicio').val('');
		$('.final').val('');	
		return false;
	});	
	
});
			
{/literal}
</script> 