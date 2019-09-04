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
class _10000
{
	public $Info;
	
	public $Purposes;
	public $Power = 0;
	public $action;
	
	public $id;
	
	public function __construct($action, $id, $purposes) //запустить функцию в зависимости от action monitoring/modulator/sfn
	{
		$action = explode("/",$action);
		$this->action = $action;
		
		$this->Purposes = $purposes;
		$this->Power = $purposes["power"];
		$this->id = $id;
		
		if($this->Power == 0) return new Exception("Передатчик не определен. Мощность 0 Вт");
		
		switch($action[0])
		{
			case "monitoring":{
				
				switch($action[1])
				{
					case "modulator":{
						
						switch($action[2])
						{
								case "sfn":{
									//какие данные нужны чтобы найти галочку sfn?
									//есть ли она тут вообще? может это мощный передатчик
									//url до галочки
									//название параметра
									
									//вызываем функцию которая даст нам информацию какие данные отправить чтобы получить страницу с SFN галочкой
									$this->Info = $this->monitoring_modulator_sfn();
								
								
									break;
								}
								case "input_primary":{
									// получить основной вход модулятора
									$this->Info = $this->monitoring_modulator_inputPrimary();
									break;
								}
								
								case "input_secondary":{
									// получить дополнительный вход модулятора
									$this->Info = $this->monitoring_modulator_inputSecondary();
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
			
			default:{
				$this->Info = "первое действие не определено";
			}
		}
	}



	public function monitoring_modulator_sfn()
	{
		/*	
		$data = array("url"=>"/config/exc_tvt_p/1/control/?id={id}", 
					"find"=>"t2SfnSynchronization",
					"power"=>$this->Power);
		
		$data["callback"] = function($html){
		
			$html = phpQuery::newDocument($html);	
		
			//найти имя РТС
			$primary = $html->find("select[name=primarySource] option:selected")->text();
		
			
			return $primary;
		
		};
		
		return $data;
		*/
	}
	
	/*
	//один тип 
	100 - Один модулятор
	250 - один модулятор
	500 - один модулятор
	1000 - два модулятора
	
	//другой тип
	2000 - два модулятора
	5000 - два модулятора
	*/

	public function monitoring_modulator_inputPrimary()
	{
		
		$array_url = array(
			"100" => "/config/mt2/input/?id={id}",
			"250" => "/config/mt2/input/?id={id}",
			"500" => "/config/mt2/input/?id={id}",
			"1000" => array("/config/mt2/0/input/?id={id}", "/config/mt2/1/input/?id={id}"),
			"2000" => array("/config/exc_tvt_p/0/control/?id={id}", "/config/exc_tvt_p/1/control/?id={id}"),
			"5000" => array("/config/exc_tvt_p/0/control/?id={id}", "/config/exc_tvt_p/1/control/?id={id}")
		);
		
		$array_param = array(
			"100" => "inpu1TsSource",
			"250" => "inpu1TsSource",
			"500" => "inpu1TsSource",
			"1000" => "inpu1TsSource",
			"2000" => "primarySource",
			"5000" => "primarySource");

		$data = array("url"=>str_replace("{id}", $this->id, $array_url[$this->Power]), 
					"find"=>$array_param[$this->Power],
					"power"=>$this->Power);
		
		$data["callback"] = function($html, $find){
		
			$html = phpQuery::newDocument($html);	
			$primary = $html->find("select[name=".$find."] option:selected")->text();
			
			return $primary;
		
		};
		
		return $data;
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
		
		$array_param = array(
			"100" => "inpu2TsSource",
			"250" => "inpu2TsSource",
			"500" => "inpu2TsSource",
			"1000" => "inpu2TsSource",
			"2000" => "secondarySource",
			"5000" => "secondarySource");

		$data = array("url"=>str_replace("{id}", $this->id, $array_url[$this->Power]), 
					"find"=>$array_param[$this->Power],
					"power"=>$this->Power);
		
		$data["callback"] = function($html, $find){
			
			$html = phpQuery::newDocument($html);	
			$secondary = $html->find("select[name=".$find."] option:selected")->text();
			
			return $secondary;	
		};
		
		return $data;
	}
	
	/*
	
	поддерживаемые функции
	$allow_actions = array(
		"monitoring"=>array("input", "lock", "sfn", "gps"),
	
		);
	*/
	
	public function __invoke()
	{
			return $this->Device;
	}
	
	public $type_name = "";
	public $type_id = "10000";

	
	
	public function getForm($reserve = false)
	{
		
		$form = array();
		switch($this->power)
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
		$this->form = $form;
		return $form;
	}
	
		
	
}





?>