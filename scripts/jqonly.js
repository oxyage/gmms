$( function() {/* предустановленные переменные*/
	
	GMMS.config = { //конфигурационные параметры
		
		autoAuth: true
		
	},
	GMMS.journal = [], //журнал
	GMMS.func = {
		
		icon: function(state, host = false){
			if(typeof host === "object")
			{
				//Object.keys(GMMS.rcu.sfn.host)
				for(let _host of Object.keys(host))
				{
					//console.log(host[_host]);
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
			
		},

		//
		/*
			GMMS.func.status() - обновить все статусы на дефолтные
		*/
		status: function(state = false, host = false){
			
			switch(state)
			{
				case "wait":{
					
					state = "<img src='style/images/loader_opta.gif' style='width:35px; height:5px;'>";
					break;
				}
				default: { // false			
				}
			}
			
			if(typeof host === "object") //массив объектов
			{
				for(let _host of Object.keys(host))
				{
					$('#container-map .rts[data-host="'+host[_host]+'"] .name').html(function(){

						if(!state)	return	$(this).parent().data("name");
						else		return state;
					});
				}
			}
			else if(!host)//значит все
			{
				$('#container-map .rts .name').html(function(){
					
					if(!state)		return	$(this).parent().data("name");
					else			return state;
				});
			}
			else
			{
				$('#container-map .rts[data-host="'+host+'"] .name').html(function(){
					
					if(!state)		return	$(this).parent().data("name");
					else			return state;
				});
			}			
		},
		select: function(target, data, checked){
			
			/*let target = $(this).attr("name");
			let data = $(this).data();
			let checked = $(this).is(':checked');
			*/
			if(target === "select-all")
			{
				$('#checkbox-select-all').prop("checked", checked);
				$('#checkbox-select-all').checkboxradio("refresh");
				if (checked){
					GMMS.func.log("Выбраны все РТПС"); //логгируем
					GMMS.rcu.select = Object.keys(GMMS.rcu.host);
					GMMS.func.icon("select");
					$("#container-select-all").hide();
					$.cookie("select", "all");
				} else {
					GMMS.func.log("Снят выбор всех РТПС"); //логгируем
					GMMS.rcu.select = [];
					GMMS.func.icon("default");
					$("#container-select-all").show();
					$("#container-select-all fieldset div").show();
					$.removeCookie("select");
				}
			}
			else if(target === "sfn")
			{
				$("#field-sfn-"+data.sfnEng+" div input").prop("checked", checked);
				$("#field-sfn-"+data.sfnEng+" div input").checkboxradio("refresh");
				if (checked)
				{
					GMMS.func.log("Выбрана ОЧС "+data.sfnName); //логгируем
					Object.keys(GMMS.rcu.sfn.host[data.sfnUid]).map(function(item, index, array){ //проходим по значениям массива
						GMMS.rcu.select.push(item); //добавляем каждый в select
					});
					GMMS.func.icon("select", Object.keys(GMMS.rcu.sfn.host[data.sfnUid]));
					$("#field-sfn-"+data.sfnEng+" div").hide();
				} else 
				{
					GMMS.func.log("Снят выбор ОЧС "+data.sfnName); //логгируем
					Object.keys(GMMS.rcu.sfn.host[data.sfnUid]).map(function(item, index, array){ //проходим по значениям массива
						GMMS.rcu.select.splice( GMMS.rcu.select.indexOf(item), 1);//удаляем каждый
					});
					GMMS.func.icon("default", Object.keys(GMMS.rcu.sfn.host[data.sfnUid]));
					$("#field-sfn-"+data.sfnEng+" div").show();
				}
			}
			else if(target === "host")
			{
				if(checked){
					GMMS.func.log("Выбрана РТПС "+data.name); //логгируем
					GMMS.rcu.select.push(data.host);
					GMMS.func.icon("select", data.host);
				}
				else{
					GMMS.func.log("Снят выбор РТПС "+data.name); //логгируем
					GMMS.rcu.select.splice( GMMS.rcu.select.indexOf(data.host), 1);
					GMMS.func.icon("default", data.host);

				}
			}
		},
		auth: function(host){ // не передавать параметры имени пользователя и пароля
		
			return $.post("api.php?route=rcu/auth",{
				host: host,
				username: "admin", //исправить, тут должно быть не так
				userpass: GMMS.rcu.host[host]["auth"]["admin"].userpass
			});
		},
		checkAuth: function(host){
			
			let result = $.Deferred();
			
			
		//доделать функцию полностью	
		 //ищем сначала в GMMS.rcu.auth
			if(typeof GMMS.rcu.auth[host] === "undefined" || typeof GMMS.rcu.auth[host].cookie === "undefined") {
				//продолжаем поиск в БД

				//return GMMS.func.connection.get(host);
				GMMS.func.connection.get(host).done(function(d){
					
					console.log(d);
					if(!d.error){
						result.resolveWith(d, ["response"]);
					}
					else{
						result.reject();					
					}
					return result;
				})
				.fail(function(e){
					console.error(e);
					
				});
				
			}
			else{
				//return GMMS.rcu.auth[host];
				result.resolveWith(GMMS.rcu.auth, [host]);
				return result;
			}
		 
		 //затем ищем свежие записи в бд
		 
		 
			
			
			
		},
		connection:{
			set: function(host){
//				console.log(GMMS.rcu.auth[host];
				return $.post("api.php?route=db/insert/connection", GMMS.rcu.auth[host]);
			},
			find: function(){
				return $.post("api.php?route=db/select/connection");
			},
			get: function(host){
				return $.post("api.php?route=db/select/connection/host", {host: host});
			}
		},
		
		log: function(message, status = null){
			let date = new Date($.now());
			date = date.toTimeString().split(" ");
			$( "#panel-log div" ).prepend("<span class='"+status+"'>"+date[0]+" "+message+"</span><br>");
			GMMS.journal.push(date[0]+" "+message);
		},
		api: function(data){
			$.post("api.php?route="+data.route, data)
			.done(function(response){
				console.log(response);
			})
		}
	},
	/* предустановленные rcu */
	GMMS.rcu = {
		host: {}, //список по хостам
		list: {}, //список
		sfn: {
			host:{}, //список по зонам по хостам
			list:{}
		},
		select: [], //выбранные объекты
		deferred:{}, //deferred объекты для запросов
		auth:{} //авторизованные хосты

	}
	
}),
//#######################################################################################################
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

}),
//#######################################################################################################
$( function() { /* события на странице*/


	/* загрузка объектов связи  */
	LoadingState = new $.Deferred(); // ждем разрешения вывести на страницу
	ConnectionFindState = new $.Deferred(); // ждем разрешения на запрос в БД за соединениями
	
	$( "#dialog-wait" ).dialog();//открываем окно
	$.get("api.php?route=db/select/rcu")
	.done(function(d){
		//код ошибки = 0
		if(!d.error)
		{
			$( "#dialog-wait p" ).html("Загружено объектов связи: "+d.response.length); //в диалоговое окно
			$( "#dialog-wait" ).dialog("close");//закрываем диалог окно
			GMMS.func.log("Объекты связи загружены из базы данных ("+d.response.list.length+")"); //логгируем	
			LoadingState.resolveWith(d.response);//разрешаем вывести на страницу
			console.log("Объекты связи загружены из БД",d);
		}
		else//если есть ошибка в ответе
		{
			$( "#dialog-wait p" ).html("Запрос объектов связи завершился с ошибкой (код "+d.error+")<br><i>"+d.response.message+"</i>");
			GMMS.func.log("Ошибка загрузки объектов связи. Смотри лог", "warn"); //логгируем	
			LoadingState.reject();
			console.warn("Ошибка в ответе от сервера",d);
		}
	})
	.fail(function(e){//если не удался запрос к файлу
		$( "#dialog-wait p" ).html("Загрузка объектов связи не удалась<br>Смотри лог");
		GMMS.func.log("Ошибка загрузки объектов связи. Смотри лог", "error"); //логгируем	
		LoadingState.reject();
		console.error("API не доступно",e);
	});
	
	
	/* вывод пользователю */
	LoadingState
	.done(function(){
		
		//в консоль
		GMMS.rcu.list = this.list;
		GMMS.rcu.host = this.host;
		GMMS.rcu.sfn.host = this.sfn.host;
		GMMS.rcu.sfn.list = this.sfn.list;
		ConnectionFindState.resolve();
		
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

		GMMS.func.log("Объекты связи нанесены на карту"); //логгируем	
			
			
		//в цикле выводим в панель выбора объектов связи по одночастотным зонам	
		for(let sfn_uid of Object.keys(this.sfn.host))
		{
			let sfn = this.sfn.list[sfn_uid];
			
			$("#container-select-all").append(
			
				//добавим панель одночастотной зоны
				$("<fieldset>", {
					"id": "field-sfn-"+sfn.eng,
					"data-sfn-eng": sfn.eng,
					"data-sfn-name": sfn.name,
					"data-sfn-uid": sfn.uid
				})
				.css({
					"margin-top": "10px"
				})
				.append(
					//Легенда панели
					$("<legend>").append(
						//добавить в тег легенду - label
						$("<label>",{ 
							"for": "sfn-"+sfn.eng 
						})
						.html("<b>Одночастотная зона "+sfn.name+"</b>")
					),
					//кнопка выбора на легенде (ОЧС)
					$("<input>", {
						"type": "checkbox",
						"id": "sfn-"+sfn.eng,
						"name": "sfn",
						"data-sfn-eng": sfn.eng,
						"data-sfn-name": sfn.name,
						"data-sfn-uid": sfn.uid
					}),
					$("<div>").css({"text-align":"left"})
				)
			);
			
			//цикл в одночастотной зоне
			for(let host of Object.keys(this.sfn.host[sfn_uid]))
			{
				$("#field-sfn-"+sfn.eng+" div").append(
					//добавляем label
					$("<label>",{ 
						"for": "checkbox-host-"+host
					})
					.text(this.host[host]["name"]),
					
					//добавляем input
					$("<input/>", {
						"type": "checkbox",
						"id": "checkbox-host-"+host,
						"name": "host",
						"data-host": host,
						"data-name": this.host[host]["name"],
						"data-eng": this.host[host]["eng"]
					})	
				
				);
				

			}						
		}
		
		GMMS.func.log("Объекты связи выведены в панель выбора"); //логгируем	

		
		//активируем все чекбоксы объектов связи
		$( "#panel-select input" ).checkboxradio({
		  icon: false
		});	

		
		/* действия кнопок выбор одночастотной зоны или всех*/
		$('#panel-select input').click(function(){
			
			
			let target = $(this).attr("name");
			let data = $(this).data();
			let checked = $(this).is(':checked');
			
			GMMS.func.select(target, data, checked);
			
		});			
			
		/*	после загрузки всех  объектов	*/	
		//ставим выбор всех объектов
		//#### доделать на остальные выборы
		if($.cookie("select") === "all")  
		{
			GMMS.func.select("select-all", false, true);
		}		
		
		//ищем авторизованные соединения 
		let find_connections = GMMS.func.connection.find();
		find_connections.done(function(d){
			
			if(!d.error)
			{
				for(let i of d.response)
				{
					GMMS.rcu.auth[i.host] = i;
					GMMS.func.status(false,i.host);
					GMMS.func.icon("ready",i.host);
					GMMS.func.log("Найдено соединение "+GMMS.rcu.host[i.host].name,"good");
				}
				console.log(GMMS.rcu.auth);
			}
			else
			{
				GMMS.func.log("Ошибка поиска соединений (#"+d.error+")","warn");
				console.warn(d);
			}
			
			
		}).fail(function(e){
			GMMS.func.log("Ошибка обращения к API при загрузке соединений","error");
			console.error(e);
		});
		
			
		
	})
	.fail(function(){
		console.warn("Вывода не будет");
		ConnectionFindState.reject();
	});
	
	
	ConnectionFindState
	.done(function(){
		console.log("ConnectionFindState done");
		let find = GMMS.func.connection.find();
	//	console.log(find);
	})
	.fail(function(){
		console.warn("Запроса авторизованных хостов не будет");
	});
	
	
/* end загрузка объектов связи */
	
	
	
	
	
	$("button.gmms").click(function(){
		console.log(GMMS);
	});
	

	console.log("jquery first");

	/* нажатие на кнопку Показать имена РТС */
	$('#show-rts-names').click(function(){
		
		if ($(this).is(':checked')){
			$.cookie("rts-names", 0);
			$("div.rts .name").hide();
		} else {
			$.cookie("rts-names", 1);
			$("div.rts .name").show();
		}
	
	});
		
	/* */
	$('#auto-auth').click(function(){
		
		if ($(this).is(':checked')){
			
			GMMS.config.autoAuth = true;
		} else {
			GMMS.config.autoAuth = false;
		}
		
		console.log("GMMS.config.autoAuth", GMMS.config.autoAuth);
	});	
		
	
		
	


}),
//#######################################################################################################
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
					
					GMMS.func.log("Скрытая авторизация на "+GMMS.rcu.host[data.host].name);
					GMMS.func.status("wait",data.host);
					GMMS.func.icon("wait",data.host);					
					
					GMMS.func.auth(data.host)
					.done(function(d){
						
						if(!d.error)
						{
							GMMS.func.log("Успешная авторизация на хост "+GMMS.rcu.host[d.host].name,"good");
							GMMS.rcu.auth[d.host] = d.response;
							//сохраняем в БД
							GMMS.func.connection.set(d.host);
							GMMS.func.status(false,d.host);
							GMMS.func.icon("ready",d.host);
							console.log(GMMS.rcu.auth);
						}
						else
						{
							GMMS.func.log("Ошибка скрытой авторизации "+GMMS.rcu.host[d.host]["name"]+" (#"+d.error+")","warn");
							GMMS.func.status(false,d.host);
							GMMS.func.icon("error",d.host);
							console.warn(d);
						}
						
					})
					.fail(function(e){
						GMMS.func.log("Ошибка обращения к API","error");
						console.error(e);
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
			
			GMMS.func.status("wait",data.host);
			GMMS.func.icon("wait",data.host);
			
		/*	if(typeof GMMS.rcu.auth[data.host] === "undefined" || typeof GMMS.rcu.auth[data.host].cookie === "undefined")
			{
				GMMS.func.log("Нет данных об авторизации "+data.name,"error");
				GMMS.func.status(false,data.host);
				GMMS.func.icon("error",data.host);
				break;
			}
		*/	
		
		//DEBUG
			GMMS.func.checkAuth(data.host).done(function(d){
				console.log(d);
			});
			break;
			/*
			
			тут же добавить проверку авторизации
			
			*/
			
			
			
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
							
							GMMS.func.log("Обновляем устройства СДК "+data.name);
							
							$.post("api.php?route=rcu/parse",{
								host: data.host,
								cookie: GMMS.rcu.auth[data.host].cookie
								
							})
							.done(function(d){
								if(!d.error)
								{
									GMMS.func.log("Успешно обновлены "+d.response.count+" устройств(а) "+GMMS.rcu.host[d.host]["name"],"good");
									GMMS.func.status(false,d.host);
									GMMS.func.icon("ready",d.host);
									console.log(d);
								}
								else
								{
									GMMS.func.log("Ошибка обновления устрйоств "+GMMS.rcu.host[d.host]["name"]+" (#"+d.error+")","warn");
									GMMS.func.status(false,d.host);
									GMMS.func.icon("error",d.host);
									console.warn(d);
								}				
							})
							.fail(function(e){
								GMMS.func.log("Ошибка обращения к API","error");
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
		case "monitoring":
		{
			GMMS.func.status("wait",data.host);
			GMMS.func.icon("wait",data.host);
			
			if(typeof GMMS.rcu.auth[data.host] === "undefined" || typeof GMMS.rcu.auth[data.host].cookie === "undefined")
			{
				GMMS.func.log("Нет данных об авторизации "+data.name,"error");
				GMMS.func.status(false,data.host);
				GMMS.func.icon("error",data.host);
				break;
			}
			
			/*
			
			тут же добавить проверку авторизации
			
			*/
			
			switch(route[1])
			{
				/*
				добавлять новые режимы мониторинга
				*/
				case "sfn":{
					
					let mux = route[2].split("-")[1];
					
					
					break;
				}
				
				case "input":{
					
					let mux = route[2].split("-")[1];
					
					break;
				}
			
				case "complex":{
					
					console.log("Комплексный мониторинг");
					
					break;
				}
			
			
				default:{
					console.warn("route[1] is undefined");
					console.log(route);
				}
				
				
			}
			break;
		}
		
		default:{
			console.warn("route[0] is undefined");
			console.log(route);
		}
	}
	
	//console.log(		$(this).data()		);
});

/* end действия на кликах меню*/



/* обновить устройства в таблице rcu */






	
}),
//#######################################################################################################
$(function(){ /*  стили */
	
}),
//#######################################################################################################
$(function(){ /*  служебные функции */
	
	
	
}),
//#######################################################################################################
$(document).click(function(event){ /* клик по документу */
	
	if($(event.target).parents("ul#menu").length === 0 && $(event.target).closest(".rts").length === 0) // если клик вне меню или на РТС
	{	
		$("ul#menu").hide();
	}
	
}),
//#######################################################################################################
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