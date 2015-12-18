<?php

	# 行銷功能

	class SEO{

		public static 
			$data, // 讀取的 SEO 資訊
			$error; // 錯誤訊息

		function __construct(){

			# 設定初始功能主頁的 SEO
			CHECK::is_array_exist(CORE::$cfg["default_function"]);

			if(CHECK::is_pass()){
				$default_str = "'".implode("','",CORE::$cfg["default_function"])."'";
				$rsnum = CRUD::dataFetch('seo',array('custom' => "name in ({$default_str})"));
				if(!empty($rsnum)){
					foreach(CORE::$cfg["default_function"] as $func_name){
						$rsnum = CRUD::dataFetch('seo',array('langtag' => CORE::$langtag,'name' => $func_name));
						if(empty($rsnum)) CRUD::dataInsert('seo',array('name' => $func_name,'langtag' => CORE::$langtag));
					}
				}else{
					# 如果都沒有則設定新紀錄
					foreach(CORE::$cfg["default_function"] as $func_name){
						CRUD::dataInsert('seo',array('name' => $func_name,'langtag' => CORE::$langtag));
					}
				}
			}
		}

		# 檢查是否有重複 SEO 檔名
		private static function filename_check($id=false,$filename,$timer=2){
			$filename = str_replace(" ",'-',$filename);

			if($id !== false){
				$sk = array('filename' => $filename,'custom' => "id != '{$id}'");
			}else{
				$sk = array('filename' => $filename);
			}

			$rsnum = CRUD::dataFetch('seo',$sk);
			if(!empty($rsnum)){
				$new_filename = $filename.$timer;
				return self::filename_check($id,$new_filename,++$timer);
			}else{
				return $filename;
			}
		}

		# 讀取 SEO
		public static function load($id){
			self::$data = array();

			if(is_numeric($id)){
				$sk = array('id' => $id);
			}else{
				$sk = array('name' => $id);
			}

			$rsnum = CRUD::dataFetch('seo',$sk);
			if(!empty($rsnum)){
				list(self::$data) = CRUD::$data;
			}
		}

		# 新增 SEO
		public static function add($tb_name,$id,array $args){
			if(!empty($args["filename"])){
				$args["filename"] = self::filename_check(false,$args["filename"]);
			}

			CRUD::dataInsert('seo',$args);
			$seo_id = DB::get_id();

			DB::update(CORE::$prefix."_".$tb_name,array('seo_id' => $seo_id,'id' => $id));
		}

		# 更新 SEO
		public static function modify(array $args){
			if(!empty($args["filename"])){
				$args["filename"] = self::filename_check($args["id"],$args["filename"]);
			}

			CRUD::dataUpdate('seo',$args);
			if(!empty(DB::$error)){
				self::$error = DB::$error;
				return false;
			}else{
				return true;
			}
		}

		# 輸出 SEO
		public static function output($args=false){
			if(is_array($args)){
				$output = $args;
			}else{
				$output = self::$data;
			}

			CHECK::is_array_exist($output);
			if(CHECK::is_pass()){
				foreach($output as $field => $var){
					switch($field){
						case "short_desc":
							$var = (!empty($var))?"<h2>{$var}</h2>":'';
						default:
							VIEW::assignGlobal("SEO_".strtoupper($field),$var);
						break;
					}
				}
			}
		}

		# SEO 檔名搜尋資料
		public static function origin($tb_name,$args=false){
			if(empty($args) || $args === false) return false;
			if(is_numeric($args)){
				return $args;
			}else{
				$rsnum = CRUD::dataFetch('seo',array("filename" => urldecode($args)));
				if(!empty($rsnum)){
					list($row) = CRUD::$data;
					$origin_num = CRUD::dataFetch($tb_name,array('seo_id' => $row["id"]));
					if(!empty($origin_num)){
						list($origin_row) = CRUD::$data;
						return $origin_row["id"];
					}
				}

				return false;
			}
		}

		# SEO 連結生成
		public static function link(array $row){
			if(!empty($row["id"]) && !empty($row["seo_id"])){
				self::load($row["seo_id"]);
				if(!empty(self::$data["filename"])){
					return self::$data["filename"];
				}

				return $row["id"];
			}

			return false;
		}
	}
?>