<?

// Thomson
// phpQuery needed


class Device extends _37400{} //класс инициализации для запуска любого типа устройства
//результат работы класса должен быть следующий
/*
1 режим - на получение данных
результат этого режима будет отправлен методом RCU->post на определенную страницу с определенными данными (куки не нужны здесь)


2 режим - на интерпретацию данных
результат этого режима будет распарсенная страница с выводом только результатов

доступные функции зависят от мощности передатчика
*/
class _37400 extends Template
{

#	public $Purposes;
	public $Power = 0;
#	public $MUX;

	//data - содержит переданные в экземпляр данные (id, purposes)
	//action - содержит действие шаблона

	public function action() //запустить функцию в зависимости от action
	{
		$this->Power = $this->device_info["power"];

		switch($this->action[0])
		{
			case "monitoring":{

				switch($this->action[1])
				{
					case "modulator":{

						switch($this->action[2])
						{
								case "inputPrimary":{

									// получить основной вход модулятора

									$this->monitoring_modulator_inputPrimary();

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

/* ############################ ИЗМЕНИТЬ ССЫЛКУ ###############################################################*/
		$array_url = array(
			"5000" => "/config/mt2/input/?id={id}"
		);


		if(is_array($array_url[$this->Power]))
			$this->POST_url = str_replace("{id}", $this->device_info["id"], $array_url[$this->Power]); // вернуть массив адресов для запросов
		else
			$this->POST_url[] = str_replace("{id}", $this->device_info["id"], $array_url[$this->Power]); // вернуть массив адресов для запросов

		$this->post_data = $this->getForm();


	}
/* ############################ ИЗМЕНИТЬ ССЫЛКУ ###############################################################*/
	public function management_modulator_toASI2(){

		$array_url = array(
			"5000" => "/config/mt2/input/?id={id}"
		);


		if(is_array($array_url[$this->Power]))
			$this->POST_url = str_replace("{id}", $this->device_info["id"], $array_url[$this->Power]); // вернуть массив адресов для запросов
		else
			$this->POST_url[] = str_replace("{id}", $this->device_info["id"], $array_url[$this->Power]); // вернуть массив адресов для запросов

		$this->post_data = $this->getForm(true);

	}

/* ############################ ИЗМЕНИТЬ ФОРМУ ###############################################################*/
	public function getForm($reserve = false)
	{

		$form = array();
		switch($this->Power)
		{
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

/* ############################ ИЗМЕНИТЬ ССЫЛКУ ###############################################################*/
	public function monitoring_modulator_inputPrimary()	{

		$array_url = array(
			"5000" => "/config/mt2/input/?id={id}"
		);


		if(is_array($array_url[$this->Power]))
			$this->POST_url = str_replace("{id}", $this->device_info["id"], $array_url[$this->Power]); // вернуть массив адресов для запросов
		else
			$this->POST_url[] = str_replace("{id}", $this->device_info["id"], $array_url[$this->Power]); // вернуть массив адресов для запросов

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

}





?>
