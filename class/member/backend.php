<?php

	# 後台會員管理

	class MEMBER_BACKEND extends OGSADMIN{
		function __construct(){
			
			list($func,$id) = CORE::$args;
			$nav_class = 'MEMBER';

			switch($func){
				case "add":
					$nav_func = 'add';
					self::$temp["MAIN"] = 'ogs-admin-member-insert-tpl.html';
					self::add();
				break;
				case "insert":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::insert();
				break;
				case "detail":
					MANAGER::level_check(1);
					self::$temp["MAIN"] = 'ogs-admin-member-detail-tpl.html';
					self::detail($id);
				break;
				case "reset-password":
					MANAGER::level_check(1);
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::reset_password($id);
				break;
				case "replace":
					MANAGER::level_check(1);
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::replace();
				break;
				case "del":
					MANAGER::level_check(1);
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::del($id);
				break;
				default:
					self::$temp["MAIN"] = 'ogs-admin-member-list-tpl.html';
					self::row();
					$func = false;
				break;
			}

			self::nav_current($nav_class,$nav_func);
		}

		# 會員列表
		private static function row(){
			$manager = SESS::get("MANAGER");
			$rsnum = CRUD::dataFetch('member',array('sk' => SK::fetch()),false,array('createdate' => 'desc'),false,true);
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_MEMBER_BLOCK");
				foreach(CRUD::$data as $key => $row){
					VIEW::newBlock("TAG_MEMBER_LIST");
					foreach($row as $field => $var){
						switch($field){
							case "gender":
								$gender = ($var)?self::$lang["male"]:self::$lang["female"];
								if(is_null($var)) $gender = self::$lang["null"];
								VIEW::assign("VALUE_".strtoupper($field),$gender);
							break;
							case "verify":
								$verify = ($var)?self::$lang["verify_done"]:self::$lang["verify_code_none"];
								VIEW::assign("VALUE_".strtoupper($field),$verify);
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

		# 新增會員
		private static function add(){
			CRUD::args_output(true,ture);
		}

		# 註冊會員
		private static function insert(){
			CHECK::is_password($_POST["password"]);
			CHECK::is_same($_POST["password"],$_POST["match_password"]);
			CHECK::is_must($_POST["name"]);
			CHECK::is_email($_POST["account"]);
			$check = CHECK::is_pass();

			$account_check = CRUD::dataFetch('member',array('account' => $_POST["account"]));

			if($check && empty($account_check)){
				# 預設值 / 更改
				$manager = SESS::get("MANAGER");

				$regist_args = $_POST;
				$regist_args["verify"] = '1';
				$regist_args["password"] = md5($_POST["password"]);

				CRUD::dataUpdate('member',$regist_args);

				if(!empty(DB::$error)){
					CRUD::args_output();
					CORE::msg(DB::$error,CORE::$manage.'member/add/');
				}else{
					CORE::msg(self::$lang["regist_done"],CORE::$manage.'member/'); # 完成訊息
				}
			}else{
				CRUD::args_output();

				if(!empty($account_check)){
					CORE::msg(self::$lang["account_exist"],CORE::$manage.'member/add/');
					return false;
				}

				CORE::msg(self::$lang["no_args_start"],CORE::$manage.'member/add/');
			}
		}

		# 會員詳細
		private static function detail($id){
			$rsnum = CRUD::dataFetch('member',array('id' => $id));
			if($rsnum == 1){
				list($row) = CRUD::$data;

				foreach($row as $field => $var){
					switch($field){
						case "verify":
							if($var){
								VIEW::assignGlobal(array(
									"VALUE_".strtoupper($field) => 'checked',
									"VALUE_DISABLE" => 'disabled',
								));
							}
						break;
						case "gender":
							VIEW::assignGlobal("VALUE_".strtoupper($field).'_CK'.$var,'selected');
						break;
						case "status":
							VIEW::assignGlobal("VALUE_".strtoupper($field).'_CK'.$var,'selected');
						break;
						default:
							VIEW::assignGlobal("VALUE_".strtoupper($field),$var);
						break;
					}
				}

				$page_args = SESS::get("PAGE");
				$sk_args = SESS::get('SK');
				if(!empty($page_args)) $page = "page-{$page_args}/";
				if(!empty($sk_args)) $sk = "{$sk_args}/";

				VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."member/{$sk}{$page}");
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["no_args"],CORE::$manage.'member/');
			}
		}

		# 產生新密碼
		private static function reset_password($id){
			CRUD::dataFetch('member',array('id' => $id));
			list($row) = CRUD::$data;

			$rand_password = CORE::rand_password();
			$forget_temp = 'ogs-mail-forget-tpl.html';

			CRUD::dataUpdate('member',array('password' => md5($rand_password),'id' => $row["id"]));
			CORE::msg(self::$lang["forget_send"],CORE::$manage.'member/detail/'.$row["id"].'/');

			# 原本樣板
			new VIEW(self::$temp_option["HULL"],self::$temp,true,1);
			$origin_output = VIEW::$output;

			# 輸出取回密碼樣板
			VIEW::assignGlobal('VALUE_RAND_PASSWORD',$rand_password);
			new VIEW($forget_temp,false,true,false);

			CORE::mail_handle(SYSTEM::$setting["email"],$row["email"],VIEW::$output,self::$lang["forget_recall"],SYSTEM::$setting["name"]); # 寄出認證信
			exit;
		}

		# 會員更新
		private static function replace(){
			CRUD::dataUpdate('member',$_POST);
			if(!empty(DB::$error)){
				CORE::msg(DB::$error,CORE::$manage.'member/detail/'.$_POST["id"]);
			}else{
				CORE::msg(self::$lang["modify_done"],CORE::$manage.'member/detail/'.$_POST["id"]);
			}
		}

		# 會員刪除
		private static function del($id){
			DB::delete(CORE::$prefix.'_member',array("id" => $id));
			if(!empty(DB::$error)){
				CORE::msg(DB::$error,CORE::$manage.'member/detail/'.$id);
			}else{
				CORE::msg(self::$lang["del_done"],CORE::$manage.'member/');
			}
		}
	}

?>