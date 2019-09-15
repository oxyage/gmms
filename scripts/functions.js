$( function() { /* только предустановленные функции */

	GMMS.func.icon = function(state, host = false){

		if(typeof host === "object")
		{
			for(let _host of Object.keys(host))
			{
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
	},
	/*
		GMMS.func.status() - обновить все статусы на дефолтные
	*/
	GMMS.func.status = function(state = false, host = false){
			
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
	
	GMMS.func.select = function(target, data, checked){ //выбор объектов связи
			
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
				GMMS.rcu.select = Object.keys(GMMS.rcu.host); //добавляем в gmms.rcu
				GMMS.func.icon("select"); //ставим иконки на select
				$("#container-select-all").hide(); //скрываем контейнер с выбором РТПС
				$.cookie("select", "all"); //добавляем в куки
			} else {
				
				GMMS.func.log("Снят выбор всех РТПС"); //логгируем
				GMMS.rcu.select = []; // очищаем gmms.rcu
				GMMS.func.icon("default"); // обновляем иконки на дефолтные
				$("#container-select-all").show(); //показываем контейнер с выбором РТПС...
				$("#container-select-all fieldset div").show(); // ... и в нем открываем все контейнеры ОЧС
				$.removeCookie("select"); //очищаем куки
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
	
	GMMS.func.selected = function(callback){
		
		if(GMMS.rcu.select.length === 0) //если выбранных объектов нет
		{
			if(Object.keys(GMMS.rcu.auth).length < 1){ //и нет авторизованных - выводим ошибку
				GMMS.func.log("Объекты связи не выбраны", "error");
				return false;
			}
			else{ //если есть авторизованные - запускаем в них
			
				GMMS.func.log("Запускаем команду на авторизованных объектах связи", "warn");
				for(host of Object.keys(GMMS.rcu.auth))
				{
					callback(host);
				}
				return true;
			}
		}
		
		//сюда приходим если есть выбранные объекты связи, на них запускаем коллбэк
		for(host of GMMS.rcu.select)
		{
			callback(host);
		}
	},
	GMMS.func.ajax = {},
	GMMS.func.ajax.start = function(){
		
		if(GMMS.rcu.select.length === 0) //нет выбранных
		{
			if(Object.keys(GMMS.rcu.auth).length < 1){ //и нет авторизованных - выводим ошибку
				GMMS.rcu.ajax = 0;
				return false;
			}
			
			GMMS.rcu.ajax = Object.keys(GMMS.rcu.auth).length; //итерируемое значение для контроля ?
			
		} else {
			
			GMMS.rcu.ajax = GMMS.rcu.select.length; //итерируемое значение для контроля ?
			
		}
		
		console.log("ajax start", GMMS.rcu.ajax);
	},
	GMMS.func.ajax.finish = function(callback = function(a = false){console.log("Завершены все ajax запросы",a);}){
		if(GMMS.rcu.ajax > 1) 
		{
			GMMS.rcu.ajax -= 1;
			console.log("ajax continue", GMMS.rcu.ajax);
			return GMMS.rcu.ajax;
		}
		else //все закончились
		{
			console.log("ajax finish", GMMS.rcu.ajax);
			callback();
			return GMMS.rcu.ajax;
		}
	},
	GMMS.func.connection = {
		set: function(host){
			return $.post("api.php?route=db/insert/connection", GMMS.rcu.auth[host]);
		},
		find: function(){
			return $.post("api.php?route=db/select/connection");
		},
		get: function(host){
			return $.post("api.php?route=db/select/connection/host", {host: host});
		}
	},
	
	GMMS.func.auth = function(host){ // не передавать параметры имени пользователя и пароля
	
		//вернуть deferred объект с результатом авторизации
		return $.post("api.php?route=rcu/auth",{
			host: host,
			username: "admin", //исправить, тут должно быть не так
			userpass: GMMS.rcu.host[host]["auth"]["admin"].userpass
		});
	},
	
	GMMS.func.autoAuth = function(host) //проверка авторизации с автоматической авторизацией
	{
		if(typeof GMMS.rcu.deferred[host] === "undefined"){
			GMMS.rcu.deferred[host] = {};
			GMMS.rcu.deferred[host].autoAuth = $.Deferred();
		}
		else{
			GMMS.rcu.deferred[host].autoAuth = $.Deferred();
		}

		
		//если уже авторизован на странице - возвращаем успех
		if(typeof GMMS.rcu.auth[host] === "object" && typeof GMMS.rcu.auth[host].cookie === "string")
		{
			GMMS.rcu.deferred[host].autoAuth.resolveWith(false, [GMMS.rcu.auth[host]]);
		}	
		//иначе - авторизация
		else{
			
			GMMS.func.auth(host)
			.done(function(data){
				if(!data.error){ // error = 0
					GMMS.rcu.auth[data.host] = data.response;
					GMMS.func.connection.set(data.host);
					GMMS.rcu.deferred[host].autoAuth.resolveWith(false, [data.response]);
				}
				else{ //error > 0
					GMMS.rcu.deferred[host].autoAuth.rejectWith(false, [data]);
				}
			})
			.fail(function(error){ //critical error
				GMMS.rcu.deferred[host].autoAuth.rejectWith(false, [error]);
			});
			
		}

		
		return GMMS.rcu.deferred[host].autoAuth;
	},
	
	GMMS.func.confirm = function(question, callback_done = false, callback_fail = false){
	
		if(confirm(question))			
			return (callback_done == false) ? true : callback_done();
		else		
			return (callback_fail == false) ? false : callback_fail();
		
	
	},
	//GMMS.func.log(message, status = "log", host = 0, object = {})
	GMMS.func.log = function(message, status = "log", host = 0, object = {}){
	
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
	
	GMMS.func.api = function(data){
		return $.post("api.php?route="+data.route, data);
	},
	
	GMMS.func.checkCookie = function(host){
		return typeof GMMS.rcu.auth[host] === "object" && typeof GMMS.rcu.auth[host].cookie === "string" && GMMS.rcu.auth[host].cookie;
	}


	
		
	


})