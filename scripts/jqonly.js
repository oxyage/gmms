$( function() {/* предустановленные переменные*/
	
	GMMS.journal = [], //журнал
	GMMS.log = function(message){
		let date = new Date($.now());
		date = date.toTimeString().split(" ");
		$( "#panel-log div" ).prepend(date[0]+" "+message+"<br>");
		GMMS.journal.push(date[0]+" "+message);
	},
	GMMS.sfn = [], //одночастотные сети
	GMMS.func = {
		/*getSFNbyList: function(list){
			
			for(let RTS of list)
			{
				if(typeof GMMS.sfn[RTS.sfn] === "undefined") GMMS.sfn[RTS.sfn] = [];
				//GMMS.sfn[RTS.sfn] = RTS;
				GMMS.sfn[RTS.sfn].push(RTS);
			}
		}*/
	}
	
}),
$( function(){ /* активация элементов на странице */
	
	
	/* диалоговое окно */
	
	//$( "#dialog" ).dialog("close");

	/*выбор объектов связи */
	$( "#panel-select" ).accordion({
	  collapsible: true,
	 // active: false,
	  heightStyle: "content",
		icons:{
			 header: "ui-icon-radio-on",
		activeHeader: "ui-icon-radio-off"
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
	
	
	/* кнопки показать имена РТС */
	$( "#show-rts-names" ).checkboxradio({
	  icon: false
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
			GMMS.log("Объекты связи успешно загружены ("+d.response.list.length+")"); //логгируем	
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
			
			console.log("this:",this);
				
			for(let RTS of this.list)
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
					"left": RTS.coord.x+"%"
				})
				.appendTo("#container-map");			
			}				

			for(let sfn_uid of Object.keys(this.sfn.host))
			{
				let sfn = this.sfn.list[sfn_uid];
								
				$("<fieldset>", {
					"id": "field-sfn-"+sfn.eng,
				})
				.css({
					"margin-top": "10px"
				})
				.append('<legend><label for="sfn-'+sfn.eng+'" >Одночастотная зона '+sfn.name+'</label><input type="checkbox" name="sfn-'+sfn.eng+'" id="sfn-'+sfn.eng+'" class="sfn"></legend><div></div><br>')
				.appendTo("#container-select-all");
				
				for(let host of Object.keys(this.sfn.host[sfn_uid]))
				{
					console.log();
					
					$("<label>",{
						"for": "checkbox-host-"+host
					})
					.append(this.host[host]["name"]+'<input type="checkbox" name="host-'+host+'" id="checkbox-host-'+host+'">')
					.appendTo("#field-sfn-"+sfn.eng+" div");
					
				}
				
			}
			
			$( "#panel-select input" ).checkboxradio({
				  icon: false
				});	

			
			/* действия кнопок выбор одночастотной зоны или всех*/
			$('#panel-select input').click(function(){
				
				let target = $(this).attr("name");
				console.log(target, $(this).is(':checked'));
				
				if(target === "select-all")
				{
					if ($(this).is(':checked')){
						$("#container-select-all").hide();
					} else {
						$("#container-select-all").show();
					}
				}
				else
				{
					if ($(this).is(':checked')){
						$("#field-"+target+" div").hide();
					} else {
						$("#field-"+target+" div").show();
					}
				}
			});			
				
				
				
				
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
	
	
	
	
	

	/* */
	$('#show-rts-names').click(function(){
		
		if ($(this).is(':checked')){
			$.cookie("rts-names", 0);
			$("div.rts .name").hide();
		} else {
			$.cookie("rts-names", 1);
			$("div.rts .name").show();
		}
	
	});
		
	
		
	


}),
$(function(){ /*  стили */
	
}),
$(document).ready(function(){
	
	console.log("jquery dom ready");
	
});