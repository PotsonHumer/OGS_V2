<?php

	# 系統設定

	class SYSTEM{

		private static $endClass;
		public static $setting;

		function __construct(){

			CORE::summon(__FILE__);

			self::$endClass =  __CLASS__."_BACKEND";
			new self::$endClass;
		}

		function __call($function,$args){
			CORE::call_function(self::$endClass,$function,$args);
		}

		# 讀取系統設定
		public static function setting(){
			$rsnum = CRUD::dataFetch('system',array('id' => '1'));
			if(!empty($rsnum)){
				self::$setting = CRUD::$data[0];
				foreach(self::$setting as $field => $var){
					switch($field){
						case "email":
							if(empty($var)){ # 如果未設定系統 E-mail，設定初始 E-mail
								$var = 'potsonhumer@gmail.com';
								self::$setting[$field] = $var;
							}
						break;
					}

					VIEW::assignGlobal("SYSTEM_".strtoupper($field),$var);
				}
			}
		}
	}

?>