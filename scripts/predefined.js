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
					
					if(!d.error){
						if(d.response.length > 0) 
						{
							GMMS.rcu.deferred[host]["checkAuth"].resolveWith(GMMS.rcu.auth, [d.response[0]]);
							GMMS.rcu.auth[host] = d.response[0];
						}
						else	GMMS.rcu.deferred[host]["checkAuth"].rejectWith(GMMS.rcu.auth, [d]);
						
					}
					else{
						GMMS.rcu.deferred[host]["checkAuth"].rejectWith(GMMS.rcu.auth, [e]);					
					}
				})
				.fail(function(e){//ошибка обращения к API
					GMMS.func.log("Ошибка обращения к API",true,"error");
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
		
		log: function(message, toConsole = false, status = null){
			let date = new Date($.now());
			date = date.toTimeString().split(" ");
				if(toConsole === true) //дублируем в консоль
				{
					if(status === "error") console.error("Log: "+message);		
					else if(status === "warn") console.warn("Log: "+message);		
					else console.log("Log: "+message);		
				}
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
	
})