<?php 
include 'data.ini.php';

$tofile = '';
foreach($_POST AS $key => $value) {
    ${$key} = trim(filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS)); 
	$tofile .= $key.':'.$value.'
';
} // end FOREACH

if($file = fopen("response.txt", "w+")){
		fputs($file, $tofile);
		fclose($file);
} // end frite to file

define('TOKEN', 'ВВЕДИТЕ СВОЙ ТЕЛЕГРАМ ТОКЕН');

include "global.php";
$link = mysqli_connect($hostName, $userName, $password, $databaseName) or die ("Error connect to database");

$data = $_POST;
ksort($data);
$str = http_build_query($data);
$sign2 = md5($str . $roskassa_secretkey);

$tofile = "
===========
".$str."
sign from roskassa: ".$sign."
sign from script: ".$sign2;
if($file = fopen("response.txt", "a+")){
		fputs($file, $tofile);
		fclose($file);
} // end frite to file

#if($sign != $sign2) exit();

$orderstarted = '';

// check for pending order
$str2select = "SELECT * FROM `temp_sess` WHERE `chatid`='$order_id' AND (`otipe`='order' AND `waitpayment`='1') ORDER BY `rowid` DESC LIMIT 1";
$result = mysqli_query($link, $str2select);
if(mysqli_num_rows($result) != 0){
	$row = @mysqli_fetch_object($result);	
		
		$str3select = "SELECT * FROM `smoservices` WHERE `id`='$row->serviceid'";
		$result3 = mysqli_query($link, $str3select);
		$row3 = @mysqli_fetch_object($result3);	
		$ordersum = $row->volume * $row3->price;
		$curtime = time();
		
		$str2ins = "INSERT INTO `orders` (`chatid`,`serviceid`,`volume`,`sum`,`times`) VALUES ('$order_id','$row->serviceid','$row->volume','$ordersum','$curtime')";
		mysqli_query($link, $str2ins);
		$orderno = mysqli_insert_id($link); 
		
		$amount = $amount-$ordersum;
		
		//SEND ORDER TO API smoservice
		##############################
		$params = array(
		"user_id" => $user_id,
		"api_key" => $api_key,
		"action" => 'create_order',
		"service_id" => $row->serviceid,
		"count" => $row->volume,
		"url" => $row->page
		);
		
		$myCurl = curl_init();
		curl_setopt_array($myCurl, array(
			CURLOPT_URL => 'https://smoservice.media/api/',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query($params)
		));
		$response = curl_exec($myCurl);
		curl_close($myCurl);
		
		$smodata = json_decode($response, true);
		##############################		
		//SEND ORDER TO API smoservice
		
		if($smodata['type'] == 'success'){
			
			$str3upd = "UPDATE `orders` SET `smoorderid`='".$smodata['data']['order_id']."' WHERE `rowid`='$orderno'";
			mysqli_query($link, $str3upd);
			
			//Referrals
			$str5select = "SELECT * FROM `users` WHERE `chatid`='$order_id'";
			$result5 = mysqli_query($link, $str5select);
			$row5 = @mysqli_fetch_object($result5);
			
			if($row5->ref != 0){
				$earn = ($ordersum/100)*$refpercent;
				$str4upd = "UPDATE `balance` SET `sum`=`sum`+$earn WHERE `chatid`='$row5->ref'";
				mysqli_query($link, $str4upd);
####################### 2022 #####################
				$str10upd = "UPDATE `users` SET `refbalance`=`refbalance`+$earn WHERE `chatid`='$row5->ref'";
				mysqli_query($link, $str10upd);		
####################### 2022 #####################
			}
			//Referrals			
		
		$orderstarted = '
Заказ отправлен в обработку';
		
		}
		
		$str2del = "DELETE FROM `temp_sess` WHERE `rowid` = '$row->rowid'";
		mysqli_query($link, $str2del);		
}
// check for pending order

$amount = number_format($amount, 2, '.', '');

$str2select = "SELECT * FROM `balance` WHERE `chatid`='$order_id'";
$result = mysqli_query($link, $str2select);
if(mysqli_num_rows($result) == 0){
	
	$str2ins = "INSERT INTO `balance` (`chatid`,`sum`) VALUES ('$order_id','$amount')";
	mysqli_query($link, $str2ins);

}else{
	
	$str2ins = "UPDATE `balance` SET `sum`=`sum`+$amount WHERE `chatid`='$order_id'";
	mysqli_query($link, $str2ins);

}

$tofile = "
===========
MySQL: ".$str2ins;
if($file = fopen("response.txt", "a+")){
		fputs($file, $tofile);
		fclose($file);
} // end frite to file

		$response = array(
			'chat_id' => $order_id,
			'text' => 'Успшено пополнен баланс на '.$amount.' рублей'.$orderstarted);
		sendit($response, 'sendMessage');	

function sendit($response, $restype){
	$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/'.$restype);  
	curl_setopt($ch, CURLOPT_POST, 1);  
	curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_exec($ch);
	curl_close($ch);	
}

function send($id, $message, $keyboard) {   
		
		//Удаление клавы
		if($keyboard == "DEL"){		
			$keyboard = array(
				'remove_keyboard' => true
			);
		}
		if($keyboard){
			//Отправка клавиатуры
			$encodedMarkup = json_encode($keyboard);
			
			$data = array(
				'chat_id'      => $id,
				'text'     => $message,
				'reply_markup' => $encodedMarkup
			);
		}else{
			//Отправка сообщения
			$data = array(
				'chat_id'      => $id,
				'text'     => $message
			);
		}
       
        $out = sendit($data, 'sendMessage');       
        return $out;
}   
?>