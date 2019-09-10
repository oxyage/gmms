<?


abstract class Template
{
	public $action; //массив действия
	public $data; // переданные данные при создании экземпляра
	
	public $method = "http";
	
	public $POST_url = array(); //массив адресов на которые посылать запрос
	public $POST_result = array(); //массив ответов от страниц
	public $POST_callback = array(); //массив результатов после обработки коллбеком
	public $POST_represent = array(); //массив результатов в читаемом представлении
	
	public $callback = array("page"=>false, "represent"=>false); //установить колбеки
	
	#public $purposes; //доп данные от скрипта ЕСЛИ НУЖНЫ ВООБЩЕ
	
	
	
	public $Info = array("full" => "", "represent"=>""); //для результата работы методов
	
	public $device_info = array(); //информация о переданном устройстве
	
	public function __construct($action, $data)//для вызова определенного метода
	{
		$action = explode("/",$action);
		$this->action = $action;
		$this->data = $data;
		
		//$this->purposes = isset($data["purposes"]) ? $data["purposes"] : NULL;
		
		$this->device_info = $data["device"];
		

		$this->action(); //запускаем action
	}
	
	abstract public function action(); // описание логики действий по маршруту act1/act2/act3
	
	public function info()// представить массив в виде строки
	{
		foreach($this->POST_callback as $i => $value) // AS1; ASI2
			$this->Info["full"] .= $value."; ";
			
		#foreach($this->POST_callback as $i => $value) // должен быть  40°; 53°
		#	$this->Info["represent"] .= $value."; ";
		$this->Info["represent"] = $this->POST_represent;
	}
	
	
	
	//abstract public function callback();
	
	
	
}





?>