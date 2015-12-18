<?php

	# 管理員管理

	class MANAGER_BACKEND extends MANAGER{

		private static $args;

		function __construct(){

			self::$args = self::$origin_args;
			$func = array_shift(self::$args);

			switch($func){
				case "level": # 權限管理
					CORE::res_init('get','box');
					self::$temp["MAIN"] = 'ogs-admin-manager-level-tpl.html';
					self::level();
				break;
				case "level-modify":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::level_modify();
				break;
				case "level-del":
					self::level_del();
				break;
				case "add": # 新增管理員
					self::$temp["MAIN"] = 'ogs-admin-manager-insert-tpl.html';
					self::add();
				break;
				case "insert":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::insert();
				break;
				case "detail": # 管理員詳細
					self::$temp["MAIN"] = 'ogs-admin-manager-detail-tpl.html';
					self::detail(self::$args[0]);
					$func = false;
				break;
				case "modify": # 資料修改處理
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::modify();
				break;
				case "ban":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::ban(self::$args[0]);
				break;
				case "reverify":
					self::$temp["MAIN"] = 'ogs-admin-manager-reverify-tpl.html';
					self::reverify();
				break;
				case "reverify-act":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::reverify_act();
				break;
				case "quick":
					self::$temp["MAIN"] = 'ogs-admin-manager-quick-tpl.html';
					self::quick();
				break;
				case "quick-insert":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::quick_insert();
				break;
				default: # 管理員列表
					self::$temp["MAIN"] = 'ogs-admin-manager-list-tpl.html';
					self::row();
				break;
			}

			self::nav_current('MANAGER',$func);
		}

		# 權限選單
		private static function level_select($level=null){
			$rsnum = CRUD::dataFetch('level',array('status' => '1'));
			if(!empty($rsnum)){
				$data = CRUD::$data;
				foreach($data as $row){
					$selected = (!is_null($level) && $level == $row["id"])?'selected':'';
					$option_array[] = '<option value="'.$row["id"].'" '.$selected.'>'.$row["name"].'</option>';
				}

				if(is_array($option_array)){
					return implode("",$option_array);
				}
			}
		}

		# 權限設定選項
		private static function level_setting($dataJson=false,$js=false){
			if(is_array(self::$class) && count(self::$class)){
				foreach(self::$class as $key => $cl_name){
					if($js){
						$lineup = (($i++ % 5) == 0)?'clear: both;':'';
						$js_setting[] = '<li style="float: left; margin: 0 15px; '.$lineup.'"><label>'.self::$lang[$cl_name].'</label> <input type="checkbox" name="class['.$cl_name.'][]" value="1"> </li>';
					}else{
						$data = json_decode($dataJson,true);

						VIEW::newBlock("TAG_LEVEL_SETTING");
						VIEW::assign(array(
							"VALUE_SETTING_LINEUP" => (($i++ % 5) == 0)?'clear: both;':'',
							"VALUE_SETTING_NAME" => self::$lang[$cl_name],
							"VALUE_SETTING_CLASS" => $cl_name,
							"VALUE_SETTING_CK" => (!empty($data[$cl_name]))?'checked':'',
						));
					}
				}

				if(is_array($js_setting)){
					return implode("",$js_setting);
				}
			}
		}

		# 權限設定
		private static function level(){
			$rsnum = CRUD::dataFetch('level',array('custom' => "id != '1'"));
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_LEVEL_BLOCK");
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_LEVEL_LIST");
					foreach($row as $field => $var){
						switch($field){
							case "status":
								VIEW::assign("VALUE_".strtoupper($field)."_CK{$var}","selected");
							break;
							default:
								VIEW::assign("VALUE_".strtoupper($field),$var);
							break;
						}
					}

					self::level_setting($row["class"]);
				}
			}else{
				VIEW::newBlock("TAG_LEVEL_BLOCK");
				VIEW::newBlock("TAG_LEVEL_LIST");
				self::level_setting($row["class"]);
			}

			VIEW::assignGlobal("JS_LEVEL_SETTING",self::level_setting(false,true));
		}

		# 權限儲存
		private static function level_modify(){
			CHECK::is_array_exist($_POST["id"]);
			if(CHECK::is_pass()){
				$field_array = array('id','name','status','class');
				foreach($_POST["id"] as $key => $id){
					foreach($field_array as $field){
						switch($field){
							case "class":
								if(is_array(self::$class)){
									foreach(self::$class as $cl_name){
										$cl_array[$cl_name] = (!empty($_POST[$field][$cl_name][$key]))?'1':'0';
									}

									$args[$field] = json_encode($cl_array);
								}
							break;
							default:
								$args[$field] = $_POST[$field][$key];
							break;
						}
					}

					if(empty($id)){
						CRUD::dataInsert('level',$args);
					}else{
						CRUD::dataUpdate('level',$args);
					}

					if(DB::$error){
						$msg = DB::$error;
						break;
					}else{
						$msg = self::$lang["modify_done"];
					}
				}
			}else{
				$msg = CHECK::$alert;
			}

			CORE::msg($msg,CORE::$manage.'manager/level/');
		}

		# 權限刪除
		private static function level_del(){
			CRUD::dataDel('level',array('id' => $_POST["call"]));
			if(!empty(DB::$error)){
				echo DB::$error;
			}else{
				echo 'DONE';
			}
		}

		# 管理員列表
		private static function row(){
			$rsnum = CRUD::dataFetch('manager',array('ban' => '0'));
			if(!empty($rsnum)){
				$data = CRUD::$data;
				VIEW::newBlock("TAG_MANAGER_BLOCK");
				foreach($data as $key => $row){
					VIEW::newBlock("TAG_MANAGER_LIST");
					foreach($row as $field => $var){
						switch($field){
							case "level":
								$level_rsnum = CRUD::dataFetch('level',array('id' => $var));
								$var = (!empty($level_rsnum))?CRUD::$data[0]["name"]:'無權限';
							break;
							case "status":
								if(empty($var)) VIEW::assign("CLASS_STATUS_RED",'red');
								$var = ($var)?self::$lang["status_on"]:self::$lang["status_off"];
								if(!empty($row["ban"])) $var = '封鎖';
							break;
						}

						VIEW::assign("VALUE_".strtoupper($field),$var);
					}
				}
			}else{
				VIEW::newBlock("TAG_NONE");
			}
		}

		# 新增自行認證管理員
		private static function quick(){
			VIEW::assign("VALUE_LEVEL_OPTION",self::level_select());
		}

		# 新增自行認證管理員
		private static function quick_insert(){
			CHECK::is_email($_POST["account"]);
			$pass = CHECK::is_pass();
			$verify_code = CORE::rand_password();
			$account_check = CRUD::dataFetch('manager',array('account' => $_POST["account"]));

			while(CRUD::dataFetch('manager',array('verify' => md5($verify_code)))){
				$verify_code = CORE::rand_password();
			}

			if($pass && empty($account_check)){
				$args = array(
					'account' => $_POST["account"],
					'level' => $_POST["level"],
					'verify' => md5($verify_code),
					'status' => '0',
				);

				CRUD::dataInsert('manager',$args);

				if(!empty(DB::$error)){
					$msg = DB::$error;
				}else{
					# 寄出認證信
					$mail_temp = 'ogs-mail-manager-verify-tpl.html';

					VIEW::assignGlobal(array(
						"MAIL_SYSTEM_NAME" => SYSTEM::$setting["name"],
						'MAIL_VERIFY_CODE' => $verify_code,
						"MAIL_URL" => CORE::$cfg["host"].CORE::$cfg["manage"],
					));

					new VIEW($mail_temp,false,true,1);

					CORE::mail_handle(SYSTEM::$setting["email"],$args["account"],VIEW::$output,CORE::$lang["manager_verify"],SYSTEM::$setting["name"]);

					$msg = self::$lang["modify_done"];
				}
			}else{
				$msg = CHECK::$alert;
			}

			CORE::msg($msg,CORE::$manage.'manager/');
		}

		# 新增管理員
		private static function add(){
			CRUD::args_output(true,true);
			VIEW::assign("VALUE_LEVEL_OPTION",self::level_select());
		}

		# 執行新增
		private static function insert(){
			CHECK::is_email($_POST["account"]);
			CHECK::is_password($_POST["password"]);
			CHECK::is_same($_POST["password"],$_POST["match_password"]);
			CHECK::is_must($_POST["name"]);

			$check = CHECK::is_pass();
			$account_check = CRUD::dataFetch('manager',array('account' => $_POST["account"]));

			if($check && empty($account_check)){
				$_POST["password"] = md5($_POST["password"]);
				CRUD::dataInsert('manager',$_POST);

				if(!empty(DB::$error)){
					CRUD::args_output();
					$msg = DB::$error;
					$path = CORE::$manage.'manager/add/';
				}else{
					$msg = self::$lang["account_open"];
					$path = CORE::$manage.'manager/';
				}
			}else{
				CRUD::args_output();
				$msg = (!$check)?CHECK::$alert:self::$lang["account_exist"];
				$path = CORE::$manage.'manager/add/';
			}

			CORE::msg($msg,$path);
		}

		# 管理員詳細
		private static function detail($id){
			$rsnum = CRUD::dataFetch('manager',array("id" => $id));
			if(!empty($rsnum)){
				$row = CRUD::$data[0];
				foreach($row as $field => $var){
					switch($field){
						case "status":
							VIEW::assign("VALUE_".strtoupper($field)."_CK".$var,'selected');
						break;
						case "level":
							VIEW::assign("VALUE_".strtoupper($field)."_OPTION",self::level_select($var));
						break;
						default:
							VIEW::assign("VALUE_".strtoupper($field),$var);
						break;
					}
				}
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["account_none"],CORE::$manage.'manager/');
			}
		}

		# 修改儲存
		private static function modify(){
			CHECK::is_must($_POST["id"],$_POST["name"]);
			CHECK::is_email($_POST["account"]);

			if(!empty($_POST["password"])){
				CHECK::is_password($_POST["password"]);
				CHECK::is_same($_POST["password"],$_POST["match_password"]);
				$password_check = true;
			}else{
				unset($_POST["password"]);
			}

			$check = CHECK::is_pass();

			if($password_check){
				$rsnum = CRUD::dataFetch('manager',array('id' => $_POST["id"],'password' => md5($_POST["old_password"])));
				$check = (!empty($rsnum))?true:false;

				if($check) $_POST["password"] = md5($_POST["password"]);
			}

			if($check){
				CRUD::dataUpdate('manager',$_POST);

				if(!empty(DB::$error)){
					$msg = DB::$error;
					$path = CORE::$manage.'manager/';
				}else{
					$manager = SESS::get('MANAGER');
					if($manager["id"] == $_POST["id"]){
						$msg = self::$lang["manager_modify"];
						$path = CORE::$manage.'manager/logout/';	
					}else{
						$msg = self::$lang["modify_done"];
						$path = CORE::$manage.'manager/';
					}
				}
			}else{
				$msg = (empty($rsnum))?self::$lang["password_error"]:CHECK::$alert;
				$path = CORE::$manage.'manager/';
			}

			CORE::msg($msg,$path);
		}

		# 禁用帳號
		private static function ban($id){
			CRUD::dataUpdate('manager',array('id' => $id,'ban' => '1'));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'manager/';
			}else{
				$msg = self::$lang["modify_done"];
				$path = CORE::$manage.'manager/';
			}

			CORE::msg($msg,$path);
		}
		
		# 自動登入認證
		private static function reverify(){
			$manager = SESS::get("MANAGER");
			$reverify_code = CORE::rand_password();
			SESS::write('reverify',$reverify_code);

			$mail_temp = 'ogs-mail-manager-reverify-tpl.html';

			VIEW::assignGlobal('VALUE_REVERIFY_CODE',$reverify_code);
			new VIEW($mail_temp,false,true,1);

			CORE::mail_handle(SYSTEM::$setting["email"],$manager["account"],VIEW::$output,CORE::$lang["manager_verify"],SYSTEM::$setting["name"]); # 寄出認證信
		}

		# 帳號認證確認
		private static function reverify_act(){
			$manager = SESS::get("MANAGER");
			$verify_code = SESS::get('reverify');

			if($verify_code === $_POST["verify_code"] && !empty($verify_code)){
				setcookie(CORE::$cfg["sess"].'_autoLogin',$manager["id"],time()+60*60*24*365,'/');
				$msg = self::$lang["verify_done"];
				$path = CORE::$manage;
			}else{
				$msg = self::$lang["verify_error"];
				$path = CORE::$manage;
			}

			CORE::msg($msg,$path);
		}
	}

?>