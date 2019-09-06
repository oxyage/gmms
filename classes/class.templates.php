<?


abstract class anyDevice
{
	public $action;
	public $Info; //для результата работы методов
	
	
	
	public $id;
	public $name;
	public $type;
	public $type_id;
	public $type_name;
	public $url;
	public $snmp_id;
	public $port;
	public $status_app;
	
	
	abstract public function __construct($action);//для вызова определенного метода
	
	
	
	
}





?>