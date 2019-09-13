/* 
функции кнопок в панели управления
*/
$( function(){ 

	/* действия на кликах меню */
	$(".panel-button").click(function(li){
		
		let data = $(this).data();
		route = data.route.split("/");

		let unix = new Date().getTime();
		GMMS.temp[unix] = {};
	
		switch(route[0]){
			
			case "rcu":{
				
				switch(route[1]) {
					case "auth":{
					
						break;
					}
					case "monitoring":{
						
						switch(route[2])
						{
						
							case "inputPrimary":{
								
								
								let mux = $("input[name=mux]:checked").val();
									
								GMMS.func.selected(function(host){
									
								GMMS.func.log(GMMS.rcu.host[host].name+": inputPrimary ["+mux+" mux] ", "log", host);
								GMMS.func.status("wait", host);
								GMMS.func.icon("wait", host);		
								GMMS.func.ajax.start();
								
									GMMS.func.autoAuth(host)
									.done(function(done_autoAuth){
										
										//console.log("autoAuth: ",done_autoAuth);
										//console.log("checkCookie: ",GMMS.func.checkCookie(done_autoAuth.host));
										
										GMMS.func.menu.rcu.monitoring.inputPrimary(done_autoAuth.host, {
											cookie: GMMS.func.checkCookie(done_autoAuth.host),
											mux: mux
										})
										.done(function(done_inputPrimary){
											
											if(!done_inputPrimary.error)
											{
												Object.values(done_inputPrimary.response.POST_callback.text_values).forEach(function(a){
													if(typeof GMMS.temp[unix][a] === "undefined")
														GMMS.temp[unix][a] = [];
													else	
														GMMS.temp[unix][a].push(done_inputPrimary.host);
												});
												
												GMMS.func.log(GMMS.rcu.host[done_inputPrimary.host].name+": "+done_inputPrimary.response.Info.represent,
																	"info", done_inputPrimary.host);
																	
												GMMS.func.status(done_inputPrimary.response.Info.represent, done_inputPrimary.host);
												
												GMMS.func.icon("ready",done_inputPrimary.host);
												
												GMMS.func.ajax.finish(function(){
													
													GMMS.func.log("Завершены все запросы", "info");
													
												});
											} else {
												
												GMMS.func.log(GMMS.rcu.host[done_inputPrimary.host].name+": "+
												done_inputPrimary.response.message+" #"+
												done_inputPrimary.error,
																"warn", done_inputPrimary.host, done_inputPrimary);
																	
												GMMS.func.status(false, done_inputPrimary.host);
												
												GMMS.func.icon("error",done_inputPrimary.host);
												GMMS.func.ajax.finish();
											}
											
											
										})
										.fail(function(fail_inputPrimary){
											
											GMMS.func.log(GMMS.rcu.host[fail_inputPrimary.host].name+": Ошибка обращения к API",
														"error", fail_inputPrimary.host, fail_inputPrimary);
											
											GMMS.func.status(false, done_inputPrimary.host);
										
											GMMS.func.icon("error",done_inputPrimary.host);
											
										});
										
										
										
									
									})
									.fail(function(fail_autoAuth){
										
										GMMS.func.log(GMMS.rcu.host[fail_autoAuth.host].name+": ошибка автоматической авторизации ", "error", fail_autoAuth.host, fail_autoAuth);
										GMMS.func.status("error", fail_autoAuth.host);
										GMMS.func.icon("error", fail_autoAuth.host);	
										
									});		
									
									
									
									
									
									
								});
								
								
								
								
								
								break;
							}
							
							
							default:{
							
								GMMS.func.log("undefined route[2] in panel", "warn",false,route);
								
							}
						}
						break;
					}
					default:{
						
						
						GMMS.func.log("undefined route[1] in panel", "warn",false,route);
					}
				}
				break;
			}
			
			default:{
				
				GMMS.func.log("undefined route[0] in panel", "warn",false,route);
			}
		}



		
	});





});