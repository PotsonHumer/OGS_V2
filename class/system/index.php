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
						case "address":
							if(!empty($var)) $output['SYSTEM_MAP'] = 'https://maps.google.com.tw/maps?f=q&hl=zh-TW&geocode&q='.$var;
						break;
						case "ga":
							$var = self::gaLoad($var);
						break;
						case "email":
							if(empty($var)){ # 如果未設定系統 E-mail，設定初始 E-mail
								$var = 'potsonhumer@gmail.com';
								self::$setting[$field] = $var;
							}
						break;
						case "facebook":
						case "gplus":
						case "twitter":
						case "instagram":
						case "linkedin":
							$output['SYSTEM_'.strtoupper($field).'_TARGET'] = (!empty($var))?'_blank':'_self';
							$var = (empty($var))?'#':'';
						break;
					}

					$output['SYSTEM'.strtoupper($field)] = $var;
				}

				VIEW::assignGlobal($output);
			}
		}

		# 輸出 ga
		public static function gaLoad($gaCode=false){
			if(!empty($gaCode)){
				VIEW::assignGlobal('SYSTEM_GA',$gaCode);
				new VIEW('ogs-fn-ga-tpl.html',false,true);
				return VIEW::$output;
			}
		}
	}

?>