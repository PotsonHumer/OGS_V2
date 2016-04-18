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
					self::add();
				break;
				case "reply":
					self::reply();
				break;
				default:
					self::row();
				break;
			}

			new VIEW('ogs-message-tpl.html',false,true);
			self::$output = VIEW::$output;
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
				$dataRow = CRUD::$data;
				foreach($dataRow as $row){
					VIEW::newBlock('TAG_MESSAGE_LIST');
					foreach($row as $field => $var){
						VIEW::assign('VALUE_'.strtoupper($field),$var);
					}

					$replyRsnum = CRUD::dataFetch('message',array('replay' => $row['reply'],'status' => '1'));
					if(!empty($replyRsnum)){
						list($reply) = CRUD::$data;
						VIEW::newBlock('TAG_MESSAGE_REPLY');
						foreach($row as $field => $var){
							VIEW::assign('VALUE_'.strtoupper($field),$var);
						}
					}
				}
			}
		}

		# 增加留言
		private static function add(){
			
		}

		# 回覆留言
		private static function reply(){

		}
	}

?>