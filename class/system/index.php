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

		# 資料輸出處裡
		private static function argsHandle($prefix='SYSTEM',array $row){
			foreach($row as $field => $var){
				switch($field){
					case "address":
						$output[$prefix.'_'.strtoupper($field).'_TARGET'] = (!empty($var))?'_blank':'_self';
						$output[$prefix.'_MAP'] = (!empty($var))?'https://maps.google.com.tw/maps?f=q&hl=zh-TW&geocode&q='.$var:'#';
					break;
					case "tel":
					case "cell":
						$output[$prefix.'_'.strtoupper($field).'_LINK'] = preg_replace('/[^\d]/','',$var);
					break;
					case "ga":
						$var = self::gaLoad($var);
					break;
					case "facebook":
					case "gplus":
					case "twitter":
					case "instagram":
					case "linkedin":
						$output[$prefix.'_'.strtoupper($field).'_TARGET'] = (!empty($var))?'_blank':'_self';
						$var = (empty($var))?'#':$var;
					break;
				}

				$output[$prefix.'_'.strtoupper($field)] = $var;
			}

			return (is_array($output))?$output:false;
		}

		# 讀取系統設定
		public static function setting(){
			$rsnum = CRUD::dataFetch('system',array('id' => '1'));
			if(!empty($rsnum)){
				list($row) = CRUD::$data;
				$output = self::argsHandle('SYSTEM',$row);

				if(empty($row['email'])) $row['email'] = 'potsonhumer@gmail.com';
				self::$setting = $row;

				VIEW::assignGlobal($output);
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
					$output = self::argsHandle('VALUE',$row);
					VIEW::assign($output);
				}
			}
		}		
	}

?>