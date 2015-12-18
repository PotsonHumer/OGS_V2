<?php

	# 產品管理

	class PRODUCTS_BACKEND extends OGSADMIN{
		function __construct(){
			
			list($func,$args) = CORE::$args;
			if(is_null($args)) $args = "null";

			$nav_class = 'PRODUCTS';

			switch($func){
				case "cate":
					$nav_func = "CATE";
					self::$temp["MAIN"] = 'ogs-admin-products-cate-list-tpl.html';
					self::$temp["TREE"] = self::$temp_option["TREE"];
					PRODUCTS::tree();
					self::cate();
				break;
				case "cate-add":
					$nav_func = "CATE";
					self::$temp["MAIN"] = 'ogs-admin-products-cate-insert-tpl.html';
					self::$temp["SEO"] = self::$temp_option["SEO"];
					self::$temp["IMAGE"] = self::$temp_option["IMAGE"];
					CORE::res_init('tab','box');
					self::cate_add();
				break;
				case "cate-insert":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::cate_insert();
				break;
				case "cate-detail":
					$nav_func = "CATE";
					self::$temp["MAIN"] = 'ogs-admin-products-cate-modify-tpl.html';
					self::$temp["SEO"] = self::$temp_option["SEO"];
					self::$temp["IMAGE"] = self::$temp_option["IMAGE"];
					CORE::res_init('tab','box');
					self::cate_detail($args);
				break;
				case "cate-modify":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::cate_modify();
				break;
				case "cate-del":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::cate_delete($args);
				break;
				case "cate-multi":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					parent::multi('products_cate',CORE::$manage.'products/cate/');
				break;
				case "add":
					self::$temp["MAIN"] = 'ogs-admin-products-insert-tpl.html';
					self::$temp["SEO"] = self::$temp_option["SEO"];
					self::$temp["IMAGE"] = self::$temp_option["IMAGE"];
					CORE::res_init('tab','box');
					self::add();
				break;
				case "insert":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::insert();
				break;
				case "detail":
					self::$temp["MAIN"] = 'ogs-admin-products-modify-tpl.html';
					self::$temp["SEO"] = self::$temp_option["SEO"];
					self::$temp["IMAGE"] = self::$temp_option["IMAGE"];
					CORE::res_init('tab','box');
					self::detail($args);
				break;
				case "modify":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::modify();
				break;
				case "del":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::delete($args);
				break;
				case "multi":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					parent::multi('products',CORE::$manage.'products/');
				break;
				case "getSort":
					parent::getSort($args);
				break;
				default:
					self::$temp["MAIN"] = 'ogs-admin-products-list-tpl.html';
					self::$temp["TREE"] = self::$temp_option["TREE"];
					PRODUCTS::tree();
					self::row();
				break;
			}

			self::nav_current($nav_class,$nav_func);
		}

		# 分類選單
		private static function cate_select($now=null,$cate="null",$level=0){
			if(is_null($cate) || empty($cate)) $cate = "null";
			$rsnum = CRUD::dataFetch('products_cate',array('parent' => $cate,"langtag" => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){

					if(!empty($level)){
						unset($i,$star);
						while(++$i <= $level){
							$star .= '****';
						}
					}

					$selected = (!is_null($now) && $now == $row["id"])?'selected':'';
					$option_array[] = '<option value="'.$row["id"].'" '.$selected.'>├'.$star.$row["subject"].'</option>';

					$option_array[] = self::cate_select($now,$row["id"],($level + 1));
				}

				if(is_array($option_array)){
					return implode("",$option_array);
				}
			}

			return false;
		}
		
		# 分類列表
		private static function cate(){
			$rsnum = CRUD::dataFetch('products_cate',array('langtag' => CORE::$langtag,'sk' => SK::fetch()),false,array('sort' => CORE::$cfg["sort"]),false,true);
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_PRODUCTS_CATE_BLOCK");

				$data = CRUD::$data;
				foreach($data as $key => $row){
					VIEW::newBlock("TAG_PRODUCTS_CATE_LIST");
					foreach($row as $field => $var){
						switch($field){
							case "status":
								$status = ($var)?self::$lang["status_on"]:self::$lang["status_off"];
								if(empty($var)) VIEW::assign("CLASS_STATUS_RED",'red');
								VIEW::assign("VALUE_".strtoupper($field),$status);
							break;
							default:
								VIEW::assign("VALUE_".strtoupper($field),$var);
							break;
						}
					}

					IMAGES::load('products_cate',$row["id"]);
					list($image) = IMAGES::$data;
					VIEW::assign(array(
						'VALUE_NUMBER' => PAGE::$start + (++$i),
						"VALUE_IMAGE" => $image["path"],
						'VALUE_NUMBER' => PAGE::$start + (++$i),
					));
				}
			}else{
				VIEW::newBlock("TAG_NONE");
			}

			VIEW::assignGlobal("VALUE_PARENT_OPTION",self::cate_select(SK::$args["parent"]));
		}

		# 分類新增
		private static function cate_add(){
			CORE::res_init('get','box');
			$rsnum = CRUD::dataFetch('products_cate',array("langtag" => CORE::$langtag));
			CRUD::args_output(true,true);
			VIEW::assignGlobal(array(
				"VALUE_SORT" => ++$rsnum,
				"VALUE_PARENT_OPTION" => self::cate_select(),
			));
		}

		# 執行分類新增
		private static function cate_insert(){
			CHECK::is_must($_POST["callback"],$_POST["subject"]);

			if(CHECK::is_pass()){
				CRUD::dataInsert('products_cate',$_POST,true,true,true);
				if(!empty(DB::$error)){
					CRUD::args_output();
					$msg = DB::$error;
					$path = CORE::$manage.'products/cate-add/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage.'products/cate/';
				}
			}else{
				CRUD::args_output();
				$msg = CHECK::$alert;
				$path = CORE::$manage.'products/cate-add/';
			}

			CORE::msg($msg,$path);
		}

		# 詳細
		private static function cate_detail($id){
			$rsnum = CRUD::dataFetch('products_cate',array('id' => $id));
			if(!empty($rsnum)){
				list($row) = CRUD::$data;
				foreach($row as $field => $var){
					switch($field){
						case "status":
							VIEW::assignGlobal("VALUE_".strtoupper($field)."_CK".$var,'selected');
						break;
						default:
							VIEW::assignGlobal("VALUE_".strtoupper($field),$var);
						break;
					}
				}

				IMAGES::output('products_cate',$row["id"]);

				VIEW::assignGlobal("VALUE_PARENT_OPTION",self::cate_select($row["parent"]));

				$page_args = SESS::get("PAGE");
				$sk_args = SESS::get('SK');
				if(!empty($page_args)) $page = "page-{$page_args}/";
				if(!empty($sk_args)) $sk = "{$sk_args}/";

				VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."products/cate/{$sk}{$page}");

				SEO::load($row["seo_id"]);
				SEO::output();
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["no_args"],CORE::$manage.'products/cate/');
			}
		}

		# 修改
		private static function cate_modify(){
			CHECK::is_must($_POST["callback"],$_POST["id"],$_POST["subject"]);
			$check = CHECK::is_pass();
			$rsnum = CRUD::dataFetch('products_cate',array('id' => $_POST["id"]));

			if($check && !empty($rsnum)){
				CRUD::dataUpdate('products_cate',$_POST,true,true);
				if(!empty(DB::$error)){
					$msg = DB::$error;
					$path = CORE::$manage.'products/cate/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage."products/cate-detail/{$_POST['id']}/";
				}
			}else{
				if(empty($rsnum)){
					$msg = self::$lang["no_data"];
					$path = CORE::$manage.'products/cate/';
				}

				if(!$check){
					$msg = CHECK::$alert;
					$path = CORE::$manage.'products/cate/';
				}
			}

			CORE::msg($msg,$path);
		}

		# 刪除
		private static function cate_delete($id=false){
			$rs = CRUD::dataDel('products_cate',array('id' => $id));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'products/cate/';
			}

			if(!$rs){
				$msg = self::$lang["del_error"];
				$path = CORE::$manage.'products/cate/';
			}else{
				$msg = self::$lang["del_done"];
				$path = CORE::$manage.'products/cate/';
			}

			CORE::msg($msg,$path);
		}

		# 分類
		################################################################################################################################
		# 項目

		# 列表
		private static function row(){
			#self::cate_list($cate); # 分類選單
			$rsnum = CRUD::dataFetch('products',array('langtag' => CORE::$langtag,'sk' => SK::fetch()),false,array('sort' => CORE::$cfg["sort"]),false,true);
			if(!empty($rsnum)){
				$data = CRUD::$data;
				VIEW::newBlock("TAG_PRODUCTS_BLOCK");

				foreach($data as $key => $row){
					VIEW::newBlock("TAG_PRODUCTS_LIST");
					foreach($row as $field => $var){
						switch($field){
							case "parent":
								CRUD::dataFetch('products_cate',array('id' => $var),array('subject'));
								list($parent) = CRUD::$data;
								VIEW::assign("VALUE_".strtoupper($field),$parent["subject"]);
							break;
							case "status":
								$status = ($var)?self::$lang["status_on"]:self::$lang["status_off"];
								if(empty($var)) VIEW::assign("CLASS_STATUS_RED",'red');
								VIEW::assign("VALUE_".strtoupper($field),$status);
							break;
							default:
								VIEW::assign("VALUE_".strtoupper($field),$var);
							break;
						}
					}

					IMAGES::load('products',$row["id"]);
					list($image) = IMAGES::$data;
					VIEW::assign(array(
						'VALUE_NUMBER' => PAGE::$start + (++$i),
						"VALUE_IMAGE" => $image["path"],
					));
				}
			}else{
				VIEW::newBlock("TAG_NONE");
			}

			VIEW::assignGlobal("VALUE_PARENT_OPTION",self::cate_select(SK::$args["parent"]));
		}

		# 新增
		private static function add(){
			$rsnum = CRUD::dataFetch('products',array("langtag" => CORE::$langtag));
			CRUD::args_output(true,true);
			VIEW::assignGlobal(array(
				"VALUE_SORT" => ++$rsnum,
				"VALUE_SHOWDATE" => date("Y-m-d"),
				"VALUE_PARENT_OPTION" => self::cate_select(),
			));
		}

		# 執行新增
		private static function insert(){
			CHECK::is_must($_POST["callback"],$_POST["subject"],$_POST["content"],$_POST["parent"]);

			if(CHECK::is_pass()){
				CRUD::dataInsert('products',$_POST,true,true,true);
				if(!empty(DB::$error)){
					CRUD::args_output();
					$msg = DB::$error;
					$path = CORE::$manage.'products/add/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage.'products/';
				}
			}else{
				CRUD::args_output();
				$msg = CHECK::$alert;
				$path = CORE::$manage.'products/add/';
			}

			CORE::msg($msg,$path);
		}

		# 詳細
		private static function detail($id){
			$rsnum = CRUD::dataFetch('products',array('id' => $id));
			if(!empty($rsnum)){
				list($row) = CRUD::$data;
				foreach($row as $field => $var){
					switch($field){
						case "parent":
							VIEW::assignGlobal("VALUE_".strtoupper($field)."_OPTION",self::cate_select($var));
						break;
						case "status":
							VIEW::assignGlobal("VALUE_".strtoupper($field)."_CK".$var,'selected');
						break;
						default:
							VIEW::assignGlobal("VALUE_".strtoupper($field),$var);
						break;
					}
				}

				IMAGES::output('products',$row["id"]);

				SEO::load($row["seo_id"]);
				SEO::output();

				$page_args = SESS::get("PAGE");
				$sk_args = SESS::get('SK');
				if(!empty($page_args)) $page = "page-{$page_args}/";
				if(!empty($sk_args)) $sk = "{$sk_args}/";

				VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."products/{$sk}{$page}");
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["no_args"],CORE::$manage.'products/');
			}
		}

		# 修改
		private static function modify(){
			CHECK::is_must($_POST["callback"],$_POST["id"],$_POST["subject"],$_POST["content"],$_POST["parent"]);
			$check = CHECK::is_pass();
			$rsnum = CRUD::dataFetch('products',array('id' => $_POST["id"]));

			if($check && !empty($rsnum)){
				CRUD::dataUpdate('products',$_POST,true,true,true);
				if(!empty(DB::$error)){
					$msg = DB::$error;
					$path = CORE::$manage.'products/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage."products/detail/{$_POST['id']}/";
				}
			}else{
				if(empty($rsnum)){
					$msg = self::$lang["no_data"];
					$path = CORE::$manage.'products/';
				}

				if(!$check){
					$msg = CHECK::$alert;
					$path = CORE::$manage.'products/';
				}
			}

			CORE::msg($msg,$path);
		}

		# 刪除
		private static function delete($id){
			$rs = CRUD::dataDel('products',array('id' => $id));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'products/';
			}

			if(!$rs){
				$msg = self::$lang["del_error"];
				$path = CORE::$manage.'products/';
			}else{
				$msg = self::$lang["del_done"];
				$path = CORE::$manage.'products/';
			}

			CORE::msg($msg,$path);
		}
	}

?>