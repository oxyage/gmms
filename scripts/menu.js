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






	
})