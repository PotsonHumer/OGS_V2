<?php

	# 後台反饋管理

	class FEEDBACK_BACKEND extends OGSADMIN{
		function __construct(){
			
			list($func,$id) = CORE::$args;
			$nav_class = 'FEEDBACK';

			switch($func){
				case "status":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::status($id);
				break;
				case "del":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::delete($id);
				break;
				case "multi":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					parent::multi('feedback',CORE::$manage.'feedback/');
				break;
				default:
					self::$temp["MAIN"] = 'ogs-admin-feedback-list-tpl.html';
					self::row();
				break;
			}

			self::nav_current($nav_class,$nav_func);
		}

		# 反饋列表
		private static function row(){
			$rsnum = CRUD::dataFetch('feedback',false,false,array('createdate' => 'desc'),false,true);
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_FEEDBACK_BLOCK");

				$data = CRUD::$data;
				foreach($data as $key => $row){
					VIEW::newBlock("TAG_FEEDBACK_LIST");
					foreach($row as $field => $var){
						switch($field){
							case "gender":
								$gender = ($var)?self::$lang['male']:self::$lang['female'];
								$var = $gender;
							break;
							case "status":
								$status = ($var)?self::$lang["status_on"]:self::$lang["status_off"];
								if(empty($var)) VIEW::assign("CLASS_STATUS_RED",'red');
								$var = $status;
							break;
						}

						VIEW::assign("VALUE_".strtoupper($field),$var);
					}

					VIEW::assign('VALUE_NUMBER',PAGE::$start + (++$i));
				}
			}else{
				VIEW::newBlock("TAG_NONE");
			}
		}

		# 刪除反饋
		private static function delete($id){
			$rs = CRUD::dataDel('feedback',array('id' => $id));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'feedback/';
			}

			if(!$rs){
				$msg = self::$lang["del_error"];
				$path = CORE::$manage.'feedback/';
			}else{
				$msg = self::$lang["del_done"];
				$path = CORE::$manage.'feedback/';
			}

			CORE::msg($msg,$path);
		}
	}

?>