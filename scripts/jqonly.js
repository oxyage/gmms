$( function() {

	console.log("jquery first");
	

	/*выбор объектов связи*/
	$( "#panel-select" ).accordion({
	  collapsible: true,
	  heightStyle: "content"
	});
	
	/* выбор РТС */
	$( "#panel-select input" ).checkboxradio({
	  icon: false
	});
	
	/* вкладки */
	$( "#panel-tabs" ).tabs({
		collapsible: true,
		
		//допустить false
		active:  (typeof $.cookie("panel-tabs") === "undefined") || 0, //исправить здесь
		activate: function(event, ui) {
			
			let tab = $( "#panel-tabs" ).tabs("option", "active");
			$.cookie("panel-tabs", tab);
			
			console.log(tab);
			
			}
	});
	
	/* журнал */
	$( "#panel-log" ).accordion({
	  collapsible: true
	});
	
	/* диалоговое окно */
	$( "#dialog" ).dialog({});
	$( "#dialog" ).dialog("close");
	
	/* диалоговое окно внутри карты */
	$( "#dialog-map" ).dialog({
		
		 position: { my: "center", at: "center", of: "#wrapper-map"}
	
	});
	
		
	/* меню объекта связи*/
	$( "#menu" ).menu({
		position: { my: "left top", at: "right-5 top+5" }
	});
	
	/* прогресс бар	*/
	$( "#progressbar" ).progressbar({
		value: false
		});

	
	
	
	


}),
$(document).ready(function(){
	
	console.log("jquery dom ready");
	
});