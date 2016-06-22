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
						case "ga":
							$var = self::gaLoad($var);
						break;
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

			self::custom();
		}

		# 輸出 ga
		public static function gaLoad($gaCode=false){
			if(!empty($gaCode)){
				VIEW::assignGlobal('SYSTEM_GA',$gaCode);
				new VIEW('ogs-fn-ga-tpl.html',false,true);
				return VIEW::$output;
			}
		}

		# 輸出分店資訊
		public static function custom($tag='TAG'){
			$rsnum = CRUD::dataFetch('system_custom',array('status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg['sort']));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $row){
					VIEW::newBlock("{$tag}_SYSTEM_CUSTOM");
					foreach($row as $field => $var){
						VIEW::assign('VALUE_'.strtoupper($field),$var);
					}
				}
			}
		}		
	}

?>