<?php 
include 'data.ini.php';

include "global.php";
$link = mysqli_connect($hostName, $userName, $password, $databaseName) or die ("Error connect to database");

$curtime = time();

// Select all positions to send
$str2select = "SELECT * FROM `delayed_posts` WHERE `sendtime` <= '$curtime'";
$result = mysqli_query($link, $str2select);
while($row = @mysqli_fetch_object($result)){

if($row->stop == 1) continue;



$response = array(
	'chat_id' => $row->chatid,
	'text' => "*Не дай боту заскучать* ".hex2bin('F09F93AB')."

Верный спутник SMM-продвижения по прежнему здесь. Без обеда и выходных, он принимает заказы. Подписчики, Лайки и просмотры!

1. Напишите команду /start

2. Выберите услугу

3. Укажите параметры

*Раз, два, три - и ваш заказ на получение лайков, просмотров и подписчиков запущен*".hex2bin('F09F988D'),
	'parse_mode' => 'Markdown');	
sendit($response, 'sendMessage');


// Update the record
$sendtime = time() + 86400*14;
$str2upd = "UPDATE `delayed_posts` SET `sendtime`='$sendtime' WHERE `rowid`='$row->rowid'";
mysqli_query($link, $str2upd);

}  // end WHILE MySQL



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

?>