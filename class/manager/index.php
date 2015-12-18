<?php

	# 管理員功能

	class MANAGER extends OGSADMIN{

		private static $endClass;
		protected static $origin_args; # 原參數

		function __construct(){
			
			self::$origin_args = CORE::$args;
			$func = array_shift(CORE::$args);

			CORE::summon(__FILE__);
			self::$endClass =  __CLASS__."_BACKEND";
			new self::$endClass;

			switch($func){
				case "verify": # 開通管理員帳號
					self::$temp["NAV"] = '';
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::verify();
					new VIEW(self::$temp_option["HULL"],self::$temp,false,1);
				break;
				case "login": # 管理員登入頁
					self::$temp["NAV"] = '';
					self::$temp["MAIN"] = 'ogs-admin-manager-login-tpl.html';
					self::autologin();
					new VIEW(self::$temp_option["HULL"],self::$temp,false,1);
				break;
				case "login-act":
					self::$temp["NAV"] = '';
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::login();
					new VIEW(self::$temp_option["HULL"],self::$temp,false,1);
				break;
				case "regist":
					self::$temp["NAV"] = '';
					self::$temp["MAIN"] = 'ogs-admin-manager-regist-tpl.html';
					new VIEW(self::$temp_option["HULL"],self::$temp,false,1);
				break;
				case "regist-act":
					self::$temp["NAV"] = '';
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::regist();
					new VIEW(self::$temp_option["HULL"],self::$temp,false,1);
				break;
				case "forget":
					session_destroy();
					self::$temp["NAV"] = '';
					self::$temp["MAIN"] = 'ogs-admin-manager-forget-tpl.html';
					new VIEW(self::$temp_option["HULL"],self::$temp,false,1);
				break;
				case "forget-act":
					session_destroy();
					self::$temp["NAV"] = '';
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::forget();
					new VIEW(self::$temp_option["HULL"],self::$temp,false,1);
				break;
				case "logout":
					session_destroy();
					self::$temp["NAV"] = '';
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					CORE::msg(self::$lang["logout_done"],CORE::$manage);
					new VIEW(self::$temp_option["HULL"],self::$temp,false,1);
				break;
			}
		}

		function __call($function,$args){
			CORE::call_function(self::$endClass,$function,$args);
		}

		# 檢查是否登入
		public static function check(){
			$manager = SESS::get('MANAGER');
			
			if(is_array($manager) && !empty($manager["id"])){
				$rsnum = CRUD::dataFetch('manager',array(
					'id' => $manager["id"],
					'status' => '1',
					'ban' => '0',
				));

				$login = ($rsnum == 1)?true:false;
			}else{
				header("location: ".CORE::$manage.'manager/login/');
				exit;
			}

			if(!$login){
				# 重導到登入頁
				self::$temp["NAV"] = '';
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["login_none"],CORE::$manage.'manager/login/');
				new VIEW(self::$temp_option["HULL"],self::$temp,false,1);
			}
		}

		# 檢查是否被禁止登入
		public static function ban_check(){
			$sec = (5 * 60); # 禁止時間 (秒)
			$now_sec = strtotime(date("Y-m-d H:i:s"));
			$ban_time_min = date("Y-m-d H:i:s",($now_sec - $sec));

			$ip = CORE::getIP();
			$rsnum = CRUD::dataFetch('ban',array('ip' => $ip,'custom' => "time > '{$ban_time_min}'"));
			if(!empty($rsnum)){
				self::$temp["NAV"] = '';
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["still_ban"],CORE::$root);
				new VIEW(self::$temp_option["HULL"],self::$temp,false,1);
			}
		}

		# 整匯所以後台使用的功能 (權限處理用)
		public static function class_handle(array $class){
			$unset = array('sk');
			foreach($unset as $funcname){
				unset($class[$funcname]);
			}

			OGSADMIN::$class = array_keys($class);
		}

		# 檢查是否符合權限
		public static function level_check($class){
			$manager = SESS::get('MANAGER');
			$rsnum = CRUD::dataFetch('level',array('id' => $manager["level"],'status' => '1'),array('class'));

			if($rsnum){
				list($row) = CRUD::$data;
				if(!empty($row["class"])){
					$cl_array = json_decode($row["class"],true);
					$pass = ($cl_array[$class])?true:false;

					if(is_array($cl_array)){
						foreach($cl_array as $cl_name => $limit){
							if(!$limit) VIEW::assignGlobal("NAV_LEVEL_".strtoupper($cl_name),'class="hide"');
						}
					}
				}else{
					$pass = false;
				}
			}else{
				VIEW::assignGlobal("NAV_LEVEL_NULL",'class="hide"');
				$pass = false;
			}

			if(!$pass && !empty($class)){
				# 如未通過
				self::$temp["NAV"] = '';
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["manager_level_ban"],CORE::$manage);
				new VIEW(self::$temp_option["HULL"],self::$temp,false,1);
				exit;
			}
		}

		# 左邊選單權限控制
		/*
		public static function nav_level_handle(){
			$manager = SESS::get('MANAGER');

			switch($manager["level"]){
				case "1":
				break;
				case "2":
					VIEW::assignGlobal("NAV_LEVEL_1",'rel="outLevel"');
				break;
				default:
					VIEW::assignGlobal("NAV_LEVEL_NULL",'class="hide"');
				break;
			}
		}
		*/

		# 開通帳號
		private static function verify(){
			CHECK::is_must($_POST["verify"]);
			if(CHECK::is_pass()){
				$rsnum = CRUD::dataFetch('manager',array('verify' => md5($_POST["verify"])));
				if(!empty($rsnum)){
					list($row) = CRUD::$data;
					SESS::write('VERIFY_ID',$row["id"]);
					CORE::msg(self::$lang["verify_done"],CORE::$manage.'manager/regist/');
					return true;
				}
			}

			CORE::msg(self::$lang["verify_error"],CORE::$root);
		}

		# 管理員註冊
		private static function regist(){
			CHECK::is_password($_POST["password"]);
			CHECK::is_same($_POST["password"],$_POST["match_password"]);
			CHECK::is_must($_POST["name"]);

			if(CHECK::is_pass()){
				$args = array(
					'id' => SESS::get('VERIFY_ID'),
					'status' => '1',
					'password' => md5($_POST["password"]),
				);

				$args = array_merge($_POST,$args);
				
				CRUD::dataUpdate('manager',$args);
				SESS::del('VERIFY_ID');
				
				if(!empty(DB::$error)){
					CORE::msg(DB::$error,CORE::$manage.'manager/login/');
				}else{
					CORE::msg(self::$lang["account_open"],CORE::$manage.'manager/login/');
				}
			}else{
				CORE::msg(CHECK::$alert,CORE::$manage.'manager/regist/');
			}
		}

		# 檢查是否有保持登入
		private static function autologin(){
			$auto_id = $_COOKIE[CORE::$cfg["sess"]."_autoLogin"];
			$rsnum = CRUD::dataFetch('manager',array('id' => $auto_id,'ban' => '0','status' => '1'));
			if(!empty($auto_id) && !empty($rsnum)){
				list($manager) = CRUD::$data;
				SESS::write('MANAGER',$manager);

				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["auto_login"],CORE::$manage);
			}
		}

		# 後台登入
		private static function login(){
			CHECK::is_email($_POST["account"]);
			CHECK::is_password($_POST["password"]);

			if(CHECK::is_pass()){
				$rsnum = CRUD::dataFetch('manager',array('ban' => '0','status' => '1','account' => $_POST["account"],'password' => md5($_POST["password"])));
				if($rsnum == 1){
					list($manager) = CRUD::$data;
					$pass = true;
				}
			}

			if(!$pass){
				$fail_count = SESS::get('LOGIN_FAIL');
				SESS::write('LOGIN_FAIL',++$fail_count);

				if($fail_count <= 3){
					CORE::msg(self::$lang["login_error"],CORE::$manage.'manager/login/');
				}else{
					# 失敗超過三次禁止登入
					session_destroy();
					CRUD::dataInsert('ban',array('ip' => CORE::getIP()));
					CORE::msg(self::$lang["login_ban"],CORE::$root);
				}
			}else{
				if(!empty($_POST["cookie"])){
					$path = CORE::$manage.'manager/reverify/';
				}else{
					$path = CORE::$manage;
				}

				SESS::write("MANAGER",$manager);
				CORE::msg(self::$lang["login_success"],$path);
			}
		}

		# 找回密碼
		private static function forget(){
			CHECK::is_email($_POST["email"]);
			if(CHECK::is_pass()){
				$rsnum = CRUD::dataFetch('manager',array('account' => $_POST["email"],'status' => '1','ban' => '0'));
				if(!empty($rsnum)){
					list($row) = CRUD::$data;
					$rand_password = CORE::rand_password();
					$forget_temp = 'ogs-mail-manager-forget-tpl.html';

					CRUD::dataUpdate('manager',array('password' => md5($rand_password),'id' => $row["id"]));

					# 輸出取回密碼樣板
					VIEW::assignGlobal('VALUE_RAND_PASSWORD',$rand_password);
					new VIEW($forget_temp,false,true,1);

					CORE::mail_handle(SYSTEM::$setting["email"],$row["account"],VIEW::$output,CORE::$lang["forget_recall"],SYSTEM::$setting["name"]); # 寄出認證信
					CORE::msg(CORE::$lang["forget_send"],CORE::$manage);
				}else{
					CORE::msg(CORE::$lang["account_none"],CORE::$manage);
				}
			}else{
				CORE::msg(CHECK::$alert,CORE::$manage);
			}
		}

	}

?>