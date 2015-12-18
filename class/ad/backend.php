<?php

	# 廣告管理

	class AD_BACKEND extends OGSADMIN{
		function __construct(){
			
			list($func,$id) = CORE::$args;
			$nav_class = 'AD';

			switch($func){
				case "cate":
					$nav_func = "CATE";
					self::$temp["MAIN"] = 'ogs-admin-ad-cate-list-tpl.html';
					self::cate();
				break;
				case "cate-add":
					$nav_func = "CATE";
					self::$temp["MAIN"] = 'ogs-admin-ad-cate-insert-tpl.html';
					self::cate_add();
				break;
				case "cate-insert":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::cate_insert();
				break;
				case "cate-detail":
					$nav_func = "CATE";
					self::$temp["MAIN"] = 'ogs-admin-ad-cate-modify-tpl.html';
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
					parent::multi('ad_cate',CORE::$manage.'ad/cate/');
				break;
				case "add":
					self::$temp["MAIN"] = 'ogs-admin-ad-insert-tpl.html';
					self::$temp["IMAGE"] = self::$temp_option["IMAGE"];
					self::add();
				break;
				case "insert":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::insert();
				break;
				case "detail":
					self::$temp["MAIN"] = 'ogs-admin-ad-modify-tpl.html';
					self::$temp["IMAGE"] = self::$temp_option["IMAGE"];
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
					parent::multi('ad',CORE::$manage.'ad/');
				break;
				default:
					self::$temp["MAIN"] = 'ogs-admin-ad-list-tpl.html';
					self::row($func);
				break;
			}

			self::nav_current($nav_class,$nav_func);
		}

		# 分類列表
		private static function cate(){
			$rsnum = CRUD::dataFetch('ad_cate',array('langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]),false,true);
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_AD_CATE_BLOCK");

				$data = CRUD::$data;
				foreach($data as $key => $row){
					VIEW::newBlock("TAG_AD_CATE_LIST");
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
			$rsnum = CRUD::dataFetch('ad_cate',array("langtag" => CORE::$langtag));
			CRUD::args_output(true,true);
			VIEW::assignGlobal("VALUE_SORT",++$rsnum);
		}

		# 執行分類新增
		private static function cate_insert(){
			CHECK::is_must($_POST["callback"],$_POST["subject"]);

			if(CHECK::is_pass()){
				CRUD::dataInsert('ad_cate',$_POST,true);
				if(!empty(DB::$error)){
					CRUD::args_output();
					$msg = DB::$error;
					$path = CORE::$manage.'ad/cate-add/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage.'ad/cate/';
				}
			}else{
				CRUD::args_output();
				$msg = CHECK::$alert;
				$path = CORE::$manage.'ad/cate-add/';
			}

			CORE::msg($msg,$path);
		}

		# 詳細
		private static function cate_detail($id){
			$rsnum = CRUD::dataFetch('ad_cate',array('id' => $id));
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
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."ad/cate/page-{$last_page}/");
				}else{
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."ad/cate/");
				}

				SEO::load($row["seo_id"]);
				SEO::output();
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["no_args"],CORE::$manage.'ad/cate/');
			}
		}

		# 修改
		private static function cate_modify(){
			CHECK::is_must($_POST["callback"],$_POST["id"],$_POST["subject"]);
			$check = CHECK::is_pass();
			$rsnum = CRUD::dataFetch('ad_cate',array('id' => $_POST["id"]));

			if($check && !empty($rsnum)){
				CRUD::dataUpdate('ad_cate',$_POST);
				if(!empty(DB::$error)){
					$msg = DB::$error;
					$path = CORE::$manage.'ad/cate/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage."ad/cate-detail/{$_POST['id']}/";
				}
			}else{
				if(empty($rsnum)){
					$msg = self::$lang["no_data"];
					$path = CORE::$manage.'ad/cate/';
				}

				if(!$check){
					$msg = CHECK::$alert;
					$path = CORE::$manage.'ad/cate/';
				}
			}

			CORE::msg($msg,$path);
		}

		# 刪除
		private static function cate_delete($id){
			$rs = CRUD::dataDel('ad_cate',array('id' => $id));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'ad/cate/';
			}

			if(!$rs){
				$msg = self::$lang["del_error"];
				$path = CORE::$manage.'ad/cate/';
			}else{
				$msg = self::$lang["del_done"];
				$path = CORE::$manage.'ad/cate/';
			}

			CORE::msg($msg,$path);
		}

		# 分類
		################################################################################################################################
		# 項目

		# 分類選單
		private static function cate_select($parent=null){
			$rsnum = CRUD::dataFetch('ad_cate',array("langtag" => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
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
			$rsnum = CRUD::dataFetch('ad_cate',array('langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
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

			$rsnum = CRUD::dataFetch('ad',$sk,false,array('sort' => CORE::$cfg["sort"]),false,true);
			if(!empty($rsnum)){
				$data = CRUD::$data;
				VIEW::newBlock("TAG_AD_BLOCK");	

				foreach($data as $key => $row){
					VIEW::newBlock("TAG_AD_LIST");
					foreach($row as $field => $var){
						switch($field){
							case "parent":
								CRUD::dataFetch('ad_cate',array('id' => $var),array('subject'));
								list($parent) = CRUD::$data;
								VIEW::assign("VALUE_".strtoupper($field),$parent["subject"]);
							break;
							case "status":
								$status = ($var)?self::$lang["status_on"]:self::$lang["status_off"];
								if(empty($var)) VIEW::assign("CLASS_STATUS_RED",'red');
								if($var==2) $status = '依照顯示時間';
								VIEW::assign("VALUE_".strtoupper($field),$status);
							break;
							default:
								VIEW::assign("VALUE_".strtoupper($field),$var);
							break;
						}
					}

					IMAGES::load('ad',$row["id"]);

					VIEW::assign(array(
						'VALUE_NUMBER' => PAGE::$start + (++$i),
						"VALUE_IMAGE" => IMAGES::$data[0]["path"],
					));
				}
			}else{
				VIEW::newBlock("TAG_NONE");
			}
		}

		# 新增
		private static function add(){
			$rsnum = CRUD::dataFetch('ad',array("langtag" => CORE::$langtag));
			CRUD::args_output(true,true);
			VIEW::assignGlobal(array(
				"VALUE_SORT" => ++$rsnum,
				"VALUE_SHOWDATE" => date("Y-m-d"),
				"VALUE_PARENT_OPTION" => self::cate_select(),
			));
		}

		# 執行新增
		private static function insert(){
			CHECK::is_must($_POST["callback"],$_POST["subject"],$_POST["parent"]);

			if(CHECK::is_pass()){
				CRUD::dataInsert('ad',$_POST,true,false,true);
				if(!empty(DB::$error)){
					CRUD::args_output();
					$msg = DB::$error;
					$path = CORE::$manage.'ad/add/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage.'ad/';
				}
			}else{
				CRUD::args_output();
				$msg = CHECK::$alert;
				$path = CORE::$manage.'ad/add/';
			}

			CORE::msg($msg,$path);
		}

		# 詳細
		private static function detail($id){
			#OGSADMIN::language_select('ad',$id);

			$rsnum = CRUD::dataFetch('ad',array('id' => $id));
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

				IMAGES::output('ad',$row["id"]);

				SEO::load($row["seo_id"]);
				SEO::output();

				$last_page = SESS::get("PAGE");
				if(!empty($last_page)){
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."ad/page-{$last_page}/");
				}else{
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."ad/");
				}
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["no_args"],CORE::$manage.'ad/');
			}
		}

		# 修改
		private static function modify(){
			CHECK::is_must($_POST["callback"],$_POST["id"],$_POST["subject"],$_POST["parent"]);
			$check = CHECK::is_pass();
			$rsnum = CRUD::dataFetch('ad',array('id' => $_POST["id"]));

			if($check && !empty($rsnum)){
				CRUD::dataUpdate('ad',$_POST,false,true);
				if(!empty(DB::$error)){
					$msg = DB::$error;
					$path = CORE::$manage.'ad/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage."ad/detail/{$_POST['id']}/";
				}
			}else{
				if(empty($rsnum)){
					$msg = self::$lang["no_data"];
					$path = CORE::$manage.'ad/';
				}

				if(!$check){
					$msg = CHECK::$alert;
					$path = CORE::$manage.'ad/';
				}
			}

			CORE::msg($msg,$path);
		}

		# 刪除
		private static function delete($id){
			$rs = CRUD::dataDel('ad',array('id' => $id));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'ad/';
			}

			if(!$rs){
				$msg = self::$lang["del_error"];
				$path = CORE::$manage.'ad/';
			}else{
				$msg = self::$lang["del_done"];
				$path = CORE::$manage.'ad/';
			}

			CORE::msg($msg,$path);
		}
	}

?>