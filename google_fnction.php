<?php

function translate($text){
	
	// APIキー
	$apiKey = "<your api key>";
	
	$json = json_encode(array(
				"q" => $text,
				"target" => 'ja' 
			)	
	);
	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://translation.googleapis.com/language/translate/v2?key=" . $apiKey); // Google Cloud Vision APIのURLを設定
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // curl_execの結果を文字列で取得
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // サーバ証明書の検証を行わない
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST"); // POSTでリクエストする
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); // 送信するHTTPヘッダーの設定
	curl_setopt($curl, CURLOPT_TIMEOUT, 15); // タイムアウト時間の設定（秒）
	curl_setopt($curl, CURLOPT_POSTFIELDS, $json); //送信するjsonデータを設定
	 
	// curl実行
	$res = curl_exec($curl);
	$data = json_decode($res,true);
	curl_close($curl);
	 
	// 出力
	//var_dump($data);
	$text = $data["data"]["translations"][0]["translatedText"];
	return $text;	
	}
	
	
	function  vision($imageNm,$analysis){
		 // APIキー
		 $apiKey = "<your api key>";
		 // リクエスト用json作成
		$json = json_encode(array(
			"requests" => array(
				array(
					"image" => array(
					"content" => base64_encode(file_get_contents($imageNm)),
				),
					"features" => array(
						array(
							 "type" => $analysis,
							"maxResults" => 3,
						 ),
					),
				),
			),
		));
		 
		// 各種オプションを設定
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://vision.googleapis.com/v1/images:annotate?key=" . $apiKey); // Google Cloud Vision APIのURLを設定
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // curl_execの結果を文字列で取得
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // サーバ証明書の検証を行わない
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST"); // POSTでリクエストする
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); // 送信するHTTPヘッダーの設定
		curl_setopt($curl, CURLOPT_TIMEOUT, 15); // タイムアウト時間の設定（秒）
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json); // 送信するjsonデータを設定
		 
		// curl実行
		$res = curl_exec($curl);
		$data = json_decode($res, true);
		curl_close($curl);
		 
		// 結果を出力
	
	
		if ($analysis == "LABEL_DETECTION"){
			$data_array = array($data["responses"][0]["labelAnnotations"][0]["description"],$data["responses"][0]["labelAnnotations"][1]["description"]);
			if($data["responses"][0]["labelAnnotations"][0]["score"] <= $data["responses"][0]["labelAnnotations"][1]["score"] + 0.05){
				return $data_array;
			}else{
				return $data_array[0];
			}
		}else if ($analysis == "TEXT_DETECTION"){
	
			return $data["responses"][0]['textAnnotations'][0]['description'];		
		}else if ($analysis == "LOGO_DETECTION"){
			
			return $data["responses"][0]['logoAnnotations'][0]['description'];		
		}else if ($analysis == "LANDMARK_DETECTION"){
			
			return $data["responses"][0]['landmarkAnnotations'][0]['description'];		
		}
	
	
	
	}

?>