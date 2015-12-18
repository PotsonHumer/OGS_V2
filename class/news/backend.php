<?php

	# 最新消息管理

	class NEWS_BACKEND extends OGSADMIN{
		function __construct(){
			
			list($func,$id) = CORE::$args;
			$nav_class = 'NEWS';

			switch($func){
				case "cate":
					$nav_func = "CATE";
					self::$temp["MAIN"] = 'ogs-admin-news-cate-list-tpl.html';
					self::cate();
				break;
				case "cate-add":
					$nav_func = "CATE";
					self::$temp["MAIN"] = 'ogs-admin-news-cate-insert-tpl.html';
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
					self::$temp["MAIN"] = 'ogs-admin-news-cate-modify-tpl.html';
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
					parent::multi('news_cate',CORE::$manage.'news/cate/');
				break;
				case "add":
					self::$temp["MAIN"] = 'ogs-admin-news-insert-tpl.html';
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
					self::$temp["MAIN"] = 'ogs-admin-news-modify-tpl.html';
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
					parent::multi('news',CORE::$manage.'news/');
				break;
				default:
					self::$temp["MAIN"] = 'ogs-admin-news-list-tpl.html';
					self::row($func);
				break;
			}

			self::nav_current($nav_class,$nav_func);
		}

		# 分類列表
		private static function cate(){
			$rsnum = CRUD::dataFetch('news_cate',array('langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]),false,true);
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_NEWS_CATE_BLOCK");

				$data = CRUD::$data;
				foreach($data as $key => $row){
					VIEW::newBlock("TAG_NEWS_CATE_LIST");
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
			$rsnum = CRUD::dataFetch('news_cate',array("langtag" => CORE::$langtag));
			CRUD::args_output(true,true);
			VIEW::assignGlobal("VALUE_SORT",++$rsnum);
		}

		# 執行分類新增
		private static function cate_insert(){
			CHECK::is_must($_POST["callback"],$_POST["subject"]);

			if(CHECK::is_pass()){
				CRUD::dataInsert('news_cate',$_POST,true,true);
				if(!empty(DB::$error)){
					CRUD::args_output();
					$msg = DB::$error;
					$path = CORE::$manage.'news/cate-add/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage.'news/cate/';
				}
			}else{
				CRUD::args_output();
				$msg = CHECK::$alert;
				$path = CORE::$manage.'news/cate-add/';
			}

			CORE::msg($msg,$path);
		}

		# 詳細
		private static function cate_detail($id){
			$rsnum = CRUD::dataFetch('news_cate',array('id' => $id));
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
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."news/cate/page-{$last_page}/");
				}else{
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."news/cate/");
				}

				SEO::load($row["seo_id"]);
				SEO::output();
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["no_args"],CORE::$manage.'news/cate/');
			}
		}

		# 修改
		private static function cate_modify(){
			CHECK::is_must($_POST["callback"],$_POST["id"],$_POST["subject"]);
			$check = CHECK::is_pass();
			$rsnum = CRUD::dataFetch('news_cate',array('id' => $_POST["id"]));

			if($check && !empty($rsnum)){
				CRUD::dataUpdate('news_cate',$_POST,true);
				if(!empty(DB::$error)){
					$msg = DB::$error;
					$path = CORE::$manage.'news/cate/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage."news/cate-detail/{$_POST['id']}/";
				}
			}else{
				if(empty($rsnum)){
					$msg = self::$lang["no_data"];
					$path = CORE::$manage.'news/cate/';
				}

				if(!$check){
					$msg = CHECK::$alert;
					$path = CORE::$manage.'news/cate/';
				}
			}

			CORE::msg($msg,$path);
		}

		# 刪除
		private static function cate_delete($id){
			$rs = CRUD::dataDel('news_cate',array('id' => $id));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'news/cate/';
			}

			if(!$rs){
				$msg = self::$lang["del_error"];
				$path = CORE::$manage.'news/cate/';
			}else{
				$msg = self::$lang["del_done"];
				$path = CORE::$manage.'news/cate/';
			}

			CORE::msg($msg,$path);
		}

		# 分類
		################################################################################################################################
		# 項目

		# 分類選單
		private static function cate_select($parent=null){
			$rsnum = CRUD::dataFetch('news_cate',array("langtag" => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
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
			$rsnum = CRUD::dataFetch('news_cate',array('langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
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

			$rsnum = CRUD::dataFetch('news',$sk,false,array('sort' => CORE::$cfg["sort"]),false,true);
			if(!empty($rsnum)){
				$data = CRUD::$data;
				VIEW::newBlock("TAG_NEWS_BLOCK");	

				foreach($data as $key => $row){
					VIEW::newBlock("TAG_NEWS_LIST");
					foreach($row as $field => $var){
						switch($field){
							case "parent":
								CRUD::dataFetch('news_cate',array('id' => $var),array('subject'));
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
			$rsnum = CRUD::dataFetch('news',array("langtag" => CORE::$langtag));
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
				CRUD::dataInsert('news',$_POST,true,true,true);
				if(!empty(DB::$error)){
					CRUD::args_output();
					$msg = DB::$error;
					$path = CORE::$manage.'news/add/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage.'news/';
				}
			}else{
				CRUD::args_output();
				$msg = CHECK::$alert;
				$path = CORE::$manage.'news/add/';
			}

			CORE::msg($msg,$path);
		}

		# 詳細
		private static function detail($id){
			$rsnum = CRUD::dataFetch('news',array('id' => $id));
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

				IMAGES::output('news',$row["id"]);

				SEO::load($row["seo_id"]);
				SEO::output();

				$last_page = SESS::get("PAGE");
				if(!empty($last_page)){
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."news/page-{$last_page}/");
				}else{
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."news/");
				}
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["no_args"],CORE::$manage.'news/');
			}
		}

		# 修改
		private static function modify(){
			CHECK::is_must($_POST["callback"],$_POST["id"],$_POST["subject"],$_POST["content"],$_POST["parent"]);
			$check = CHECK::is_pass();
			$rsnum = CRUD::dataFetch('news',array('id' => $_POST["id"]));

			if($check && !empty($rsnum)){
				CRUD::dataUpdate('news',$_POST,true,true);
				if(!empty(DB::$error)){
					$msg = DB::$error;
					$path = CORE::$manage.'news/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage."news/detail/{$_POST['id']}/";
				}
			}else{
				if(empty($rsnum)){
					$msg = self::$lang["no_data"];
					$path = CORE::$manage.'news/';
				}

				if(!$check){
					$msg = CHECK::$alert;
					$path = CORE::$manage.'news/';
				}
			}

			CORE::msg($msg,$path);
		}

		# 刪除
		private static function delete($id){
			$rs = CRUD::dataDel('news',array('id' => $id));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'news/';
			}

			if(!$rs){
				$msg = self::$lang["del_error"];
				$path = CORE::$manage.'news/';
			}else{
				$msg = self::$lang["del_done"];
				$path = CORE::$manage.'news/';
			}

			CORE::msg($msg,$path);
		}
	}

?>