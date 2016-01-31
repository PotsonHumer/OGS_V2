<?php
	
	# 建立、讀取、更新、刪除 資料庫補助 class

	class CRUD{

		public static 
			$id, // 紀錄新資料 id
			$data, // 紀錄取得資料
			$parent_tb_name  = false, // 參考父系表單名稱
			$args; // 輸出參數

		function __construct(){}

		# 引號處理
		private static function strslashes($var=false){
			if(!get_magic_quotes_gpc()){
				#return addslashes($var); //stripslashes
				return filter_var($var, FILTER_SANITIZE_MAGIC_QUOTES);
			}else{
				return $var;
			}
		}

		# 篩選條件處理
		public static function sk_handle($sk=false){
			CHECK::is_array_exist($sk);

			if(CHECK::is_pass()){
				foreach($sk as $field => $var){
					switch($field){
						case "sk":
							if(!empty($var)) $where_array[] = $var;
						break;
						case "custom":
							$where_array[] = $var;
						break;
						default:
							switch($var){
								case "!null":
									$where_array[] = $field.' IS NOT NULL';
								break;
								case "null":
									$where_array[] = $field.' IS NULL';
								break;
								default:
									if(!empty($equation)) unset($equation);

									if(preg_match("/%([^%])+/", $var) || preg_match("/([^%])+%/", $var)){
										$equation = 'like';
									}

									if(!is_array($var) && (preg_match("/^!/", $var))){
										$equation = '!=';
										$varArray = str_split($var);
										array_shift($varArray);
										$var = implode('',$varArray);
									}

									if(is_array($var)){
										$equation = 'in';
										$var = "('".implode("','",$var)."')";
									}else{
										$var = "'{$var}'";
									}

									$var = self::strslashes($var);

									$equation = (isset($equation))?" {$equation} ":' = ';
									$where_array[] = $field.$equation."{$var}";
								break;
							}
						break;
					}
				}

				if(is_array($where_array)){
					return implode(" and ",$where_array);
				}
			}

			return false;
		}

		# 取得欄位設定
		private static function field_handle($fetch_array=false){
			CHECK::is_array_exist($fetch_array);
			if(!CHECK::is_pass()) return '*';
			return implode(",",$fetch_array);
		}

		# 排序方法
		private static function order_handle($order_array=false){
			CHECK::is_array_exist($order_array);

			if(CHECK::is_pass()){
				foreach($order_array as $order_field => $order_sort){
					$order_str_array[] = $order_field.' '.$order_sort;
				}
			}

			if(is_array($order_str_array)){
				return implode(",",$order_str_array);
			}else{
				return false;
			}
		}

		# 檢查欄位是否符合資料表
		private static function match_field($tb_name,array $args){
			$sql = DB::field(CORE::$prefix."_".$tb_name);
			$rsnum = DB::num($sql);

			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					$args_field = array_keys($args);
					if(in_array($row["Field"],$args_field)){
						$var = self::strslashes($args[$row["Field"]]);
						$new_args[$row["Field"]] = $var;
					}
				}

				CHECK::is_array_exist($new_args);
				if(CHECK::is_pass()){
					return $new_args;
				}else{
					DB::$error = CORE::$lang["no_args"];
					return false;
				}
			}

			return false;
		}

		# 內容路徑處理
		private static function content_handle($args=false,$type=false){
			if(is_array($args)){
				$put = ($type)?'out':'in';
				foreach($args as $key => $var){
					$new_args[$key] = CORE::content_file_str_replace($var,$put);
				}

				return $new_args;
			}else{
				return $args;
			}
		}

		# 取得父系 id
		private static function getParent($tb_name,$parent,$nowLangtag){
			if(!empty(self::$parent_tb_name)) $tb_name = self::$parent_tb_name;
			$rsnum = CRUD::dataFetch('lang',array('sheet' => $tb_name));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					$related_array = json_decode($row["related"],true);
					if($related_array[CORE::$langtag] == $parent){
						return $related_array[$nowLangtag];
					}
				}
			}else{
				return 'null';
			}
		}

		# private
		//-------------------------------------------------------------------------------------------------------------------
		# public

		# 參數恢復
		public static function args_output($output=false,$tpl=false,$args=false){
			if(!$output){
				$sess_args = ($args !== false)?$args:$_REQUEST;
				SESS::write('last_args',$sess_args);
			}else{
				$output_args = ($args !== false)?$args:SESS::get('last_args');
				CHECK::is_array_exist($output_args);
				if(CHECK::is_pass()){
					self::$args = $output_args;

					if($tpl){
						foreach($output_args as $field => $value){
							switch($field){
								case "filename":
								case "title":
								case "h1":
								case "keywords":
								case "description":
								case "short_desc":
									$prefix = "SEO_";
								break;
								default:
									$prefix = "VALUE_";
								break;
							}

							VIEW::assignGlobal($prefix.strtoupper($field),$value);
						}
					}
				}

				SESS::del('last_args');
				return self::$args;
			}
		}

		# 清除紀錄資料列紀錄並轉出
		public static function dataClear(){
			$data = self::$data;
			self::$data = false;
			return $data;
		}

		# 取得 left join 表資料
		public static function dataJoin(array $tb_name,array $ason,$sk=false,$fetch=false,$sort=false,$limit=false,$page=false){
			self::dataClear();
			foreach($tb_name as $key => $tb){
				$tableArray[] = CORE::$prefix."_{$tb} as ".$ason[$key];
			}

			$tableSQL = implode(" LEFT JOIN ",$tableArray);
			$select = array(
				'table' => $tableSQL,
				'field' => self::field_handle($fetch),
				'where' => self::sk_handle($sk),
				'order' => self::order_handle($sort),
				'limit' => $limit,
			);

			$sql = DB::select($select);
			$rsnum = DB::num($sql);

			if($page){
				if(is_numeric($page) && $page > 0) PAGE::$num = $page;
				list($sql,$rsnum) = PAGE::handle($select,$sql,$rsnum);
			}

			switch($rsnum){
				case 0:
				break;
				case 1:
					self::$data[0] = self::content_handle(DB::fetch($sql),true);
				break;
				default:
					while($row = DB::fetch($sql)){
						$all_row[] = self::content_handle($row,true);
					}

					self::$data = $all_row;
				break;
			}

			return $rsnum;
		}

		# 取得資料數
		public static function dataNum($tb_name,$sk=false,$fetch=false,$sort=false,$limit=false,$goFetch=false){
			self::dataClear();

			$select = array(
				'table' => CORE::$prefix.'_'.$tb_name,
				'field' => self::field_handle($fetch),
				'where' => self::sk_handle($sk),
				'order' => self::order_handle($sort),
				'limit' => $limit,
			);

			$sql = DB::select($select);
			$rsnum = DB::num($sql);

			if($goFetch){
				return array($sql,$rsnum);
			}else{
				return $rsnum;
			}
		}

		# 取得各表資料
		public static function dataFetch($tb_name,$sk=false,$fetch=false,$sort=false,$limit=false,$page=false){
			list($sql,$rsnum) = self::dataNum($tb_name,$sk,$fetch,$sort,$limit,true);

			if($page){
				if(is_numeric($page) && $page > 0) PAGE::$num = $page;
				list($sql,$rsnum) = PAGE::handle($select,$sql,$rsnum);
			}

			switch($rsnum){
				case 0:
				break;
				case 1:
					self::$data[0] = self::content_handle(DB::fetch($sql),true);
				break;
				default:
					while($row = DB::fetch($sql)){
						$all_row[] = self::content_handle($row,true);
					}

					self::$data = $all_row;
				break;
			}

			return $rsnum;
		}

		# 生成語系對應 id (lang_id)
		public static function dataLink($tb_name,array $id_array){
			$args = array(
				'sheet' => $tb_name,
				'related' => json_encode($id_array),
			);

			DB::insert(CORE::$prefix."_lang",$args);
			$lang_id = DB::get_id();

			foreach($id_array as $langtag => $id){
				DB::update(CORE::$prefix."_".$tb_name,array('lang_id' => $lang_id,'id' => $id));
			}
		}

		# 創建資料
		public static function dataInsert($tb_name,array $args,$multi=false,$seo=false,$images=false){
			$new_args = self::match_field($tb_name,$args);

			if(!$new_args){
				return false;
			}

			if($multi){
				$origin_parent = (isset($new_args["parent"]))?$new_args["parent"]:false;

				# 多重語系同步儲存
				foreach(CORE::$cfg["lang"] as $lang_array){
					$new_args["langtag"] = $lang_array[1];

					# 自動關閉其他語系
					if($new_args["langtag"] != CORE::$langtag && isset($new_args["status"])) $new_args["status"] = '0';

					# 取得父系連結 id
					if(!empty($origin_parent)){
						if($new_args["langtag"] != CORE::$langtag){
							$new_args["parent"] = self::getParent($tb_name,$origin_parent,$new_args["langtag"]);
						}else{
							$new_args["parent"] = $origin_parent;
						}
					}

					DB::insert(CORE::$prefix."_".$tb_name,self::content_handle($new_args));
					$data_id[$new_args["langtag"]] = DB::get_id();

					# 自動排序
					if($new_args["sort"]){
						SORT::auto($tb_name,$new_args["langtag"],$data_id[$new_args["langtag"]],$new_args["sort"]);
					}

					# 圖片處理
					if(is_array($args["images"]) && $images){
						IMAGES::add($tb_name,$args["images"],$data_id[$new_args["langtag"]]);
					}

					# SEO 設定
					if($seo) SEO::add($tb_name,$data_id[$new_args["langtag"]],$args);
				}

				if(is_array($data_id)) self::dataLink($tb_name,$data_id);
				self::$id = $data_id;
			}else{
				DB::insert(CORE::$prefix."_".$tb_name,self::content_handle($new_args));
				self::$id = DB::get_id();

				# 自動排序
				if($new_args["sort"]){
					SORT::auto($tb_name,CORE::$langtag,self::$id,$new_args["sort"]);
				}

				# 圖片處理
				if(is_array($args["images"]) && $images){
					IMAGES::add($tb_name,$args["images"],self::$id);
				}
			}
		}

		# 修改資料
		public static function dataUpdate($tb_name,array $args,$seo=false,$images=false,$primary=false){
			$new_args = self::match_field($tb_name,$args);

			CHECK::is_array_exist($new_args);

			if(CHECK::is_pass()){
				$new_args = array_reverse($new_args);
				DB::update(CORE::$prefix."_".$tb_name,self::content_handle($new_args),$primary);

				# 自動排序
				if($new_args["sort"]){
					SORT::auto($tb_name,CORE::$langtag,$new_args["id"],$new_args["sort"]);
				}

				# 圖片處理
				if(is_array($args["images"]) && $images){
					IMAGES::modify($args["images"],$tb_name,$new_args["id"]);
				}

				if($seo){
					$args["id"] = $args["seo_id"];
					SEO::modify($args);
				}
			}else{
				return false;
			}
		}

		# 刪除資料
		public static function dataDel($tb_name,array $args){
			$rsnum = CRUD::dataFetch($tb_name,$args);
			if(!empty($rsnum)){
				list($row) = CRUD::$data;

				DB::delete(CORE::$prefix."_".$tb_name,$args);

				# 去除語系連結
				if(isset($row["lang_id"]) && !empty($row["lang_id"])){
					$lang_rsnum = CRUD::dataFetch("lang",array("id" => $row["lang_id"]));
					if(!empty($lang_rsnum)){
						list($lang_row) = CRUD::$data;
						$lang_related = json_decode($lang_row["related"],true);
						if(is_array($lang_related)){
							unset($lang_related[CORE::$langtag]);
							if(count($lang_related) >= 1){
								$new_related = array(
									'id' => $lang_row["id"],
									'related' => json_encode($lang_related),
								);

								CRUD::dataUpdate("lang",$new_related);
							}else{
								$related_none = true;
							}
						}else{
							$related_none = true;
						}

						# 沒有可連結資料,刪除整筆記錄
						if($related_none){
							DB::delete(CORE::$prefix."_lang",array('id' => $row["lang_id"]));
						}
					}
				}

				# 刪除 seo 紀錄
				if(isset($row["seo_id"]) && !empty($row["seo_id"])) DB::delete(CORE::$prefix."_seo",array('id' => $row["seo_id"]));

				# 刪除圖片記錄
				IMAGES::del($tb_name,$row["id"]);

				return true;
			}else{
				return false;
			}
		}
	}

?>