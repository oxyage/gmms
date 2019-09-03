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
				console.log("rcu.auth: ",GMMS.rcu.auth);
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
	

	console.log("jquery events");

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
		
	
		
	


})