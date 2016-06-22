<?php

	# 前台留言功能

	class MESSAGE_FRONTEND extends MESSAGE{

		private static 
			$temp,
			$id; # 資料 id

		function __construct(){

			list($args) = CORE::$args;
			self::$temp = CORE::$temp_main;

			switch($args){
				case "add":
					self::$temp["MAIN"] = CORE::$temp_option["MSG"];
					self::add();
				break;
				case "reply":
					self::$temp["MAIN"] = CORE::$temp_option["MSG"];
					self::reply();
				break;
				default:
					self::row();
					new VIEW('ogs-message-tpl.html',false,true);
					self::$output = VIEW::$output;
					return;
				break;
			}

			CORE::common_resource();

			VIEW::assignGlobal('SEO_H1','留言系統資訊');

			new VIEW(CORE::$temp_option["HULL"],self::$temp,false,false);
		}

		# 檢查是否為管理者
		private static function check(){
			$manager = SESS::get('MANAGER');
			
			if(is_array($manager) && !empty($manager["id"])){
				$rsnum = CRUD::dataFetch('manager',array(
					'id' => $manager["id"],
					'status' => '1',
					'ban' => '0',
				));

				if($rsnum == 1) return true;
			}

			return false;
		}

		# 留言列表顯示
		private static function row(){
			if(empty(self::$func) || empty(self::$dataID)) return false;

			$rsnum = CRUD::dataFetch('message',array('status' => '1','func' => self::$func,'dataID' => self::$dataID,'reply' => 'null'));
			if(!empty($rsnum)){
				VIEW::newBlock('TAG_MESSAGE_BLOCK');

				$dataRow = CRUD::$data;
				foreach($dataRow as $row){
					VIEW::newBlock('TAG_MESSAGE_LIST');
					foreach($row as $field => $var){
						switch($field){
							case "gender":
								$avatar = ($var)?'male.png':'female.png';
								$var = ($var)?'先生':'小姐';
								VIEW::assign('VALUE_AVATAR',CORE::$root.'images/'.$avatar);
							break;
						}
						VIEW::assign('VALUE_'.strtoupper($field),$var);
					}

					# 顯示回覆訊息
					$replyRsnum = CRUD::dataFetch('message',array('reply' => $row['id'],'status' => '1'));
					if(!empty($replyRsnum)){
						VIEW::assign('VALUE_MESSAGE_CLASS','response');

						list($reply) = CRUD::$data;
						VIEW::newBlock('TAG_MESSAGE_REPLY');
						foreach($reply as $field => $var){
							VIEW::assign('VALUE_'.strtoupper($field),$var);
						}
					}else{
						VIEW::assign('VALUE_MESSAGE_CLASS','message_main');

						# 顯示回覆欄位
						if(self::check()){
							VIEW::newBlock('TAG_MESSAGE_REPLY_BLOCK');
							VIEW::assign(array(
								"VALUE_REPLY" => $row['id'],
								"VALUE_PATH" => $_SERVER['REQUEST_URI'],
							));

							VIEW::gotoBlock('TAG_MESSAGE_LIST');
						}
					}
				}
			}

			VIEW::assignGlobal(array(
				'VALUE_DATAID' => self::$dataID,
				'VALUE_FUNC' => self::$func,
				'VALUE_PATH' => $_SERVER['REQUEST_URI'],
			));
		}

		# 增加留言
		private static function add(){
			if(!empty($_POST['dataID']) && !empty($_POST['func'])){
				CHECK::is_must($_POST['name'],$_POST['content']);
				CHECK::is_email($_POST['email']);

				if(CHECK::is_pass()){
					CRUD::dataInsert('message',$_POST);
					if(!empty(DB::$error)){
						$msg = 'Error !'.DB::$error;
					}else{
						$msg = CORE::$lang['submit_done'];
					}
				}else{
					$msg = CORE::$lang['no_args'];
				}
			}else{
				$msg = CORE::$lang['no_args'];
			}

			CORE::msg($msg,$_POST['path']);
		}

		# 回覆留言
		private static function reply(){
			CHECK::is_must($_POST['content'],$_POST['reply']);
			if(CHECK::is_pass()){
				CRUD::dataInsert('message',$_POST);
				if(!empty(DB::$error)){
					$msg = 'Error !'.DB::$error;
				}else{
					$msg = CORE::$lang['submit_done'];
				}
			}else{
				$msg = CORE::$lang['no_args'];
			}

			CORE::msg($msg,$_POST['path']);
		}
	}

?>