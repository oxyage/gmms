<?php


/*
Класс для Базы данных, методы работы с ней
*/
class db
{
	private $host = "localhost";
	private $user = "gmms";
	private $pass = "gmms";
	private $name = "gmms";

	public $link;
	
	public function __construct($args = false)
	{
		if(is_array($args)) {//если передали массив, проходим по нему
			foreach($args as $key => $value)//
			{
				if(isset($this->$key))	$this->$key = $value;
				else
				{
					return new Exception("Ошибка создания объекта db: неверный параметр");
				}
			}
		}
	}
	
	public function connect()
	{
		$connect = mysql_connect($this->host, $this->user, $this->pass);
		if(!$connect)		return new Exception("Ошибка подключения к БД. ".mysql_error());
		else
		{
			$select_db = mysql_select_db($this->name, $connect);
			if($select_db !== FALSE) return $this->link = $connect;
			else return new Exception("Ошибка выбора базы данных. ".mysql_error());
		}		
	}
	
	public function __invoke(){
		return $this->link;
	} 	
	
	public function query($sql)
	{
		$result = mysql_query($sql);
		if($result == FALSE) return new Exception("Запрос SQL завершился ошибкой. ".mysql_error());
		else return $result;
	}
	
	public function fetch_assoc($result_sql)
	{
		$result = array();
		while($row = mysql_fetch_assoc($result_sql))
		{
			array_push($result, $row);
		}
		return $result;
	}
	
}






?>