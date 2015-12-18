<?php

	# 前台會員功能

	class STOCK_FRONTEND extends STOCK{

		public static $test;

		function __construct(){}

		# 讀取規格
		public static function fetch($id){
			self::$test = 1;
			$rsnum = CRUD::dataFetch('stock_bind',array('id' => $id));
			if(!empty($rsnum)){
				return CRUD::$data[0];
			}else{
				return false;
			}
		}
	}


?>