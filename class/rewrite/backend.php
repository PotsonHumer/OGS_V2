<?php

	# 轉址設定管理

	class REWRITE_BACKEND extends OGSADMIN{
		function __construct(){
			
			list($func) = CORE::$args;

			switch($func){
				case "replace":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::replace();
				break;
				case "del":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::del();
				break;
				default:
					CORE::res_init('get','box');
					self::$temp["MAIN"] = 'ogs-admin-rewrite-tpl.html';
					self::row();
				break;
			}

			self::nav_current('REWRITE');
		}

		# 轉址設定顯示
		private static function row(){
			$rsnum = CRUD::dataFetch('rewrite',false,false,array('id' => 'desc'));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $row){
					VIEW::newBlock('TAG_REWRITE_LIST');
					foreach($row as $field => $var){
						switch($field){
							case "status":
								$field = 'VALUE_'.strtoupper($field).'_CK'.$var;
								$var = 'selected';
							break;
						}

						VIEW::assign("VALUE_".strtoupper($field),$var);
					}
				}
			}else{
				VIEW::newBlock('TAG_REWRITE_LIST');
			}
		}

		# 轉址設定更新
		private static function replace(){
			CHECK::is_array_exist($_POST["id"]);

			if(CHECK::is_pass()){
				$fieldArray = array('origin','target','status','id');
				foreach($_POST["id"] as $key => $ID){
					unset($args);

					foreach($fieldArray as $field){
						switch($field){
							case "origin":
								$var = preg_replace('/(http:\/\/|https:\/\/)([^\/]*)(.)/si','$1'.CORE::$cfg['url'].'$3',$_POST[$field][$key]);
							break;
							default:
								$var = $_POST[$field][$key];
							break;
						}

						$args[$field] = $var;
					}

					if(empty($ID)){
						unset($args['id']);
						CRUD::dataInsert('rewrite',$args);
					}else{
						CRUD::dataUpdate('rewrite',$args);
					}

					if(!empty(DB::$error)){
						$msg = DB::$error;
					}else{
						$msg = self::$lang["modify_done"];
					}
				}
			}else{
				$msg = CHECK::$alert;
			}

			CORE::msg($msg,CORE::$manage.'rewrite/');
		}

		# 轉址刪除
		private static function del(){
			$path = CORE::$manage.'rewrite/';
			if(empty($_POST['call'])){
				echo self::$lang['no_args'];
				return false;
			}

			CRUD::dataDel('rewrite',array('id' => $_POST['call']));
			if(!empty(DB::$error)){
				$msg = DB::$error;
			}else{
				$msg = self::$lang['del_done'];
			}

			echo $msg;
		}
	}

?>