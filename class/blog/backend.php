<?php

	# 部落格管理

	class BLOG_BACKEND extends OGSADMIN{
		function __construct(){
			
			list($func,$id) = CORE::$args;
			$nav_class = 'BLOG';

			switch($func){
				case "cate":
					$nav_func = "CATE";
					self::$temp["MAIN"] = 'ogs-admin-blog-cate-list-tpl.html';
					self::cate();
				break;
				case "cate-add":
					$nav_func = "CATE";
					self::$temp["MAIN"] = 'ogs-admin-blog-cate-insert-tpl.html';
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
					self::$temp["MAIN"] = 'ogs-admin-blog-cate-modify-tpl.html';
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
					parent::multi('blog_cate',CORE::$manage.'blog/cate/');
				break;
				case "add":
					self::$temp["MAIN"] = 'ogs-admin-blog-insert-tpl.html';
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
					self::$temp["MAIN"] = 'ogs-admin-blog-modify-tpl.html';
					self::$temp["SEO"] = self::$temp_option["SEO"];
					self::$temp["IMAGE"] = self::$temp_option["IMAGE"];
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
					parent::multi('blog',CORE::$manage.'blog/');
				break;
				default:
					self::$temp["MAIN"] = 'ogs-admin-blog-list-tpl.html';
					self::row($func);
				break;
			}

			self::nav_current($nav_class,$nav_func);
		}

		# 分類列表
		private static function cate(){
			$rsnum = CRUD::dataFetch('blog_cate',array('langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]),false,true);
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_BLOG_CATE_BLOCK");

				$data = CRUD::$data;
				foreach($data as $key => $row){
					VIEW::newBlock("TAG_BLOG_CATE_LIST");
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
			$rsnum = CRUD::dataFetch('blog_cate',array("langtag" => CORE::$langtag));
			CRUD::args_output(true,true);
			VIEW::assignGlobal("VALUE_SORT",++$rsnum);
		}

		# 執行分類新增
		private static function cate_insert(){
			CHECK::is_must($_POST["callback"],$_POST["subject"]);

			if(CHECK::is_pass()){
				CRUD::dataInsert('blog_cate',$_POST,true,true);
				if(!empty(DB::$error)){
					CRUD::args_output();
					$msg = DB::$error;
					$path = CORE::$manage.'blog/cate-add/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage.'blog/cate/';
				}
			}else{
				CRUD::args_output();
				$msg = CHECK::$alert;
				$path = CORE::$manage.'blog/cate-add/';
			}

			CORE::msg($msg,$path);
		}

		# 詳細
		private static function cate_detail($id){
			$rsnum = CRUD::dataFetch('blog_cate',array('id' => $id));
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
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."blog/cate/page-{$last_page}/");
				}else{
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."blog/cate/");
				}

				SEO::load($row["seo_id"]);
				SEO::output();
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["no_args"],CORE::$manage.'blog/cate/');
			}
		}

		# 修改
		private static function cate_modify(){
			CHECK::is_must($_POST["callback"],$_POST["id"],$_POST["subject"]);
			$check = CHECK::is_pass();
			$rsnum = CRUD::dataFetch('blog_cate',array('id' => $_POST["id"]));

			if($check && !empty($rsnum)){
				CRUD::dataUpdate('blog_cate',$_POST,true);
				if(!empty(DB::$error)){
					$msg = DB::$error;
					$path = CORE::$manage.'blog/cate/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage."blog/cate-detail/{$_POST['id']}/";
				}
			}else{
				if(empty($rsnum)){
					$msg = self::$lang["no_data"];
					$path = CORE::$manage.'blog/cate/';
				}

				if(!$check){
					$msg = CHECK::$alert;
					$path = CORE::$manage.'blog/cate/';
				}
			}

			CORE::msg($msg,$path);
		}

		# 刪除
		private static function cate_delete($id){
			$rs = CRUD::dataDel('blog_cate',array('id' => $id));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'blog/cate/';
			}

			if(!$rs){
				$msg = self::$lang["del_error"];
				$path = CORE::$manage.'blog/cate/';
			}else{
				$msg = self::$lang["del_done"];
				$path = CORE::$manage.'blog/cate/';
			}

			CORE::msg($msg,$path);
		}

		# 分類
		################################################################################################################################
		# 項目

		# 分類選單
		private static function cate_select($parent=null){
			$rsnum = CRUD::dataFetch('blog_cate',array("langtag" => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
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
			$rsnum = CRUD::dataFetch('blog_cate',array('langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
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

			$rsnum = CRUD::dataFetch('blog',$sk,false,array('sort' => CORE::$cfg["sort"]),false,true);
			if(!empty($rsnum)){
				$data = CRUD::$data;
				VIEW::newBlock("TAG_BLOG_BLOCK");	

				foreach($data as $key => $row){
					VIEW::newBlock("TAG_BLOG_LIST");
					foreach($row as $field => $var){
						switch($field){
							case "parent":
								CRUD::dataFetch('blog_cate',array('id' => $var),array('subject'));
								list($parent) = CRUD::$data;
								VIEW::assign("VALUE_".strtoupper($field),$parent["subject"]);
							break;
							case "hot":
							case "status":
								$status = ($var)?self::$lang["status_on"]:self::$lang["status_off"];
								if(empty($var)) VIEW::assign("CLASS_".strtoupper($field)."_RED",'red');
								VIEW::assign("VALUE_".strtoupper($field),$status);
							break;
							default:
								VIEW::assign("VALUE_".strtoupper($field),$var);
							break;
						}
					}

					VIEW::assign(array(
						'VALUE_NUMBER' => PAGE::$start + (++$i),
						'VALUE_VIEW_TOTAL' => number_format($row['view_custom'] + $row['view_number']),
					));
				}
			}else{
				VIEW::newBlock("TAG_NONE");
			}
		}

		# 新增
		private static function add(){
			$rsnum = CRUD::dataFetch('blog',array("langtag" => CORE::$langtag));
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
				$_POST['createdate'] = date('Y-m-d H:i:s');

				CRUD::dataInsert('blog',$_POST,true,true,true);
				if(!empty(DB::$error)){
					CRUD::args_output();
					$msg = DB::$error;
					$path = CORE::$manage.'blog/add/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage.'blog/';
				}
			}else{
				CRUD::args_output();
				$msg = CHECK::$alert;
				$path = CORE::$manage.'blog/add/';
			}

			CORE::msg($msg,$path);
		}

		# 詳細
		private static function detail($id){
			$rsnum = CRUD::dataFetch('blog',array('id' => $id));
			if(!empty($rsnum)){
				list($row) = CRUD::$data;
				foreach($row as $field => $var){
					switch($field){
						case "parent":
							VIEW::assignGlobal("VALUE_".strtoupper($field)."_OPTION",self::cate_select($var));
						break;
						case "hot":
						case "status":
							VIEW::assignGlobal("VALUE_".strtoupper($field)."_CK".$var,'selected');
						break;
						default:
							VIEW::assignGlobal("VALUE_".strtoupper($field),$var);
						break;
					}
				}

				IMAGES::output('blog',$row["id"]);

				SEO::load($row["seo_id"]);
				SEO::output();

				$last_page = SESS::get("PAGE");
				if(!empty($last_page)){
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."blog/page-{$last_page}/");
				}else{
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."blog/");
				}
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["no_args"],CORE::$manage.'blog/');
			}
		}

		# 修改
		private static function modify(){
			CHECK::is_must($_POST["callback"],$_POST["id"],$_POST["subject"],$_POST["content"],$_POST["parent"]);
			$check = CHECK::is_pass();
			$rsnum = CRUD::dataFetch('blog',array('id' => $_POST["id"]));

			if($check && !empty($rsnum)){
				CRUD::dataUpdate('blog',$_POST,true,true);
				if(!empty(DB::$error)){
					$msg = DB::$error;
					$path = CORE::$manage.'blog/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage."blog/detail/{$_POST['id']}/";
				}
			}else{
				if(empty($rsnum)){
					$msg = self::$lang["no_data"];
					$path = CORE::$manage.'blog/';
				}

				if(!$check){
					$msg = CHECK::$alert;
					$path = CORE::$manage.'blog/';
				}
			}

			CORE::msg($msg,$path);
		}

		# 刪除
		private static function delete($id){
			$rs = CRUD::dataDel('blog',array('id' => $id));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'blog/';
			}

			if(!$rs){
				$msg = self::$lang["del_error"];
				$path = CORE::$manage.'blog/';
			}else{
				$msg = self::$lang["del_done"];
				$path = CORE::$manage.'blog/';
			}

			CORE::msg($msg,$path);
		}
	}

?>