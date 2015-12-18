<?php

	# 系統設定管理

	class SYSTEM_BACKEND extends OGSADMIN{
		function __construct(){
			
			list($func) = CORE::$args;

			switch($func){
				case "seo":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::seo();
				break;
				case "replace":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::replace();
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
					VIEW::assign("VALUE_NAME",self::$lang[$row["name"]]);

					VIEW::newBlock("TAG_SEO_TAB");
					foreach($row as $field => $var){
						switch($field){
							case "name":
								VIEW::assign("VALUE_".strtoupper($field),self::$lang[$var]);
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
				$args = array_merge($_POST,array('id' => '1'));
				CRUD::dataUpdate('system',$args);

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
						$msg = array(DB::$error,CORE::$manage.'system/');
						CORE::msg($msg);
						return false;
					}
				}

				$msg = array(self::$lang["modify_done"],CORE::$manage.'system/');
			}else{
				$msg = array(CHECK::$alert,CORE::$manage.'system/');
			}

			CORE::msg($msg);
		}
	}

?>