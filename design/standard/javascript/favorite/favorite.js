$( document ).ready( function() {
	$("div[class^='podFavBtn']").click(function(){
		item_id = $(this).attr("id").split('_')[1];
		if ($(this).attr("class")=="podFavBtnFav"){
			$(this).removeClass().addClass("podFavBtn");
			$.post('/favorites/removeFavorite',{itemID:item_id},function(data){});
		}
		else{
			$(this).removeClass().addClass("podFavBtnFav");
			$.post('/favorites/addfavorite',{itemID:item_id},function(data){});
		}
	});	
});