<?php

	# 圖片相關及時處裡

	class IMGHANDLE{

		private static $args; # 儲存輸入參數

		function __construct(){
			
			$func = array_shift(CORE::$args);
			self::$args = CORE::$args;

			switch($func){
				case "resize": # 相關參數 0 => width, 1 => height, 2 => images path
					self::resize();
				break;
				default:
					self::error();
				break;
			}
		}

		# 縮放圖片
		private static function resize(){
			$width = array_shift(self::$args);
			$height = array_shift(self::$args);
			CHECK::is_array_exist(self::$args);

			if(CHECK::is_pass()){
				$path = implode("/",self::$args);
				IMAGES::resize($path,$width,$height);
			}else{
				self::error();
			}
		}

		# 錯誤處理
		private static function error(){
			include ROOT_PATH.'404.htm';
			exit;
		}
	}

?>