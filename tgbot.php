<?php 
include 'data.ini.php';

$data = file_get_contents('php://input');
$data = json_decode($data, true);
 
if (empty($data['message']['chat']['id']) AND empty($data['callback_query']['message']['chat']['id']))
{
	exit();
}


include "global.php";
$link = mysqli_connect($hostName, $userName, $password, $databaseName) or die ("Error connect to database");
mysqli_set_charset($link, "utf8");

#################################

if (isset($data['message']['chat']['id']))
{
	$chat_id = $data['message']['chat']['id'];
}
else if (isset($data['callback_query']['message']['chat']['id']))
{
	$chat_id = $data['callback_query']['message']['chat']['id'];
}

// Register new user in DB
if(isset($data['callback_query']['message']['chat']['username']) && $data['callback_query']['message']['chat']['username'] != ''){
	$fname = $data['callback_query']['message']['chat']['first_name'];
	$lname = $data['callback_query']['message']['chat']['last_name'];
	$uname = $data['callback_query']['message']['chat']['username'];
} else{
	$fname = $data['message']['from']['first_name'];
	$lname = $data['message']['from']['last_name'];
	$uname = $data['message']['from']['username'];	
}
$time = time();
if($fname != '' && $uname != ''){
	$str2select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
	$result = mysqli_query($link, $str2select);
	if(mysqli_num_rows($result) == 0){
		$str2ins = "INSERT INTO `users` (`chatid`,`fname`,`lname`,`username`) VALUES ('$chat_id','$fname','$lname','$uname')";
		mysqli_query($link, $str2ins);	
		$result = mysqli_query($link, $str2select);
	}
	$row = @mysqli_fetch_object($result);	
}
// Register new user in DB

############### START ###############
if( preg_match("/\/start/i", $data['message']['text'] )){

//register subscriber
$newrecord = $chat_id."|".$data['message']['from']['first_name']." ".$data['message']['from']['last_name']."|".$data['message']['from']['username'];
if(file_exists('subscribers.php')) include 'subscribers.php';
if(isset($user) && count($user) > 0){
	if(!in_array($newrecord, $user)){
		$towrite = "\$user[] = '".addslashes($newrecord)."';\n";
		
	}
}else{
	$towrite = "\$user[] = '".addslashes($newrecord)."';\n";
} // end IF-ELSE count($user) > 0

if(isset($towrite) && $towrite != ''){
	if($file = fopen("subscribers.php", "a+")){
		fputs($file,$towrite);
		fclose($file);
	} // end frite to file
}
//register subscriber

// record referral
$ref = trim(str_replace("/start", "", $data['message']['text']));
if($ref != ''){
	if($ref != $chat_id){
		$str2select = "SELECT `ref` FROM `users` WHERE `chatid`='$chat_id'";
		$result = mysqli_query($link, $str2select);
		$row = @mysqli_fetch_object($result);
		if($row->ref == 0){
			$str2upd = "UPDATE `users` SET `ref`='$ref' WHERE `chatid`='$chat_id'";
			mysqli_query($link, $str2upd);
			
			$response = array(
					'chat_id' => $ref,
					'text' => hex2bin('F09F92B0').' '.$data['message']['from']['first_name'].' '.$data['message']['from']['last_name'].' зарегистрировался по вашей партнерской ссылке.
			
Используйте эту ссылку для приглашения пользователей:
t.me/smoapp_bot?start='.$ref);
			sendit($response, 'sendMessage');			
		}
	}
}
// record referral

makeMenu();

delayed_start();

}
elseif(preg_match("/Создать новый заказ/", $data['message']['text']) || $data['callback_query']['data'] == 14){

$arInfo["inline_keyboard"][0][0]["callback_data"] = 1;
$arInfo["inline_keyboard"][0][0]["text"] = hex2bin('E29EA1')." Инстаграм";
$arInfo["inline_keyboard"][1][0]["callback_data"] = 2;
$arInfo["inline_keyboard"][1][0]["text"] = hex2bin('E29EA1')." Вконтакте";
$arInfo["inline_keyboard"][2][0]["callback_data"] = 3;
$arInfo["inline_keyboard"][2][0]["text"] = hex2bin('E29EA1')." Ютуб";
$arInfo["inline_keyboard"][3][0]["callback_data"] = 4;
$arInfo["inline_keyboard"][3][0]["text"] = hex2bin('E29EA1')." Телеграм";
$arInfo["inline_keyboard"][4][0]["callback_data"] = 5;
$arInfo["inline_keyboard"][4][0]["text"] = hex2bin('E29EA1')." Одноклассники";
$arInfo["inline_keyboard"][5][0]["callback_data"] = 6;
$arInfo["inline_keyboard"][5][0]["text"] = hex2bin('E29EA1')." Фейсбук";
$arInfo["inline_keyboard"][6][0]["callback_data"] = 7;
$arInfo["inline_keyboard"][6][0]["text"] = hex2bin('E29EA1')." Твиттер";
$arInfo["inline_keyboard"][7][0]["callback_data"] = 8;
$arInfo["inline_keyboard"][7][0]["text"] = hex2bin('E29EA1')." Мой мир";
$arInfo["inline_keyboard"][8][0]["callback_data"] = 9;
$arInfo["inline_keyboard"][8][0]["text"] = hex2bin('E29EA1')." АСКфм";
$arInfo["inline_keyboard"][9][0]["callback_data"] = 10;
$arInfo["inline_keyboard"][9][0]["text"] = hex2bin('E29EA1')." Твич";
$arInfo["inline_keyboard"][10][0]["callback_data"] = 11;
$arInfo["inline_keyboard"][10][0]["text"] = hex2bin('E29EA1')." Музыка";
$arInfo["inline_keyboard"][11][0]["callback_data"] = 12;
$arInfo["inline_keyboard"][11][0]["text"] = hex2bin('E29EA1')." Приложения";
$arInfo["inline_keyboard"][12][0]["callback_data"] = 13;
$arInfo["inline_keyboard"][12][0]["text"] = hex2bin('E29EA1')." ТикТок";
send($chat_id, "Выберите категорию, в которой вы бы хотели заказать услугу:", $arInfo); 	

}
elseif(preg_match("/Мои заказы/", $data['message']['text'])){
	
	$orderslist = 'Ваши заказы:
';
	$str2select = "SELECT * FROM `orders` WHERE `chatid`='$chat_id' ORDER BY `rowid`";
	$result = mysqli_query($link, $str2select);
	while($row = @mysqli_fetch_object($result)){
		
		if($row->status == 1){
			$orderslist .= $row->smoorderid.' | '.$row->volume.' единиц  - '.$row->sum.' RUB | Завершен
';
		}else{
			
		//Get data from smoservice
		##########################
		$params = array(
		"user_id" => $user_id,
		"api_key" => $api_key,
		"action" => 'check_order',
		"order_id" => $row->smoorderid
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
		##########################		
		//Get data from smoservice		
		
		$orderstatus = ($smodata['data']['status'] == 'completed') ? 'Завершен' : $smodata['data']['status'];
		$orderslist .= $row->smoorderid.' | '.$row->volume.' единиц  - '.$row->sum.' RUB | '.$orderstatus.'
';		
		if($smodata['data']['status'] == 'completed'){
			$str5upd = "UPDATE `orders` SET `status`='1' WHERE `rowid`='$row->rowid'";	
			mysqli_query($link, $str5upd);	
		}
		elseif($smodata['data']['status'] == 'Отменен'){
			$str2ins = "UPDATE `balance` SET `sum`=`sum`+$row->sum WHERE `chatid`='$chat_id'";
			mysqli_query($link, $str2ins);
			
			$str2del = "DELETE FROM `orders` WHERE `rowid` = '$row->rowid'";
			mysqli_query($link, $str2del);	
		
		}
		

		}

	}  // end WHILE MySQL
	
	$response = array(
		'chat_id' => $chat_id,
		'text' => $orderslist);
	sendit($response, 'sendMessage');	
	
	
}
elseif(preg_match("/Мой баланс/", $data['message']['text'])){
	
	$str2select = "SELECT * FROM `balance` WHERE `chatid`='$chat_id'";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	
	$balancesum = (mysqli_num_rows($result) == 0) ? 0 : $row->sum;
	
	$curtime = time();
	$str2ins = "INSERT INTO `temp_sess` (`chatid`,`otipe`,`times`) VALUES ('$chat_id','balance','$curtime')";
	mysqli_query($link, $str2ins);

	$response = array(
		'chat_id' => $chat_id,
		'text' => 'Ваш баланс: '.$balancesum.' руб.
'.hex2bin('F09F92B3').' Вы можете пополнить баланс, указав сумму пополнения в рублях:');
	sendit($response, 'sendMessage');		

}
elseif(preg_match("/Заработать/", $data['message']['text'])){
		
	$str12select = "SELECT * FROM `users` WHERE `ref`='$chat_id'";
	$result12 = mysqli_query($link, $str12select);
	$numOfReferals = mysqli_num_rows($result12);

	$str14select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
	$result14 = mysqli_query($link, $str14select);
	$row14 = @mysqli_fetch_object($result14);
		
	$refbalance = ($row14->refbalance > 0) ? $row14->refbalance : "0.00";
		
	$response = array(
		'chat_id' => $chat_id,
		'text' => hex2bin('F09F92B0').' Приглашайте активных пользователей в бот и получайте '.$refpercent.'% от суммы пополнения рефералов.

Вы пригласили: '.$numOfReferals.' человек 

Ваш заработок: '.$refbalance.' ₽

Используйте эту ссылку для приглашения пользователей:
t.me/smoapp_bot?start='.$chat_id);
	sendit($response, 'sendMessage');		
		
}
elseif(preg_match("/Поддержка/", $data['message']['text'])){

	$response = array(
		'chat_id' => $chat_id,
		'text' => 'Всегда рады вам помочь! Заходите: https://t.me/smoservice_bot');
	sendit($response, 'sendMessage');			
	
}
elseif(preg_match("/FAQ/", $data['message']['text'])){
				
	$response = array(
		'chat_id' => $chat_id,
		'text' => hex2bin('E29D93').' Как быстро запустится заказ?
'.hex2bin('E29D95').' Мгновенно

'.hex2bin('E29D93').' Не забанят ли меня?
'.hex2bin('E29D95').' Нет

'.hex2bin('E29D93').' Как работает сервис, каким образом выполняются заказы?
'.hex2bin('E29D95').' Мы предлагаем вам подписчиков как из CPA сетей, так и подписчиков с рекламы, привлеченных через социальные сети. Подписчики CPA добавляются в группу или подписываются на страницу за вознаграждение. Что такое CPA, можете почитать у нас на сайте в информационном разделе вопросов и ответов. Наш сервис старается работать только с качественными источниками и использовать только лучшие из возможных методов для выполнения каждого заказа.');
	sendit($response, 'sendMessage');							
				
}
else{

if( $data['callback_query']['data'] > 0 && $data['callback_query']['data'] <= 13 ){
	
	buildServiceList($data['callback_query']['data']);
	
}
elseif( $data['callback_query']['data'] > 100){
	
	orderService($data['callback_query']['data']);
	
}
else{
// IF manual entrance

	if(preg_match("/^[0-9]+$/", trim($data['message']['text']))){
	
		$temptime = time() - 600;
		
		$str2select = "SELECT * FROM `temp_sess` WHERE `chatid`='$chat_id' AND (`otipe`='order' AND `times`>'$temptime') ORDER BY `rowid` DESC LIMIT 1";
		$result = mysqli_query($link, $str2select);
		$row = @mysqli_fetch_object($result);

		if(mysqli_num_rows($result) == 0){
			
			// balance
			$str6select = "SELECT * FROM `temp_sess` WHERE `chatid`='$chat_id' AND `otipe`='balance' ORDER BY `rowid` DESC LIMIT 1";
			$result6 = mysqli_query($link, $str6select);
			$row6 = @mysqli_fetch_object($result6);

			if(mysqli_num_rows($result6) == 0){
				
				$response = array(
					'chat_id' => $chat_id,
					'text' => 'Произошла ошибка. Начните пополнение баланса заново.');
				sendit($response, 'sendMessage');						

			}else{
				
				$paylink = makelink(trim($data['message']['text']));

				$url = $paylink;
				$arInfo["inline_keyboard"][0][0]["text"] = hex2bin('F09F92B3')." Пополнить на ".$data['message']['text']." руб.";
				$arInfo["inline_keyboard"][0][0]["url"] = rawurldecode($url);
				send($chat_id, "Перейдите по ссылке для пополнения:", $arInfo);							
			
			}
			// balance
		
		}else{
			if($row->times < $temptime)	{
				$response = array(
					'chat_id' => $chat_id,
					'text' => 'У вас нет активной услуги или время оформления заказа истекло. Создайте задачу заново.');
				sendit($response, 'sendMessage');						
			} else{
			
			$str3select = "SELECT * FROM `smoservices` WHERE `id`='$row->serviceid'";
			$result3 = mysqli_query($link, $str3select);
			$row3 = @mysqli_fetch_object($result3);
			
			if($data['message']['text'] < $row3->min || $data['message']['text'] > $row3->max){
				$response = array(
					'chat_id' => $chat_id,
					'text' => 'Введите число, удовлетворяющее нужному промежутку!');
				sendit($response, 'sendMessage');						
			}else{			
				
					$str2upd = "UPDATE `temp_sess` SET `volume`='".$data['message']['text']."' WHERE `rowid`='$row->rowid'";
					mysqli_query($link, $str2upd);
					
					$response = array(
						'chat_id' => $chat_id,
						'text' => 'Введите адрес целевой страницы:');
					sendit($response, 'sendMessage');						
				
			}
			}
		
		}
	}
	elseif(preg_match("/https/i", $data['message']['text'])){
	
		$str2select = "SELECT * FROM `temp_sess` WHERE `chatid`='$chat_id' AND `otipe`='order' ORDER BY `rowid` DESC LIMIT 1";
		$result = mysqli_query($link, $str2select);
		$row = @mysqli_fetch_object($result);
		
		$str3select = "SELECT * FROM `smoservices` WHERE `id`='$row->serviceid'";
		$result3 = mysqli_query($link, $str3select);
		$row3 = @mysqli_fetch_object($result3);		
	
		$curtime = time();
		$ordersum = $row->volume * $row3->price;
		
		$str4select = "SELECT * FROM `balance` WHERE `chatid`='$chat_id'";
		$result4 = mysqli_query($link, $str4select);
		$row4 = @mysqli_fetch_object($result4);
		if(mysqli_num_rows($result4) == 0 || $row4->sum < $ordersum){
			$str2upd = "UPDATE `temp_sess` SET `waitpayment`='1' WHERE `rowid`='$row->rowid'";
			mysqli_query($link, $str2upd);	

			$str3upd = "UPDATE `temp_sess` SET `page`='".$data['message']['text']."' WHERE `rowid`='$row->rowid'";
			mysqli_query($link, $str3upd);
			
			// cleaning DB
			$str7select = "SELECT * FROM `temp_sess` WHERE `chatid`='$chat_id' AND `otipe`='order'";
			$result7 = mysqli_query($link, $str7select);
			while($row7 = @mysqli_fetch_object($result7)){
				if($row7->rowid != $row->rowid){
					$str2del = "DELETE FROM `temp_sess` WHERE `rowid` = '$row7->rowid'";
					mysqli_query($link, $str2del);					
				}
			}  // end WHILE MySQL
			// cleaning DB
						
			$paylink = makelink($ordersum);
			$arInfo["inline_keyboard"][0][0]["text"] = hex2bin('F09F92B3')." Пополнить на ".$ordersum." руб.";
			$arInfo["inline_keyboard"][0][0]["url"] = rawurldecode($paylink);
			send($chat_id, "Недостаточно средств на балансе. Перейдите по ссылке для пополнения:", $arInfo);	
									
		}
		else{

		$str2ins = "INSERT INTO `orders` (`chatid`,`serviceid`,`volume`,`sum`,`times`) VALUES ('$chat_id','$row->serviceid','$row->volume','$ordersum','$curtime')";
		mysqli_query($link, $str2ins);
		$orderno = mysqli_insert_id($link); 
		
		$str2upd = "UPDATE `balance` SET `sum`=`sum`-$ordersum WHERE `chatid`='$chat_id'";
		mysqli_query($link, $str2upd);
		
		$str2del = "DELETE FROM `temp_sess` WHERE `rowid` = '$row->rowid'";
		mysqli_query($link, $str2del);		

		//SEND ORDER TO API smoservice
		##############################
		$params = array(
		"user_id" => $user_id,
		"api_key" => $api_key,
		"action" => 'create_order',
		"service_id" => $row->serviceid,
		"count" => $row->volume,
		"url" => trim($data['message']['text'])
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
			
					$response2 = array(
						'chat_id' => $chat_id,
						'text' => 'Заказ размещен!');
					sendit($response2, 'sendMessage');	
					
			//Referrals
			$str5select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
			$result5 = mysqli_query($link, $str5select);
			$row5 = @mysqli_fetch_object($result5);
			
			if($row5->ref != 0){
				$earn = ($ordersum/100)*$refpercent;
				$str4upd = "UPDATE `balance` SET `sum`=`sum`+$earn WHERE `chatid`='$row5->ref'";
				mysqli_query($link, $str4upd);
			}
			//Referrals			

		}else{
					$response2 = array(
						'chat_id' => $chat_id,
						'text' => 'Что-то пошло не так. Заказ не размещен.');
					sendit($response2, 'sendMessage');				
		}
	
		
		} // IF no balance

	} else{
		
		$response = array(
			'chat_id' => $chat_id,
			'text' => 'Произошла ошибка. Описание:
URL введен неверно

Попробуйте создать заказ заново');
		sendit($response, 'sendMessage');			
	
	}

// IF manuale entrance
}
} // if-else /start


 
exit('ok'); //Обязательно возвращаем "ok", чтобы телеграмм не подумал, что запрос не дошёл

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

function makelink($sum){

	global $link, $chat_id, $roskassa_publickey, $roskassa_secretkey;
	
	$curtime = time();
	$str2ins = "INSERT INTO `paylinks` (`chatid`,`times`,`status`,`sum`) VALUES ('$chat_id','$curtime','0','$sum')";
	mysqli_query($link, $str2ins);
	$last_id = mysqli_insert_id($link);
	
	$secret = $roskassa_secretkey;
	$data = array(
		'shop_id'=>$roskassa_publickey,
		'amount'=>$sum,
		'currency'=>'RUB',
		'order_id'=>$chat_id
		#'test'=>1
	);
	ksort($data);
	$str = http_build_query($data);
	$sign = md5($str . $secret);
	
	###############2021#################
	$formpage = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Форма оплаты @smoapp_bot</title>

<style type="text/css">
.txt {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	font-weight: normal;
	color: #000;
	text-decoration: none;
}
.sum{
	font-size: 24px;
	font-weight: bold;
}
</style>
</head>

<body>
<div align="center" class="txt"><br><br>
Вы оплачиваете:<br> <span class="sum">'.$sum.' руб.</span><br><br>
<form action="https://tegro.money/pay/" method="post">
<input type="hidden" name="shop_id" value="'.$roskassa_publickey.'">


<!-- Товар 1 -->
<input type="hidden" name="receipt[items][0][name]" value="Платёж за услуги в интернете">
<input type="hidden" name="receipt[items][0][count]" value="1">
<input type="hidden" name="receipt[items][0][price]" value="'.$sum.'">

<!-- общая сумма оплаты должна равняться сумме всех товаров! -->
<input type="hidden" name="amount" value="'.$sum.'">

<input type="hidden" name="order_id" value="'.$chat_id.'">
<input type="hidden" name="lang" value="ru">
<input type="hidden" name="currency" value="RUB">
<input type="hidden" name="sign" value="'.$sign.'">
<input type="submit" value="Оплатить" style="width:200px; padding:15px;">
</form>
</div>
</body>
</html>';

$filename = "payform/".$chat_id."_".time().".php";
/*if($file = fopen($filename, "w+")){
		fputs($file, $formpage);
		fclose($file);
} // end frite to file*/
	
	#return 'https://smoapp.ru/TGBot/'.$filename;
	###############2021#################	
	return 'https://tegro.money/pay/?'.$str.'&sign='.$sign;
	
}

function makeMenu(){

	global $link, $chat_id;	
	
	$arInfo["keyboard"][0][0]["text"] = hex2bin('F09F94A5')." Создать новый заказ";
	$arInfo["keyboard"][1][0]["text"] = hex2bin('F09F92B9')." Мои заказы";
	$arInfo["keyboard"][1][1]["text"] = hex2bin('F09F92BC')." Мой баланс";
	$arInfo["keyboard"][2][0]["text"] = hex2bin('F09F92B0')." Заработать";
	$arInfo["keyboard"][2][1]["text"] = hex2bin('F09F92A1')." Поддержка";	
	$arInfo["keyboard"][2][2]["text"] = hex2bin('F09F93A2')." FAQ";		
	$arInfo["resize_keyboard"] = TRUE;
	
	send($chat_id, 'Выберите в меню ниже интересующий Ваc раздел:', $arInfo);

}

function buildServiceList($num){
	global $link, $chat_id;	
	
	switch ($num) {
		case 1:
		$prefix = "inst-";
		break;
		case 2:
		$prefix = "vk-";
		break;
		case 3:
		$prefix = "yt-";
		break;
		case 4:
		$prefix = "tg-";
		break;
		case 5:
		$prefix = "ok-";
		break;
		case 6:
		$prefix = "fb-";
		break;
		case 7:
		$prefix = "tw-";
		break;
		case 8:
		$prefix = "mm-";
		break;
		case 9:
		$prefix = "dasoasd-";
		break;
		case 10:
		$prefix = "twh-";
		break;
		case 11:
		$prefix = "spotify-";
		break;
		case 12:
		$prefix = "app-";
		break;
		case 13:
		$prefix = "tt-";
		break;
	}	
	
	$str2select = "SELECT * FROM `smoservices` WHERE `code` LIKE '%$prefix%'";
	$result = mysqli_query($link, $str2select);
	$c = 0;
	while($row = @mysqli_fetch_object($result)){
		$arInfo["inline_keyboard"][$c][0]["callback_data"] = $row->id;
		$arInfo["inline_keyboard"][$c][0]["text"] = hex2bin('E29EA1')." ".$row->name;
		$c++;
	}  // end WHILE MySQL
	$arInfo["inline_keyboard"][$c][0]["callback_data"] = 14;
	$arInfo["inline_keyboard"][$c][0]["text"] = hex2bin('E286A9')." Назад";
	send($chat_id, "Выберите сервис:", $arInfo); 		
	
}

function orderService($num){
	global $link, $chat_id;	
	
	$str2select = "SELECT * FROM `smoservices` WHERE `id`='$num'";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	
	$curtime = time();
	$str2ins = "INSERT INTO `temp_sess` (`chatid`,`serviceid`,`times`,`otipe`) VALUES ('$chat_id','$num','$curtime','order')";
	mysqli_query($link, $str2ins);
	
	$response = array(
		'chat_id' => $chat_id,
		'text' => hex2bin('F09F91B4').'Заказ услуги "'.$row->name.'"

'.hex2bin('F09F92B3').' Цена - '.$row->price.' RUB. за одну единицу (Подписчик, лайк, репост)

'.hex2bin('F09F9187').' Введите количество для заказа от '.$row->min.' до '.$row->max.'');
	sendit($response, 'sendMessage');	

}

function delayed_start(){
	global $link, $chat_id;
	
	$str2select = "SELECT * FROM `delayed_posts` WHERE `chatid`='$chat_id'";
	$result33 = mysqli_query($link, $str2select);
	if(mysqli_num_rows($result33) == 0){
		// post 14 days
		$sendtime = time() + 86400*14;
		$str33ins = "INSERT INTO `delayed_posts` (`chatid`,`sendtime`) VALUES ('$chat_id','$sendtime')";
		mysqli_query($link, $str33ins);
	}	
}