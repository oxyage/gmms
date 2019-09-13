/* 
route 
*/
$( function() {
	
	
	GMMS.func = {
		menu:{
			auth:{
				operator: function(host, userpass){
					window.open("http://"+host+"/config/devices/?username=operator&userpass="+userpass,"_blank");	
					return true;
				},
				admin: function(host, userpass){
					console.log("admin?");
					window.open("http://"+host+"/config/devices/?username=admin&userpass="+userpass,"_blank");
					return true;
				},
				check: function(host){
				
					if(typeof GMMS.rcu.auth[host] === "object" && typeof GMMS.rcu.auth[host].cookie === "string")
					{
						return true;
					}	
					return false;
				},
				blank: function(host){
					window.open("http://"+host+"/config/devices/","_blank");
					return true;
				}
			},
			rcu:{ // GMMS.func.menu.rcu
				table:{ // GMMS.func.menu.rcu.table

					parse: function(host){
						return $.post("api.php?route=rcu/parse",{
								host: host,
								cookie: GMMS.func.checkCookie(host)});
					}
				},
				monitoring:{// GMMS.func.menu.rcu.monitoring
					
					inputPrimary: 	function(host, device_info){	return GMMS.func.rcu.monitoring.inputPrimary(host,device_info); },
					inputSecondary: function(host, device_info){	return GMMS.func.rcu.monitoring.inputSecondary(host,device_info); }
					
						
						
					}
				
					
					
			}
		
		},
			
			
	
		db:{//gmms.func.db.select.rcu
			update:{
				rcu: {
					devices:function(host, device_info){
					
						return $.post("api.php?route=db/update/rcu",{
								host: host,
								rcu_name: device_info.rcu_name,
								devices_hash: device_info.devices_hash,
								devices_table: device_info.devices_table});

						}	
				}
			},
			select:{
				device: function(host, device_info){
					
					return $.post("api.php?route=db/select/device",{
									host: host,
									func: device_info.func,
									mux: device_info.mux
								});
				},
				rcu: function(){
					return $.get("api.php?route=db/select/rcu");
				}
			}
		},
		
		rcu:{
			device: function(host, device_info)
			{
				return $.post("api.php?route=rcu/device",{
					host: host,
					cookie: device_info.cookie,
					device: device_info.device,
					type_id: device_info.type_id,
					action: device_info.action							
				});
			},
			monitoring:{
				inputPrimary:function(host, device_info){
					
					return $.post("api.php?route=rcu/monitoring/inputPrimary",{
							host: host,
							cookie: device_info.cookie,
							mux: device_info.mux});
				},
				inputSecondary:function(host, device_info){
					
					return $.post("api.php?route=rcu/monitoring/inputSecondary",{
							host: host,
							cookie: device_info.cookie,
							mux: device_info.mux});
				}
				
				
				
			}
			
			
		},
		
/*

route[0]:
menu
db
rcu

*/
		

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
										GMMS.func.log("Успешная авторизация на хост "+GMMS.rcu.host[d.host].name, "info", d.host);
										GMMS.rcu.auth[d.host] = d.response;
										//сохраняем в БД
										GMMS.func.connection.set(d.host);
										GMMS.func.status(false,d.host);
										GMMS.func.icon("ready",d.host);
									//	console.log(GMMS.rcu.auth);
									}
									else
									{
										GMMS.func.log("Ошибка авторизации "+GMMS.rcu.host[d.host]["name"]+" (#"+d.error+")","warn",d.host,d);
										GMMS.func.status(false,d.host);
										GMMS.func.icon("error",d.host);
									//	console.warn(d);
									}
								
									
								})
								.fail(function(e){
									GMMS.func.log("Ошибка обращения к API","error",e.host,e);
								//	console.error(e);
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
	}
	/* предустановленные rcu */
	
	
})