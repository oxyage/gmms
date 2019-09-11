/* 
предустановленные переменные
*/
$( function() {
	
	GMMS.config = { //конфигурационные параметры
		
		autoAuth: true
		
	},
	GMMS.journal = [], //журнал
	GMMS.func = {
		
		
		
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
								return false;
							}
							
							data.response = data.response[0];
							//теперь можно отправлять запрос на само устройство

							if(typeof GMMS.rcu.auth[data.host] === "undefined" || GMMS.rcu.auth[data.host].cookie === "undefined"){
								GMMS.func.log(GMMS.rcu.host[data.host].name+": объект не авторизован","error", data.host, data);
								GMMS.func.status(false,data.host);
								GMMS.func.icon("error",data.host);
								return false;
							}
							
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
									GMMS.func.log(GMMS.rcu.host[d.host].name+": "+d.response.Info.represent,"info", d.host, d);
									GMMS.func.status(d.response.Info.represent, d.host);
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
						
						case "updateStatus":{
						
							GMMS.func.status();						
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