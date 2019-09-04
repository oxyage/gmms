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
				case "check":{
					
					GMMS.func.log("Проверка авторизации на "+GMMS.rcu.host[data.host].name,true);
					
					GMMS.func.checkAuth(data.host)
					.done(function(d){
					
						GMMS.func.log("Хост "+GMMS.rcu.host[data.host].name+" авторизован",false,"good");
						console.log("-rcu.auth",GMMS.rcu.auth);
					})
					.fail(function(e){
						
						//если ошибка обращения к API - выходим
						//если просто не найдены сведения - авторизовываемся
						if(e.error === 0)
						{
							GMMS.func.log("Запись об авторизации не найдена - "+GMMS.rcu.host[e.host].name, true,"warn");
						}
						else {
							GMMS.func.log("Ошибка обращения к API", true,"error");
						}
					});
					
					
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
							GMMS.func.log("Успешная авторизация на хост "+GMMS.rcu.host[d.host].name, true,"good");
							GMMS.rcu.auth[d.host] = d.response;
							//сохраняем в БД
							GMMS.func.connection.set(d.host);
							GMMS.func.status(false,d.host);
							GMMS.func.icon("ready",d.host);
							console.log(GMMS.rcu.auth);
						}
						else
						{
							GMMS.func.log("Ошибка скрытой авторизации "+GMMS.rcu.host[d.host]["name"]+" (#"+d.error+")",true,"warn");
							GMMS.func.status(false,d.host);
							GMMS.func.icon("error",d.host);
							console.warn(d);
						}
						
					})
					.fail(function(e){
						GMMS.func.log("Ошибка обращения к API",true,"error");
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
			
			if(typeof GMMS.rcu.auth[data.host] === "undefined" || typeof GMMS.rcu.auth[data.host].cookie === "undefined")
			{
				GMMS.func.log("Нет данных об авторизации "+data.name,true,"error");
				GMMS.func.status(false,data.host);
				GMMS.func.icon("error",data.host);
				break;
			}
			
			switch(route[1])
			{
				case "devices":
				{
					switch(route[2])
					{
						case "update":
						{
							GMMS.func.rcu.function.devices.update(data.host);				

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
				GMMS.func.log("Нет данных об авторизации "+data.name,true,"error");
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
				
				case "inputSecondary":{
					
					GMMS.func.rcu.monitoring.inputSecondary(data.host);
					
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