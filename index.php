<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<title>Система группового управления</title>
	<!-- Мета-теги -->
	<meta charset="utf-8">
	<!-- <meta http-equiv="refresh" content="10"> -->
	
	<link rel="shortcut icon" href="style/images/favicon.ico" type="image/x-icon">
	
	<!--Файлы поставщиков /vendor -->
	<!-- jQuery -->
	<!--<script type="text/javascript" src="/vendor/jquery-3.4.1.js"></script>-->
	<script type="text/javascript" src="vendor/jquery-3.4.1.min.js"></script>
	
	<!-- jQuery UI-->
	<!--<script type="text/javascript" src="vendor/jquery-ui.js"></script>-->
	<script type="text/javascript" src="vendor/jquery-ui.min.js"></script>
	<!--<link rel="stylesheet" href="vendor/jquery-ui.css">-->
	<link rel="stylesheet" href="vendor/jquery-ui.min.css?<?=time()?>">
	
	<!-- jQuery Cookie -->
	<script type="text/javascript" src="vendor/jquery.cookie.js"></script>
		
		
	<!--Пользовательские скрипты --->
	<!--Служебные функции --->
	<script type="text/javascript" src="scripts/main.js?<?=time()?>"></script>
	
	<!--<script type="text/javascript" src="scripts/jqonly.js?1142221"></script>-->
	
	<!-- Предустановленные переменные и функции -->
	<script type="text/javascript" src="scripts/predefined.js?<?=time()?>"></script>
	
	<!-- Элементарные функции -->
	<script type="text/javascript" src="scripts/functions.js?<?=time()?>"></script>
	
	<!-- Активация элементов на странице -->
	<script type="text/javascript" src="scripts/elements.js?<?=time()?>"></script>
	
	<!-- События на странице -->
	<script type="text/javascript" src="scripts/events.js?<?=time()?>"></script>
	
	<!-- Кнопки на панели управления -->
	<script type="text/javascript" src="scripts/panel.js?<?=time()?>"></script>
	
	<!-- Меню объекта связи и действия с ним -->
	<script type="text/javascript" src="scripts/menu.js?<?=time()?>"></script>
	

	
	<!--Пользовательская таблица стилей -->
	<link rel="stylesheet" href="style/style.css?<?=time()?>">

	<!-- Убрать в соответствующий файл -->
	<script>
	
	$( function() {
	/* для отладки*/
	  
	  
	/* для поддержания соединения */  
	setInterval(function(){
	  console.log("Обновляем +300 сек");
	}, 300000);

	console.log("index.html");
		
	});
	</script>

</head>
<body>

<!-- Диалоговое окно ожидания-->
<div id="dialog-wait" title="Пожалуйста, подождите" style="text-align:center;">
  <p>Загрузка объектов связи<br><img src="style/images/loader_opta.gif"></p>
  
</div>



<!-- Строим на таблице для удобства масштабирования-->
<table border="0" width="100%" style="min-height:1010px; min-width:1600px; max-width:2000px;">
<tr>

<!---------- КАРТА ------------->
<td id="wrapper-map" style="vertical-align:top; width: 1130px; border-right:1px solid #000;">
<div id="container-map" style="background:url('style/images/maps.png') no-repeat; height:1000px; width:1130px; top:0; position: relative;">

</div>
<!---@ end КАРТА-->





<!-- Диалоговое окно внутри карты -->
<div id="dialog-map" style="" title="Диалоговое окно внутри карты">
	<p>Диалоговое окно только внутри карты</p>
</div>




<!-- Меню объекта связи -->
<ul id="menu" data-name="" data-host="" >
	<!-- Заголовок -->
  <li class="ui-widget-header"><div id="menu-header">Имя РТПС</div></li>
  
  <!-- Перейти в СДК -> -->
  <li>
    <div><span class="ui-icon ui-icon-extlink"></span>Перейти в СДК</div>
	 <ul style="width:200px">
	 
	 <!-- Выбрать привилегии -->
	 <li class="ui-widget-header"><div>Привилегии</div></li>
      <li class="action" data-route="auth/operator" title="Авторизоваться как оператор">
        <div><span class="ui-icon ui-icon-person"></span>Оператор</div>
      </li>
	 <li class="action" data-route="auth/admin" title="Войти с правами администратора">
        <div><span class="ui-icon ui-icon-alert"></span>Администратор</div>
      </li>
	  <li class="action" data-route="auth/" title="Перейти на страницу авторизации">
        <div><span class="ui-icon ui-icon-extlink"></span>Страница входа</div>
      </li>
    </ul>
  </li>
  
   <!-- Перейти в 2+1 СДК -> -->
  <li style="display:none" id="srp">
    <div><span class="ui-icon ui-icon-extlink"></span>Перейти в СРП 2+1</div>
	 <ul style="width:200px">
	 
	 <!-- Выбрать привилегии -->
	 <li class="ui-widget-header"><div>Привилегии</div></li>
      <li class="action" data-route="auth-srp/operator" title="Авторизоваться как оператор">
        <div><span class="ui-icon ui-icon-person"></span>Оператор</div>
      </li>
	 <li class="action" data-route="auth-srp/admin" title="Войти с правами администратора">
        <div><span class="ui-icon ui-icon-alert"></span>Администратор</div>
      </li>
	  <li class="action" data-route="auth-srp/" title="Перейти на страницу авторизации">
        <div><span class="ui-icon ui-icon-extlink"></span>Страница входа</div>
      </li>
    </ul>
  </li>
 
  <!-- Служебные функции -> -->
  <li class="" style="display:none">
    <div><span class="ui-icon ui-icon-tag"></span>Служебные функции</div>	
    <ul style="width:250px">
	
	<!-- Параметры -> -->
	<li class="ui-widget-header"><div>Функции</div></li>
		<li class="action ui-state-disabled" data-route="rcu/devices/update"> <div><span class="ui-icon ui-icon-arrowreturn-1-s"></span>Обновить устройства</div></li>
		<li class="action" data-route="rcu/table/update"> <div><span class="ui-icon ui-icon-refresh"></span>Обновить таблицу СДК</div></li>
		<li class="action ui-state-disabled" data-route="function/connection/clear"><div>Сбросить соединение</div></li>
		<li class="action ui-state-disabled" data-route="auth/check"><div><span class=""></span>Проверка авторизации</div></li>
		<li class="action" data-route="auth/quiet"><div><span class="ui-icon ui-icon-key"></span>Скрытая авторизация</div></li>
	</ul>
  </li>
  
  <!-- Мониторинг параметров ui-state-disabled-> -->
  <li class=""  style="display:none">
    <div><span class="ui-icon ui-icon-search"></span>Мониторинг</div>	
    <ul style="width:350px">
	

	<!-- Параметры -> -->
	<li class="ui-widget-header"><div>Параметры</div></li>
      <li>
        <div>Входной сигнал передатчика (основной) </div>
		<ul style="width:130px">
		
			<!-- Выбор мультиплекса -->
			<li class="ui-widget-header"><div>Мультиплекс</div></li>
			<li class="action" data-route="rcu/monitoring/inputPrimary/1"><div>1 MUX</div></li>
			<li class="action" data-route="rcu/monitoring/inputPrimary/2"><div>2 MUX</div></li>
		</ul>	
      </li>
	  <li class=" ui-state-disabled">
        <div>Входной сигнал передатчика (дополнительный)</div>
		<ul style="width:130px">
		
			<!-- Выбор мультиплекса -->
			<li class="ui-widget-header"><div>Мультиплекс</div></li>
			<li class="action" data-route="monitoring/inputSecondary/mux-1"><div>1 MUX</div></li>
			<li class="action" data-route="monitoring/inputSecondary/mux-2"><div>2 MUX</div></li>
		</ul>	
      </li>
	  <li>
        <div>Синхронизация SFN</div>
		<ul style="width:130px">
		
			<!-- Выбор мультиплекса -->
			<li class="ui-widget-header"><div>Мультиплекс</div></li>
			<li class="action" data-route="monitoring/sfn/mux-1"><div>1 MUX</div></li>
			<li class="action" data-route="monitoring/sfn/mux-2"><div>2 MUX</div></li>
		</ul>
      </li>
	  <li class="action" data-route="monitoring/temperature/rcu"><div>Температура СДК</div></li>
    </ul>
  </li>
  
  <!-- Комплексная проверка всех показателей-->
  <li class="" data-route="monitoring/complex"  style="display:none">
    <div><span class="ui-icon ui-icon-signal-diag"></span>Комплексная проверка</div>
	<ul style="width:130px">
			<!-- Выбор мультиплекса -->
			<li class="ui-widget-header"><div>Результаты проверки</div></li>
			<div>
			Исправить стиль <br>
			Здесь будут отображаться результаты проверки всех систем и устройств, которые будут заданы далее
			</div>
			
		</ul>
  </li>
</ul>


<!-- Прогрессбар -->
<div class="progressbar" style="width:50px; height:10px;"></div>






</td>

<!-- ################################################################################################################### -->
<!--Панель управления-->
<td id="panel" style="vertical-align:top; border-left:1px solid #000; background-color: #cecece;">

<div>
<button style="width: 100%;" class="ui-button ui-widget ui-corner-all gmms"><span class="ui-icon ui-icon-gear"></span> GMMS </button>
</div>


<hr>


<div class="tabs" id="panel-tabs">
  <ul>
    <li><a href="#panel-tabs-monitoring"><span class="ui-icon ui-icon-search"></span> Мониторинг</a></li>
    <li><a href="#panel-tabs-sunoutage"><span class="ui-icon ui-icon-calendar"></span> Интерференция</a></li>
    <li><a href="#panel-tabs-additional"><span class="ui-icon ui-icon-wrench"></span> Дополнительно</a></li>
  </ul>
  
  
  <div id="panel-tabs-monitoring">
 
 
<div class="accordion" id="panel-monitoring" ><!-- start panel select-->
	<h3>Мониторинг параметров передатчиков</h3>
	<div style="padding-bottom:3.5em;">
		<div style="text-align:center; padding: 0; "> 
			<label for="1mux">1 мультиплекс</label>
			<input class="panel-radio" name="mux" type="radio" id="1mux" checked value="1" data-route=""> 
		  
			<label for="2mux">2 мультиплекс</label>
			<input class="panel-radio" name="mux" type="radio" id="2mux" value="2" data-route=""> 
		</div>
		<hr>
		<button class="panel-button" data-route="rcu/monitoring/inputPrimary" >Основной спутниковый вход </button>
		
	
	
	
	
	</div>
	<h3>Мониторинг реплейсеров</h3>
	<div style="">
	
		<button class="panel-button" data-route="system/networkid1" >NetworkID федеральный</button>
		<button class="panel-button" data-route="system/networkid2" >NetworkID региональный</button> <br><br>
		<button class="panel-button" data-route="system/main_delay" >Общая задержка</button>
		<button class="panel-button" data-route="system/leading_source" >Leading Source</button><br><br>
		<button class="panel-button" data-route="system/offset_stream" >Offset Streams</button><br><br>
		<button class="panel-button" data-route="system/status_replacement" >Статус замещения</button>
		<button class="panel-button" data-route="system/version_sw" >Версия прошивки</button>
	
	</div>
	
	<h3>Мониторинг приемников</h3>
	<div style="">
		
		<h2> Ericsson RX8330 - федеральный (1 MUX)</h2>
		<button class="panel-button" data-route="system/federal_rx_rf1_freq" >Частота</button>
		<button class="panel-button" data-route="system/federal_rx_rf1_symrate" >Символьная скорость</button>
		<button class="panel-button" data-route="system/federal_rx_rf1_status" >Статус захвата сигнала</button>
		<hr>
		<!--
		<h2> RTM TRK-555 - региональный</h2>
		<button class="panel-button" data-route="system/regional_rx_rf1_freq" >Частота</button>
		<button class="panel-button" data-route="system/regional_rx_rf1_symrate" >Символьная скорость</button>
		<button class="panel-button" data-route="system/regional_rx_rf1_status" >Уровень захвата сигнала</button> 
		<br>
		<button class="panel-button" data-route="system/get_margin_1" data-accept="Запрос LKM 1 тюнер">Link Margin 1 тюнер</button>
		<button class="panel-button" data-route="system/get_margin_2" data-accept="Запрос LKM 2 тюнер">Link Margin 2 тюнер</button>
		<br>	
		<button class="panel-button" data-route="system/get_asi_out_1" >ASI Out 1 (региональный 40°)</button>
		<br>
		<button class="panel-button" data-route="system/get_asi_out_2" >ASI Out 2 (резервный 53°)</button>
		<br>
		<button class="panel-button" data-route="system/get_asi_out_3" >ASI Out 3 </button>
		<br>
		<button class="panel-button" data-route="system/get_asi_out_4" >ASI Out 4 (Радио России)</button>
		<br>
		-->
			
	</div>
	
	<h3>Мониторинг сплайсеров</h3>
	<div style="">
		<button class="panel-button" data-route="system/splicer_get_bypass" >Проверка режима Bypass</button> <!-- 1.3.6.1.4.1.49675.21.0  -->
		<button class="panel-button" data-route="system/splicer_get_count_plp">Число PLP IN_0</button><!-- 1.3.6.1.4.1.49675.49.1.5.1  -->
		<button class="panel-button" data-route="system/splicer_get_in0_confcurrbr">ASI IN_0 Bitrate</button><!-- 1.3.6.1.4.1.49675.49.1.7.1  -  1.3.6.1.4.1.49675.49.1.8.1 -->
		<br><br>
		<button class="panel-button" data-route="system/splicer_get_confbr" >Configure Bitrate</button> <!-- 1.3.6.1.4.1.49675.51.1.5.1  1.3.6.1.4.1.49675.51.1.5.2  -->
		<button class="panel-button" data-route="system/splicer_get_currbr" >Current Bitrate</button> <!--   1.3.6.1.4.1.49675.51.1.6.1  1.3.6.1.4.1.49675.51.1.6.2 -->
		<button class="panel-button" data-route="system/splicer_get_diff_confcurr" >Разница между ConfBr/CurrBr</button>
		<br><br>			
	</div>
	
	
</div>
 
 
	<hr>

  </div>
  <div id="panel-tabs-sunoutage">
 
<div style="text-align:center; padding: 0; "> 
<label for="1mux_management">1 мультиплекс</label>
<input class="panel-radio" name="mux_management" type="radio" id="1mux_management" checked value="1" data-route=""> 
		  
<label for="2mux_management">2 мультиплекс</label>
<input class="panel-radio" name="mux_management" type="radio" id="2mux_management" value="2" data-route=""> 
  
</div>
<hr>  
<button class="panel-button" data-route="rcu/management/goto40" >Перейти на 40° основной спутник</button>

<button class="panel-button" data-route="rcu/management/goto53" >Перейти на 53° резервный спутник</button>
 
  </div>
  
  
  <div id="panel-tabs-additional" >
   
   <div class="accordion" id="panel-additional" ><!-- start panel select-->
   <h3>Управление реплейсерами</h3>
	<div style="padding-bottom:3.5em;">
	<br><br>
		<button class="panel-button" data-route="system/replacementOFF" >Отключить замещение</button>
		<button class="panel-button" data-route="system/replacementON" >Включить замещение</button> <br><br>
	</div>
	
	
	
   </div>
   
   

  
  </div>
</div>

<hr>




<!-- Выбор объектов связи -->
<div class="accordion" id="panel-select"><!-- start panel select-->
	<h3> Список объектов связи</h3>
	<div style="text-align:center"> <!-- start container panel select-->
		<label for="checkbox-select-all" style="width:70%; font-weight: bold;">Выбрать все</label>
		<input type="checkbox" name="select-all" id="checkbox-select-all">
		
		<div id="container-select-all" style="text-align:right">
		<hr>
		</div>
	</div>  <!--end container panel-select-->
</div><!-- end panel-select-->

<hr>


<div id="panel-log">
  <h2> Журнал выполнения команд</h2>
  <div style="max-height:300px;" class="journal"></div>
</div>

</td>
</tr>
</table>



<!-- Диалоговое окно -->
<div id="dialog" title="Диалоговое окно">
  <p>Диалог</p>
</div>



</body>
</head>