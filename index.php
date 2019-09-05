<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<title>Система группового управления</title>
	<!-- Мета-теги -->
	<meta charset="utf-8">
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
	
	<!-- Активация элементов на странице -->
	<script type="text/javascript" src="scripts/elements.js?<?=time()?>"></script>
	
	<!-- События на странице -->
	<script type="text/javascript" src="scripts/events.js?<?=time()?>"></script>
	
	<!-- Меню объекта связи и действия с ним -->
	<script type="text/javascript" src="scripts/menu.js?<?=time()?>"></script>
	

	
	<!--Пользовательская таблица стилей -->
	<link rel="stylesheet" href="style/style.css?12">

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
 
  <!-- Служебные функции -> -->
  <li class="">
    <div><span class="ui-icon ui-icon-tag"></span>Служебные функции</div>	
    <ul style="width:250px">
	
	<!-- Параметры -> -->
	<li class="ui-widget-header"><div>Функции</div></li>
		<li class="action ui-state-disabled" data-route="function/devices/update"> <div><span class="ui-icon ui-icon-arrowreturn-1-s"></span>Обновить таблицу СДК</div></li>
		<li class="action" data-route="function/devices/update"> <div><span class="ui-icon ui-icon-refresh"></span>Обновить устройства</div></li>
		<li class="action ui-state-disabled" data-route="function/connection/clear"><div>Сбросить соединение</div></li>
		<li class="action" data-route="auth/check"><div><span class=""></span>Проверка авторизации</div></li>
		<li class="action" data-route="auth/quiet"><div><span class="ui-icon ui-icon-key"></span>Скрытая авторизация</div></li>
	</ul>
  </li>
  
  <!-- Мониторинг параметров ui-state-disabled-> -->
  <li class="">
    <div><span class="ui-icon ui-icon-search"></span>Мониторинг</div>	
    <ul style="width:350px">
	
	<!-- Параметры -> -->
	<li class="ui-widget-header"><div>Параметры</div></li>
      <li>
        <div>Входной сигнал передатчика (основной) </div>
		<ul style="width:130px">
		
			<!-- Выбор мультиплекса -->
			<li class="ui-widget-header"><div>Мультиплекс</div></li>
			<li class="action" data-route="monitoring/inputPrimary/mux-1"><div>1 MUX</div></li>
			<li class="action" data-route="monitoring/inputPrimary/mux-2"><div>2 MUX</div></li>
		</ul>	
      </li>
	  <li>
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
  <li class="action" data-route="monitoring/complex">
    <div><span class="ui-icon ui-icon-signal-diag action"></span>Комплексная проверка</div>
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

<!--панель управления-->
<td id="panel" style="vertical-align:top; border-left:1px solid #000; background-color: #cecece;">

<div>
<button style="width: 100%;" class="ui-button ui-widget ui-corner-all gmms"><span class="ui-icon ui-icon-gear"></span> GMMS </button>
</div>


<hr>

<!-- Выбор объектов связи -->
<div id="panel-select"><!-- start panel select-->
	<h3> Выбрать объекты связи</h3>
	<div style="text-align:center"> <!-- start container panel select-->
		<label for="checkbox-select-all" style="width:70%; font-weight: bold;">Выбрать все</label>
		<input type="checkbox" name="select-all" id="checkbox-select-all">
		<hr>
		<div id="container-select-all" style="text-align:left"></div>
	</div>  <!--end container panel-select-->
</div><!-- end panel-select-->

<hr>

<div id="panel-tabs">
  <ul>
    <li><a href="#panel-tabs-monitoring"><span class="ui-icon ui-icon-search"></span> Мониторинг</a></li>
    <li><a href="#panel-tabs-sunoutage"><span class="ui-icon ui-icon-calendar"></span> Интерференция</a></li>
    <li><a href="#panel-tabs-additional"><span class="ui-icon ui-icon-wrench"></span> Дополнительно</a></li>
  </ul>
  <div id="panel-tabs-monitoring">
    Мониторинг сигналов ASI<br>
	Активный вход спутниковый
  </div>
  <div id="panel-tabs-sunoutage">
    Переход на 53 и 40 градус раздельно по мультиплексам
  </div>
  <div id="panel-tabs-additional">
   
   Включение выключение замены PLP<br>

<label for="show-rts-names">Имена объектов связи</label>
<input type="checkbox" name="" id="show-rts-names"> 

<br>
<br>

<label for="auto-auth">Автоматическая авторизация</label>
<input type="checkbox" name="" id="auto-auth"> 
  
  
  
  </div>
</div>

<hr>

<div id="panel-log">
  <h2> Журнал выполнения команд</h2>
  <div style="max-height:300px;"></div>
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