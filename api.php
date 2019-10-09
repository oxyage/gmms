<?

ini_set('display_errors', 1);//выводить ошибки
ini_set('display_startup_errors', 1);//выводить ошибки startup
ini_set('error_reporting', E_ALL);//выводить ошибки
ini_set('default_charset', "UTF-8");//нормальная кодировка


define("DIRSEP", DIRECTORY_SEPARATOR);
define('SITE_PATH', realpath(dirname(__FILE__).DIRSEP."..".DIRSEP).DIRSEP."gmms".DIRSEP); //корневая папка
define('CLASSES_PATH', SITE_PATH."classes".DIRSEP); // папка с классами
define('CONFIG_PATH', SITE_PATH."config".DIRSEP); // папка с настройками
define('TEMPLATES_PATH', CLASSES_PATH."templates".DIRSEP); //папка с шаблонами устройств

/*
класс для взаимодействия сервера с удаленным СДК по HTTP

http://10.32.1.3/gmms/api.php?route=rcu/auth&host=10.32.1.2&username=admin&userpass=kq9OXFVTKb&debug

#Доступные route

rcu/auth
rcu/post
rcu/parse

db/select/rcu
db/update/rcu

*/

/*
коды ошибок API
10
11
12
13
14
15


*/

//если включаем режим отладки
$Debug = isset($_REQUEST["debug"]) ? true : false;

header("Charset=utf-8"); //нормальная кодировка

if($Debug)	header("Content-Type: text/html");
else		header("Content-Type: application/json");


//обрабатываем маршрут
$Route = empty($_REQUEST["route"]) ? "index" : trim($_REQUEST["route"]);
$Route = explode("/",$Route);
/*
$Route[0] - mode
$Route[1] - action
$Route[2] - reserved
*/

$API = new API;

if($Debug) $API->debug = true;

$API->host = !empty($_REQUEST["host"]) ?  $_REQUEST["host"] : false;

$db_ini = parse_ini_file(CONFIG_PATH."db.ini");
//подключаем класс бд
$API->module(CLASSES_PATH."class.db.php");
//создаем экземпляр класса db
$db = new db($db_ini);
$db->connect(); //соединеняемся с БД и выбираем базу данных

switch($Route[0])
{
	case "system":
	{
		try	{
			API::checkArgs("host");
		}
		catch(Exception $e)
		{
			$API($e); break;
		}

		$input = array("host"=>$_REQUEST["host"]);

		switch($Route[1])
		{
			case "networkid1":{

				$getNetworkID_1 = exec("snmpget -m 0  -L n -c private -v 2c -t 8 ".$input["host"].":8001 1.3.6.1.4.1.22909.1.3.13.1.3.1.1.1");
				$getNetworkID_1 = explode(" = ", $getNetworkID_1);
				$getNetworkID_1 = $getNetworkID_1[1];
				$API($getNetworkID_1);
				break;
			}
			case "networkid2":{

				$getNetworkID_2 = 		exec("snmpget -m 0  -L n -c private -v 2c -t 8 ".$input["host"].":8001 1.3.6.1.4.1.22909.1.3.13.1.3.1.1.2");
				$getNetworkID_2 = explode(" = ", $getNetworkID_2);
				$getNetworkID_2 = $getNetworkID_2[1];
				$API($getNetworkID_2);
				break;
			}
			case "main_delay":{
				$getMainDelay = 		exec("snmpget -m 0  -L n -c private -v 2c -t 8 ".$input["host"].":8001 1.3.6.1.4.1.22909.1.3.15.1.1.1.11.9");
				$getMainDelay = explode(" = ", $getMainDelay);
				$getMainDelay = $getMainDelay[1];
				$API($getMainDelay);
				break;
			}
			case "leading_source":{
				$getLeadingSource = 	exec("snmpget -m 0  -L n -c private -v 2c -t 8 ".$input["host"].":8001 1.3.6.1.4.1.22909.1.3.15.1.1.1.12.9");
				$getLeadingSource = explode(" = ", $getLeadingSource);
				$getLeadingSource = $getLeadingSource[1];
				$API($getLeadingSource);
				break;
			}
			case "leading_source_delay":{
				$getLeadingSourceDelay = exec("snmpget -m 0  -L n -c private -v 2c -t 8 ".$input["host"].":8001 1.3.6.1.4.1.22909.1.3.15.1.1.1.13.9");
				$getLeadingSourceDelay = explode(" = ", $getLeadingSourceDelay);
				$getLeadingSourceDelay = $getLeadingSourceDelay[1];
				$API($getLeadingSourceDelay);
				break;
			}
			default:{
				$API("Undefined system/Route[1]");
			}
		}
		//Network ID replacer

		#getNetworkID_2 = exec("snmpget -m 0  -L n -c private -v 2c -t 8 ".$input["host"].":8001 1.3.6.1.4.1.22909.1.3.13.1.3.1.1.2");

		#echo "IP адрес СДК: 10.32.%%i.2 | Параметр Main delay (Задержка)"
		#snmpget -m 0  -L n -c private -v 2c -t 8 10.32.%%i.2:8001 1.3.6.1.4.1.22909.1.3.15.1.1.1.11.9

		#echo "IP адрес СДК: 10.32.%%i.2 | Параметр Leading Source (Основной поток, 2. IN)"
		#snmpget -m 0  -L n -c private -v 2c -t 8 10.32.%%i.2:8001 1.3.6.1.4.1.22909.1.3.15.1.1.1.12.9

		#echo "IP адрес СДК: 10.32.%%i.2 | Параметр Leading Source Delay (Задержка основного потока, ~120 мс)"
		#snmpget -m 0  -L n -c private -v 2c -t 8 10.32.%%i.2:8001 1.3.6.1.4.1.22909.1.3.15.1.1.1.13.9


		break;
	}
	case "rcu": // rcu
	{
		switch($Route[1])
		{
			case "auth": // rcu/auth
			{
				try	{
					API::checkArgs("host,username,userpass");
				}
				catch(Exception $e)
				{
					$API($e); break;
				}

				$input = array("host"=>$_REQUEST["host"],
				"username"=>$_REQUEST["username"],
				"userpass"=>$_REQUEST["userpass"]);

				$API->module(CLASSES_PATH."class.rcu.php");

				//создали объект класса RCU
				//заполнили данными
				$RCU = new RCU($input);
				//запросили авторизацию получили заголовки
				$RCU->headers = $RCU->auth(); //
				//обработали заголовки
				$RCU->headers = $RCU->get_headers($RCU->headers);

				if($RCU->headers instanceof Exception)	{
					$API($RCU->headers);	break;
				}

			//выделяем куки
				$RCU->cookie = $RCU->get_cookie($RCU->headers);
				//сохраняем соединение
				$RCU->set_connection();
				//выдали ответ пользователю
				$API($RCU->connection);
				#$API($RCU->headers);


				break;
			}

			//post
			case "post": //  rcu/post
			{
				try	{
					API::checkArgs("url,cookie,data,host");
				}
				catch(Exception $e)
				{
					$API($e); break;
				}

				$input = array("url"=>$_REQUEST["url"],
				"cookie"=>$_REQUEST["cookie"],
				"data"=>$_REQUEST["data"],
				"host"=>$_REQUEST["host"]);

				$API->module(CLASSES_PATH."class.rcu.php");

				//создаем объект типа RCU
				$RCU = new RCU;
				$RCU->host = $input["host"];
				//делаем POST запрос
				$POST = $RCU->post($input["url"], $input["cookie"], $input["data"]);
				//выводим результат

				$API($POST);



				break;
			}

			case "parse": //  rcu/parse
			{
				/* парсинг страницы устройств*/

				try	{
					API::checkArgs("host,cookie");
				}
				catch(Exception $e)
				{
					$API($e); break;
				}

				$input = array("host"=>$_REQUEST["host"],
				"cookie"=>$_REQUEST["cookie"]);

				$API->module(CLASSES_PATH."class.phpQuery.php");
				$API->module(CLASSES_PATH."class.rcu.php");

				//создаем объект типа RCU
				$RCU = new RCU;
				$RCU->host = $input["host"]; //для запроса
				$RCU->cookie = $input["cookie"];
				//формируем url для запроса
				$URL = $RCU->protocol."://".$RCU->host.$RCU->basedir;
				//делаем POST запрос
				$POST = $RCU->post($URL);

				//отправляем результат запроса в функцию parse
				$parseresult = $RCU->parse($POST);



				//подмешиваем в ответ хост

				//выводим результат
				$API($parseresult);

				break;
			}
			case "device": // rcu/device		для работы с определенным абстрактным устройством
			{
				try	{
					API::checkArgs("host,cookie,type_id,action"); //id = ид устройства в таблице (для ссылок), type_id = для подключения шаблона
				}
				catch(Exception $e)
				{
					$API($e); break;
				}

				$input = array("host"=>$_REQUEST["host"],
						"cookie"=>@$_REQUEST["cookie"], //для post запроса
						"type_id"=>$_REQUEST["type_id"], //для подключения шаблона
						"device"=>@$_REQUEST["device"], //полная запись из таблицы устройства
						"action"=>$_REQUEST["action"],//действия с устройством напр monitoring/input/1
						"post_data"=>@$_REQUEST["post_data"]
						//мультиплекс нужен для ответа
					);

				$API->module(CLASSES_PATH."class.phpQuery.php");
				$API->module(CLASSES_PATH."class.rcu.php");
				$API->module(CLASSES_PATH."class.templates.php");
				$API->module(TEMPLATES_PATH.$input["type_id"].".php");

				//ставим куки и хост
				$RCU = new RCU;
				$RCU->host = $input["host"];
				$RCU->cookie = $input["cookie"];
				$RCU->post_data = $input["post_data"];

				//создаем экземпляр класса
				//сразу с действием и доп параметрами
				$Device = new Device($input["action"], array("device"=>$input["device"])); // результат запуска функции по `action` пути
				//в результате работы конструктора будет вызван action()

				/*
				в зависимости от Device->method (snmp or http)
				*/

				$sizeof_url = sizeof($Device->POST_url);

				for($i = 0; $i < $sizeof_url; $i++)
				{
					$URL = $RCU->protocol."://".$RCU->host.$Device->POST_url[$i];
					$POST = $RCU->post($URL);
					$Device->POST_result[$i] = $POST;//полученную страницы записываем
					if($sizeof_url > 1 and !empty($RCU->post_data)) sleep(10);
				}

/*
				//делаем несколько запросов
				foreach($Device->POST_url as $i => $path)
				{
					$URL = $RCU->protocol."://".$RCU->host.$path;
					$POST = $RCU->post($URL);
					$Device->POST_result[$i] = $POST;//полученную страницы записываем
					sleep(10); //задержка выполнения скрипта на время принятия решения модулятору
				}
*/
				//после получения всей информации можно запускать callback для обработки этих страниц
				//$this->POST_result - обходить этот массив
				// callback должен сам знать что искать
				$Device->POST_callback = $Device->callback["page"]($Device->device_info, $Device->POST_result); //вызвать коллбек обработки
				$Device->POST_represent = $Device->callback["represent"]($Device->device_info, $Device->POST_callback); // интерпретировать ответ в удобный вид
				$Device->info(); //преобразовать массив в строку

				/*

				result = {
					full:{'ASI1', }
					represent:{'40°'}
					?waited:{'ASI1'}

				}

				*/

				//вернуть ответ
				$API($Device);break; //debug
				#$API($Device->Info);
				break;


				//получили данные, теперь их отправляем

				//формируем url для запроса
				//$Device->Info["url"] = str_replace("{id}", $input["id"], $Device->Info["url"]);


			}
			case "monitoring":{

				switch($Route[2])
				{
					case "inputPrimary":{ //основной вход

						try	{
							API::checkArgs("host, cookie, mux");
						}
						catch(Exception $e)
						{
							$API($e); break;
						}

						$input = array("host"=>$_REQUEST["host"],
						"mux"=>$_REQUEST["mux"],
						"cookie"=>$_REQUEST["cookie"]);

						if(!strcmp($input["cookie"],"false") or strlen($input["cookie"]) != 50) {

							$API(new Exception("Объект связи не авторизован")); break;
						}

						//$API($input["cookie"]); break;
						/*	ПОИСК УСТРОЙСТВА В БД */

						$select_device = $db->query("SELECT * FROM `devices` WHERE `host`='".$input["host"]
						."' AND `name` LIKE '%".$input["mux"]." MUX%' AND `type_id`='10000' ORDER BY `id` ASC");

						if($db->num_rows($select_device) < 1)
						{
							$API(new Exception("Устройств по заданным критериям не найдено"));
							break;
						}
						else if($db->num_rows($select_device) > 1)
						{
							$API->debug_param["num_rows"] = $db->num_rows($select_device);
							$API->debug_param["fetch_assoc"] = $db->fetch_assoc($select_device);
							$API(new Exception("По заданным критериям найдено больше одного устройства"));
							break;
						}

						$select_device = $db->fetch_assoc($select_device);


						#$API($select_device);break; //debug


						/*	ОТПРАВЛЯЕМ ДАННЫЕ НА ШАБЛОН	*/

						$API->module(CLASSES_PATH."class.phpQuery.php");
						$API->module(CLASSES_PATH."class.rcu.php");
						$API->module(CLASSES_PATH."class.templates.php");
						$API->module(TEMPLATES_PATH."10000.php");

						$RCU = new RCU;
						$RCU->host = $input["host"];
						$RCU->cookie = $input["cookie"];

						//
						$Device = new Device("monitoring/modulator/inputPrimary", array("device"=>$select_device[0])); // результат запуска функции по `action` пути

						$sizeof_url = sizeof($Device->POST_url);

						for($i = 0; $i < $sizeof_url; $i++)
						{
							$URL = $RCU->protocol."://".$RCU->host.$Device->POST_url[$i];
							$POST = $RCU->post($URL);
							$Device->POST_result[$i] = $POST;//полученную страницы записываем
						}


						$Device->POST_callback = $Device->callback["page"]($Device->device_info, $Device->POST_result); //вызвать коллбек обработки
						$Device->POST_values = $Device->POST_callback["values"];
						$Device->POST_represent = $Device->callback["represent"]($Device->device_info, $Device->POST_callback["text"]); // интерпретировать ответ в удобный вид
						$Device->info(); //преобразовать массив в строку


						$API($Device);


						break;
					}



					default:{


						$API(new Exception("undefined route #2 by route 'rcu/monitoring'", 3));
					}
				}

				break;
			}
			case "management":{

				switch($Route[2])
				{
					case "goto40":{ //основной вход

						try	{
							API::checkArgs("host, cookie, mux");
						}
						catch(Exception $e)
						{
							$API($e); break;
						}

						$input = array("host"=>$_REQUEST["host"],
						"mux"=>$_REQUEST["mux"],
						"cookie"=>$_REQUEST["cookie"]);

						if(!strcmp($input["cookie"],"false") or strlen($input["cookie"]) != 50) {

							$API(new Exception("Объект связи не авторизован")); break;
						}


						$select_device = $db->query("SELECT * FROM `devices` WHERE `host`='".$input["host"]
						."' AND `name` LIKE '%".$input["mux"]." MUX%' AND `type_id`='10000' ORDER BY `id` ASC");

						if($db->num_rows($select_device) < 1)
						{
							$API(new Exception("Устройств по заданным критериям не найдено"));
							break;
						}
						else if($db->num_rows($select_device) > 1)
						{
							$API->debug_param["num_rows"] = $db->num_rows($select_device);
							$API->debug_param["fetch_assoc"] = $db->fetch_assoc($select_device);
							$API(new Exception("По заданным критериям найдено больше одного устройства"));
							break;
						}

						$select_device = $db->fetch_assoc($select_device);

						/*	ОТПРАВЛЯЕМ ДАННЫЕ НА ШАБЛОН	*/

						$API->module(CLASSES_PATH."class.phpQuery.php");
						$API->module(CLASSES_PATH."class.rcu.php");
						$API->module(CLASSES_PATH."class.templates.php");
						$API->module(TEMPLATES_PATH."10000.php");

						$RCU = new RCU;
						$RCU->host = $input["host"];
						$RCU->cookie = $input["cookie"];

						$Device = new Device("management/modulator/toASI1", array("device"=>$select_device[0])); // результат запуска функции по `action` пути

						$RCU->post_data = $Device->post_data;

						//$API(array("device"=>$Device, "rcu"=>$RCU));	break;	#debug

						$sizeof_url = sizeof($Device->POST_url);

						for($i = 0; $i < $sizeof_url; $i++)
						{
							$URL = $RCU->protocol."://".$RCU->host.$Device->POST_url[$i];
							$POST = $RCU->post($URL);	########WARNING

							$Device->POST_result[$i] = array($URL, $RCU->post_data);//полученную страницы записываем
							if($sizeof_url > 1 and !empty($RCU->post_data)) sleep(10);
						}


						#$API($Device); break;	#debug


						unset($Device);
						unset($RCU);

						sleep(10);

						$RCU = new RCU;
						$RCU->host = $input["host"];
						$RCU->cookie = $input["cookie"];
						$RCU->post_data = "";

						$Device = new Device("monitoring/modulator/inputPrimary", array("device"=>$select_device[0])); // результат запуска функции по `action` пути

						$sizeof_url = sizeof($Device->POST_url);

						for($i = 0; $i < $sizeof_url; $i++)
						{
							$URL = $RCU->protocol."://".$RCU->host.$Device->POST_url[$i];
							$POST = $RCU->post($URL);
							$Device->POST_result[$i] = $POST;//полученную страницы записываем
						}


						$Device->POST_callback = $Device->callback["page"]($Device->device_info, $Device->POST_result); //вызвать коллбек обработки
						$Device->POST_values = $Device->POST_callback["values"];
						$Device->POST_represent = $Device->callback["represent"]($Device->device_info, $Device->POST_callback["text"]); // интерпретировать ответ в удобный вид
						$Device->info(); //преобразовать массив в строку


						$API($Device);





						/*
						*/




						break;
					}
					case "goto53":{

						try	{
							API::checkArgs("host, cookie, mux");
						}
						catch(Exception $e)
						{
							$API($e); break;
						}

						$input = array("host"=>$_REQUEST["host"],
						"mux"=>$_REQUEST["mux"],
						"cookie"=>$_REQUEST["cookie"]);

						if(!strcmp($input["cookie"],"false") or strlen($input["cookie"]) != 50) {

							$API(new Exception("Объект связи не авторизован")); break;
						}


						$select_device = $db->query("SELECT * FROM `devices` WHERE `host`='".$input["host"]
						."' AND `name` LIKE '%".$input["mux"]." MUX%' AND `type_id`='10000' ORDER BY `id` ASC");

						if($db->num_rows($select_device) < 1)
						{
							$API(new Exception("Устройств по заданным критериям не найдено"));
							break;
						}
						else if($db->num_rows($select_device) > 1)
						{
							$API->debug_param["num_rows"] = $db->num_rows($select_device);
							$API->debug_param["fetch_assoc"] = $db->fetch_assoc($select_device);
							$API(new Exception("По заданным критериям найдено больше одного устройства"));
							break;
						}

						$select_device = $db->fetch_assoc($select_device);

						/*	ОТПРАВЛЯЕМ ДАННЫЕ НА ШАБЛОН	*/

						$API->module(CLASSES_PATH."class.phpQuery.php");
						$API->module(CLASSES_PATH."class.rcu.php");
						$API->module(CLASSES_PATH."class.templates.php");
						$API->module(TEMPLATES_PATH."10000.php");

						$RCU = new RCU;
						$RCU->host = $input["host"];
						$RCU->cookie = $input["cookie"];

						$Device = new Device("management/modulator/toASI2", array("device"=>$select_device[0])); // результат запуска функции по `action` пути

						$RCU->post_data = $Device->post_data;

						$sizeof_url = sizeof($Device->POST_url);

						for($i = 0; $i < $sizeof_url; $i++)
						{
							$URL = $RCU->protocol."://".$RCU->host.$Device->POST_url[$i];
							$POST = $RCU->post($URL);	######## WARNING

							$Device->POST_result[$i] = array($URL, $RCU->post_data);//полученную страницы записываем
							if($sizeof_url > 1 and !empty($RCU->post_data)) sleep(10);
						}


						//$API($Device);


						unset($Device);
						unset($RCU);

						sleep(10);

						$RCU = new RCU;
						$RCU->host = $input["host"];
						$RCU->cookie = $input["cookie"];
						$RCU->post_data = "";

						$Device = new Device("monitoring/modulator/inputPrimary", array("device"=>$select_device[0])); // результат запуска функции по `action` пути

						$sizeof_url = sizeof($Device->POST_url);

						for($i = 0; $i < $sizeof_url; $i++)
						{
							$URL = $RCU->protocol."://".$RCU->host.$Device->POST_url[$i];
							$POST = $RCU->post($URL);
							$Device->POST_result[$i] = $POST;//полученную страницы записываем
						}


						$Device->POST_callback = $Device->callback["page"]($Device->device_info, $Device->POST_result); //вызвать коллбек обработки
						$Device->POST_values = $Device->POST_callback["values"];
						$Device->POST_represent = $Device->callback["represent"]($Device->device_info, $Device->POST_callback["text"]); // интерпретировать ответ в удобный вид
						$Device->info(); //преобразовать массив в строку


						$API($Device);


						break;
					}
					default:{
						$API(new Exception("undefined route #3 by route 'rcu/management'", 3));
					}
				}
				break;
			}
			default: // если не указан 1 маршрут
			{
				$API(new Exception("undefined route #1 by route 'rcu'", 2));
			}
		}

		break;
	}


	case "db":
	{
		switch($Route[1])
		{
			case "select": // db/select
			{
				switch($Route[2])//для предустановленного выбора
				{
					case "rcu": // db/select/rcu
					{
						$get_rcu = $db->query("SELECT * FROM `rcu`");
						$get_rcu = $db->fetch_assoc($get_rcu);

						/*	группируем по ОЧС	*/
						$sfn = array();// список одночастотных зон list, хосты - host

						/* делаем простой список объектов связи */
						$list = array();

						/* список объектов связи по хостам */
						$host = array();

						//для каждой строки декодим json
						foreach($get_rcu as $i => $row)
						{
							#$row["coord"] = json_decode($row["coord"],true);
							$row["auth"] = json_decode($row["auth"],true);
							#$row["devices_table"] = json_decode($row["devices_table"],true);

							$list[] = $row;
							$host[$row["host"]] = $row;

							$sfn_info = array("name"=>$row["sfn_name"],"eng"=>$row["sfn_eng"],"uid"=>$row["sfn_uid"]);
							//список одночастотных зон sfn[]
							$sfn["list"][$sfn_info["uid"]] = $sfn_info;

							$sfn["host"][$sfn_info["uid"]][$row["host"]] = $row;

						}
######## Вынести в класс #########
						$result = array(); //вернем результат

						$API(array(
						"sfn"=>$sfn,
						"list"=>$list,
						"host"=>$host));

						break;
					}
					case "connection": // db/select/connection
					{
						//1440 sec = 24 минуты

						if(sizeof($Route) > 3)
						{

							if(strcmp($Route[3], "host") !== 0) break;

							try	{
							API::checkArgs("host");
							}
							catch(Exception $e)
							{
								$API($e); break;
							}

							$input = array("host"=>$_REQUEST["host"]);

							$connection = $db->query("SELECT `cookie`,`host`,`remote`,`timestamp`,`username`,`userpass` FROM `connections` WHERE `host`='".$input["host"]."' AND TIME_TO_SEC(TIMEDIFF(NOW(),`timestamp`)) < 1440 GROUP BY `host` ORDER BY `timestamp` DESC LIMIT 1");

							$connection = $db->fetch_assoc($connection);

							$API($connection);

							break;
						}

						$connections = $db->query("SELECT `cookie`,`host`,`remote`,`timestamp`,`username`,`userpass` FROM `connections` INNER JOIN (
							SELECT max(`uid`) AS `maxUid` FROM `connections` WHERE TIME_TO_SEC(TIMEDIFF(NOW(),`timestamp`)) < 1440 GROUP BY `host`
							) AS `MAX` ON `connections`.`uid` = `MAX`.`maxUid` ORDER BY `uid` DESC");

						$connections = $db->fetch_assoc($connections);
/*
						if($db->num_rows($connections) > 1)
						{
							$connections = $db->fetch_assoc($connections);
						}
						else $connections = array($db->fetch_assoc($connections));
	*/
						$API($connections);
						break;
						//SELECT * FROM `connections` WHERE DATE(`timestamp`) = CURDATE() AND TIME(`timestamp`) - CURTIME() < 1440 ORDER BY `uid` DESC

					}

					case "device": // db/select/device поиск определенного УСТРОЙСТВА с host, mux
					{
						$API->module(CLASSES_PATH."class.rcu.php");
						try	{
						API::checkArgs("host");
						}
						catch(Exception $e)
						{
							$API($e); break;
						}

						//обязательный параметр - host
						$input = array("host"=>$_REQUEST["host"],
						"mux"=>@$_REQUEST["mux"],
						"func"=>@$_REQUEST["func"]);


						$WHERE = "WHERE `host`='".$input["host"]."'";
						if(!empty($input["mux"]))
						{
							$WHERE .= " AND `name` LIKE '%".$input["mux"]." MUX%'";
						}

						if(!empty($input["func"]))
						{
							$WHERE .= " AND `type` LIKE '%".$input["func"]."%'";
						}

						$devices = $db->query("SELECT * FROM `devices` ".$WHERE." ORDER BY `id` ASC");
						$devices = $db->fetch_assoc($devices);

						if(sizeof($devices) < 1)
						{
							$API(new Exception("Устройств по заданным критериям не найдено", 411));
							break;
						}
						/*
						foreach($devices as $i => $device)
						{
							$devices[$i]["purposes"] = RCU::purposes($device);
						}
						*/

						$API($devices);

						break;

					}

					case "log":
					{
						try	{
							API::checkArgs("start_date,finish_date");
						}
						catch(Exception $e)
						{
							$API($e); break;
						}
						$input = array("start_date"=>$_REQUEST["start_date"],
						"finish_date"=>$_REQUEST["finish_date"]);

						$get_log = $db->query("SELECT * FROM `log` WHERE `timestamp` BETWEEN '".$input["start_date"]."' AND '".$input["finish_date"]."' ORDER BY `uid` DESC");
						$get_log = $db->fetch_assoc($get_log);



						$API($get_log);
						break;
					}


					case "devices": // db/select/devices/*
					{
						$API->module(CLASSES_PATH."class.rcu.php");
						switch($Route[3])
						{
							case "host":{ //db/select/devices/host

								try	{
								API::checkArgs("host");
								}
								catch(Exception $e)
								{
									$API($e); break;
								}

								$input = array("host"=>$_REQUEST["host"]);

								$devices = $db->query("SELECT * FROM `devices` WHERE `host`='".$input["host"]."' ORDER BY `id` ASC");
								$devices = $db->fetch_assoc($devices);

								foreach($devices as $i => $device)
								{
									$devices[$i]["purposes"] = RCU::purposes($device);
								}


								$API($devices);
								break;

							}
							case "mux":{ //db/select/devices/mux



								try	{
								API::checkArgs("mux");
								}
								catch(Exception $e)
								{
									$API($e); break;
								}

								$input = array("mux"=>$_REQUEST["mux"]);

								$devices = $db->query("SELECT * FROM `devices` WHERE `name` LIKE '%".$input["mux"]." MUX%' ORDER BY `id` ASC");
								$devices = $db->fetch_assoc($devices);

								foreach($devices as $i => $device)
								{
									$devices[$i]["purposes"] = RCU::purposes($device);
								}

								$API($devices);

								break;
							}
							case "purpose":{ //db/select/devices/host

								try	{
									API::checkArgs("host");
								}
								catch(Exception $e)
								{
									$API($e); break;
								}

								$input = array("host"=>$_REQUEST["host"],
								"mux"=>@$_REQUEST["mux"],
								"func"=>@$_REQUEST["func"]);


								$WHERE = "WHERE `host`='".$input["host"]."'";
								if(!empty($input["mux"]))
								{
									$WHERE .= " AND `name` LIKE '%".$input["mux"]."%'";
								}

								if(!empty($input["func"]))
								{
									$WHERE .= " AND `type` LIKE '%".$input["func"]."%'";
								}

								$devices = $db->query("SELECT * FROM `devices` ".$WHERE." ORDER BY `id` ASC");
								$devices = $db->fetch_assoc($devices);

								foreach($devices as $i => $device)
								{
									$devices[$i]["purposes"] = RCU::purposes($device);
								}

								$API($devices);

								break;
							}

							default:{
								$devices = $db->query("SELECT * FROM `devices` ORDER BY `host` ASC");
								$devices = $db->fetch_assoc($devices);
								$API($devices);

							}
						}




						break;
					}

					default:
					{
						//не обзательный параметр
					}
				}

				break;
			}
			case "update": // db/update
			{
				switch($Route[2])//для предустановленного выбора
				{
					case "rcu": // db/update/devices
					{
						//обновить информацию об устройствах в БД

						//передать на вход host и список устройств! хэш вычисляем здесь
						try	{
							API::checkArgs("host,devices_hash,devices_table,rcu_name");
						}
						catch(Exception $e)
						{
							$API($e); break;
						}

						$input = array("host"=>$_REQUEST["host"],
						"rcu_name" => $_REQUEST["rcu_name"],
						"devices_hash" => $_REQUEST["devices_hash"],
						"devices_table"=>$_REQUEST["devices_table"]);

						//устанавливаем соединение с БД
						$update_rcu = $db->query("UPDATE `rcu` SET
						`rcu_name` = '".$input["rcu_name"]."',
						`devices_table`='".mysql_real_escape_string($input["devices_table"])."',
						`devices_hash`='".$input["devices_hash"]."'
						WHERE `host` = '".$input["host"]."';");

						$API($update_rcu);
						break;
					}
					default:{
						//необязательный параметр
					}
				}
				break;
			}
			case "insert":{ // db/insert

				switch($Route[2])//для предустановленного выбора
				{

					case "connection":{ // db/insert/connection

						try	{
							API::checkArgs("host,cookie,username,userpass,timestamp,remote");
						}
						catch(Exception $e)
						{
							$API($e); break;
						}

						$input = array("host"=>$_REQUEST["host"],
						"cookie"=>$_REQUEST["cookie"],
						"username"=>$_REQUEST["username"],
						"userpass"=>$_REQUEST["userpass"],
						"timestamp"=>$_REQUEST["timestamp"],
						"remote"=>$_REQUEST["remote"]);

						$insert_connection = $db->query("INSERT INTO `gmms`.`connections` (`uid`, `host`, `cookie`, `username`, `userpass`, `timestamp`, `remote`) VALUES (NULL, '".$input["host"]."', '".$input["cookie"]."', '".$input["username"]."', '".$input["userpass"]."', CURRENT_TIMESTAMP, '".$input["remote"]."');");

						$API($insert_connection);

						break;
					}
					case "log":{ // db/insert/log

						try	{
							API::checkArgs("message, host");
						}
						catch(Exception $e)
						{
							$API($e); break;
						}

						$input = array("host"=>$_REQUEST["host"],
						"message"=>$_REQUEST["message"],
						"object"=>@$_REQUEST["object"]);



						$insert_log = $db->query("INSERT INTO `gmms`.`log` (`uid`, `timestamp`, `remote`, `host`, `message`, `object`)
						VALUES (NULL, CURRENT_TIMESTAMP, '".$_SERVER["REMOTE_ADDR"]."', '".$input["host"]."', '".$input["message"]."', '".$input["object"]."');");

						$API($insert_log);

						break;
					}
					default:{
						//необязательный параметр
					}
				}


				break;
			}

			default:
			{
				$API(new Exception("undefined route #1 by route 'db'", 2));
			}
		}
		break;
	}

	default: //если не указан 0 маршрут
	{
		$API(new Exception("undefined route #0", 1));
	}
}




class API
{
	public static $protocol = "http"; // протокол для всех обращений в СДК

	//API::checkArgs("arg1,arg2,arg3");
	public static function checkArgs($argsString) //проверка входных переменных argsString - входные переменные
	{
		$arrayArgs = explode(",", $argsString);
		foreach($arrayArgs as $i => $arg)
		{
			if(( !is_numeric($_REQUEST[trim($arg)]) and empty($_REQUEST[trim($arg)])) or !isset($_REQUEST[trim($arg)]))
				throw new Exception("Проверьте входные данные", 15);
		}
		return true;
	}
/*
	//функция поиска заголовка в массиве haystack, needle
	public static function find_headers($haystack, $needle) // ищем в заголовках haystack заголовках needle
	{
		foreach($haystack as $group => $groupArray)
		{
			foreach($groupArray as $name => $value) //value может быть Array
			{
				if(!strcmp($name, $needle)) //если нашли нужный заголовок
				{
					return $value;
				}
			}
		}
	}
*/


	public $error = 0;
	public $response;
	public $debug = false;
	public $host = false;
	public $debug_param;

	public function __invoke($a = null)
	{
		if($a instanceof Exception)
		{
			$this->error = ($a->getCode() == 0) ? -999 : $a->getCode();
			$this->response = array(
				"message" => $a->getMessage(),
				"file" => $a->getFile(),
				"line" => $a->getLine(),
				"trace"=>$a->getTrace(),
				"request"=>$_REQUEST,
				"debug_param"=>$this->debug_param
				);
		}
		else
		{
			$this->response = $a;
		}

		//только для вывода
		$result = array("error" => $this->error, "response" => $this->response, "host" => $this->host);

		 if($this->debug)
		 {
			echo "<pre>"; print_r($result); echo "</pre>";
		 }
		 else
			 echo json_encode($result);
	}


	public function module($file) //функция подключения модуля с проверкой на его наличие
	{
		$check = file_exists($file);
		if($check === FALSE)
		{
			return $this(new Exception("Невозможно подключить файл :: ".$file));
		}
		else
		{
			include($file);
		}

	}

}


/*

Throwable::getMessage — Gets the message
Throwable::getCode — Gets the exception code
Throwable::getFile — Gets the file in which the object was created
Throwable::getLine — Gets the line on which the object was instantiated
Throwable::getTrace — Gets the stack trace
Throwable::getTraceAsString — Gets the stack trace as a string
Throwable::getPrevious — Returns the previous Throwable
Throwable::__toString — Gets a string representation of the thrown object

*/
?>
