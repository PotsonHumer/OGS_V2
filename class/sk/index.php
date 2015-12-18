<?php

	# 蒐尋功能
	class SK{

		public static 
			$now,
			$args;

		function __construct(){
			if(is_array($_POST["sk"])){
				foreach($_POST["sk"] as $field => $var){
					switch($field){
						case "to":
							$to = $var;
						break;
						default:
							$sk[] = "{$field}:{$var}";
						break;
					}
				}

				if(is_array($sk)){
					$to_array = explode("/",$to);
					if(empty($to_array[count($to_array) - 1])) array_pop($to_array);
					$newto = implode("/",$to_array);

					$sk_str = implode("|",$sk);
					header("location: {$newto}/sk-{$sk_str}/");
				}
			}
		}

		public static function fetch(){
			SESS::del('SK');

			if(!empty(self::$now)){
				SESS::write('SK',self::$now);
				$sk_str = urldecode(self::$now);
				$sk_str = str_replace('sk-','',$sk_str);
				$sk_array = explode("|",$sk_str);

				foreach($sk_array as $sk_group){
					list($field,$value) = explode(":",$sk_group);
					if(!empty($value)){
						switch($field){
							case "parent":
								$sk[] = "{$field} = '{$value}'";
							break;
							default:
								$sk[] = "{$field} like '%{$value}%'";
							break;
						}
						self::$args[$field] = $value;
						VIEW::assignGlobal("SK_".strtoupper($field),$value);
					}
				}

				if(is_array($sk)){
					return implode(" and ",$sk);
				}
			}

			return false;
		}
	}

?>