$( function() {

	console.log("jquery first");
	

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
	  active: false,
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