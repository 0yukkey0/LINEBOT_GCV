<?php

//オートローダの指定
require_once __DIR__ . '/vendor/autoload.php';
require "unit.php";
require "google_fnction.php";
require "lock.php";

//CurlHTTPClientとLINEBotのインスタンス化
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);

//署名の検証作業
$signature = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
try {
  $events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);
} catch(\LINE\LINEBot\Exception\InvalidSignatureException $e) {
  error_log("parseEventRequest failed. InvalidSignatureException => ".var_export($e, true));
} catch(\LINE\LINEBot\Exception\UnknownEventTypeException $e) {
  error_log("parseEventRequest failed. UnknownEventTypeException => ".var_export($e, true));
} catch(\LINE\LINEBot\Exception\UnknownMessageTypeException $e) {
  error_log("parseEventRequest failed. UnknownMessageTypeException => ".var_export($e, true));
} catch(\LINE\LINEBot\Exception\InvalidEventRequestException $e) {
  error_log("parseEventRequest failed. InvalidEventRequestException => ".var_export($e, true));
}

//メッセージ型のチェックとオウム返し
foreach ($events as $event) {

	//if (!($event instanceof \LINE\LINEBot\Event\MessageEvent)) {
	  //error_log('Non message event has come');
	  //continue;
	//}

	if($event instanceof \LINE\LINEBot\Event\MessageEvent\ImageMessage){
		lock();
		//イベントコンテンツの取得
		$content = $bot->getMessageContent($event->getMessageId());
		//コンテンツヘッダーを取得
		$headers = $content->getHeaders();

		//フォルダ指定とファイル名の取得
		$dir_path = 'imgs';
		$filename = 'tmp';
		
		//コンテンツの種類を取得
		$extension = explode('/',$headers['Content-Type'])[1];

		//保存先フォルダに画像を保存
		file_put_contents($dir_path . '/' . $filename . '.' . $extension,$content->getRawBody());
		
		//URLの作成
		$filepath = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $dir_path . '/' . $filename . '.' . $extension ;

		file_put_contents("path.txt",$filepath);
	
		selectMessage($bot,$event->getReplyToken());	
	}else if ($event instanceof \LINE\LINEBot\Event\PostbackEvent) {

		$analysis = $event->getPostbackData();
		$filepath = file_get_contents("https://" . $_SERVER["HTTP_HOST"] . "/path.txt");

		//visionに画像を投げる
		$text = vision($filepath,$analysis);
		//分析失敗で$dataに値がない場合
		if ($text == NULL){
			$text = '画像の分析に失敗しました…'."\n".'別の画像を送信するか別の分析方法を試してみてください。';
			unlock();
			replyTextMessage($bot, $event->getReplyToken(), $text);
		}

		//テキスト分析なら翻訳せずに返却する
		if($analysis == "TEXT_DETECTION"){
			unlock();
			replyTextMessage($bot, $event->getReplyToken(), $text);	
		}else{
			$message = 'この画像は'."\n";
			//$textが配列かどうか
			if(is_array($text)){
				$num = count($text);
				for($i = 0;$i<$num;$i++){
					$text[$i] = translate($text[$i]); 
					$message = $message . '「'.$text[$i].'」'."\n";
				}
			}else{
				//日本語翻訳
				$message = $message . translate($text);	
			}
			$message = $message . 'の画像かな？';
			unlock();
			replyTextMessage($bot, $event->getReplyToken(), $message);
		}
	}else if($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage){
		$text = $event->getText();	//メッセージ読み込み
		if($text=="研究内容"){
			makeTemplate($bot,$event->getReplyToken());
		}
		else if($text ==  "syoukichi" or $text ==  "OREO" or $text ==  "eins"){
			replyImageMessage($bot, $event->getReplyToken(), "https://" . $_SERVER["HTTP_HOST"] . "/logo/" . $text . ".jpg","https://" . $_SERVER["HTTP_HOST"] . "/logo/" . $text . ".jpg");
		}else{
			
		}
	}
}

 ?>