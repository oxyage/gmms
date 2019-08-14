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
	
	/* выбор одночастотной зоны*/
	$('#panel-select legend input').click(function(){
		
		let sfn = $(this).attr("name");

		//console.log(sfn);
		//console.log($("#field-"+sfn+" input"));
		
		
		if ($(this).is(':checked')){
			$("#field-"+sfn+" div").hide();
			//$("#field-"+sfn+" input[name^=host]").checkboxradio('option', 'disabled', true);
			//$("#field-"+sfn+" input").prop('checked',true).checkboxradio("refresh");
		} else {
			$("#field-"+sfn+" div").show();
			//$("#field-"+sfn+" input[name^=host]").checkboxradio('option', 'disabled', false);
			//$("#field-"+sfn+" input").prop('checked',false).checkboxradio("refresh");
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