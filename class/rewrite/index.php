<?php

	# 轉址設定

	class REWRITE{

		private static $endClass;

		function __construct($end_switch=false){

			CORE::summon(__FILE__);

			if($end_switch){
				self::$endClass =  __CLASS__."_BACKEND";
				new self::$endClass;
			}
		}

		function __call($function,$args){
			CORE::call_function(self::$endClass,$function,$args);
		}

		public static function handle($uri_array=false){
			if(empty($uri_array)) return $uri_array;
			foreach($uri_array as $uriItem){
				$sql = "SELECT * FROM ".CORE::$cfg['prefix']."_rewrite WHERE origin LIKE '%{$uriItem}%'";
				$rs = DB::execute($sql);
				$rsnum = DB::num($rs);

				while($row = DB::fetch($rs)){
					foreach($row as $field => $var){
						$args[$field] = CORE::content_file_str_replace($var,'out');
					}

					$nowUrl = ($_SERVER['HTTPS'])?'https':'http';
					$nowUrl = $nowUrl.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

					if(urlencode($args['origin']) == urlencode($nowUrl)){
						header("Location: ".$args['target'],TRUE,301);
						exit;
					}
				}
			}

			return $uri_array;
		}
	}

?>