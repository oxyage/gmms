$( function() {

	GMMS.journal = [], //журнал
	GMMS.log = function(message){
		let date = new Date($.now());
		date = date.toTimeString().split(" ");
		$( "#panel-log div" ).prepend(date[0]+" "+message+"<br>");
		GMMS.journal.push(date[0]+" "+message);
	};

	console.log("jquery first");
	
	/* загружаем объекты связи */
	
	$( "#dialog-wait" ).dialog();//открываем окно
	$.get("api.php?route=db/select/rcu")
	.done(function(d){
		
		/*
		
		добавить запись в журнал
		
		*/
		
		//код ошибки = 0
		if(!d.error)
		{
			$( "#dialog-wait p" ).html("Загружено объектов связи: "+d.response.length); //в диалоговое окно
			$( "#dialog-wait" ).dialog("close");//закрываем диалог окно
			GMMS.log("Объекты связи успешно загружены ("+d.response.length+")"); //логгируем	
			console.log(d);
		}
		else//если есть ошибка в ответе
		{
			$( "#dialog-wait p" ).html("Запрос объектов связи завершился с ошибкой (код "+d.error+")<br><i>"+d.response.message+"</i>");
			console.error(d);
		}
	
	})
	.fail(function(e){//если не удался запрос к файлу
		$( "#dialog-wait p" ).html("Загрузка объектов связи не удалась<br>Смотри лог");
		console.error(e);
	});
	
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
$(function(){
	
}),
$(document).ready(function(){
	
	console.log("jquery dom ready");
	
});