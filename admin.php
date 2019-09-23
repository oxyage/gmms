<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<title>Администрирование GMMS</title>
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

		/* делаем вкладки */
		$("#panel-management").tabs({
			active: 0
		});
		
		
		/**/
		
		
		
		var dateFormat = "mm-dd-yy",
		from = $( "#from" )
		.datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 1,
		dateFormat: dateFormat,
		firstDay: 1
		})
		.on( "change", function() {
			to.datepicker( "option", "minDate", getDate( this ) );
		}),
		
		to = $( "#to" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1,
			dateFormat: dateFormat,
			firstDay: 1
			
		})
		.on( "change", function() {
			from.datepicker( "option", "maxDate", getDate( this ) );

		});

		function getDate( element ) {
			var date;
			try 
			{
				date = $.datepicker.parseDate( dateFormat, element.value );
			} catch( error ) 
			{
				date = null;
			}

			return date;
		}
		
		
		$("#show_log").click(function(){
			
			let start_date = new Date($("#from").val()+" 00:00:00").getTime()/1000;
			let finish_date = new Date($("#to").val()+" 00:00:00").getTime()/1000;
			
	
		});
	
		
	});
	</script>

</head>
<body>



<div class="tabs" id="panel-management">
  <ul>
    <li><a href="#panel-tabs-rcu">РТПС</a></li>
    <li><a href="#panel-tabs-log">Логи</a></li>
    <li><a href="#panel-tabs-devices">Устройства</a></li>
  </ul>
  <div id="panel-tabs-rcu">
  
  <form>  
	<input type="text" size='3' name='uid' value='23' readonly>
	<input type="text" size='8' name='host' value='10.32.1.2'>
	<input type="text" size='10' name='name' value='Брянск'>
	<input type="text" size='10' name='rcu_name' value='ЗИП Брянск'>
	<input type="text" size='10' name='name_eng' value='Bryansk'>
	<input type="text" size='10' name='sfn_name' value='Брянск'>
	<input type="text" size='10' name='sfn_eng' value='Bryansk'>
	<input type="text" size='3' name='sfn_uid' value='1'>
	<input type="text" size='5' name='coord' value='{"x":47, "y":98}'>
	<input type="text" size='5' name='auth' value='{}'>
	<input type="text" size='5' name='devices_table' value='{}'>
	<input type="text" size='5' name='devices_hash' value='{}'>
  
  </form>
  
  </div>
  <div id="panel-tabs-log">
<!--
SELECT * FROM `log` WHERE `timestamp` BETWEEN '2019-09-23 12:00:00' AND '2019-09-23 13:00:00' ORDER BY `uid` DESC
-->  
  
<label for="from">Начало</label> <input type="text" id="from" name="from" autocomplete="off">

<label for="to">конец </label> <input type="text" id="to" name="to" autocomplete="off"> 

<button id='show_log' class="ui-button ui-widget ui-corner-all"> Показать </button>
  
 
  
  
  </div>
  <div id="panel-tabs-devices">
  
  
  </div>
</div>
 
 
 
 




<?




?>



</body>
</head>