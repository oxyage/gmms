<?

// Блок Модулятор передатчика
// phpQuery needed


class Device extends _10000{} //класс инициализации для запуска любого типа устройства
//результат работы класса должен быть следующий
/*
1 режим - на получение данных
результат этого режима будет отправлен методом RCU->post на определенную страницу с определенными данными (куки не нужны здесь)


2 режим - на интерпретацию данных
результат этого режима будет распарсенная страница с выводом только результатов

доступные функции зависят от мощности передатчика
*/
class _10000 extends Template
{
	
#	public $Purposes;
	public $Power = 0;
#	public $MUX;
	
	//data - содержит переданные в экземпляр данные (id, purposes)
	//action - содержит действие шаблона
	
	public function action() //запустить функцию в зависимости от action 
	{
		$this->Power = $this->device_info["power"];
		
/*
		$this->Purposes = $data["purposes"];
		$this->Power = $purposes["power"];

		$this->MUX = $purposes["mux"];
		
		if($this->Power == 0) return new Exception("Передатчик не определен. Мощность 0 Вт");
	*/	
		switch($this->action[0])
		{
			case "monitoring":{
				
				switch($this->action[1])
				{
					case "modulator":{
						
						switch($this->action[2])
						{
								case "sfn":{
									//какие данные нужны чтобы найти галочку sfn?
									//есть ли она тут вообще? может это мощный передатчик
									//url до галочки
									//название параметра
									
									//вызываем функцию которая даст нам информацию какие данные отправить чтобы получить страницу с SFN галочкой
									$this->monitoring_modulator_sfn();
								
								
									break;
								}
								case "inputPrimary":{
									
									// получить основной вход модулятора
									
									$this->monitoring_modulator_inputPrimary();
									
									break;
								}
								
								case "inputSecondary":{
									
									// получить дополнительный вход модулятора
									$this->monitoring_modulator_inputSecondary();
									
									break;
								}
							
								default:{
									$this->Info =  "третье действие не определено";
								}
						}	
						
						break;
					}
					
					default:{
						$this->Info = "второе действие не определено";
					}
					
				}
				
				
				break;
			}
			case "get_form":{
				
				switch($this->action[1])
				{
					case "modulator":{
				
							switch($this->action[2])
							{
								case "input":{
									
									//$this->get_form_modulator_input();
									
									break;
								}
								default:{
									$this->Info = "action[2] undefined";
								}
							}
				
						break;
					}
					default:{
					
						$this->Info = "action[1] undefined";
					}
				}
				
				
				break;
			}
			
			default:{
				$this->Info = "первое действие не определено";
			}
		}
	}

	public function monitoring_modulator_sfn(){	}
	public function monitoring_modulator_inputPrimary()	{

		$array_url = array(
			"100" => "/config/mt2/input/?id={id}",
			"250" => "/config/mt2/input/?id={id}",
			"500" => "/config/mt2/input/?id={id}",
			"1000" => array("/config/mt2/0/input/?id={id}", "/config/mt2/1/input/?id={id}"),
			"2000" => array("/config/exc_tvt_p/0/control/?id={id}", "/config/exc_tvt_p/1/control/?id={id}"),
			"5000" => array("/config/exc_tvt_p/0/control/?id={id}", "/config/exc_tvt_p/1/control/?id={id}")
		);


		if(is_array($array_url[$this->Power]))
			$this->POST_url = str_replace("{id}", $this->device_info["id"], $array_url[$this->Power]); // вернуть массив адресов для запросов
		else
			$this->POST_url[] = str_replace("{id}", $this->device_info["id"], $array_url[$this->Power]); // вернуть массив адресов для запросов
		
		#return $this; //debug
		
		$this->callback["page"] = function($device_info, $POST_result = array()){
			
			$result = array();
			
			
			// DEBUG! inpu1TsSource primarySource 
			$array_param = array(
			"100" => "inpu2TsSource",
			"250" => "inpu2TsSource",
			"500" => "inpu2TsSource",
			"1000" => "inpu2TsSource",
			"2000" => "secondarySource",
			"5000" => "secondarySource");
			
			$power = $device_info["power"];

			foreach($POST_result as $i => $html)
			{
				$html = phpQuery::newDocument($html);	
				$primary = $html->find("select[name=".$array_param[$power]."] option:selected")->text(); 
				$primary_value = $html->find("select[name=".$array_param[$power]."]")->val(); 
				$primary_text_value = $html->find("select[name=".$array_param[$power]."] option:selected")->text(); 
				$result["text"][] = $primary;
				$result["values"][] = $primary_value;
				$result["text_values"][] = str_replace(" ","",$primary_text_value);
			}

			return $result;			
		}; //callback[page]

		$this->callback["represent"] = function($device_info, $POST_callback = array()){
			
			#debug
			#return $POST_callback;
			
			$text = $device_info["mux"]." MUX: ";
			
			$ASItoSAT = array("ASI1"=>"40°", "ASI 1"=>"40°","ASI2"=>"53°", "ASI 2"=>"53°");
			
			foreach($POST_callback as $i => $a)
			{	
				$text .= $ASItoSAT[$a]."; ";
			}
			
			return $text;
		};


	}
	
	public function monitoring_modulator_inputSecondary()
	{
		$array_url = array(
			"100" => "/config/mt2/input/?id={id}",
			"250" => "/config/mt2/input/?id={id}",
			"500" => "/config/mt2/input/?id={id}",
			"1000" => array("/config/mt2/0/input/?id={id}", "/config/mt2/1/input/?id={id}"),
			"2000" => array("/config/exc_tvt_p/0/control/?id={id}", "/config/exc_tvt_p/1/control/?id={id}"),
			"5000" => array("/config/exc_tvt_p/0/control/?id={id}", "/config/exc_tvt_p/1/control/?id={id}")
		);


		if(is_array($array_url[$this->Power]))
			$this->POST_url = str_replace("{id}", $this->device_info["id"], $array_url[$this->Power]); // вернуть массив адресов для запросов
		else
			$this->POST_url[] = str_replace("{id}", $this->device_info["id"], $array_url[$this->Power]); // вернуть массив адресов для запросов
		
		$this->callback["page"] = function($device_info, $POST_result = array()){
			
			$result = array();
			
	
			$array_param = array(
			"100" => "inpu2TsSource",
			"250" => "inpu2TsSource",
			"500" => "inpu2TsSource",
			"1000" => "inpu2TsSource",
			"2000" => "secondarySource",
			"5000" => "secondarySource");
			
			$power = $device_info["power"];
			
			
			foreach($POST_result as $i => $html)
			{
				$html = phpQuery::newDocument($html);	
				$secondary = $html->find("select[name=".$array_param[$power]."] option:selected")->text(); 
				$result[] = $secondary;
			}

			return $result;			
		};
		
		$this->callback["represent"] = function($device_info, $POST_callback = array()){
			/*	
		
			$text = $device_info["mux"]." MUX: ";
			
			foreach($POST_callback as $i => $a)
			{			
				$text .= $a."; ";	 //АНИКАК
			}
			
			return $text;
			*/	
			
			return $POST_callback;
		};
		/*		
		$data["represent"] = function($array){
			
			//как представить результат
			
			$text = $this->MUX." MUX: ";
			if(!is_array($array))
			{
					$text .= $this->AsiToSat($array);
					return $text;
			}
			
			foreach($array as $i => $a)
			{			
				$text .= $this->AsiToSat($a)."; ";	
			}
			
			return $text;
		};
		*/

	}
	
	
	public function AsiToSat($asi)
	{
		switch($asi)
		{
			case "ASI 1":{}
			case "ASI1":{
				return "40°";
				break;
			}
			case "ASI 2":{}
			case "ASI2":{
				return "53°";	
				break;
			}
			default:{
				return $asi;	
			}
		}
	}
	
	/*
	
	поддерживаемые функции
	$allow_actions = array(
		"monitoring"=>array("input", "lock", "sfn", "gps"),
	
		);
	*/
	
	
	/*
	парсинг главной страницы устройства
		$html = phpQuery::newDocumentHTML($html);
	
				if(strlen($html->find("span#idSysDeviceTypeID")->text()) < 2)
				{

					$device_type_id = $html->find("div.skoll")->text();	
					//переработать технологию поиска
					// это не пробелы
					$device_type_id = explode(" ",$device_type_id);
					$device["type_id"] = intval($device_type_id[2]);
				}
				else	
				{		
					$device["type_id"] = intval($html->find("span#idSysDeviceTypeID")->text());	
				}
				

				$device["type_name"] = $html->find("td.ctrl_panel table:eq(0) table:eq(0)")->text();	
	
	
	*/

		
	
}





?>