<?php

function replyTextMessage($bot, $replyToken, $text) {
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text));
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

function replyImageMessage($bot, $replyToken, $originalImageUrl, $previewImageUrl) {
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($originalImageUrl, $previewImageUrl));
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

function replyVideoMessage($bot, $replyToken, $originalImageUrl, $previewImageUrl) {
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\VideoMessageBuilder($originalImageUrl, $previewImageUrl));
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

function replyButtonsTemplate($bot, $replyToken, $alternativeText, $imageUrl, $title, $text, ...$actions) {
  $actionArray = array();
  foreach($actions as $value) {
    array_push($actionArray, $value);
  }
  $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
    $alternativeText,
    new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder ($title, $text, $imageUrl, $actionArray)
  );
  $response = $bot->replyMessage($replyToken, $builder);
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

function selectMessage($bot,$replyToken){
	replyButtonsTemplate($bot,$replyToken,
	"画像分析方法の選択",
	"https://" . $_SERVER["HTTP_HOST"] . "/imgs/logo1.jpg",
	"画像分析方法の選択",
	"画像を検知しました。どのような分析を試しますか？",
	new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
		"コンテンツ分析", "LABEL_DETECTION"),
	new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
		"テキスト分析", "TEXT_DETECTION"),
	new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
		"企業ロゴ分析", "LOGO_DETECTION"),
	new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
		"ランドマーク分析", "LANDMARK_DETECTION")
	);
	
}

function makeTemplate($bot,$replyToken){
	replyButtonsTemplate($bot,$replyToken,
	"研究内容紹介",
	"https://" . $_SERVER["HTTP_HOST"] . "/imgs/logo1.jpg",
	"研究内容紹介",
	"研究内容を見たいチーム名をタップしてね",
	new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
	  "syoukichi", "syoukichi" ),
	new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
	  "OREO", "OREO"),
	new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
	  "Eins", "eins")
	);

}


function makeMessage($bot,$replyToken, $pass){
  if($pass!=""){
    replyButtonsTemplate($bot,$replyToken,
      "顔認証",
      "https://" . $_SERVER["HTTP_HOST"] . "/imgs/logo12.jpg",
      "顔認証",
      "どの画像を見ますか？",
      new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
        "そのままの画像", "photo:original_" . $pass),
      new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
        "楽天カードマン", "photo:detected_" . $pass)
      /* new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
        "何歳？", "photo:test_" . $pass) */
      );
  }else{
      replyButtonsTemplate($bot,$replyToken,
      "研究内容紹介",
      "https://" . $_SERVER["HTTP_HOST"] . "/imgs/logo11.jpg",
      "研究内容紹介",
      "研究内容を見たいチーム名をタップしてね",
      new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
        "syoukichi", "movie:syoukichi" ),
      new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
        "OREO", "movie:OREO"),
      new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
        "Eins", "movie:eins")
      );
  }

}


  ?>
