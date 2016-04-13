<?php

	# 廣告功能

	class AD{

		private static $endClass;
		public static $cate = false;

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

		# 取得廣告分類
		public static function cateFetch(){
			return CORE::$cfg['ad_cate'];
		}

		# 廣告分類選單
		public static function cateSelect($nowID=false){
			$cate = self::cateFetch();
			CHECK::is_array_exist($cate);
			if(CHECK::is_pass()){
				foreach($cate as $cateID => $cateStr){
					$selected = (!empty($nowID) && $nowID == $cateID)?'selected':'';
					$optionArray[] = '<option value="'.$cateID.'" '.$selected.'>'.$cateStr.'</option>';
				}

				return implode('',$optionArray);
			}else{
				return false;
			}
		}

		# 廣告分類列表
		public static function cateList($nowID=false){
			$cate = self::cateFetch();
			CHECK::is_array_exist($cate);
			if(CHECK::is_pass()){
				VIEW::newBlock('TAG_CATE_BLOCK');
				foreach($cate as $cateID => $cateStr){
					VIEW::newBlock('TAG_CATE_LIST');
					VIEW::assign(array(
						'VALUE_ID' => $cateID,
						'VALUE_SUBJECT' => $cateStr,
						'VALUE_CURRENT' => (!empty($nowID) && $nowID == $cateID)?'current':'',
					));
				}

				if(empty($nowID)) VIEW::assignGlobal('NONE_CURRENT','current');
			}
		}
	}

?>