<?php

	# 前台反饋功能

	class FEEDBACK_FRONTEND extends FEEDBACK{

		private static 
			$temp,
			$m_id, # 會員 id
			$id; # 資料 id

		function __construct(){

			list($args) = CORE::$args;
			self::$m_id = SESS::get('m_id');
			self::$temp = CORE::$temp_main;

			CRUMBS::fetch('feedback');

			switch($args){
				case "list": # 顯示反饋
					self::$temp["MAIN"] = 'ogs-feedback-list-tpl.html';
					self::row();
				break;
				case "add": # 執行增加反饋
					self::$temp["MAIN"] = CORE::$temp_option["MSG"];
					self::add();
				break;
				default:
					self::$temp["MAIN"] = 'ogs-feedback-tpl.html';
				break;
			}

			CORE::common_resource();

			new VIEW(CORE::$temp_option["HULL"],self::$temp,false,false);
		}


		# 反饋顯示
		private static function row(){
			$rsnum = CRUD::dataFetch('feedback',array('status' => '1'),false,array('createdate' => 'desc'));
			if(!empty($rsnum)){
				VIEW::newBlock('TAG_FEEDBACK_BLOCK');
				$dataRow = CRUD::$data;
				foreach($dataRow as $row){
					VIEW::newBlock('TAG_FEEDBACK_LIST');
					foreach($row as $field => $var){
						VIEW::assign('VALUE_'.strtoupper($field),$var);
					}

					# 顯示評分星星數
					if(!empty($row['score'])){
						VIEW::newBlock('TAG_SCORE_BLOCK');
						VIEW::assign('VALUE_SCORE',$row['score']);
						$score = 0;

						while(++$score <= $row['score']){
							VIEW::newBlock('TAG_SCORE_STAR');
						}
					}
				}
			}
		}

		# 增加反饋
		private static function add(){
			CHECK::is_must($_POST["callback"],$_POST["name"],$_POST["content"]);
			CHECK::is_email($_POST["email"]);

			if(CHECK::is_pass()){
				CRUD::dataInsert('feedback',$_POST);
				if(!empty(DB::$error)){
					$msg = 'Error! '.DB::$error;
				}else{
					$msg = CORE::$lang['submit_done'];
				}
			}else{
				$msg = CHECK::$alert;
			}

			CORE::msg($msg,CORE::$root.'feedback/');
		}
	}

?>