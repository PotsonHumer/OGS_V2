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
			self::subjectFetch();
			
			if(!empty($m_id)){
				CRUD::dataFetch('member',array('id' => $m_id));
				list($row) = CRUD::$data;

				foreach($row as $field => $var){
					switch($field){
						case "gender":
							VIEW::assignGlobal('VALUE_'.strtoupper($field).'_CK'.$var,'selected');
						break;
						default:
							VIEW::assignGlobal('VALUE_'.strtoupper($field),$var);
						break;
					}
				}
			}

			CRUD::args_output(true,true);

			SEO::load('contact');
			if(empty(SEO::$data["h1"])) SEO::$data["h1"] = CORE::$lang["contact"];
			SEO::output();
			CRUMBS::fetch('contact');
		}

		# 取得主題
		private static function subjectFetch(){
			$rsnum = CRUD::dataFetch('contact_subject',false,false,array('sort' => CORE::$cfg['sort']));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				VIEW::newBlock("TAG_SUBJECT_NAME");

				foreach($dataRow as $row){
					VIEW::newBlock('TAG_SUBJECT_LIST');
					VIEW::assign('VALUE_SUBJECT',$row['subject']);
				}
			}else{
				VIEW::newBlock('TAG_SUBJECT_LIST');
				VIEW::assign('VALUE_SUBJECT','無類型');
			}
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
							case "gender":
								$var = (empty($var))?CORE::$lang["female"]:CORE::$lang["male"];
							break;
						}

						VIEW::assignGlobal('VALUE_'.strtoupper($field),$var);
					}

					$msg = CORE::$lang["submit_done"];

					$mail_temp = 'ogs-mail-contact-tpl.html'; # 信件樣板
					new VIEW($mail_temp,false,true,false);

					CORE::mail_handle($_POST["email"],SYSTEM::$setting["email"],VIEW::$output,CORE::$lang["contact_mail"],SYSTEM::$setting["name"]); # 寄出認證信
				}
			}else{
				$msg = CHECK::$alert;
				if(empty($recaptcha)) $msg = '請勾選驗證';
				CRUD::args_output();
			}

			CORE::msg($msg,CORE::$root.'contact/');
		}

		# 驗證
		private static function recaptcha(){
			$toURL = "https://www.google.com/recaptcha/api/siteverify";
			$post = array(
				"secret" => "6LdAdx0TAAAAAO0gu8Vz131Xfos5R6E5bxTxdkzD",
				"response" => $_POST["g-recaptcha-response"],
			);

			$ch = curl_init();
			$options = array(
				CURLOPT_URL => $toURL,
				CURLOPT_HEADER => 0,
				CURLOPT_VERBOSE => 0,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_USERAGENT => "Mozilla/4.0 (compatible;)",
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => http_build_query($post),
			);

			curl_setopt_array($ch, $options);
			// CURLOPT_RETURNTRANSFER=true 會傳回網頁回應,
			// false 時只回傳成功與否
			$result = curl_exec($ch); 
			curl_close($ch);
			
			$rsArray = json_decode($result,true);
			return $rsArray["success"];
		}
	}

?>