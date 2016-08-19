<?php

	# 表單、各類回應頁面

	class RESPONSE{

		private static $temp;

		function __construct($func=false,$title=false,$custom=false){
			self::$temp = CORE::$temp_main;
			self::$temp['MAIN'] = 'ogs-response-tpl.html';

			$tplPath = CORE::$temp.self::$temp['MAIN'];
			file_put_contents($tplPath,SYSTEM::$setting['response']);

			switch($func){
				case "feedback":
					VIEW::assignGlobal(array(
						'SEO_H1' => (!empty($title))?$title:'感謝您的評價',
					));
				break;
				case "contact":
					VIEW::assignGlobal(array(
						'SEO_H1' => (!empty($title))?$title:'感謝您的留言',
					));
				break;
			}

			if(is_array($custom)){
				VIEW::assignGlobal($custom);
			}

			$msg = SESS::get('msg');

			if(empty($msg)){
				header('location: '.CORE::$root.$func.'/');
				exit;
			}

			SESS::del('msg');
			if(is_array($msg)){

				$output['TAG_NAME'] = CORE::fetchName(array($msg['lastName'],$msg['firstName']),'call',$msg['gender']);

				foreach($msg as $field => $var){
					$output['TAG_'.strtoupper($field)] = $var;
				}

			}else{
				$output['TAG_MSG'] = $msg;
			}

			VIEW::assignGlobal($output);

			CORE::common_resource();

			new VIEW(CORE::$temp_option["HULL"],self::$temp,false,false);
		}

		public static function register($msg,$path){
			CHECK::is_array_exist($msg);

			if(CHECK::is_pass()){
				foreach($msg as $field => $var){
					SESS::write('msg',$field,$var);
				}
			}else{
				SESS::write('msg',$msg);
			}

			header("location: {$path}");
		}
	}

?>