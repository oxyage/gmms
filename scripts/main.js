/* ################ служебные функции */
$(function(){ 
	
	GMMS = {
		func:{},
		config: { //конфигурационные параметры
		
		},
		journal: [], //журнал
		temp:{} //для временных значений
		
	},
	
	GMMS.rcu = {
		host: {}, //список по хостам
		list: {}, //список
		sfn: {
			host:{}, //список по зонам по хостам
			list:{}
		},
		select: [], //выбранные объекты
		deferred:{}, //deferred объекты для запросов
		auth:{}, //авторизованные хосты
		devices:{}, //устройства
		ajax: 0 //храним количество ajax запросов
	},
	
	/* ################# клик по документу */
	$(document).click(function(event){ 
	
		if($(event.target).parents("ul#menu").length === 0 && $(event.target).closest(".rts").length === 0) // если клик вне меню или на РТС
		{	
			$("ul#menu").hide();
			//$("ul#menu #srp").hide();
		}
	
	}),

	/* ################### после загрузки DOM */
	$(document).ready(function(){ 
		
		
		console.log("jquery dom ready");
		
	})
	




	
})


