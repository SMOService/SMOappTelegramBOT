<?php 
include 'data.ini.php';

include "global.php";
$link = mysqli_connect($hostName, $userName, $password, $databaseName) or die ("Error connect to database");
mysqli_set_charset($link, "utf8");

$params = array(
"user_id" => $user_id,
"api_key" => $api_key,
"action" => 'services'
);

// sending POST request
$myCurl = curl_init();
curl_setopt_array($myCurl, array(
    CURLOPT_URL => 'https://smoservice.media/api/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($params)
));
$response = curl_exec($myCurl);
curl_close($myCurl);

if($response != '') mysqli_query($link, "TRUNCATE TABLE `smoservices`");

$data = json_decode($response, true);
foreach ($data['data'] as $key => $value) {
$colmns = "";
$values = "";
	foreach ($value as $key2 => $value2) {
		$colmns .= "`".$key2."`,";
		$values .= "'".$value2."',";		
	}
	
	$colmns = substr($colmns, 0, -1);
	$values = substr($values, 0, -1);
	
	$str2ins = "INSERT INTO `smoservices` ($colmns) VALUES ($values)";
	mysqli_query($link, $str2ins);	
}


?>