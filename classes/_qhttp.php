<?php

/*
echo "<pre>";

$getter = new SimpleXMLElement($getter);
print_r($getter);
echo "</pre>";
*/



#$cookie = $_GET["cookie"]; //RCUSESSID=3519bf5444f2dc7c789a474f46f4ca0a; path=/


$getter = get($_GET["url"], $_GET["cookie"]);

echo "<textarea style='width: 810px; height: 250px; margin: 0px;'>";
echo $getter;
echo "</textarea><hr>";

/* #формирование массива
$getter = new SimpleXMLElement($getter);
$array = [];
foreach($getter->value as $i => $object)
{
	$array[(string)$object->attributes()["id"]] = (string)$object->attributes()["val"];
}
pre($array);
*/


function get($url, $cookie)
{
	
	$curl = curl_init(); // инициализируем CURL
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //возврат результата передачи в качестве строки
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
	curl_setopt($curl, CURLOPT_HEADER, true); //включить заголовки в вывод
	curl_setopt($curl, CURLOPT_NOBODY, false ); // при true  = HTTP::HEAD, при false = включает тело в ответ
	
	#рабочий вариант передачи cookie
	curl_setopt($curl, CURLOPT_COOKIE, $cookie); //куки	
	
	#curl_setopt($curl, CURLOPT_POST, 1); //передаем методом POST
	#curl_setopt($curl, CURLOPT_POSTFIELDS, ["username"=>"admin", "userpass"=>"kq9OXFVTKb"]);
	
	
	//curl_setopt($curl, CURLOPT_COOKIEJAR,  __DIR__."/tmp/cookie.txt");
	
	//не этот
	//curl_setopt($curl, CURLOPT_COOKIEFILE, __DIR__."tmp/cookie_".$this->host.".txt");
	curl_setopt($curl, CURLOPT_URL, $url);
	
	$response = curl_exec($curl);

	return $response;

	
}



function pre($a, $n = null)
{
	echo "<pre>";
	echo $n;
	print_r($a);
	echo "</pre>";
}










