/* 
активация элементов на странице 
*/
$( function(){ 


	/* активируем все вкладки */
	$( "#panel .tabs" ).tabs({});
	

	$( "#panel-monitoring" ).accordion({
	  collapsible: true,
	  active: false, //скрыть при загрузке
	  heightStyle: "content"
	});

	/* панель выбора объектов связи */
	$( "#panel-select" ).accordion({
	  collapsible: true,
	  active: false, //скрыть при загрузке
	  heightStyle: "content",
		icons:{
			 header: "ui-icon-radio-on",
		activeHeader: "ui-icon-radio-off"
		}
	});
	
	/* панель выбора объектов связи */
	$( "#panel-additional" ).accordion({
	  collapsible: true,
	  active: false, //скрыть при загрузке
	  heightStyle: "content",
		icons:{
			 header: "ui-icon-radio-on",
		activeHeader: "ui-icon-radio-off"
		}
	});
	
	
		
	
	/* вкладки на странице */
	$( "#panel-tabs" ).tabs({
		collapsible: true,
		//допустить false
		active:  (typeof $.cookie("panel-tabs") !== "undefined" && $.cookie("panel-tabs")) || 2, //активная закладка
		activate: function() { //сохраняем в куки			
			let tab = $( "#panel-tabs" ).tabs("option", "active");
			$.cookie("panel-tabs", tab);

			console.log("tab #",tab);
			
			}
	});
	
	/* радиокнопки */
	$( ".panel-radio" ).checkboxradio({
	  icon: false
	});
	
	$( ".panel-button" ).button();

	
	/* кнопки показать имена РТС */
	$( "#show-rts-names" ).checkboxradio({
	  icon: false
	});
	
	/* кнопки показать имена РТС */
	$( "#auto-auth" ).checkboxradio({
		disabled: true,
		icon: false
	});
	$("#auto-auth").prop("checked", true);
	$("#auto-auth" ).checkboxradio("refresh");
	
	
	/* журнал */
	$( "#panel-log" ).accordion({
	  collapsible: true,
	  active: $.cookie("panel-log") === "0" && parseInt($.cookie("panel-log")),
	  heightStyle: "content",
	  icons: {
		  header: "ui-icon-note",
		activeHeader: "ui-icon-note"
		  },
		activate: function( event, ui ) { //добавить куки
			
			let panelLog = $( "#panel-log" ).accordion("option", "active");
			$.cookie("panel-log", panelLog);
			console.log($.cookie("panel-log"));
			
		}  
	});
	
	console.log("jqonly.js");
	
	/* диалоговое окно */
	$( "#dialog" ).dialog({});
	$( "#dialog" ).dialog("close");
	
	/* диалоговое окно внутри карты */
	$( "#dialog-map" ).dialog({
		 position: { my: "center", at: "center", of: "#wrapper-map"}
	
	});
	$( "#dialog-map" ).dialog("close"); //закрываем при старте
	
		
	/* меню объекта связи*/
	$( "#menu" ).menu({
		//position: { my: "left top", at: "right-5 top+5" }
		items: "> :not(.ui-widget-header)"
	}).hide();
	
	
	/* прогресс бар	*/
	$( ".progressbar" ).progressbar({
		value: false
		});

})