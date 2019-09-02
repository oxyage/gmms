/* ################ служебные функции */
$(function(){ 
	
	GMMS = {},
	
	/* ################# клик по документу */
	$(document).click(function(event){ 
	
		if($(event.target).parents("ul#menu").length === 0 && $(event.target).closest(".rts").length === 0) // если клик вне меню или на РТС
		{	
			$("ul#menu").hide();
		}
	
	}),

	/* ################### после загрузки DOM */
	$(document).ready(function(){ 
		
		
		console.log("jquery dom ready");
		
	})
	




	
})


