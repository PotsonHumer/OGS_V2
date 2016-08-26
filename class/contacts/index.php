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

		# 主題選單
		protected static function subjectOption($id=false){
			$rsnum = CRUD::dataFetch('contact_subject',false,false,array('sort' => CORE::$cfg['sort']));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $row){
					$selected = (!empty($id) && $id == $row['id'])?'selected':'';
					$optionArray[] = '<option value="'.$row['id'].'" '.$selected.'>'.$row['subject'].'</option>';
				}
			}else{
				$optionArray[] = '<option value="null">無類型</option>';
			}

			if(is_array($optionArray)){
				return implode("\n",$optionArray);
			}
		}

		# 取得主題資料
		protected static function subjectFetch($id,$field='*'){
			$rsnum = CRUD::dataFetch('contact_subject',array('id' => $id),array($field));
			if(!empty($rsnum)){
				list($row) = CRUD::$data;
				return $row[$field];
			}

			return false;
		}
	}

?>