<?

// Блок СДК - 5.3
// phpQuery needed


class init extends _33000{}//класс инициализации для запуска любого типа устройства
class _33000 extends Device
{

	public $TemperatureProcessor;
	public $InnerTermoscan;
	public $foot_panel;	
		
	//номер измерения - страница где его найти	
	
	public $assocURL = array(
	"12" => "/config/rcu/?id=%id%",
	"13" => "/config/rcu/?id=%id%",
	"11" => "/config/rcu/dry-out/?id=%id%"
	);

	
		
	public function info()
	{
		
		return array(
		"innertermoscan" => $this->InnerTermoscan,
		"tempprocessor" => $this->TemperatureProcessor,
		"foot_panel" => $this->foot_panel
		);
	
	}
	
	
	public function init()
	{	
		$this->name = $this->pqHTML->find("div.device_name")->text();
	
		//оставить пояснение к селекторам
		$this->TemperatureProcessor = $this->pqHTML->find("table.tethys:eq(1) tr:eq(1) td:eq(2)")->text();
		$this->InnerTermoscan = $this->pqHTML->find("table.tethys:eq(1) tr:eq(2) td:eq(2)")->text();
		$this->foot_panel = $this->pqHTML->find("td.foot_panel_2 b")->text();	
	}

	//этот класс должен содержать методы доступа к различным страницам устройства
	
	



//	public function 
	





}



?>