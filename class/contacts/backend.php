<?php

	# 後台聯絡我們管理

	class CONTACT_BACKEND extends OGSADMIN{
		function __construct(){
			
			list($func,$id) = CORE::$args;
			$nav_class = 'CONTACT';

			switch($func){
				case "subject":
					$nav_class = 'CONTACT_SUBJECT';
					self::$temp["MAIN"] = 'ogs-admin-contact-subject-tpl.html';
					self::subject();
				break;
				case "subject_replace":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::subject_replace();
				break;
				case "subject_del":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::subject_del($id);
				break;
				case "detail":
					self::$temp["MAIN"] = 'ogs-admin-contact-detail-tpl.html';
					self::detail($id);
				break;
				case "del":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::delete($id);
				break;
				case "multi":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					parent::multi('contact',CORE::$manage.'contact/');
				break;
				case "reply":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::reply();
				break;
				default:
					self::$temp["MAIN"] = 'ogs-admin-contact-list-tpl.html';
					self::row();
				break;
			}

			self::nav_current($nav_class,$nav_func);
		}

		# 設定主題
		private static function subject(){
			$rsnum = CRUD::dataFetch('contact_subject',array('langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $row){
					VIEW::newBlock('TAG_CONTACT_SUBJECT_LIST');
					foreach($row as $field => $var){
						VIEW::assign('VALUE_'.strtoupper($field),$var);
					}
				}
			}

			VIEW::assignGlobal('ADD_SORT',($rsnum + 1));
		}

		# 儲存主題
		private static function subject_replace(){
			if(is_array($_POST['id'])){
				foreach($_POST['id'] as $key => $ID){
					$newArgs = array(
						'subject' => $_POST['subject'][$key],
						'sort' => $_POST['sort'][$key],
						'email' => $_POST['email'][$key],
						'id' => $ID,
					);

					CRUD::dataUpdate('contact_subject',$newArgs);
					if(!empty(DB::$error)){
						break;
					}
				}
			}else{
				CHECK::is_must($_POST['subject']);
				if(CHECK::is_pass()){
					CRUD::dataInsert('contact_subject',$_POST,true);
					$msg = self::$lang["modify_done"];
				}else{
					$msg = self::$lang["no_args"];
				}
			}

			if(!empty(DB::$error)) $msg = DB::$error;

			CORE::msg($msg,CORE::$manage.'contact/subject/');
		}

		# 刪除主題
		private static function subject_del($id){
			CRUD::dataDel('contact_subject',array('id' => $id));
			CORE::msg(self::$lang["del_done"],CORE::$manage.'contact/subject/');
		}

		# 聯絡我們列表
		private static function row(){
			$rsnum = CRUD::dataFetch('contact',false,false,array('createdate' => 'desc'),false,true);
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_CONTACT_BLOCK");

				$data = CRUD::$data;
				foreach($data as $key => $row){
					VIEW::newBlock("TAG_CONTACT_LIST");
					foreach($row as $field => $var){
						switch($field){
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


		# 聯絡我們詳細
		private static function detail($id){
			$rsnum = CRUD::dataFetch('contact',array('id' => $id));
			if(!empty($rsnum)){
				list($row) = CRUD::$data;
				foreach($row as $field => $var){
					switch($field){
						case "gender":
							$var = (empty($var))?self::$lang["female"]:self::$lang["male"];
							if(is_null($var)) $var = self::$lang["null"];
						default:
							VIEW::assignGlobal("VALUE_".strtoupper($field),$var);
						break;
					}
				}

				$last_page = SESS::get("PAGE");
				if(!empty($last_page)){
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."contact/page-{$last_page}/");
				}else{
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."contact/");
				}
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["no_args"],CORE::$manage.'contact/');
			}
		}


		# 刪除聯絡我們
		private static function delete($id){
			$rs = CRUD::dataDel('contact',array('id' => $id));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'contact/';
			}

			if(!$rs){
				$msg = self::$lang["del_error"];
				$path = CORE::$manage.'contact/';
			}else{
				$msg = self::$lang["del_done"];
				$path = CORE::$manage.'contact/';
			}

			CORE::msg($msg,$path);
		}


		# 回覆
		private static function reply(){
			$rsnum = CRUD::dataFetch('contact',array('id' => $_POST["id"]));
			$id = $_POST["id"];
			if(!empty($rsnum) && !empty($id)){
				list($row) = CRUD::$data;

				CHECK::is_must($_POST["reply"]);
				CHECK::is_email($row["email"]);
				if(CHECK::is_pass()){
					CRUD::dataUpdate('contact',array('id' => $id,'reply' => $_POST["reply"]));

					$mail_temp = 'ogs-mail-contact-reply-tpl.html';

					VIEW::assignGlobal(array(
						'VALUE_REPLY' => $_POST["reply"],
						"VALUE_CONTENT" => $row["content"],
					));
					new VIEW($mail_temp,false,true,1);

					CORE::mail_handle(SYSTEM::$setting["email"],$row["email"],VIEW::$output,self::$lang["reply"],SYSTEM::$setting["name"]); # 寄出認證信

					$msg = self::$lang["reply_done"];
				}else{
					$msg = CHECK::$alert;
				}
			}else{
				$msg = self::$lang["no_args"];
			}

			CORE::msg($msg,CORE::$manage."contact/detail/{$id}/");
		}
	}

?>