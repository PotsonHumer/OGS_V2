<?php

	// 方便使用 Session class
	class SESS{
		
		public static $val; // 輸出 get 處理的值
		private static $act; // 動作標記
		private static $args; // 紀錄輸入值
		private static $write; // 寫入值
		
		function __construct(){} // No need
		
		// 取得 session
		public static function get(){
			
			if(is_array(self::$args)){
				$args = self::$args;
			}else{
				$args = func_get_args();
			}
			
			if(is_array($args)){
				foreach($args as $key_title){
					if(is_numeric($key_title)){
						$keys_array[] = '['.$key_title.']';
					}else{
						$keys_array[] = '["'.$key_title.'"]';
					}
				}
				
				$keys_str = implode("",$keys_array);
				
				switch(self::$act){
					case "write":
						if(is_array(self::$write)){
							foreach(self::$write as $index => $write){
								$index_str = (is_numeric($index))?'['.$index.']':'["'.$index.'"]';
								eval('$_SESSION[CORE::$cfg["sess"]]'.$keys_str.$index_str.' = "'.$write.'";');
							}
						}else{
							eval('$_SESSION[CORE::$cfg["sess"]]'.$keys_str.' = "'.self::$write.'";');
						}
					break;
					case "del":
						eval('unset($_SESSION[CORE::$cfg["sess"]]'.$keys_str.');');
						return true;
					break;
					default:
						eval('$output = $_SESSION[CORE::$cfg["sess"]]'.$keys_str.';');
						
						self::$val = $output;
						return $output;
					break;
				}
			}else{
				eval('$output = $_SESSION[CORE::$cfg["sess"]];');
				
				self::$val = $output;
				return $output;
			}
		}
		
		// 寫入 session
		public static function write(){
			
			$args = func_get_args();
			self::$write = array_pop($args); // 最後一個輸入為寫入值
			self::$act = 'write';
			self::$args = $args;

			if(is_array(self::$args)){
				self::get();
			}
			
			self::$act = false;
			self::$args = false;
			self::$write = false;
		}
		
		// 刪除 session
		public static function del(){
			self::$act = 'del';
			self::$args = func_get_args();
			
			self::get();
			
			self::$act = false;
			self::$args = false;
		}
	}

?>