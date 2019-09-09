<?


// Реплейсер Nevion
// phpQuery needed


class Device extends _35810{} //класс инициализации для запуска любого типа устройства

class _35810 extends Template
{
	public $method = "snmp";
	
	public function action() //запустить функцию в зависимости от action 
	{
		switch($this->action[0])
		{
			case "get":{
				
				
			}
			case "set":{}	
			default:{
				$this->Info
			}	
		}
	}

}
?>