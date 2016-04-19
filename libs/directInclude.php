<?php

	# 直接引入 css / js

	class DINCLUDE{

		private static 
			$sourceArray,
			$linePart = '-', # 分隔線條樣式
			$lineLength = '120'; # 分隔線長度 (有幾個樣式字樣)

		function __construct(){}

		# 判斷輸入資源類型
		private static function source($path){
			$pathArray = explode(".",$path);
			return array_pop($pathArray);
		}

		# 分隔線
		private static function splitLine($path){
			if(self::$lineLength > 0){
				while(++$i <= self::$lineLength){
					$backLine .= self::$linePart;
				}

				while(++$s <= 4){
					$frontLine .= self::$linePart;
				}
			}

			$startLine = "/* {$frontLine} START [{$path}] {$backLine} */";
			$endLine = "/* {$frontLine} END [{$path}] {$backLine} */";

			return array($startLine,$endLine);
		}

		# 引入處理
		private static function includeHandle($path){
			$realPath = dirname(dirname(__FILE__)).'/..'.$path;
			if(file_exists($realPath)){
				$lineArray = file($realPath,FILE_SKIP_EMPTY_LINES);
				if(is_array($lineArray)){
					foreach($lineArray as $key => $line){
						#$lineArray[$key] = str_replace('url("../','url("'.self::$cfg["base_root"],$lineArray[$key]);
						$handleArray[$key] = preg_replace('/url\((..\/)+images\//','url('.CORE::$root.'images/',$line);
						$handleArray[$key] = preg_replace('/url\((..\/)+fonts\//','url('.CORE::$root.'fonts/',$handleArray[$key]);
						#$lineArray[$key] = $line;
					}

					return implode("",$handleArray);
				}
			}
		}

		# css 處理
		private static function cssHandle($source=false,$path=false){
			list($startLine,$endLine) = self::splitLine($path);
			$includeArray = array("<style>",$startLine,$source,$endLine,'</style>');
			return implode("\n",$includeArray);
		}

		# js 處理
		private static function jsHandle($source=false,$path=false){
			list($startLine,$endLine) = self::splitLine($path);
			$includeArray = array("<script>",$startLine,$source,$endLine,'</script>');
			return implode("\n",$includeArray);
		}

		# 類型處理
		private static function typeHandle($path){
			$type = self::source($path);
			$source = self::includeHandle($path);

			switch($type){
				case "css":
					$rsSource = self::cssHandle($source,$path);
				break;
				case "js":
					$rsSource = self::jsHandle($source,$path);
				break;
			}

			return $rsSource;
		}

		# 引入處理
		public static function allHandle($path){
			#self::$sourceArray = false;
			return self::typeHandle($path);
		}
	}

?>