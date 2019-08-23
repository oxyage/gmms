$( function() {/* предустановленные переменные*/
	
	GMMS.journal = [], //журнал
	GMMS.log = function(message, status = null){
		let date = new Date($.now());
		date = date.toTimeString().split(" ");
		$( "#panel-log div" ).prepend("<span class='"+status+"'>"+date[0]+" "+message+"</span><br>");
		GMMS.journal.push(date[0]+" "+message);
	},
	GMMS.func = {
		icon: function(state, host = false){
					
			
			if(typeof host === "object")
			{
				//Object.keys(GMMS.rcu.sfn.host)
				for(let _host of Object.keys(host))
				{
					console.log(host[_host]);
					$('.rts[data-host="'+host[_host]+'"] .tower').attr("class", "tower "+state);	
				}
			}
			else if(!host)//значит все
			{
				$('.rts	.tower').attr("class", "tower "+state);
			}
			else
			{
				$('.rts[data-host="'+host+'"] .tower').attr("class", "tower "+state);
			}
			//$('[data-host="'+host+'"] .tower').css({"background-color": _status});	
			
		}
	},
	GMMS.api = function(data){
		//console.log($.param(data,true));
		$.post("api.php?route="+data.route, data)
		.done(function(response){
			console.log(response);
		});
	}
	
}),
$( function(){ /* активация элементов на странице */

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

}),
$( function() { /* события на странице*/


	/* загрузка объектов связи  */
	LoadingState = new $.Deferred(); // ждем разрешения вывести на страницу
	
	$( "#dialog-wait" ).dialog();//открываем окно
	$.get("api.php?route=db/select/rcu")
	.done(function(d){
		//код ошибки = 0
		if(!d.error)
		{
			$( "#dialog-wait p" ).html("Загружено объектов связи: "+d.response.length); //в диалоговое окно
			$( "#dialog-wait" ).dialog("close");//закрываем диалог окно
			GMMS.log("Объекты связи загружены из базы данных ("+d.response.list.length+")"); //логгируем	
			LoadingState.resolveWith(d.response);//разрешаем вывести на страницу
			console.log("Загруженные объекты связи",d);
		}
		else//если есть ошибка в ответе
		{
			$( "#dialog-wait p" ).html("Запрос объектов связи завершился с ошибкой (код "+d.error+")<br><i>"+d.response.message+"</i>");
			GMMS.log("Ошибка загрузки объектов связи. Смотри лог", "warn"); //логгируем	
			LoadingState.reject();
			console.warn("Ошибка в ответе от сервера",d);
		}
	})
	.fail(function(e){//если не удался запрос к файлу
		$( "#dialog-wait p" ).html("Загрузка объектов связи не удалась<br>Смотри лог");
		GMMS.log("Ошибка загрузки объектов связи. Смотри лог", "error"); //логгируем	
		LoadingState.reject();
		console.error("API не доступно",e);
	});
	
	
	/* вывод пользователю */
	LoadingState
	.done(function(){
		
		//console.log("this:",this);
		//в консоль
		GMMS.rcu = this;
		
		// в цикле выводим на карту
		for(let RTS of this.list)
		{
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
			.click(function(rts){
				let name = $(this).data("name");
				let host = $(this).data("host");
				
				$("#menu")
				.css({
					top: rts.pageY+"px",
					left: rts.pageX+"px"			
				})
				.data({
					name: name,
					host: host
				})
				.show();
				
				$("#menu .ui-widget-header div:first").text(name);
			
			})
			.appendTo("#container-map");			
		}				

			
		//в цикле выводим в панель выбора объектов связи по одночастотным зонам	
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
			
			//цикл в одночастотной зоне
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

		GMMS.log("Объекты связи нанесены на карту"); //логгируем		
			
		
		//активируем все чекбоксы объектов связи
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
					GMMS.log("Выбраны все объекты связи"); //логгируем
					$("#container-select-all").hide();
				} else {
					$("#container-select-all").show();
				}
			}
			else
			{
				if ($(this).is(':checked')){
//						let name = target.split("-");
//					GMMS.log("Выбран объект связи "+GMMS.name[1]); //логгируем								
					$("#field-"+target+" div").hide();
				} else {
					$("#field-"+target+" div").show();
				}
			}
		});			
			
		
	})
	.fail(function(){
		console.warn("Вывода не будет");
	});
	
/* end загрузка объектов связи */
	
	
	
	
	
	$("button.gmms").click(function(){
		console.log(GMMS);
	});
	

	console.log("jquery first");
	
	
	
	
	
	
	

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
$(function(){ /*  меню объектов связи, действия с ними */

/* действия на кликах меню */
$("li.action").click(function(li){
	
	let data = $(this).data();
	route = data.route.split("/");

	//использовать только при отправке запроса т.к. заменяется при быстрых кликах
	data["host"] = $("#menu").data("host"); //
	data["name"] = $("#menu").data("name"); //
	
	switch(route[0])
	{
		case "auth":
		{

			let operator = GMMS.rcu.host[data.host]["auth"]["operator"];
			let admin = GMMS.rcu.host[data.host]["auth"]["admin"];						
			
			switch(route[1])
			{
				case "operator":{
					window.open("http://"+data.host+"/config/devices/?username=operator&userpass="+operator.userpass,"_blank");				
					break;
				}
				case "admin":{
					window.open("http://"+data.host+"/config/devices/?username=admin&userpass="+admin.userpass,"_blank");
					break;
				}
				case "quiet":{
					console.log("Тихая авторизация на "+data.name);
					$.post("api.php?route=rcu/auth",{
						host: data.host,
						username: "admin",
						userpass: admin.userpass
					})
					.done(function(a){
						console.log(a);
					})
					.fail(function(e){
						console.warn(e);
					});
					break;
				}
				default:{
					window.open("http://"+data.host+"/config/devices/","_blank");
				}
				
			}
			break;
		}
		case "function":
		{
			switch(route[1])
			{
				case "devices":
				{
					switch(route[2])
					{
						case "update":
						{
							console.log("Будем обновлять устройства в таблицах");
							console.log("Первым делом таблица RCU");
							
							GMMS.log("Обновляем устройства СДК "+data.name);
							
							/* добавить прогрессбар */
							
							$.post("api.php?route=rcu/parse",{
								host: data.host,
								cookie: "RCUSESSID=6a81dbd75e57121f8d79896d0c427c63; path=/"
								
							})
							.done(function(d){
								if(!d.error)
								{
									GMMS.log("Успешно обновлены "+d.response.count+" устройств(а) "+GMMS.rcu.host[d.host]["name"],"good");
									console.log(d.response);
								}
								else
								{
									GMMS.log("Ошибка обновления устрйоств "+GMMS.rcu.host[d.host]["name"]+" (#"+d.error+")","warn");
									console.warn(d);
								}				
							})
							.fail(function(e){
								GMMS.log("Ошибка обновления устрйоств (см. лог)","error");
								console.error(e);
							});
							

							break;
						}
						
						
					}
					
					break;
				}
				default:{
					
				}
			}
			break;
		}
		
		
		default:{
			console.warn("route[0] is null");
			console.log(route);
		}
	}
	
	//console.log(		$(this).data()		);
});

/* end действия на кликах меню*/



/* обновить устройства в таблице rcu */






	
}),
$(function(){ /*  стили */
	
}),
$(function(){ /*  служебные функции */
	
	
	
}),
$(document).click(function(event){ /* клик по документу */
	
	if($(event.target).parents("ul#menu").length === 0 && $(event.target).closest(".rts").length === 0) // если клик вне меню или на РТС
	{	
		$("ul#menu").hide();
	}
	
}),
$(document).ready(function(){ /* после загрузки DOM */
	
	
	console.log("jquery dom ready");
	
});



/*
API запрос

$.post("api.php?route=",{})
.done(function(d){
	if(!d.error)
	{

	}
	else
	{
	console.warn(d);
	}				
});
.fail(function(e){
	console.error(e);
});


*/