/* 
предустановленные переменные
*/
$( function() {
	
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
		select: function(target, data, checked){ //выбор объектов связи
			
			/*let target = $(this).attr("name");
			let data = $(this).data();
			let checked = $(this).is(':checked');
			*/
			if(target === "select-all")
			{
				
				$('#checkbox-select-all, #container-select-all input').prop("checked", checked);
				$('#checkbox-select-all, #container-select-all input').checkboxradio("refresh");
				
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
		selected:function(callback){
			
			if(GMMS.rcu.select.length === 0) 
			{
				GMMS.func.log("Объекты связи не выбраны", "error");
				return false;
			}
			
			for(host of GMMS.rcu.select)
			{
				callback(host);
			}
		},
		auth: function(host){ // не передавать параметры имени пользователя и пароля
		
			return $.post("api.php?route=rcu/auth",{
				host: host,
				username: "admin", //исправить, тут должно быть не так
				userpass: GMMS.rcu.host[host]["auth"]["admin"].userpass
			});
		},
		checkAuth: function(host)
		{
			
			if(typeof GMMS.rcu.auth[host] === "object" && GMMS.rcu.auth[host].cookie !== "undefined")
			{
					return true;
			}	
	
			return false;
			
		},
		checkAuth_deferred: function(host){
			
			if(typeof GMMS.rcu.deferred[host] === "undefined") 
				GMMS.rcu.deferred[host] = {};
			

			GMMS.rcu.deferred[host]["checkAuth"] = $.Deferred();
			

			if(typeof GMMS.rcu.auth[host] === "object" && GMMS.rcu.auth[host].cookie !== "undefined")
			{
				GMMS.rcu.deferred[host]["checkAuth"].resolveWith(GMMS.rcu.auth, [GMMS.rcu.auth[host]]);
			}
			else
			{
				//продолжаем поиск в БД
				//console.log("Требуется поиск в БД");
				GMMS.func.connection.get(host).done(function(d){
					let host = d.host;
					if(!d.error){
						if(d.response.length > 0) 
						{
							GMMS.rcu.deferred[host]["checkAuth"].resolveWith(GMMS.rcu.auth, [d.response[0]]);
							GMMS.rcu.auth[host] = d.response[0];
						}
						else	//автоматическая попытка авторизации
						{
							
							GMMS.func.auth(host)
							.done(function(d){
								
								GMMS.rcu.deferred[d.host]["checkAuth"].resolveWith(GMMS.rcu.auth, [d.response[0]]);
								GMMS.rcu.auth[d.host] = d.response[0];
							})
							.fail(function(){
								GMMS.rcu.deferred[d.host]["checkAuth"].rejectWith(GMMS.rcu.auth, [d]);	
							});
							
						//
						}	
						
						
					}
					else{
						GMMS.rcu.deferred[host]["checkAuth"].rejectWith(GMMS.rcu.auth, [e]);					
					}
				})
				.fail(function(e){//ошибка обращения к API
				//	GMMS.func.log("Ошибка обращения к API",true,"error");
					GMMS.rcu.deferred[host]["checkAuth"].rejectWith(GMMS.rcu.auth, [e]);
				});	
			}
			
			return GMMS.rcu.deferred[host]["checkAuth"];		
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
		devices:{
			load: function(){ //загрузить все из БД
				return $.post("api.php?route=db/select/devices");
			}
		},
		//GMMS.func.rcu 
		rcu:{
			function:{
				devices:{
					update: function(host){
						
						let data = {
							name: GMMS.rcu.host[host].name, 
							host: host
						};
						
						//console.log("Будем обновлять устройства в таблицах");
						//console.log("Первым делом таблица RCU");
						
						GMMS.func.log("Обновляем устройства СДК "+data.name);
						
						$.post("api.php?route=rcu/parse",{
							host: data.host,
							cookie: GMMS.rcu.auth[data.host].cookie
							
						})
						.done(function(d){
							if(!d.error)
							{
								GMMS.func.log("Успешно обновлены "+d.response.count+" устройств(а) "+GMMS.rcu.host[d.host]["name"],"info", d.host, d);
								
								
								
								/*обновить в БД*/
								GMMS.func.status(false,d.host);
								GMMS.func.icon("ready",d.host);
								
								$.post("api.php?route=db/update/rcu.devices",{
									host: d.host,
									rcu_name: d.response.rcu_name,
									devices_hash: d.response.devices_hash,
									devices_table: d.response.devices_table})
								.done(function(){})
								.fail(function(e){
									console.log(d.host+": не удалось обновить устройства в БД");
								});
								
							}
							else
							{
								GMMS.func.log("Ошибка обновления устрйоств "+GMMS.rcu.host[d.host]["name"]+" (#"+d.error+")","info", d.host, d);
								GMMS.func.status(false,d.host);
								GMMS.func.icon("error",d.host);
								console.warn(d);
							}				
						})
						.fail(function(e){
							GMMS.func.log("Ошибка обращения к API","error",e.host,e);
							console.error(e);
						});
						
						}
						
					}
				},
			monitoring:{ //GMMS.func.rcu.monitoring
				
				
				inputPrimary: function(host, mux){
					
					let data = {
							name: GMMS.rcu.host[host].name, 
							host: host
						};

					//находим устройство
					$.post("api.php?route=db/select/device",{
						host: data.host,
						func: "Передатчик",
						mux: mux
					})
					.done(function(data){
						if(!data.error)
						{
							if(data.response.length > 1){
								
								GMMS.func.log(data.host+" Загружено более двух устройств по одному критерию","warn", data.host, data);
							}
							
							data.response = data.response[0];
							//теперь можно отправлять запрос на само устройство

							$.post("api.php?route=rcu/device",{
								host: data.host,
								cookie: GMMS.rcu.auth[data.host].cookie || false,
								device: data.response,
								type_id: 10000,
								action: "monitoring/modulator/input_primary"							
							})
							.done(function(d){
								if(!d.error)
								{
									GMMS.func.log(GMMS.rcu.host[d.host].name+": "+d.response.represent,"info", d.host, d);
									GMMS.func.status(d.response.represent, d.host);
									GMMS.func.icon("ready",d.host);
								}
								else
								{
									GMMS.func.log("Ошибка обновления устрйоств "+GMMS.rcu.host[d.host]["name"]+" (#"+d.error+")","warn", d.host, d);
									GMMS.func.status(false,d.host);
									GMMS.func.icon("error",d.host);
									console.warn(d);
								}				
							})
							.fail(function(e){
								GMMS.func.log("Ошибка обращения к API","error", e.host, e);
								console.error(e);
							});
							
						}
						else
						{
							GMMS.func.log("Не могу получить устройства из БД "+GMMS.rcu.host[d.host]["name"]+" (#"+d.error+")","warn", d.host, d);
							GMMS.func.status(false,d.host);
							GMMS.func.icon("error",d.host);
							console.warn(d);
						}
					})
					.fail(function(e){
						
						GMMS.func.log("Ошибка обращения к API","error", e.host,e);
						console.error(e);
						
					});
					
					/*
					
					
					*/
					
				}
			}

		
		},
		panelClick:function(data){
			
			let route = data.route.split("/");
			
			switch(route[0])
			{
				case "rcu":{
					
					switch(route[1])
					{
						case "auth":{
							
							GMMS.func.selected(function(host){
				
							
								GMMS.func.log("Авторизация на "+GMMS.rcu.host[host].name, "log", host);
								GMMS.func.status("wait",host);
								GMMS.func.icon("wait",host);					
								
								GMMS.func.auth(host)
								.done(function(d){
									
									
									if(!d.error)
									{
										GMMS.func.log("Успешная авторизация на хост "+GMMS.rcu.host[d.host].name, "info", d.host, d);
										GMMS.rcu.auth[d.host] = d.response;
										//сохраняем в БД
										GMMS.func.connection.set(d.host);
										GMMS.func.status(false,d.host);
										GMMS.func.icon("ready",d.host);
										console.log(GMMS.rcu.auth);
									}
									else
									{
										GMMS.func.log("Ошибка авторизации "+GMMS.rcu.host[d.host]["name"]+" (#"+d.error+")","warn",d.host,d);
										GMMS.func.status(false,d.host);
										GMMS.func.icon("error",d.host);
										console.warn(d);
									}
								
									
								})
								.fail(function(e){
									GMMS.func.log("Ошибка обращения к API","error",e.host,e);
									console.error(e);
								});
							}); // gmms.rcu.selected end

							break;
						}
						
						
						case "monitoring":{
							
							
							
							switch(route[2])
							{
								case "inputPrimary":{
									
									
									//let mux = route[3];
									let mux = $("input[name=mux]:checked").val();
									
									GMMS.func.selected(function(host){
										
										console.log(host);
										
										GMMS.func.log("inputPrimary ["+mux+" mux]: "+GMMS.rcu.host[host].name, "log", host);
										GMMS.func.status("wait",host);
										GMMS.func.icon("wait",host);		
										GMMS.func.rcu.monitoring.inputPrimary(host, mux);										
									});

									break;
								}
								/*case "":{break;}
								case "":{break;}
								case "":{break;}
								case "":{break;}
								case "":{break;}
								case "":{break;}*/
								default:{
									console.error("route[2] undefined");
								}
								
							}
							
							break;
						}
						
						
						
						default:{
							console.error("route[1] undefined");
						}
					}
					break;
				}
				default:{
					console.error("route[0] undefined");
				}
			}
			
			
			
			
			
		},
		//GMMS.func.log(message, status = "log", host = 0, object = {})
		log: function(message, status = "log", host = 0, object = {}){
			
			/*
			
			Задачи функции:
			
			*Отобразить сообщение в 
			-консоли + объект ответа
			-журнале сообщение 
			
			*Записать в БД - нужны host и json-объект ответа
			
			*Записать в GMMS.journal
			
			Формат в журнале:
			<red>чч:мм:сс Ошибка, смотри лог {name} </red>
			<yellow>чч:мм:сс Предупреждение, смотри лог {name} </yellow>
			<green>чч:мм:сс Успешное выполнение команды {name} </green>
			чч:мм:сс Обычное действие {name}
			
			Формат в консоли:
			console.error("Ошибка", name, object)
			console.warn("Предупреждение", name, object)
			console.log("Успешное выполнение или обычное действие", name, object)
			
			Формат в БД:
			Передать text, host
			remote, timestamp - запишутся автоматически
			
			
			Как построить фунцию?
			
			Передать параметры:
			message, хост, объект ответа , статус ошибки
			
			
			
			Рефакторинг функции:
			передавать host
			передавать object для консоли
			JSON объект записывать в БД
			
			*/
			
			//GMMS.func.log(message, status = "log", host = 0, object = {})
			
			function addZero(i){
				return (i < 10 && "0"+i) || i; 
			}
		
			let date = new Date(); //формируем дату
			time = addZero(date.getHours())+":"+addZero(date.getMinutes())+":"+addZero(date.getSeconds());
			
			$( "#panel-log div.journal" ).prepend("<div class='"+status+"'>"+time+" "+message+"</div>"); //пишем в журнал
				
			GMMS.journal.push(time+" "+message, object); //в журнал переменной

			console[status](message, object);//в консоль
			
			GMMS.func.api({ //в базу данных
				route: "db/insert/log",
				message: message,
				host: host,
				object: null//JSON.stringify(object)
			})
			.fail(function(e){
				console.error("GMMS.func.log",e)
			});
			
		},
		api: function(data){
			return $.post("api.php?route="+data.route, data);
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
		auth:{}, //авторизованные хосты
		devices:{} //устройства
	}
	
})