<?php

	# 前台聯絡我們功能

	class CONTACT_FRONTEND extends CONTACT{

		private static 
			$temp,
			$id; # 資料 id

		function __construct(){

			list($func) = CORE::$args;
			self::$temp = CORE::$temp_main;
			$m_id = SESS::get('m_id');

			switch($func){
				case "thankyou":
					new RESPONSE('contact',CORE::$lang['contactThanks']);
				break;
				case "add":
					self::$temp["MAIN"] = CORE::$temp_option["MSG"];
					self::add($m_id);
				break;
				default:
					self::$temp["MAIN"] = 'ogs-contact-tpl.html';
					self::form($m_id);
				break;
			}

			PRODUCTS::nav();

			new VIEW(CORE::$temp_option["HULL"],self::$temp,false,false);
		}

		# 顯示聯絡我們
		private static function form($m_id=false){			
			if(!empty($m_id)){
				CRUD::dataFetch('member',array('id' => $m_id));
				list($row) = CRUD::$data;

				foreach($row as $field => $var){
					switch($field){
						case "gender":
							$field = $field.'_CK'.$var;
							$var = 'selected';
						break;
					}

					$output['VALUE_'.strtoupper($field)] = $var;
				}
			}

			$output['VALUE_SUBJECT_OPTION'] = self::subjectOption($row['subject']);

			VIEW::assignGlobal($output);

			CRUD::args_output(true,true);

			SEO::load('contact');
			if(empty(SEO::$data["h1"])) SEO::$data["h1"] = CORE::$lang["contact"];
			SEO::output();
			CRUMBS::fetch('contact');
		}

		# 紀錄聯絡我們
		private static function add($m_id=false){
			$recaptcha = self::recaptcha();

			CHECK::is_must($_POST["name"],$_POST["tel"],$_POST["content"]);
			CHECK::is_email($_POST["email"]);

			if($recaptcha && CHECK::is_pass()){
				$args = array(
					'm_id' => (!empty($m_id))?$m_id:"null",
					'content' => htmlspecialchars($_POST["content"],ENT_NOQUOTES),
				);

				$args = array_merge($_POST,$args);

				CRUD::dataInsert('contact',$args);
				if(!empty(DB::$error)){
					$msg = DB::$error;
				}else{
					foreach($_POST as $field => $var){
						switch($field){
							case "subject":
								$subjectmail = self::subjectFetch($var,'email');
								$var = self::subjectFetch($var,'subject');
							break;
							case "gender":
								$var = (empty($var))?CORE::$lang["female"]:CORE::$lang["male"];
							break;
						}

						VIEW::assignGlobal('VALUE_'.strtoupper($field),$var);
					}

					$msg = CORE::$lang["submit_done"];

					$mail_temp = 'ogs-mail-contact-tpl.html'; # 信件樣板
					new VIEW($mail_temp,false,true,false);

					$fromMail = (!empty($subjectmail))?$subjectmail:SYSTEM::$setting["email"];

					CORE::mail_handle($_POST["email"],$fromMail,VIEW::$output,CORE::$lang["contact_mail"],SYSTEM::$setting["name"]); # 寄出認證信
					$rs = true;
				}
			}else{
				$msg = CHECK::$alert;
				if(empty($recaptcha)) $msg = '請勾選驗證';
				CRUD::args_output();
			}

			if($rs){
				RESPONSE::register($_POST,CORE::$root.'contact/thankyou/');
				exit;
			}else{
				CORE::msg($msg,CORE::$root.'contact/');
			}
		}
	}

?>