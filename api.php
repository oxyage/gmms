<?

ini_set('display_errors', 1);//выводить ошибки
ini_set('display_startup_errors', 1);//выводить ошибки startup
ini_set('error_reporting', E_ALL);//выводить ошибки
ini_set('default_charset', "UTF-8");//нормальная кодировка

define("classes_dir", "classes");

define("DIRSEP", DIRECTORY_SEPARATOR);
define('SITE_PATH', realpath(dirname(__FILE__).DIRSEP."..".DIRSEP).DIRSEP."gmms".DIRSEP); //корневая папка 
define('CLASSES_PATH', SITE_PATH."classes".DIRSEP); // папка с классами
define('CONFIG_PATH', SITE_PATH."config".DIRSEP); // папка с настройками

/*
класс для взаимодействия сервера с удаленным СДК по HTTP

http://10.32.1.3/gmms/2.0.0/api.php?route=rcu/auth&host=10.32.1.2&username=admin&userpass=kq9OXFVTKb&debug


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
				
				include(CLASSES_PATH."class.rcu.php");
				
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
					API::checkArgs("url,cookie,data");
				}
				catch(Exception $e)
				{
					$API($e); break;
				}
				
				$input = array("url"=>$_REQUEST["url"], 
				"cookie"=>$_REQUEST["cookie"],
				"data"=>$_REQUEST["data"]);
								
				include(CLASSES_PATH."class.rcu.php");
				
				//создаем объект типа RCU
				$RCU = new RCU;
				//делаем POST запрос
				$POST = $RCU->post($input["url"], $input["cookie"], $input["data"]);
				//выводим результат
				$API($POST);
				
				
			
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
		include(CLASSES_PATH."class.db.php");
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
						$API($db->fetch_assoc($get_rcu));
						
						break;
					}
					
					default:
					{
						//не обзательный параметр
					}
				}
				
				
				/*try	{
					API::checkArgs("table");
				}
				catch(Exception $e)
				{
					$API($e); break;
				}
				
				$result = array($db, $db());
				$API($result);
				*/
				break;
			}
			case "update":
			{
				$API();
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
			if(empty($_REQUEST[$arg]) or !isset($_REQUEST[$arg])) 
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
		$result = array("error" => $this->error, "response" => $this->response);
						  
		 if($this->debug)
		 { 
			echo "<pre>"; print_r($result); echo "</pre>";
		 }
		 else	
			 echo json_encode($result);
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