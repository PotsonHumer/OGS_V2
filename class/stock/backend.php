<?php

	class STOCK_BACKEND extends OGSADMIN{
		function __construct(){
			
			$func = array_shift(CORE::$args);

			switch($func){
				case "cate": # 類別管理
					CORE::res_init('get','box');
					self::$temp["MAIN"] = 'ogs-admin-stock-cate-tpl.html';
					self::cate();
				break;
				case "cate-replace": # 類別更新
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::cate_replace();
				break;
				case "cate-del": # 類別刪除
					self::cate_del();
				break;
				case "item": # 項目管理
					CORE::res_init('get','box');
					self::$temp["MAIN"] = 'ogs-admin-stock-item-tpl.html';
					self::item();
				break;
				case "item-replace": # 項目更新
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::item_replace();
				break;
				case "item-del":
					self::item_del();
				break;
				case "replace":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::replace();
				break;
				case "del":
					self::del();
				break;
				default:
					CORE::res_init('get','box');
					self::$temp["MAIN"] = 'ogs-admin-stock-bind-tpl.html';
					self::bind($func);
					$func = false;
				break;
			}

			self::nav_current('STOCK',$func);
		}

		# 規格類別
		private static function cate(){
			$rsnum = CRUD::dataFetch('stock_cate',false,false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				foreach(CRUD::$data as $row){
					VIEW::newBlock("TAG_STOCK_CATE");
					foreach($row as $field => $var){
						switch($field){
							case "status":
								VIEW::assign('VALUE_'.strtoupper($field).'_CK'.$var,'selected');
							break;
							default:
								VIEW::assign('VALUE_'.strtoupper($field),$var);
							break;
						}
					}
				}
			}else{
				VIEW::newBlock("TAG_STOCK_CATE");
				VIEW::assign(array(
					"VALUE_STATUS_CK1" => 'selected',
				));
			}
		}

		# 規格類別更新
		private static function cate_replace(){
			CHECK::is_array_exist($_POST["id"]);
			if(CHECK::is_pass()){
				$sql = DB::field(CORE::$prefix.'_stock_cate');
				while($row = DB::fetch($sql)){
					$field_array[] = $row["Field"];
				}

				foreach($_POST["id"] as $key => $id){
					foreach($field_array as $field){
						$args[$field] = $_POST[$field][$key];
					}

					$args["sort"] = $key;

					if(empty($id)){
						CRUD::dataInsert('stock_cate',$args);
					}else{
						CRUD::dataUpdate('stock_cate',$args);
					}

					if(!empty(DB::$error)){
						CORE::msg(DB::$error,CORE::$manage.'stock/cate/');
					}
				}

				CORE::msg(self::$lang["modify_done"],CORE::$manage.'stock/cate/');
			}else{
				CORE::msg(self::$lang["no_args"],CORE::$manage.'stock/cate/');
			}
		}

		# 規格類別刪除
		private static function cate_del(){
			DB::delete(CORE::$prefix.'_stock_cate',array("id" => $_POST["call"]));
			if(!empty(DB::$error)){
				echo DB::$error;
			}else{
				echo 'DONE';
			}
		}

		# 規格分類選單
		private static function cate_select($id=false){
			$rsnum = CRUD::dataFetch('stock_cate',false,false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				foreach(CRUD::$data as $row){
					$selected = (!empty($id) && $row["id"] == $id)?'selected':'';
					$option_array[] = '<option value="'.$row["id"].'" '.$selected.'>'.$row["name"].'</option>';
				}

				return implode("",$option_array);
			}
		}

		# 規格項目管理
		private static function item(){
			$cate = array_shift(CORE::$args);
			if(CRUD::dataFetch('stock_cate',false,false,array('sort' => CORE::$cfg["sort"]))){
				VIEW::newBlock('TAG_CATE_BLOCK');
				foreach(CRUD::$data as $cate_row){
					VIEW::newBlock("TAG_CATE_LIST");
					VIEW::assign(array(
						"VALUE_CATE_ID" => $cate_row["id"],
						"VALUE_CATE_NAME" => $cate_row["name"],
						"VALUE_CATE_CURRENT" => ($cate_row["id"] == $cate)?'class="theme"':'',
					));
				}

				if(empty($cate)) VIEW::assignGlobal("VALUE_ALL_CURRENT",'class="theme"');
			}

			$sk = (!empty($cate))?array('cate' => $cate):false;
			$rsnum = CRUD::dataFetch('stock_item',$sk,false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				foreach(CRUD::$data as $row){
					VIEW::newBlock("TAG_STOCK_TIEM");
					foreach($row as $field => $var){
						switch($field){
							case "cate":
								VIEW::assign('VALUE_CATE_OPTION',self::cate_select($row["cate"]));
							break;
							case "status":
								VIEW::assign('VALUE_'.strtoupper($field).'_CK'.$var,'selected');
							break;
							default:
								VIEW::assign('VALUE_'.strtoupper($field),$var);
							break;
						}
					}
				}
			}else{
				VIEW::newBlock("TAG_STOCK_TIEM");
				VIEW::assign(array(
					"VALUE_STATUS_CK1" => 'selected',
					"VALUE_CATE_OPTION" => self::cate_select(),
				));
			}

			VIEW::assignGlobal("JS_CATE_OPTION",self::cate_select());
		}

		# 規格項目更新
		private static function item_replace(){
			CHECK::is_array_exist($_POST["id"]);
			if(CHECK::is_pass()){
				$sql = DB::field(CORE::$prefix.'_stock_item');
				while($row = DB::fetch($sql)){
					$field_array[] = $row["Field"];
				}

				foreach($_POST["id"] as $key => $id){
					foreach($field_array as $field){
						$args[$field] = $_POST[$field][$key];
					}

					$args["sort"] = $key;

					if(empty($id)){
						CRUD::dataInsert('stock_item',$args);
					}else{
						CRUD::dataUpdate('stock_item',$args);
					}

					if(!empty(DB::$error)){
						CORE::msg(DB::$error,CORE::$manage.'stock/item/');
					}
				}

				CORE::msg(self::$lang["modify_done"],CORE::$manage.'stock/item/');
			}else{
				CORE::msg(self::$lang["no_args"],CORE::$manage.'stock/item/');
			}
		}

		# 規格項目刪除
		private static function item_del(){
			DB::delete(CORE::$prefix.'_stock_item',array("id" => $_POST["call"]));
			if(!empty(DB::$error)){
				echo DB::$error;
			}else{
				echo 'DONE';
			}
		}

		# 產品列表
		private static function products_list($id=false){
			$select = array(
				'table' => 'bg_product_list',
				'field' => '*',
				//'where' => "",
				'order' => "list_category ".CORE::$cfg["sort"],
				//'limit' => $limit,
			);

			$sql = DB::select($select);
			$rsnum = DB::num($sql);

			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					if(empty($last_cate) || $last_cate != $row["list_category"]) VIEW::newBlock("TAG_CATE_LIST");

					VIEW::newBlock("TAG_P_LIST");
					VIEW::assign(array(
						"VALUE_P_ID" => $row["id"],
						"VALUE_P_NAME" => $row["list_name"],
						"VALUE_P_LINK" => 'http://'.CORE::$cfg["url"].'/product_list.php?list_id='.$row["id"],
						"VALUE_P_CURRENT" => ($id == $row["id"])?'class="current"':''
					));

					$last_cate = $row["list_category"];
					if($id == $row["id"]) $p_row = $row;
				}

				if(is_array($p_row)) return $p_row;
			}
		}

		# 規格項目選單 元素生成
		private static function item_cate($item=false){
			$rsnum = CRUD::dataFetch('stock_cate',false,false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				foreach(CRUD::$data as $key => $row){
					$option = self::item_select($row["id"],$item);
					$select_array[] = ' <select name="item_'.$key.'[]"><option value="null">選擇'.$row["name"].'</option>'.$option.'</select> ';
				}

				return implode("",$select_array);
			}
		}

		# 規格項目選單
		private static function item_select($cate,$item=false){
			$rsnum = CRUD::dataFetch('stock_item',array('cate' => $cate),false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				foreach(CRUD::$data as $row){
					$selected = (is_array($item) && in_array($row["id"],$item))?'selected':'';
					$option_array[] = '<option value="'.$row["id"].'" '.$selected.'>'.$row["name"].'</option>';
				}

				return implode("",$option_array);
			}
		}

		# 規格綁定
		private static function bind($p_id=false){
			$p_row = self::products_list($p_id);

			if(empty($p_id)){
				VIEW::newBlock("TAG_NONE_STOCK");
			}else{
				VIEW::newBlock("TAG_STOCK_BLOCK");
				VIEW::assign("VALUE_P_NAME",$p_row["list_name"]);

				$rsnum = CRUD::dataFetch('stock_bind',array('p_id' => $p_row["id"]),false,array('sort' => CORE::$cfg["sort"]));
				if(!empty($rsnum)){
					foreach(CRUD::$data as $row){
						VIEW::newBlock("TAG_STOCK_LIST");
						foreach($row as $field => $var){
							switch($field){
								case "item":
									$item = json_decode($var);
									VIEW::assign('VALUE_STOCK_SELECT',self::item_cate($item));
								break;
								case "status":
									VIEW::assign('VALUE_'.strtoupper($field).'_CK'.$var,'selected');
								break;
								default:
									VIEW::assign('VALUE_'.strtoupper($field),$var);
								break;
							}
						}
					}
				}else{
					VIEW::newBlock("TAG_STOCK_LIST");
					VIEW::assign(array(
						"VALUE_P_ID" => $p_row["id"],
						"VALUE_STOCK_SELECT" => self::item_cate(),
					));
				}

				VIEW::assignGlobal(array(
					"JS_STOCK_SELECT" => self::item_cate(),
					"JS_P_ID" => $p_row["id"],
				));
			}
		}

		# 規格更新
		private static function replace(){
			CHECK::is_array_exist($_POST["id"]);
			if(CHECK::is_pass()){
				$sql = DB::field(CORE::$prefix.'_stock_bind');
				while($row = DB::fetch($sql)){
					$field_array[] = $row["Field"];
				}

				foreach($_POST["id"] as $key => $id){
					$rsnum = CRUD::dataFetch('stock_cate'); # 取得規格類別數量

					foreach($field_array as $field){
						switch($field){
							case "item":
								for($i=0;$i<$rsnum;$i++){
									if($_POST['item_'.$i][$key] !== 'null') $item[$i] = $_POST['item_'.$i][$key];

									if(empty($_POST["name"][$key])){
										CRUD::dataFetch('stock_item',array('id' => $item[$i])); # 取得規格項目名稱
										if(!empty(CRUD::$data[0]["name"])) $name[$i] = CRUD::$data[0]["name"];
									}
								}

								$args["item"] = json_encode($item);
								$args["name"] = (empty($_POST["name"][$key]))?implode(" / ",$name):$_POST["name"][$key];
							break;
							default:
								$args[$field] = $_POST[$field][$key];
							break;
						}
					}

					$args["sort"] = $key;

					if(empty($id)){
						CRUD::dataInsert('stock_bind',$args);
					}else{
						CRUD::dataUpdate('stock_bind',$args);
					}

					if(empty($key)) $p_id = $args["p_id"];

					if(!empty(DB::$error)){
						CORE::msg(DB::$error,CORE::$manage."stock/{$p_id}");
					}
				}

				CORE::msg(self::$lang["modify_done"],CORE::$manage."stock/{$p_id}");
			}else{
				CORE::msg(self::$lang["no_args"],CORE::$manage."stock/{$p_id}");
			}
		}

		# 規格刪除
		private static function del(){
			DB::delete(CORE::$prefix.'_stock_bind',array("id" => $_POST["call"]));
			if(!empty(DB::$error)){
				echo DB::$error;
			}else{
				echo 'DONE';
			}
		}

	}

?>