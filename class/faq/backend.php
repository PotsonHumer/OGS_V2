<?php

	# 問與答管理

	class FAQ_BACKEND extends OGSADMIN{
		function __construct(){
			
			list($func,$id) = CORE::$args;
			$nav_class = 'FAQ';

			switch($func){
				case "cate":
					$nav_func = "CATE";
					self::$temp["MAIN"] = 'ogs-admin-faq-cate-list-tpl.html';
					self::cate();
				break;
				case "cate-add":
					$nav_func = "CATE";
					self::$temp["MAIN"] = 'ogs-admin-faq-cate-insert-tpl.html';
					self::$temp["SEO"] = self::$temp_option["SEO"];
					CORE::res_init('tab','box');
					self::cate_add();
				break;
				case "cate-insert":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::cate_insert();
				break;
				case "cate-detail":
					$nav_func = "CATE";
					self::$temp["MAIN"] = 'ogs-admin-faq-cate-modify-tpl.html';
					self::$temp["SEO"] = self::$temp_option["SEO"];
					CORE::res_init('tab','box');
					self::cate_detail($id);
				break;
				case "cate-modify":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::cate_modify();
				break;
				case "cate-del":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::cate_delete($id);
				break;
				case "cate-multi":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					parent::multi('faq_cate',CORE::$manage.'faq/cate/');
				break;
				case "add":
					self::$temp["MAIN"] = 'ogs-admin-faq-insert-tpl.html';
					self::$temp["SEO"] = self::$temp_option["SEO"];
					CORE::res_init('tab','box');
					self::add();
				break;
				case "insert":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::insert();
				break;
				case "detail":
					self::$temp["MAIN"] = 'ogs-admin-faq-modify-tpl.html';
					self::$temp["SEO"] = self::$temp_option["SEO"];
					CORE::res_init('tab','box');
					self::detail($id);
				break;
				case "modify":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::modify();
				break;
				case "del":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::delete($id);
				break;
				case "multi":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					parent::multi('faq',CORE::$manage.'faq/');
				break;
				default:
					self::$temp["MAIN"] = 'ogs-admin-faq-list-tpl.html';
					self::row($func);
				break;
			}

			self::nav_current($nav_class,$nav_func);
		}

		# 分類列表
		private static function cate(){
			$rsnum = CRUD::dataFetch('faq_cate',array('langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]),false,true);
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_FAQ_CATE_BLOCK");

				$data = CRUD::$data;
				foreach($data as $key => $row){
					VIEW::newBlock("TAG_FAQ_CATE_LIST");
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

					VIEW::assign('VALUE_NUMBER',PAGE::$start + (++$i));
				}
			}else{
				VIEW::newBlock("TAG_NONE");
			}
		}

		# 分類新增
		private static function cate_add(){
			$rsnum = CRUD::dataFetch('faq_cate',array("langtag" => CORE::$langtag));
			CRUD::args_output(true,true);
			VIEW::assignGlobal("VALUE_SORT",++$rsnum);
		}

		# 執行分類新增
		private static function cate_insert(){
			CHECK::is_must($_POST["callback"],$_POST["subject"]);

			if(CHECK::is_pass()){
				CRUD::dataInsert('faq_cate',$_POST,true,true);
				if(!empty(DB::$error)){
					CRUD::args_output();
					$msg = DB::$error;
					$path = CORE::$manage.'faq/cate-add/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage.'faq/cate/';
				}
			}else{
				CRUD::args_output();
				$msg = CHECK::$alert;
				$path = CORE::$manage.'faq/cate-add/';
			}

			CORE::msg($msg,$path);
		}

		# 詳細
		private static function cate_detail($id){
			$rsnum = CRUD::dataFetch('faq_cate',array('id' => $id));
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

				$last_page = SESS::get("PAGE");
				if(!empty($last_page)){
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."faq/cate/page-{$last_page}/");
				}else{
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."faq/cate/");
				}

				SEO::load($row["seo_id"]);
				SEO::output();
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["no_args"],CORE::$manage.'faq/cate/');
			}
		}

		# 修改
		private static function cate_modify(){
			CHECK::is_must($_POST["callback"],$_POST["id"],$_POST["subject"]);
			$check = CHECK::is_pass();
			$rsnum = CRUD::dataFetch('faq_cate',array('id' => $_POST["id"]));

			if($check && !empty($rsnum)){
				CRUD::dataUpdate('faq_cate',$_POST,true);
				if(!empty(DB::$error)){
					$msg = DB::$error;
					$path = CORE::$manage.'faq/cate/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage."faq/cate-detail/{$_POST['id']}/";
				}
			}else{
				if(empty($rsnum)){
					$msg = self::$lang["no_data"];
					$path = CORE::$manage.'faq/cate/';
				}

				if(!$check){
					$msg = CHECK::$alert;
					$path = CORE::$manage.'faq/cate/';
				}
			}

			CORE::msg($msg,$path);
		}

		# 刪除
		private static function cate_delete($id){
			$rs = CRUD::dataDel('faq_cate',array('id' => $id));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'faq/cate/';
			}

			if(!$rs){
				$msg = self::$lang["del_error"];
				$path = CORE::$manage.'faq/cate/';
			}else{
				$msg = self::$lang["del_done"];
				$path = CORE::$manage.'faq/cate/';
			}

			CORE::msg($msg,$path);
		}

		# 分類
		################################################################################################################################
		# 項目

		# 分類選單
		private static function cate_select($parent=null){
			$rsnum = CRUD::dataFetch('faq_cate',array("langtag" => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					$selected = (!is_null($parent) && $parent == $row["id"])?'selected':'';
					$option_array[] = '<option value="'.$row["id"].'" '.$selected.'>'.$row["subject"].'</option>';
				}

				if(is_array($option_array)){
					return implode("",$option_array);
				}
			}

			return false;
		}

		# 分類選單列表
		private static function cate_list($id=false){
			$rsnum = CRUD::dataFetch('faq_cate',array('langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			if($rsnum > 1){
				$parent = CRUD::$data;
				VIEW::newBlock("TAG_PARENT_BLOCK");
				foreach($parent as $key => $row){
					VIEW::newBlock("TAG_PARENT_LIST");
					foreach($row as $field => $var){
						VIEW::assign('VALUE_'.strtoupper($field),$var);
					}

					if($id == $row["id"]){
						$current = true;
						VIEW::assign("VALUE_CURRENT",'theme');
					}
				}
			}

			if(!$current) VIEW::assignGlobal("NONE_CURRENT",'theme');
		}

		# 列表
		private static function row($parent=false){
			if(!empty($parent)){
				$sk = array('langtag' => CORE::$langtag,'parent' => $parent);
			}else{
				$sk = array('langtag' => CORE::$langtag);
			}

			self::cate_list($parent); # 分類選單

			$rsnum = CRUD::dataFetch('faq',$sk,false,array('sort' => CORE::$cfg["sort"]),false,true);
			if(!empty($rsnum)){
				$data = CRUD::$data;
				VIEW::newBlock("TAG_FAQ_BLOCK");	

				foreach($data as $key => $row){
					VIEW::newBlock("TAG_FAQ_LIST");
					foreach($row as $field => $var){
						switch($field){
							case "parent":
								CRUD::dataFetch('faq_cate',array('id' => $var),array('subject'));
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

					VIEW::assign('VALUE_NUMBER',PAGE::$start + (++$i));
				}
			}else{
				VIEW::newBlock("TAG_NONE");
			}
		}

		# 新增
		private static function add(){
			$rsnum = CRUD::dataFetch('faq',array("langtag" => CORE::$langtag));
			CRUD::args_output(true,true);
			VIEW::assignGlobal(array(
				"VALUE_SORT" => ++$rsnum,
				"VALUE_PARENT_OPTION" => self::cate_select(),
			));
		}

		# 執行新增
		private static function insert(){
			CHECK::is_must($_POST["callback"],$_POST["subject"],$_POST["content"],$_POST["parent"]);

			if(CHECK::is_pass()){
				CRUD::dataInsert('faq',$_POST,true);
				if(!empty(DB::$error)){
					CRUD::args_output();
					$msg = DB::$error;
					$path = CORE::$manage.'faq/add/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage.'faq/';
				}
			}else{
				CRUD::args_output();
				$msg = CHECK::$alert;
				$path = CORE::$manage.'faq/add/';
			}

			CORE::msg($msg,$path);
		}

		# 詳細
		private static function detail($id){
			$rsnum = CRUD::dataFetch('faq',array('id' => $id));
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

				$last_page = SESS::get("PAGE");
				if(!empty($last_page)){
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."faq/page-{$last_page}/");
				}else{
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."faq/");
				}
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["no_args"],CORE::$manage.'faq/');
			}
		}

		# 修改
		private static function modify(){
			CHECK::is_must($_POST["callback"],$_POST["id"],$_POST["subject"],$_POST["content"],$_POST["parent"]);
			$check = CHECK::is_pass();
			$rsnum = CRUD::dataFetch('faq',array('id' => $_POST["id"]));

			if($check && !empty($rsnum)){
				CRUD::dataUpdate('faq',$_POST,true);
				if(!empty(DB::$error)){
					$msg = DB::$error;
					$path = CORE::$manage.'faq/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage."faq/detail/{$_POST['id']}/";
				}
			}else{
				if(empty($rsnum)){
					$msg = self::$lang["no_data"];
					$path = CORE::$manage.'faq/';
				}

				if(!$check){
					$msg = CHECK::$alert;
					$path = CORE::$manage.'faq/';
				}
			}

			CORE::msg($msg,$path);
		}

		# 刪除
		private static function delete($id){
			$rs = CRUD::dataDel('faq',array('id' => $id));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'faq/';
			}

			if(!$rs){
				$msg = self::$lang["del_error"];
				$path = CORE::$manage.'faq/';
			}else{
				$msg = self::$lang["del_done"];
				$path = CORE::$manage.'faq/';
			}

			CORE::msg($msg,$path);
		}
	}

?>