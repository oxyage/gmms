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
			case "management":{
				switch($this->action[1])
				{
					case "modulator":{
						
						switch($this->action[2])
						{
							case "toASI1":{
								
								//$this->get_form_modulator_input();
								
								$this->management_modulator_toASI1();
								
								break;
							}
							case "toASI2":{
								
								//$this->get_form_modulator_input();
								
								$this->management_modulator_toASI2();
								
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
				$this->Info = "action[0] undefined";
			}
		}
	}

	public $post_data = array();
	
	public function management_modulator_toASI1(){
		
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
	
		$this->post_data = $this->getForm();
		
		
	}
	public function management_modulator_toASI2(){
		
		
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
	
		$this->post_data = $this->getForm(true);
		
	}
	
	
	public function getForm($reserve = false)
	{
		
		$form = array();
		switch($this->Power)
		{
			case "100":{}
			case "250":{}
			case "500":{}
			case "1000":
			{
				$form = array(
						"inpu1TsSource" => 1, //источник входа 1
						"inpu2TsSource" => 2, //источник входа 2
						//"cleverSwitching" => false, //интеллектуальное переключение
						//"cleverAutoSwitchBack" => false, //автопереключение назад
						"cleverErrorThreshold" => 5,	 //допустимое количество ошибок
						"cleverValidThreshold" => "80000",//количество нормальных пакетов для переключения назад

						"cleverPrimaryInputIP" => 0, //основной вход
					//	"cleverSwitchIP" => false, //интеллектуальное переключение
					//	"cleverAutoSwitchBackIP" => false, //автопереключение назад
						"cleverRtpPacketTimeoutIP" => 3, // время отсутствия RTP пакетов
						"cleverRtpValidPacketTimeIP" => 3, //время приема RTP пакетов

					//	"ip1Dhcp" => false, // DHCP on
						"ip1IP" => "192.168.250.209", // IP 1 адрес
						"ip2IP" => "192.168.1.210", // IP 2 адрес
						"ip1Subnet" => "255.255.255.0", // ip 1 маска
						"ip2Subnet" => "255.255.255.0", // ip 2 маска
						"ip1Gateway" => "192.168.250.10", // ip 1 шлюз
						"ip2Gateway" => "192.168.1.254", // ip 2 шлюз
						"ip1VlanID" => 0, // Идентификатор VLAN ip 1
						"ip2VlanID" => 0, // идентификатор vlan ip 2

						"ip1RxReception" => 1, //режим приема IP 1
						"ip2RxReception" => 1, //режим приема IP 2
						"ip1RxIgmpVersion" => 0, //версия IGMP ip 1
						"ip2RxIgmpVersion" => 0, //версия IGMP ip 2
						"ip1RxUdpPort" => "1234", // udp порт ip 1
						"ip2RxUdpPort" => "1234",// udp порт ip 2
						"ip1RxMulticast" => "224.1.2.1", // адрес multicast группы ip 1
						"ip2RxMulticast" => "224.1.2.2", // адрес multicast группы ip 2
						"ip1RxUdpTimeout" => "30", //время ожидания приема UDP ip 1
						"ip2RxUdpTimeout" => "30", //время ожидания приема UDP ip 2
						"ip1RxLatency" => "100", //задержка в приемнике ip 1
						"ip2RxLatency" => "100",//задержка в приемнике ip 2

						"ip1IgmpMode" => 0,		//Режим фильтрации
						"ip2IgmpMode" => 0);
						
				if($reserve)
				{
					$form["inpu1TsSource"] = 2;
					$form["inpu2TsSource"] = 2;
				}
				else
				{
					$form["inpu1TsSource"] = 1;
					$form["inpu2TsSource"] = 2;
				}	
						
						
				break;
			}
			case "2000":{}
			case "5000":
			{
				$form = array(
					"primarySource" => 1, //источник основного входа 1, 2, 3, 4
					"secondarySource" => 1, //источник доп входа 1, 2, 3, 4
					"routingPolicy" => 1, //политика переключения 1,2,3,4
					"referenceSource" => 4, //источник опорного сигнала 1, 2, 3, 4
					"submit"=>"%CF%F0%E8%EC%E5%ED%E8%F2%FC" );
							
				if($reserve)
				{
					$form["primarySource"] = 2;
					$form["secondarySource"] = 2;
				}
				else
				{
					$form["primarySource"] = 1;
					$form["secondarySource"] = 1;
				}	
				break;
			}
			default:
			{			
				$form = "default";
			}
		}
		$this->post_data = $form;
		return $form;
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
			
			#########################################################
			// DEBUG! 
			// inpu1TsSource 
			// primarySource 
			#########################################################
			$array_param = array(
			"100" => "inpu1TsSource",
			"250" => "inpu1TsSource",
			"500" => "inpu1TsSource",
			"1000" => "inpu1TsSource",
			"2000" => "primarySource",
			"5000" => "primarySource");
			
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