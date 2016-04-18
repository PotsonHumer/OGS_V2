<?php

	# 留言功能

	class MESSAGE{

		private static $endClass;
		public static
			$dataID = false, # 資料 ID
			$func = false, # 使用留言的功能
			$output; # 輸出


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
	}

?>