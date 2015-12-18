<?php

	class ROUTER extends CORE{

		public static 
			$uri, # 取得的 uri
			$class, # 紀錄轉址 class
			$args; # 解析完成後參數

		function __construct(){

			# 去除根目錄
			if(self::$cfg["root"] == "/"){
				$uri = preg_replace("/^\//", '', $_SERVER["REQUEST_URI"],1);
			}else{
				$uri = str_replace(CORE::$cfg["root"], '', $_SERVER["REQUEST_URI"]);
			}

			$uri = urldecode($uri);

			# 解析位置
			if(!empty($uri)){
				$uri_array = explode("/",$uri); # 拆解 uri
				$uri_array = self::lang_switch($uri_array); # 語系解析
				self::args_switch($uri_array);  # 參數解析
			}else{
				# 首頁
				self::$class = 'INDEX';
			}
		}

		# 語系解析
		private static function lang_switch(array $uri_array){
			$origin_array = $uri_array;
			$lang_str = array_shift($uri_array);
			$lang_array = CORE::$cfg["lang"][$lang_str];
			
			if(isset($lang_array) && is_array($lang_array)){
				CORE::$cfg["router"] = $lang_str;
				CORE::$cfg["langfix"] = $lang_array[0];
				CORE::$cfg["langtag"] = $lang_array[1];

				return $uri_array;
			}else{
				return $origin_array;
			}
		}

		# 參數解析
		private static function args_switch(array $uri_array){

			# 刪除尾空直
			if(empty($uri_array[count($uri_array) - 1])){
				array_pop($uri_array);
			}

			# 頁次偵測
			$uri_array = self::page_handle($uri_array);
			$uri_array = self::sk_handle($uri_array);

			if(is_array($uri_array)){
				$origin_args = $uri_array;
				self::$class = strtoupper(array_shift($uri_array));
				self::$args = $uri_array;

				if(!class_exists(self::$class)){
					self::$args = $origin_args;
					self::$class = 'INDEX';
				}
			}else{
				# 首頁
				self::$class = 'INDEX';
			}
		}

		# 頁次處裡
		private static function page_handle(array $uri_array){
			$origin_args = $uri_array;
			$last_args = array_pop($uri_array);
			
			if(preg_match('/page-/', $last_args)){
				PAGE::$now = preg_replace('/page-([0-9]+)/', "$1", $last_args); # 載入頁次參數
				$return_args = $uri_array;
			}else{
				$return_args = $origin_args;
			}

			self::$uri = implode("/",$return_args);
			return $return_args;
		}

		# 搜尋處理
		private static function sk_handle(array $uri_array){
			$origin_args = $uri_array;
			$last_args = array_pop($uri_array);

			if(preg_match('/sk-/', $last_args)){
				SK::$now = preg_replace('/page-([^\/]+)/', "$1", $last_args); # 載入搜尋參數
				$return_args = $uri_array;
			}else{
				$return_args = $origin_args;
			}

			#self::$uri = implode("/",$return_args);
			return $return_args;
		}

		# 初始功能
		public static function class_init(){
			CHECK::is_must(self::$class);

			if(CHECK::is_pass()){
				CORE::$class = self::$class;
				CORE::$args = self::$args;
				
				new self::$class();
			}
		}
	}
?>