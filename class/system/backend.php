<?php

	# 系統設定管理

	class SYSTEM_BACKEND extends OGSADMIN{
		function __construct(){
			
			list($func) = CORE::$args;

			switch($func){
				case "seo":
					self::$temp["MAIN"] = 'ogs-admin-system-seo-tpl.html';
					self::row();
				break;
				case "seo-replace":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::seo();
				break;
				case "replace":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::replace();
				break;
				case "custom":
					CORE::res_init('get','box');
					self::$temp["MAIN"] = 'ogs-admin-system-custom-tpl.html';
					self::custom();
				break;
				case "custom-replace":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::customReplace();
				break;
				case "custom-del":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::customDel();
				break;				
				default:
					self::$temp["MAIN"] = 'ogs-admin-system-tpl.html';
					self::row();
					$func = false;
				break;
			}

			self::nav_current('SYSTEM');
		}

		# 系統設定顯示
		private static function row(){
			CORE::res_init('tab','box');
			$rsnum = CRUD::dataFetch('system',array('id' => '1'));
			if(!empty($rsnum)){
				$row = CRUD::$data[0];
				foreach($row as $field => $var){
					VIEW::assignGlobal("VALUE_".strtoupper($field),$var);
				}
			}

			# SEO
			$rsnum = CRUD::dataFetch('seo',array('custom' => "name != ''",'langtag' => CORE::$langtag));
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_SEO_BLOCK");
				foreach(CRUD::$data as $key => $row){
					VIEW::newBlock("TAG_SEO_TITLE");

					switch($row["name"]){
						case "index":
							VIEW::assign("VALUE_NAME",self::$lang['home']);
						break;
						default:
							VIEW::assign("VALUE_NAME",self::$lang[$row["name"]]);
						break;
					}

					VIEW::newBlock("TAG_SEO_TAB");
					foreach($row as $field => $var){
						switch($field){
							case "name":
								$name = ($var == 'index')?self::$lang['home']:self::$lang[$var];
								VIEW::assign("VALUE_".strtoupper($field),$name);
							break;
							default:
								VIEW::assign("VALUE_".strtoupper($field),$var);
							break;
						}
					}
				}
			}
		}

		# 系統設定更新
		private static function replace(){
			CHECK::is_email($_POST["email"]);
			CHECK::is_must($_POST["callback"]);

			if(CHECK::is_pass()){
				$systemExist = CRUD::dataFetch('system',array('id'=> '1'));

				$args = array_merge($_POST,array('id' => '1'));

				if($systemExist){
					CRUD::dataUpdate('system',$args);
				}else{
					CRUD::dataInsert('system',$args);
				}

				if(!empty(DB::$error)){
					$msg = array(DB::$error,CORE::$manage.'system/');
				}else{
					$msg = array(self::$lang["modify_done"],CORE::$manage.'system/');
				}
			}else{
				$msg = array(CHECK::$alert,CORE::$manage.'system/');
			}

			CORE::msg($msg);
		}

		# 各功能主頁 SEO 更新
		private static function seo(){
			CHECK::is_array_exist($_POST["id"]);
			CHECK::is_must($_POST["callback"]);

			if(CHECK::is_pass()){
				$field_rs = DB::field(CORE::$prefix.'_seo');
				while($field_row = DB::fetch($field_rs)){
					if($field_row["Field"] != "langtag" && $field_row["Field"] != "name"){
						$field_array[] = $field_row["Field"];
					}
				}

				foreach($_POST["id"] as $key => $id){
					foreach($field_array as $field){
						$args[$field] = $_POST[$field][$key];
					}

					CRUD::dataUpdate('seo',$args);
					if(!empty(DB::$error)){
						$msg = array(DB::$error,CORE::$manage.'system/seo/');
						CORE::msg($msg);
						return false;
					}
				}

				$msg = array(self::$lang["modify_done"],CORE::$manage.'system/seo/');
			}else{
				$msg = array(CHECK::$alert,CORE::$manage.'system/seo/');
			}

			CORE::msg($msg);
		}

		# 分店資訊
		private static function custom(){
			$rsnum = CRUD::dataFetch('system_custom',array('langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg['sort']));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $row){
					VIEW::newBlock('TAG_CUSTOM_LIST');
					foreach($row as $field => $var){
						switch($field){
							case "status":
								$field = $field.'_ck'.$var;
								$var = 'selected';
							break;
						}

						VIEW::assign('VALUE_'.strtoupper($field),$var);
					}
				}
			}else{
				VIEW::newBlock('TAG_CUSTOM_LIST');
			}
		}

		# 分店資訊更新
		private static function customReplace(){
			CHECK::is_array_exist($_POST['id']);
			if(CHECK::is_pass()){
				foreach($_POST['id'] as $key => $ID){
					unset($args);

					$fields = array('name','tel','address','time','status','sort','id');
					foreach($fields as $field){
						switch($field){
							case "sort":
								$var = $key + 1;
							break;
							case "id":
								if(empty($ID)) continue;
							default:
								$var = $_POST[$field][$key];;
							break;
						}

						$args[$field] = $var;
					}

					if(empty($ID)){
						CRUD::dataInsert('system_custom',$args,true);
					}else{
						CRUD::dataUpdate('system_custom',$args);
					}

					if(!empty(DB::$error)){
						$msg = DB::$error;
					}
				}
			}else{
				$msg = self::$lang['no_args'];
			}

			if(empty($msg)) $msg = self::$lang['modify_done'];

			CORE::msg($msg,CORE::$manage.'system/custom/');
		}

		# 刪除分店資訊
		private static function customDel(){
			if(empty($_POST['call'])){
				echo self::$lang['no_args'];
				return;
			}

			CRUD::dataDel('system_custom',array('id' => $_POST['call']));
			if(!empty(DB::$error)){
				$msg = DB::$error;
			}else{
				$msg = self::$lang['del_done'];
			}

			echo $msg;
		}		
	}

?>