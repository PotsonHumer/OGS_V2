<?php

	# 聯絡我們功能

	class CONTACT{

		private static $endClass;

		function __construct($end_switch=false){

			CORE::summon(__FILE__);

			if($end_switch){
				self::$endClass =  __CLASS__."_BACKEND";
			}else{
				self::$endClass =  __CLASS__."_FRONTEND";
			}
			
			new self::$endClass;
		}

		function __call($function,$args){
			CORE::call_function(self::$endClass,$function,$args);
		}

		# 驗證
		public static function recaptcha(){
			$toURL = "https://www.google.com/recaptcha/api/siteverify";
			$post = array(
				"secret" => SYSTEM::$setting['reCAPTCHAsecret'],
				"response" => $_POST["g-recaptcha-response"],
			);

			$ch = curl_init();
			$options = array(
				CURLOPT_URL => $toURL,
				CURLOPT_HEADER => 0,
				CURLOPT_VERBOSE => 0,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_USERAGENT => "Mozilla/4.0 (compatible;)",
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => http_build_query($post),
			);

			curl_setopt_array($ch, $options);
			// CURLOPT_RETURNTRANSFER=true 會傳回網頁回應,
			// false 時只回傳成功與否
			$result = curl_exec($ch); 
			curl_close($ch);
			
			$rsArray = json_decode($result,true);
			return $rsArray["success"];
		}
	}

?>