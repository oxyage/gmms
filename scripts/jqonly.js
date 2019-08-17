$( function() {/* предустановленные переменные*/
	
	GMMS.journal = [], //журнал
	GMMS.log = function(message){
		let date = new Date($.now());
		date = date.toTimeString().split(" ");
		$( "#panel-log div" ).prepend(date[0]+" "+message+"<br>");
		GMMS.journal.push(date[0]+" "+message);
	}
	
}),
$( function() { /* события на странице*/


	/* загружаем объекты связи */
	
	LoadingState = new $.Deferred(); // ждем разрешения вывести на страницу
	
	$( "#dialog-wait" ).dialog();//открываем окно
	$.get("api.php?route=db/select/rcu")
	.done(function(d){
		//код ошибки = 0
		if(!d.error)
		{
			$( "#dialog-wait p" ).html("Загружено объектов связи: "+d.response.length); //в диалоговое окно
			$( "#dialog-wait" ).dialog("close");//закрываем диалог окно
			LoadingState.resolveWith(d.response);//разрешаем вывести на страницу
			GMMS.log("Объекты связи успешно загружены ("+d.response.length+")"); //логгируем	
			console.log("Загруженные объекты связи",d);
		}
		else//если есть ошибка в ответе
		{
			$( "#dialog-wait p" ).html("Запрос объектов связи завершился с ошибкой (код "+d.error+")<br><i>"+d.response.message+"</i>");
			GMMS.log("Ошибка загрузки объектов связи. Смотри лог"); //логгируем	
			LoadingState.reject();
			console.error("Ошибка в ответе от сервера",d);
		}
	})
	.fail(function(e){//если не удался запрос к файлу
		$( "#dialog-wait p" ).html("Загрузка объектов связи не удалась<br>Смотри лог");
		GMMS.log("Ошибка загрузки объектов связи. Смотри лог"); //логгируем	
		LoadingState.reject();
		console.error("API не доступно",e);
	});
	
	
		/* вывод на страницу */
		LoadingState
		.done(function(){
			console.log("Начинаем вывод на страницу", this);
				
			for(let RTS of this)
			{
				//console.log(RTS);
				$("<div/>", {
					"class": "rts", 
					"html": "<div title='"+RTS.name+"' class='tower'></div><div class='name'>"+RTS.name+"</div>",
					"data-name": RTS.name,
					"data-host": RTS.host,
					"data-sfn": RTS.sfn
				})
				.css({
					"top": (100-RTS.coord.y)+"%",
					"left": RTS.coord.x+"%"/*,
					"font-weight": "bold",
					"min-width": "150px",
					"max-height": "25px",
					"max-width": "150px",
					"white-space": "nowrap",
					"z-index": 1,
					"position": "absolute"*/
				})
				.appendTo("#container-map");
			}				

			
			
			//$("div", {class:"rts" }).appendTo("#wrapper-map div");
			
			
		})
		.fail(function(){
			console.log("Вывода не будет");
		});
	
	/* end загрузка объектов связи */
	
	
	
	$("button.gmms").click(function(){
		console.log(GMMS);
	});
	

	console.log("jquery first");
	
	//$( "#dialog-wait" ).dialog({});
	
	
	
	/* диалоговое окно */
	
	//$( "#dialog" ).dialog("close");

	/*выбор объектов связи */
	$( "#panel-select" ).accordion({
	  collapsible: true,
	  active: false,
	  heightStyle: "content",
		icons:{
			 header: "ui-icon-radio-on",
		activeHeader: "ui-icon-radio-off"
		}
	});
	
	/* кнопки выбор РТС */
	$( "#panel-select input" ).checkboxradio({
	  icon: false
	});
	
	/* действия кнопок выбор одночастотной зоны или всех*/
	$('#panel-select legend input').click(function(){
		
		let target = $(this).attr("name");
		console.log(target, $(this).is(':checked'));
		
		if ($(this).is(':checked')){
			$("#field-"+target+" div").hide();
		} else {
			$("#field-"+target+" div").show();
		}
	
		
	});
	
		
	
	
	
	/* вкладки */
	$( "#panel-tabs" ).tabs({
		collapsible: true,
		
		//допустить false
		active:  (typeof $.cookie("panel-tabs") !== "undefined" && $.cookie("panel-tabs")) || 2, //активная закладка
		activate: function() {
			
			let tab = $( "#panel-tabs" ).tabs("option", "active");
			$.cookie("panel-tabs", tab);
			
			console.log("tab #",tab);
			
			}
	});
	
	/* журнал */
	$( "#panel-log" ).accordion({
	  collapsible: true,
	  //active: false,
	  heightStyle: "content",
	  icons: {
		  header: "ui-icon-note",
		activeHeader: "ui-icon-note"
		  }
	});
	
	/* диалоговое окно */
	$( "#dialog" ).dialog({});
	$( "#dialog" ).dialog("close");
	
	/* диалоговое окно внутри карты */
	$( "#dialog-map" ).dialog({
		 position: { my: "center", at: "center", of: "#wrapper-map"}
	
	});
	$( "#dialog-map" ).dialog("close");
	
		
	/* меню объекта связи*/
	$( "#menu" ).menu({
		position: { my: "left top", at: "right-5 top+5" }
	});
	
	/* прогресс бар	*/
	$( "#progressbar" ).progressbar({
		value: false
		});

	
	
	
	


}),
$(function(){ /*  стили */
	
}),
$(document).ready(function(){
	
	console.log("jquery dom ready");
	
});