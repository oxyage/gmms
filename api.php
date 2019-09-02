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

switch($Route[0])
{
	case "rcu":
	{
		switch($Route[1])
		{
			case "auth":
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
			case "post":
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
			
			case "parse": //parse
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
				
				//обновить в базе данных хэш
				#$update = $db->query("UPDATE `rcu` SET `devices_hash` = '".$devices_hash."' WHERE `host` = '".$input["host"]."'; ");
				
				
				//подмешиваем в ответ хост
				
				//выводим результат
				$API($parseresult);
				
				break;
			}
			case "device": //*для работы с устройством
			{
				try	{
					API::checkArgs("host,cookie,id,type_id"); //id = ид устройства в таблице (для ссылок), type_id = для подключения шаблона
				}
				catch(Exception $e)
				{
					$API($e); break;
				}
				
				$input = array("host"=>$_REQUEST["host"], 
				"cookie"=>@$_REQUEST["cookie"],
				"type_id"=>$_REQUEST["type_id"],
				"id"=>$_REQUEST["id"],
				"action"=>@$_REQUEST["action"]//действия с устройством напр monitoring/input/1
				);
				
				$API->module(CLASSES_PATH."class.phpQuery.php");
				$API->module(CLASSES_PATH."class.rcu.php");
				$API->module(TEMPLATES_PATH.$input["type_id"].".php");
				
				//ставим куки
				$RCU = new RCU;
				$RCU->host = $input["host"];
				$RCU->cookie = $input["cookie"];
				
				$Device = new Device($input["action"]); // результат запуска функции по `action` пути
				
				
				
				
				
				$API($device);
				
				/*
				
				API принимает данные на вход
				соответственно подключает необходимые файлы
				rcu
				template
				выдать возможные функции от устройства !
				как необязательные параметры передать коды запросов - на получение информации или отправку формы
				
				по полученным кодам запроса - из template получить необходимые данные
				отправить их посредством rcu->post
				результат запроса интерпретировать снова в template
				результат интерпретации получить в API и отправить пользователю
				

			
				*/
				
				
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
		//если 0 маршрут db
		//парсим настройки для бд
		$db_ini = parse_ini_file(CONFIG_PATH."db.ini");
		//подключаем класс бд
		$API->module(CLASSES_PATH."class.db.php");
		//создаем экземпляр класса db
		$db = new db($db_ini);
		$db->connect(); //соединеняемся с БД и выбираем базу данных
		
		switch($Route[1])
		{
			case "select":
			{
				switch($Route[2])//для предустановленного выбора
				{
					case "rcu":
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
							$row["coord"] = json_decode($row["coord"],true);
							$row["auth"] = json_decode($row["auth"],true);
							
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
					case "connection":
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
						
						$API($connections);	
						break;
						//SELECT * FROM `connections` WHERE DATE(`timestamp`) = CURDATE() AND TIME(`timestamp`) - CURTIME() < 1440 ORDER BY `uid` DESC
						
					}
					
					default:
					{
						//не обзательный параметр
					}
				}
				
				break;
			}
			case "update":
			{
				switch($Route[2])//для предустановленного выбора
				{
					case "rcu":
					{
						//обновить информацию об устройствах в БД
						
						//сначала обновим хэш в таблице rcu
						//devices обновляем в другом запросе
						
						
						//передать на вход host и список устройств! хэш вычисляем здесь						
						try	{
							API::checkArgs("host,devices");
						}
						catch(Exception $e)
						{
							$API($e); break;
						}
									
						$input = array("host"=>$_REQUEST["host"], 
						"devices"=>$_REQUEST["devices"]);
						
						//устанавливаем соединение с БД
						$update_rcu = $db->query("UPDATE `gmms`.`rcu` SET `devices_hash` = '".md5($input["devices"])."' WHERE `rcu`.`host` = '".$input["host"]."';");
							
						$API($update_rcu);	
						break;
					}
					default:{
						//необязательный параметр
					}
				}
				break;
			}
			case "insert":{
				
				switch($Route[2])//для предустановленного выбора
				{
					
					case "connection":{
						
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
			if(empty($_REQUEST[trim($arg)]) or !isset($_REQUEST[trim($arg)])) 
				throw new Exception("Проверьте входные данные", 15);
		}
		return true;
	}

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
	

	
	public $error = 0;
	public $response;
	public $debug = false;
	public $host = false;
	
	public function __invoke($a = null)
	{
		if($a instanceof Exception)
		{
			$this->error = $a->getCode();
			$this->response = array(
				"message" => $a->getMessage(), 
				"file" => $a->getFile(), 
				"line" => $a->getLine(), 
				"trace"=>$a->getTrace()
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