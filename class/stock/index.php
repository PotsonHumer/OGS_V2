<?php

	# 規格 / 庫存功能

	class STOCK{

		private static $endClass;

		function __construct($end_switch=false){

			CORE::summon(__FILE__);

			if($end_switch){
				self::$endClass =  "STOCK_BACKEND";
			}else{
				self::$endClass =  "STOCK_FRONTEND";
			}
			
			new self::$endClass;
		}

		function __call($function,$args){
			CORE::call_function(self::$endClass,$function,$args);
		}

		# 讀取規格
		public static function fetch($id){
			$rsnum = CRUD::dataFetch('stock_bind',array('id' => $id));
			if(!empty($rsnum)){
				return CRUD::$data[0];
			}else{
				return false;
			}
		}
	}

?>