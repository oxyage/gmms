/* 
 меню объектов связи, действия с ними 
*/
$(function(){ 

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
			switch(route[1])
			{
				case "operator":{
					let operator = GMMS.rcu.host[data.host]["auth"]["operator"];
					GMMS.func.menu.auth.operator(data.host, operator.userpass);			
					break;
				}
				case "admin":{
					let admin = GMMS.rcu.host[data.host]["auth"]["admin"];						
					GMMS.func.menu.auth.admin(data.host, operator.userpass);		
					break;
				}
				case "check":{
					
					GMMS.func.log("Проверка авторизации на "+GMMS.rcu.host[data.host].name,"log",data.host);
					
					if(!GMMS.func.menu.auth.check(data.host))	{
						GMMS.func.log("Хост "+GMMS.rcu.host[data.host].name+" не авторизован","warn",data.host);
					} else {
						GMMS.func.log("Хост "+GMMS.rcu.host[data.host].name+" авторизован","info",data.host);
					}
					
					break;
				}
				case "quiet":{
					
					GMMS.func.log("Скрытая авторизация на "+GMMS.rcu.host[data.host].name, "log", data.host);
					GMMS.func.status("wait",data.host);
					GMMS.func.icon("wait",data.host);					
					
					GMMS.func.auth(data.host)
					.done(function(done_auth){
						
						if(!done_auth.error)
						{
							GMMS.func.log("Успешная авторизация на хост "+GMMS.rcu.host[done_auth.host].name, "info", done_auth.host, done_auth);
						
							GMMS.rcu.auth[done_auth.host] = done_auth.response;
							GMMS.func.connection.set(done_auth.host);//сохраняем в БД
							
							GMMS.func.status(false,done_auth.host);
							GMMS.func.icon("ready",done_auth.host);
							console.log("GMMS.rcu.auth: ",GMMS.rcu.auth);
						}
						else
						{
							GMMS.func.log("Ошибка скрытой авторизации "+GMMS.rcu.host[done_auth.host]["name"]+" (#"+done_auth.error+")",
																							"warn",	done_auth.host,done_auth);
							GMMS.func.status(false,done_auth.host);
							GMMS.func.icon("error",done_auth.host);
						}
						
					})
					.fail(function(fail_auth){
						GMMS.func.log("Ошибка обращения к API при авторизации", "error", fail_auth.host, fail_auth);
					});
					break;
				}
				default:{
					GMMS.func.menu.blank(data.host);
				}
				
			}
			break;
		}
		
		case "rcu":
		{
			
			GMMS.func.status("wait", data.host);
			GMMS.func.icon("wait", data.host);
			
			switch(route[1]) // rcu/*
			{
				case "table": //  rcu/table
				{
					switch(route[2])  //  rcu/table/*
					{
						case "update": //  rcu/table/update
						{
							GMMS.func.log("Обновляем таблицу устройства СДК "+data.name);
							
							GMMS.func.menu.rcu.table.parse(data.host)//парсим устройства
							.done(function(done_parse){
								
								if(!done_parse.error)
								{
									GMMS.func.log("Успешно обновлены "+done_parse.response.count+" устройств(а) "+GMMS.rcu.host[done_parse.host]["name"],"info", done_parse.host, done_parse);
									
									GMMS.func.status(false,done_parse.host);
									GMMS.func.icon("ready",done_parse.host);

									/*обновить в БД*/			
									GMMS.func.db.update.rcu.devices(done_parse.host, {
										rcu_name: done_parse.response.rcu_name,
										devices_hash: done_parse.response.devices_hash,
										devices_table: done_parse.response.devices_table	
									})
									.done(function(done_update){
										if(done_update.error !== 0)	{
										
										GMMS.func.log("Ошибка обновления устройств(а) в БД "+GMMS.rcu.host[done_update.host]["name"],"warn", done_update.host, done_update);
										
										}
									})
									.fail(function(fail_update){
										GMMS.func.log("Ошибка обращения к API при обновлении устройств","error", fail_update.host, fail_update);
									});
									
								}
								else
								{
									GMMS.func.log("Ошибка обновления устрйоств "+GMMS.rcu.host[done_parse.host]["name"]+" (#"+done_parse.error+")","info", done_parse.host, done_parse);
									GMMS.func.status(false,done_parse.host);
									GMMS.func.icon("error",done_parse.host);
								}				
							})
							.fail(function(fail_parse){
								GMMS.func.log("Ошибка обращения к API при парсинге устройств", "error", fail_parse.host, fail_parse);
								GMMS.func.status(false, fail_parse.host);
								GMMS.func.icon("error", fail_parse.host);
								
							});					

							break;
						}
						default:{
							
							GMMS.func.log("Route[2] rcu/devices/[*] is undefined", "warn", false, route);
						}
					}
					
					break;
				}
				case "monitoring":{ //rcu/monitoring
					
					
					switch(route[2]) //  rcu/monitoring/*
					{
						case "inputPrimary":
						{
						
							let mux = parseInt(route[3]);

							GMMS.func.menu.rcu.monitoring.inputPrimary(data.host, {mux: mux});


							break;


							//находим устройство
							GMMS.func.db.select.device(data.host, {func:"Передатчик", mux: mux})
							.done(function(done_select){
								
	
								
								if(!done_select.error)
								{
									if(done_select.response.length > 1){
										
										GMMS.func.log(done_select.host+" Загружено более двух устройств по одному критерию", 
																							"warn", done_select.host, done_select);
										return false;
									}
									
									done_select.response = done_select.response[0];
									
									//отправляем действие на шаблон
									GMMS.func.rcu.device(done_select.host, {
										cookie: GMMS.rcu.auth[done_select.host].cookie,
										device: done_select.response,
										type_id: done_select.response.type_id,
										action: "monitoring/modulator/input_primary"
										
									})
									.done(function(done_action){
										if(!done_action.error)
										{
											GMMS.func.log(GMMS.rcu.host[done_action.host].name+": "+done_action.response.Info.represent,
																"info", done_action.host, done_action);
											GMMS.func.status(done_action.response.Info.represent, done_action.host);
											GMMS.func.icon("ready",done_action.host);
										}
										else
										{
											GMMS.func.log("Ошибка обновления устрйоств "+GMMS.rcu.host[done_action.host]["name"]+" (#"+done_action.error+")",
																"warn", done_action.host, done_action);
											GMMS.func.status(false,done_action.host);
											GMMS.func.icon("error",done_action.host);
										}				
									})
									.fail(function(fail_action){
										GMMS.func.log("Ошибка обращения к API при работе устройством", "error", fail_action.host, fail_action);
									});
								}
								else
								{
									GMMS.func.log("Ошибка получения устройства из БД "+GMMS.rcu.host[done_select.host]["name"]+" (#"+done_select.error+")",
																							"warn", done_select.host, done_select);
									GMMS.func.status(false,done_select.host);
									GMMS.func.icon("error",done_select.host);
								}
							})
							.fail(function(fail_select){ //не нашли устройство в БД
								
								GMMS.func.log("Ошибка обращения к API при выборе устройств из БД", "error", fail_select.host, fail_select);
								GMMS.func.status(false,fail_select.host);
								GMMS.func.icon("error",fail_select.host);
							});
								

						
						break;
						} // end InputPrimary
					
				
						case "complex":{
							
							console.log("Комплексный мониторинг");
							
							break;
						}
						default:{
							
							GMMS.func.log("Route[2] rcu/monitoring/[*] is undefined", "warn", false, route);
						}
						
					}
					break;
				}
				
				
				
				
				default:{
					GMMS.func.log("Route[1] rcu/[*] is undefined", "warn", false, route);	
				}
			}
				
				
	
			
			break;
		}		
		default:{
			GMMS.func.log("Route[0] is undefined", "warn", false, route);	
		}
	}
});

/* end действия на кликах меню*/





	
})