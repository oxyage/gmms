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


*/
class _10000
{
	public $Info;
	
	public $Purposes;
	public $Power = 0;
	public $action = "";
	
	public function __construct($action, $purposes) //запустить функцию в зависимости от action monitoring/modulator/sfn
	{
		$action = explode("/",$action);
		$this->action = $action;
		
		$this->Purposes = $purposes;
		$this->Power = $purposes["power"];
		
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
		
		
		
		$data = array("url"=>"/config/exc_tvt_p/1/control/?id={id}", 
					"find"=>"t2SfnSynchronization",
					"power"=>$this->Power);
		
		$data["callback"] = function($html){
		
			$html = phpQuery::newDocument($html);	
		
			//найти имя РТС
			$primary = $html->find("select[name=primarySource] option:selected")->text();
			/**/
			
			return $primary;
		
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
	
	/*public $general_info;
	public $power;
	public $titan;
	public $input_url;
	public $input_name;

	public $form;*/
	
	public $type_name = "";
	public $type_id = "10000";

	
	public $TxPower = array(	"100"=>"Полярис ТВЦ2-100",
							"250"=>"Полярис ТВЦ2-200/250",
							"500"=>"Полярис ТВЦ2-300/500",
							"1000"=>"Полярис ТВЦ2-1000",
							"2000"=>"Полярис ТВЦ/ТВЦ2-2000",
							"5000"=>"Полярис ТВЦ/ТВЦ2-5000");		

	/*	41000	40250	40501	40100	20700	20800	*/	
	
	//WORK
	public function getInputURLbyPower($id)
	{
		switch($this->power)
		{
			case "100":{}
			case "250":{}
			case "500":
			{
				$this->input_url = "/config/mt2/input/?id=".$id;
				$this->input_name = "inpu1TsSource";
				break;
			}
			case "1000":
			{
				$this->input_url[0] = "/config/mt2/0/input/?id=".$id;
				$this->input_url[1] = "/config/mt2/1/input/?id=".$id;	
				$this->input_name = "inpu1TsSource";			
				break;
			}
			case "2000":{}
			case "5000":
			{
				$this->input_url[0] = "/config/exc_tvt_p/0/control/?id=".$id;	
				$this->input_url[1] = "/config/exc_tvt_p/1/control/?id=".$id;	
				$this->input_name = "primarySource";
				break;
			}
		
			default:{}
		}
	}	

	//WORK
	public function setType($type_name) // определяем мощность по имени типа
	{
		foreach($this->TxPower as $power => $model)
		{
			if(false !== strpos($type_name, $model))
			{

			$this->power = $power;
			}
		}
	}
	
	public function parseForm()
	{
		$find = "";
		switch($this->power)
		{
			case "100":{}
			case "250":{}
			case "500":{}
			case "1000":
			{
				$find = $this->pqHTML->find("select[name=inpu1TsSource] option:selected")->text();	
				break;
			}
			case "2000":{}
			case "5000":
			{
				$find = $this->pqHTML->find("select[name=primarySource] option:selected")->text();		
				break;
			}
			default:{			
			$find = "default";
			}
		}
	
		
		return $find;
	}
	
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
	
		
	public function init()
	{	
	#$this->name = $this->pqHTML->find("div.skathi")->text();
	#$this->general_info = $this->pqHTML->find("span#idSysModel")->text();
	#$this->DefinePower();
	//здесь же разбираем левое меню для нахождения ссылок на управление входными сигналами
	
	#$this->titan = $this->pqHTML->find("table.titan")->text();	
	
	
	
	
	}
	/*
	public function info()
	{
		
		return array(
		"HTML"=>$this->HTML,
		"titan"=>$this->titan,
		"input_url" => $this->input_url,
		"power" => $this->power
		);
		#
		#"name" => $this->name,
		#
		#"general_info" => $this->general_info,
		#"type_id" => $this->type_id);
	
	}
*/
	/*
	
	этот класс должен содержать методы доступа к различным страницам устройства
	
	
	*/




}




?>