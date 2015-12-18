<?php

	# 後台介紹頁管理

	class INTRO_BACKEND extends OGSADMIN{
		function __construct(){
			
			list($func,$id) = CORE::$args;
			$nav_class = 'INTRO';

			switch($func){
				case "add":
					self::$temp["MAIN"] = 'ogs-admin-intro-insert-tpl.html';
					self::$temp["SEO"] = self::$temp_option["SEO"];
					CORE::res_init('tab','box');
					self::add();
				break;
				case "insert":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::insert();
				break;
				case "detail":
					self::$temp["MAIN"] = 'ogs-admin-intro-modify-tpl.html';
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
					parent::multi('intro',CORE::$manage.'intro/');
				break;
				default:
					self::$temp["MAIN"] = 'ogs-admin-intro-list-tpl.html';
					self::row();
				break;
			}

			self::nav_current($nav_class,$nav_func);
		}

		# 介紹頁列表
		private static function row(){
			$rsnum = CRUD::dataFetch('intro',array('langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]),false,true);
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_INTRO_BLOCK");

				$data = CRUD::$data;
				foreach($data as $key => $row){
					VIEW::newBlock("TAG_INTRO_LIST");
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

		# 新增介紹頁
		private static function add(){
			$rsnum = CRUD::dataFetch('intro',array("langtag" => CORE::$langtag));
			CRUD::args_output(true,true);
			VIEW::assignGlobal("VALUE_SORT",++$rsnum);
		}

		# 執行新增
		private static function insert(){
			CHECK::is_must($_POST["callback"],$_POST["subject"],$_POST["content"]);

			if(CHECK::is_pass()){
				CRUD::dataInsert('intro',$_POST,true,true);
				if(!empty(DB::$error)){
					CRUD::args_output();
					$msg = DB::$error;
					$path = CORE::$manage.'intro/add/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage.'intro/';
				}
			}else{
				CRUD::args_output();
				$msg = CHECK::$alert;
				$path = CORE::$manage.'intro/add/';
			}

			CORE::msg($msg,$path);
		}

		# 介紹頁詳細
		private static function detail($id){
			$rsnum = CRUD::dataFetch('intro',array('id' => $id));
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
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."intro/page-{$last_page}/");
				}else{
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."intro/");
				}

				SEO::load($row["seo_id"]);
				SEO::output();
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["no_args"],CORE::$manage.'intro/');
			}
		}

		# 修改介紹頁
		private static function modify(){
			CHECK::is_must($_POST["callback"],$_POST["id"],$_POST["subject"],$_POST["content"]);
			$check = CHECK::is_pass();
			$rsnum = CRUD::dataFetch('intro',array('id' => $_POST["id"]));

			if($check && !empty($rsnum)){
				CRUD::dataUpdate('intro',$_POST,true);
				if(!empty(DB::$error)){
					$msg = DB::$error;
					$path = CORE::$manage.'intro/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage."intro/detail/{$_POST['id']}/";
				}
			}else{
				if(empty($rsnum)){
					$msg = self::$lang["no_data"];
					$path = CORE::$manage.'intro/';
				}

				if(!$check){
					$msg = CHECK::$alert;
					$path = CORE::$manage.'intro/';
				}
			}

			CORE::msg($msg,$path);
		}

		# 刪除介紹頁
		private static function delete($id){
			$rs = CRUD::dataDel('intro',array('id' => $id));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'intro/';
			}

			if(!$rs){
				$msg = self::$lang["del_error"];
				$path = CORE::$manage.'intro/';
			}else{
				$msg = self::$lang["del_done"];
				$path = CORE::$manage.'intro/';
			}

			CORE::msg($msg,$path);
		}
	}

?>