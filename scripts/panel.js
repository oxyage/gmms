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
			case "system":{
				
				switch(route[1]) {
					
					case "networkID1":{
						
						GMMS.func.selected(function(host){
							
							GMMS.func.log(GMMS.rcu.host[host].name+": network ID [1] ", "log", host);
							GMMS.func.status("wait", host);
							GMMS.func.icon("wait", host);		
							GMMS.func.ajax.start();
							
							GMMS.func.api({
								route: "system",
								host: host
							})
							.done(function(done_networkID1){
								
								
								GMMS.func.log(GMMS.rcu.host[done_networkID1.host].name+": "+done_networkID1.response,
													"info", done_networkID1.host);
													
								GMMS.func.status(done_networkID1.response, done_networkID1.host);
								
								GMMS.func.icon("ready",done_networkID1.host);
								
							})
							.fail(function(fail_networkID1){
								
							});
								
							
							
							
						});
						
						break;
					}


				
					default:{
						GMMS.func.log("undefined route[1] in panel", "warn",false,route);
					}
				}
				
				break;
			}
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
											
											GMMS.func.status(false, fail_inputPrimary.host);
										
											GMMS.func.icon("error",fail_inputPrimary.host);
											GMMS.func.ajax.finish();
											
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

						}
						break;
					}
					case "management":{
								
						let mux = $("input[name=mux_management]:checked").val();		
								
						switch(route[2])
						{
						
							case "goto40":{
							
								if(!GMMS.func.confirm("Вы собираетесь перевести передатчики на основной спутник 40°. Подтвердите действие", 
								function(){
									console.log("Подтвержден переход на 40°");
									return true;
								}, 
								function(){
									console.warn("Не подтвержден переход на 40°");
									return false;
								})) 
									break;
								
							
							
								
								GMMS.func.selected(function(host){
									
								GMMS.func.log(GMMS.rcu.host[host].name+": goto40° ["+mux+" mux] ", "log", host);
								GMMS.func.status("wait", host);
								GMMS.func.icon("wait", host);		
								GMMS.func.ajax.start();
								
									GMMS.func.autoAuth(host)
									.done(function(done_autoAuth){
										
										GMMS.func.rcu.management.goto40(done_autoAuth.host, {
											cookie: GMMS.func.checkCookie(done_autoAuth.host),
											mux: mux
										})
										.done(function(done_goto40){
											
											
											if(!done_goto40.error)
											{
											
												console.log(done_goto40.response);
												
												GMMS.func.log(GMMS.rcu.host[done_goto40.host].name+": "+done_goto40.response.Info.represent,
																	"info", done_goto40.host);
																	
												GMMS.func.status(done_goto40.response.Info.represent, done_goto40.host);
												
												GMMS.func.icon("ready",done_goto40.host);
												
												GMMS.func.ajax.finish(function(){
													
													GMMS.func.log("Завершены все запросы", "info");
													
												});
											} else {
												
												GMMS.func.log(GMMS.rcu.host[done_goto40.host].name+": "+
												done_goto40.response.message+" #"+
												done_goto40.error,
																"warn", done_goto40.host, done_goto40);
																	
												GMMS.func.status(false, done_goto40.host);
												
												GMMS.func.icon("error",done_goto40.host);
												GMMS.func.ajax.finish();
											}									
										})
										.fail(function(fail_goto40){
											
											GMMS.func.log(GMMS.rcu.host[fail_goto40.host].name+": Ошибка обращения к API",
														"error", fail_goto40.host, fail_goto40);
											
											GMMS.func.status(false, fail_goto40.host);
										
											GMMS.func.icon("error",fail_goto40.host);
											GMMS.func.ajax.finish();
											
										});
										
										
									})
									.fail(function(fail_autoAuth){
										
										GMMS.func.log(GMMS.rcu.host[fail_autoAuth.host].name+": ошибка автоматической авторизации ", "error", fail_autoAuth.host, fail_autoAuth);
										GMMS.func.status("error", fail_autoAuth.host);
										GMMS.func.icon("error", fail_autoAuth.host);
										GMMS.func.ajax.finish();
									});
								
								});

							
							break;
							}
							case "goto53":{
							
								let accept = GMMS.func.confirm("Вы собираетесь перевести передатчики на резервный спутник 53°. Подтвердите действие", 
								function(){
									console.log("Подтвержден переход на 53°");
									return true;
								}, 
								function(){
									console.warn("Не подтвержден переход на 53°");
									return false;
								});
								
								if(!accept) break;
								
								
								GMMS.func.selected(function(host){
									
								GMMS.func.log(GMMS.rcu.host[host].name+": goto53° ["+mux+" mux] ", "log", host);
								GMMS.func.status("wait", host);
								GMMS.func.icon("wait", host);		
								GMMS.func.ajax.start();
								
									GMMS.func.autoAuth(host)
									.done(function(done_autoAuth){
										
										GMMS.func.rcu.management.goto53(done_autoAuth.host, {
											cookie: GMMS.func.checkCookie(done_autoAuth.host),
											mux: mux
										})
										.done(function(done_goto53){
											
											
											if(!done_goto53.error)
											{
												console.log(done_goto53.response);
												
												GMMS.func.log(GMMS.rcu.host[done_goto53.host].name+": "+done_goto53.response.Info.represent,
																	"info", done_goto53.host);
																	
												GMMS.func.status(done_goto53.response.Info.represent, done_goto53.host);
												
												GMMS.func.icon("ready",done_goto53.host);
												
												GMMS.func.ajax.finish(function(){
													
													GMMS.func.log("Завершены все запросы", "info");
													
												});
											} else {
												
												GMMS.func.log(GMMS.rcu.host[done_goto53.host].name+": "+
												done_goto53.response.message+" #"+
												done_goto53.error,
																"warn", done_goto53.host, done_goto53);
																	
												GMMS.func.status(false, done_goto53.host);
												
												GMMS.func.icon("error",done_goto53.host);
												GMMS.func.ajax.finish();
											}									
										})
										.fail(function(fail_goto53){
											
											GMMS.func.log(GMMS.rcu.host[fail_goto53.host].name+": Ошибка обращения к API",
														"error", fail_goto53.host, fail_goto53);
											
											GMMS.func.status(false, fail_goto53.host);
										
											GMMS.func.icon("error",fail_goto53.host);
											GMMS.func.ajax.finish();
											
										});
										
										
									})
									.fail(function(fail_autoAuth){
										
										GMMS.func.log(GMMS.rcu.host[fail_autoAuth.host].name+": ошибка автоматической авторизации ", "error", fail_autoAuth.host, fail_autoAuth);
										GMMS.func.status("error", fail_autoAuth.host);
										GMMS.func.icon("error", fail_autoAuth.host);
										GMMS.func.ajax.finish();
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