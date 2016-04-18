<?php

	# 後台留言管理

	class MESSAGE_BACKEND extends OGSADMIN{
		function __construct(){
			
			list($func,$id) = CORE::$args;
			$nav_class = 'MESSAGE';

			switch($func){
				case "add":
					self::$temp["MAIN"] = 'ogs-admin-message-insert-tpl.html';
					self::$temp["SEO"] = self::$temp_option["SEO"];
					CORE::res_init('tab','box');
					self::add();
				break;
				case "insert":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::insert();
				break;
				case "detail":
					self::$temp["MAIN"] = 'ogs-admin-message-modify-tpl.html';
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
					parent::multi('message',CORE::$manage.'message/');
				break;
				case "multiChange":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					$idArray = parent::multiChange('message');

					if(is_array($idArray)){
						foreach($idArray as $id){
							$_POST['id'] = $id;
							self::modify();
						}
					}
				break;
				default:
					self::$temp["MAIN"] = 'ogs-admin-message-list-tpl.html';
					self::row();
				break;
			}

			self::nav_current($nav_class,$nav_func);
		}

		# 留言列表
		private static function row(){
			$rsnum = CRUD::dataFetch('message',array('langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]),false,true);
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_MESSAGE_BLOCK");

				$data = CRUD::$data;
				foreach($data as $key => $row){
					VIEW::newBlock("TAG_MESSAGE_LIST");
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

		# 新增留言
		private static function add(){
			$rsnum = CRUD::dataFetch('message',array("langtag" => CORE::$langtag));
			CRUD::args_output(true,true);
			VIEW::assignGlobal("VALUE_SORT",++$rsnum);
		}

		# 執行新增
		private static function insert(){
			CHECK::is_must($_POST["callback"],$_POST["subject"],$_POST["content"]);

			if(CHECK::is_pass()){
				CRUD::dataInsert('message',$_POST,true,true);
				if(!empty(DB::$error)){
					CRUD::args_output();
					$msg = DB::$error;
					$path = CORE::$manage.'message/add/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage.'message/';
				}
			}else{
				CRUD::args_output();
				$msg = CHECK::$alert;
				$path = CORE::$manage.'message/add/';
			}

			CORE::msg($msg,$path);
		}

		# 留言詳細
		private static function detail($id){
			$rsnum = CRUD::dataFetch('message',array('id' => $id));
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
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."message/page-{$last_page}/");
				}else{
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."message/");
				}

				parent::$langID = $row['lang_id'];

				SEO::load($row["seo_id"]);
				SEO::output();
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["no_args"],CORE::$manage.'message/');
			}
		}

		# 修改留言
		private static function modify(){
			CHECK::is_must($_POST["callback"],$_POST["id"],$_POST["subject"],$_POST["content"]);
			$check = CHECK::is_pass();
			$rsnum = CRUD::dataFetch('message',array('id' => $_POST["id"]));

			if($check && !empty($rsnum)){
				CRUD::dataUpdate('message',$_POST,true);
				if(!empty(DB::$error)){
					$msg = DB::$error;
					$path = CORE::$manage.'message/';
				}else{
					$msg = self::$lang["modify_done"];
					#$path = CORE::$manage."message/detail/{$_POST['id']}/";
					$path = $_SERVER['HTTP_REFERER'];
				}
			}else{
				if(empty($rsnum)){
					$msg = self::$lang["no_data"];
					$path = CORE::$manage.'message/';
				}

				if(!$check){
					$msg = CHECK::$alert;
					$path = CORE::$manage.'message/';
				}
			}

			CORE::msg($msg,$path);
		}

		# 刪除留言
		private static function delete($id){
			$rs = CRUD::dataDel('message',array('id' => $id));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'message/';
			}

			if(!$rs){
				$msg = self::$lang["del_error"];
				$path = CORE::$manage.'message/';
			}else{
				$msg = self::$lang["del_done"];
				$path = CORE::$manage.'message/';
			}

			CORE::msg($msg,$path);
		}
	}

?>