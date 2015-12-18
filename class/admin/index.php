<?php

	# 後台功能

	class OGSADMIN{

		public static 
			$lang, # 語言包
			$temp, # 樣板
			$temp_option, # 選項樣板
			$func, # 功能名稱
			$langID, # 使用的 lang_id
			$class; # 已宣告建構子

		function __construct(){
			
			CORE::$bgend = true;
			CORE::summon(__FILE__);
			
			self::$func = $func = array_shift(CORE::$args);
			self::$temp = CORE::$cfg["temp"]['admin'];
			self::$temp_option = CORE::$cfg["temp"]['admin_option'];
			self::$lang = include CORE::$path.'lang/lang-cht.php';

			CORE::res_init('ogs_admin','font','fix','css');
			CORE::res_init('default','js');

			# 預定使用功能
			$function = array(
				'system' => false,
				'manager' => true,
				'ad' => true,
				'intro' => true,
				'faq' => true,
				'news' => true,
				'products' => true,
				'order' => false,
				'member' => true,
				'contact' => true,
				'sk' => false,
			);

			MANAGER::ban_check();
			MANAGER::class_handle($function);

			if(isset($function[$func])){
				$func_name = strtoupper($func);
				new $func_name($function[$func]);
			}else{
				self::$temp["MAIN"] = self::$temp_option["INDEX"];
			}

			MANAGER::check(); # 登入檢查
			MANAGER::level_check($func); # 權限檢查

			self::language_select();
			
			if(!CHECK::is_ajax()){
				new VIEW(self::$temp_option["HULL"],self::$temp,false,1);
			}
		}

		# 目前左邊選單標籤
		public static function nav_current($class,$func){
			$func_tag = (!empty($func))?'_'.strtoupper($func):'';
			VIEW::assignGlobal('CURRENT_NAV_'.$class.$func_tag,'class="current"');
		}

		# 後台語系選單
		public static function language_select(){
			if(!empty(self::$langID) && CRUD::dataFetch('lang',array('id' => self::$langID))){
				list($row) = CRUD::$data;
				$related = json_decode($row["related"],true);
			}

			unset($option,$option_array);
			$lang_array = array_keys(CORE::$cfg["lang"]);
			foreach($lang_array as $lang){
				$path = ($lang == $lang_array[0])?CORE::$cfg["root"]:CORE::$cfg["root"].$lang.'/';
				$selected = (CORE::$cfg["router"] == $lang)?'selected':'';

				$args = CORE::$args;

				if(!empty($related[$lang])){
					array_pop($args);
					$lang_path = self::$func.'/'.implode("/",$args).'/'.$related[$lang].'/';
				}else{
					$args = (is_array($args) && count($args))?implode("/",$args).'/':'';
					$lang_path = self::$func.'/'.$args;
				}

				$option_array[] = '<option value="'.$path.'ogsadmin/'.$lang_path.'" '.$selected.'>'.self::$lang[$lang].'</option>';
			}

			$option = implode("",$option_array);
			VIEW::assignGlobal("VALUE_LANGUAGE_OPTION",$option);
		}
		
		# 多重控制
		public static function multi($tb_name=false,$path=false){
			list($func,$action,$args) = CORE::$args;
			CHECK::is_array_exist($_POST["id"]);
			CHECK::is_must($action);

			if(CHECK::is_pass() && $tb_name !== false){

				# 依照排序要求更改陣列排序, 以符合自動排序邏輯
				if($action == "sort"){
					asort($_POST["sort"]);
					foreach($_POST["sort"] as $id => $sort){
						if(isset($_POST["id"][$id]) && !empty($_POST["id"][$id])) $new_args[] = $_POST["id"][$id];
					}
				}else{
					$new_args = $_POST["id"];
				}

				foreach($new_args as $key => $id){
					switch($action){
						case "sort":
							CRUD::dataUpdate($tb_name,array('id' => $id,'sort' => $_POST["sort"][$id]));
							if(!empty(DB::$error)){
								$msg = DB::$error;
							}
						break;
						case "status":
							CRUD::dataUpdate($tb_name,array('id' => $id,'status' => $args));
							if(!empty(DB::$error)){
								$msg = DB::$error;
							}
						break;
						case "clone":
							/*
							$rsnum = CRUD::dataFetch($tb_name,array('id' => $id));
							if(!empty($rsnum)){
								list($row) = CRUD::$data;
								unset($row["id"]);

								CRUD::dataInsert($tb_name,$row);
								if(!empty(DB::$error)){
									$msg = DB::$error;
								}
							}else{
								$msg = self::$lang["no_args"];
							}
							*/
						break;
						case "del":
							$rs = CRUD::dataDel($tb_name,array('id' => $id));
							if(!empty(DB::$error)){
								$msg = DB::$error;
							}

							if(!$rs){
								$msg = self::$lang["del_error"];
							}
						break;
					}

					if(!empty($msg)){
						break;
					}
				}
			}else{
				$msg = self::$lang["no_args"];
			}

			if(empty($msg)){
				$msg = self::$lang["modify_done"];
			}else{
				$path = self::$temp_option["MSG"];
			}

			CORE::msg($msg,$path);
		}

		# 取得排序
		public static function getSort($tb_name){
			$field = DB::field(CORE::$prefix.'_'.$tb_name);
			while($row = DB::fetch($field)){
				if($row['Field'] == "langtag") $langtag = true;
			}

			if($langtag){
				$rsnum = CRUD::dataFetch($tb_name,array('parent' => $_POST["call"],'langtag' => CORE::$langtag));
			}else{
				$rsnum = CRUD::dataFetch($tb_name,array('parent' => $_POST["call"]));
			}
			echo ++$rsnum;
		}
		
	}

?>