<?


/*
Класс для СДК, методы работы с ним и свойства
Объект класса RCU - сам СДК
*/
class RCU
{
	/* 	свойства объектов класса 
	все свойства публичные для нормального доступа	*/
	
	//данные для авторизации в СДК раздеделены по привилегиям (необходимо заполнять поля логин и пароль из базы данных)
	public $auth = array("admin"=>array("username"=>"admin", "userpass"=>""), 
						"operator"=>array("username"=>"operator", "userpass"=>"")); 
	
	public $username;//для текущей авторизации
	public $userpass;
	public $cookie;

	//информация о последнем curl запросе $request[curl], $request[server]
	public $request; 
	public $headers; // последние полученные заголовки
	
	//протокол соединения с СДК
	public $protocol = "http";
	
	//IP адрес СДК
	public $host;
	
	//базовая дирректория для авторизации и других запросов
	public $basedir = "/config/devices/";
	
	//координаты РТС на карте (от 0 до 100)
	public $coord = array("x"=>"", "y"=>"");
	
	//имя РТПС
	public $name;
	
	//ОЧЗ
	public $sfn;
	
	//соединение с СДК
	public $connection = array("host"=>"",
								"cookie"=>"", //полученные куки авторизации
								"remote"=>"", //IP адрес авторизованного клиента
								"timestamp"=>"", //timestamp время авторизации
								"username"=>"", //имя авторизованного пользователя
								"userpass"=>"" //пароль авторизованного пользователя
								//..для добавления новых
								);
	
	
	
	/* методы 
	все взаимодействия с СДК через методы */
	
	
	//при создании объекта класса заполняем свойства если они были переданы
	public function __construct($args = null)
	{
		if(is_null($args)) return true;
		foreach($args as $key => $value)
		{
			if(property_exists('RCU', $key))
			{
				$this->$key = $value;				
			}	
		}
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
	
	public function get_cookie($headers)
	{
		$cookie = $this->find_headers($headers, "set-cookie");
		if(is_array($cookie))//если в заголовках несколько куки
		{
			return $cookie[1]; //берем второй
		}
		
		return $cookie; //иначе - единственный
	}
	
	public function set_connection()
	{
		$this->connection["host"] = $this->host; 
		$this->connection["cookie"] = $this->cookie; 
		$this->connection["remote"] = $this->request["server"]["REMOTE_ADDR"];
		$this->connection["username"] = $this->username;
		$this->connection["userpass"] = $this->userpass;
		$this->connection["timestamp"] = time();
	}
	
	//функция обработки заголовков API::get_headers($httpheader)
	public static function get_headers($responseHeaders)
	{
		if(empty($responseHeaders) or !is_string($responseHeaders)) //проверка входных данных
		{
			return new Exception("Входные данные функции не являются строкой или строка пустая");
		}
		
		$headers = array();//массив с заголовками которые вернем в результате функции
		
		//первым делом делим несколько ответных заголовков
		$responseHeaders = explode("\n\r\n", trim($responseHeaders));//разделяем несколько ответов
		
		//sizeof(responseHeaders) равен количеству ответов от сервера (групп заголовков)
		// проходим по массиву ответа где group - это номер группы заголовков
		// textHeaders - неразделенный текст заголовков
		foreach($responseHeaders as $group => $textHeaders) 
		{
			$arrayHeaders = explode("\n", trim($textHeaders));//разделяем заголовки из группы на строки
			
			$headers[$group] = array();
			foreach($arrayHeaders as $s => $string) //проходим по массиву где s это строка с заголовком 
			{
				if(strstr($string, "HTTP/1.1") !== FALSE)
				{
					$headers[$group]['http'] = $string;//статус ответа	
					continue;
				}
								
				$string = explode(":",$string, 2); //разделяем строку заголовка по первому двоеточию
			
				$name = strtolower(trim($string[0])); //переводим в нижний регистр имя заголовка
				$header = trim($string[1]); // сам заголовок
								
				if(!array_key_exists($name, $headers[$group]))	//если заголовка нет в массиве - добавляем
				{
					$headers[$group][$name] = $header; //убираем пробелы из значения и кладем в массив
				}
				else								//если заголовок уже есть в массиве
				{
					if(!is_array($headers[$group][$name]))	    //если еще не массив 
						$headers[$group][$name] = array($headers[$group][$name]);    //делаем массив
					array_push($headers[$group][$name], $header); //кладем в массив
				}
			}
		}


		return $headers; //возвращаем массив с заголовками вида [header]=>value
	}
	
	
	//метод для авторизации
	public function auth()
	{
					
		$url = $this->protocol."://".$this->host.$this->basedir; //полный адрес страницы авторизации
		
		$curl = curl_init(); // инициализируем CURL
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //возврат результата передачи в качестве строки
		curl_setopt($curl, CURLOPT_HEADER, 1); //включаем заголовок в ответ
		curl_setopt($curl, CURLOPT_NOBODY, 1); // содержимое страницы нам не нужно
		curl_setopt($curl, CURLOPT_POST, 1); //передаем методом POST
		curl_setopt($curl, CURLOPT_POSTFIELDS, array("username"=>$this->username, "userpass"=>$this->userpass));
		curl_setopt($curl, CURLOPT_COOKIEJAR, __DIR__."tmp/cookie_".$this->host.".cookie");
		curl_setopt($curl, CURLOPT_COOKIEFILE, __DIR__."tmp/cookie_".$this->host.".txt");
		curl_setopt($curl, CURLOPT_URL, $url);
		
		$headers = curl_exec($curl); //у нас только заголовки
		$this->request = array("curl"=>curl_getinfo($curl), "server"=>$_SERVER);
		
		if($headers === FALSE)//если запрос не удался выбрасываем исключение
		{
			return new Exception("CURL запрос для авторизации завершился с ошибкой:\n\r".
			curl_errno($curl).": ".curl_error($curl), 11);			
		}

		return $headers;					
	}
	
	public $url;
	public $data;
	public function post($url, $cookie = null, $data = null)
	{
		if(empty($url))	return new Exception("Отсутствует обязательный параметр 'url'", 20);
		if(empty($cookie)) $cookie = $this->cookie;
		//url - полный адрес страницы с протоколом
		
		$curl = curl_init(); // инициализируем CURL
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //возврат результата передачи в качестве строки
		curl_setopt($curl, CURLOPT_HEADER, 0); //не включаем заголовок в ответ
		curl_setopt($curl, CURLOPT_NOBODY, 0); // содержимое страницы нам нужно
		curl_setopt($curl, CURLOPT_POST, 1); //передаем методом POST
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data); //передаваемые данные
		curl_setopt($curl, CURLOPT_COOKIE, $cookie); //куки	
		curl_setopt($curl, CURLOPT_URL, $url); //адрес
		$response = curl_exec($curl); //запрос
		$this->request = array("curl"=>curl_getinfo($curl), "server"=>$_SERVER);
		
		if($response === FALSE)//если запрос не удался - исключение
		{
			//$get_info = curl_getinfo($curl); //нужна инфа
			
			return new Exception("CURL запрос для POST завершился с ошибкой:\n".curl_errno($curl).": ".curl_error($curl), 21);			
		}
	
		if($this->request["curl"]["http_code"] == 200) //только если удачный запрос, завершаем нормально
		{
			//HTML страница (не менять кодировку здесь - отдать полностью обработчику как есть)
			return $response;	
		}
		else //иначе выбрасываем исключение
		{
			return new Exception("HTTP code ответа от сервера не равен 200 (".$url.")", 22);		
		}
	}
	
	/*  определение принадлежности подключенного устройства к */
	public static function purposes($device)
	{
		$purpose = array();
		
		/* принадлежность к мультиплексу */
		if(strpos($device["name"], "1 MUX") !== FALSE) $purpose["mux"] = 1;
		else if(strpos($device["name"], "2 MUX") !== FALSE) $purpose["mux"] = 2;
			
		/* функция устройства */
		
		if(strpos($device["type"], "Передатчик") !== FALSE) $purpose["func"] = "Tx";
		else if(strpos($device["type"], "Реплейсер") !== FALSE) 
		{
			$purpose["func"] = "Repl";
			$purpose["mux"] = 1;
		}
		
		
		/* мощность передатчика */
		if(strpos($device["type"], "Передатчик") !== FALSE)
		{
			if(strpos($device["name"], "250") !== FALSE) $purpose["power"] = 250;
			else if(strpos($device["name"], "500") !== FALSE) $purpose["power"] = 500;
			else if(strpos($device["name"], "1000") !== FALSE) $purpose["power"] = 1000;
			else if(strpos($device["name"], "2000") !== FALSE) $purpose["power"] = 2000;
			else if(strpos($device["name"], "5000") !== FALSE) $purpose["power"] = 5000;
			else if(strpos($device["name"], "100Вт") !== FALSE) $purpose["power"] = 100;
		}
		
		return $purpose;	
	}
	
	public function parse($html)
	{
		if(empty($html))	return new Exception("Пустой обязательный параметр 'html'", 20);
				
		if(!defined('phpQuery')) 	return new Exception("Не подключен парсер class.phpQuery.php", 21);	
		
		$html = phpQuery::newDocument($html);	
		
		//найти имя РТС
		$RCU_NAME = $html->find("div.menu_centre_name")->text();

		//путь к таблице с устройствами
		$Table = $html->find("table.pandora"); // таблица
		$Table = pq($Table);
			
		//если ошибка в поиске	
		if(!strlen($RCU_NAME) or !strlen($Table->text()))	
			return new Exception("Не найдена таблица устройств и имя РТС на странице. Возможно авторизация не удалась", 22);
		
		//содержание таблицы
		$Table_content = $html->find("table.pandora tr:gt(0)");
		$Table_content = pq($Table_content);;
		
		//возвращаемый массив с устройствами
		$DEVICES = array();
		
		//обходим массив таблицы девайсов
		//начинаем парсинг со второй строки, т.к. первая - заголовок
		foreach($Table_content as $tr) 
		{
			$tr = pq($tr);// к объекту phpQuery
			
			$one_device = array();//создаем новое устройство
			
			$one_device['url'] = $tr->find("a:first")->attr("href"); //url устройства
			
			/*
			обходим столбцы с 0 по 4
			0 - id
			1 - name
			2 - type
			3 - port
			4 - status_app (img)		*/
			
			$column = 0;
			foreach($tr->find("td a:lt(4)") as $a) //проходим по строкам
			{
				$a = pq($a);//phpQuery
				
				$value = addslashes($a->text());


				switch($column)
				{
					case 0:{	
						$one_device["id"] = $value; //добавляем свойство 
						break;
					}
					case 1:{	
						$one_device["name"] = $value; //добавляем свойство 
						break;
					}
					case 2:{	
						$one_device["type"] = $value; //добавляем свойство 
						break;
					}
					case 3:{
						$one_device["port"] = $value; //добавляем свойство 
						break;
					}
					
					case 4:{
						$img = $a->html();
						$img = pq($img);
						$value = str_replace("status_app_","",$img->attr("id"));	
						$one_device["status_app"] = $value; //добавляем свойство 		
						break;	
					}
					
					default:{
						
					}
				}				
				
				$column += 1;
			}

			$one_device["purposes"] = $this->purposes($one_device);
			$DEVICES[] = $one_device;	
		}
		
		/*
		
		добавить группировку
		1 MUX =
		2 MUX =
		СДК
		(или какие то параметры поиска устройства)
		
		*/
		
		return array(
		"rcu_name"=>$RCU_NAME, 
		"count"=>sizeof($DEVICES), 
		"devices"=>$DEVICES, 
		"hash"=>md5($Table->text())		
		);
		
	}
	
	
	
}


?>