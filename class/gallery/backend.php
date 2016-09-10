<?php

	# 相簿管理

	class GALLERY_BACKEND extends OGSADMIN{
		function __construct(){
			
			list($func,$id) = CORE::$args;
			$nav_class = 'GALLERY';

			switch($func){
				case "cate":
					$nav_func = "CATE";
					self::$temp["MAIN"] = 'ogs-admin-gallery-cate-list-tpl.html';
					self::cate();
				break;
				case "cate-add":
					$nav_func = "CATE";
					self::$temp["MAIN"] = 'ogs-admin-gallery-cate-insert-tpl.html';
					self::$temp["SEO"] = self::$temp_option["SEO"];
					CORE::res_init('tab','box');
					self::cate_add();
				break;
				case "cate-insert":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::cate_insert();
				break;
				case "cate-detail":
					$nav_func = "CATE";
					self::$temp["MAIN"] = 'ogs-admin-gallery-cate-modify-tpl.html';
					self::$temp["SEO"] = self::$temp_option["SEO"];
					CORE::res_init('tab','box');
					self::cate_detail($id);
				break;
				case "cate-modify":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::cate_modify();
				break;
				case "cate-del":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::cate_delete($id);
				break;
				case "cate-multi":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					parent::multi('gallery_cate',CORE::$manage.'gallery/cate/');
				break;
				case "add":
					self::$temp["MAIN"] = 'ogs-admin-gallery-insert-tpl.html';
					self::$temp["SEO"] = self::$temp_option["SEO"];
					self::$temp["IMAGE"] = self::$temp_option["IMAGE"];
					CORE::res_init('tab','get','box');
					self::add();
				break;
				case "insert":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::insert();
				break;
				case "detail":
					self::$temp["MAIN"] = 'ogs-admin-gallery-modify-tpl.html';
					self::$temp["SEO"] = self::$temp_option["SEO"];
					self::$temp["IMAGE"] = self::$temp_option["IMAGE"];
					CORE::res_init('tab','get','box');
					self::detail($id);
				break;
				case "modify":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::modify();
				break;
				case "del":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::delete($id);
				break;
				case "multi":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					parent::multi('gallery',CORE::$manage.'gallery/');
				break;
				case "images":
					self::images();
				break;
				case "imagesDel":
					self::imagesDel();
				break;
				case "seek":
					self::seek($id);
				break;
				default:
					self::$temp["MAIN"] = 'ogs-admin-gallery-list-tpl.html';
					self::row($func);
				break;
			}

			self::nav_current($nav_class,$nav_func);
		}

		# 分類列表
		private static function cate(){
			$rsnum = CRUD::dataFetch('gallery_cate',array('langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]),false,true);
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_GALLERY_CATE_BLOCK");

				$data = CRUD::$data;
				foreach($data as $key => $row){
					VIEW::newBlock("TAG_GALLERY_CATE_LIST");
					foreach($row as $field => $var){
						switch($field){
							case "status":
								$status = ($var)?self::$lang["status_on"]:self::$lang["status_off"];
								if(empty($var)) VIEW::assign("CLASS_STATUS_RED",'red');
								VIEW::assign("VALUE_".strtoupper($field),$status);
							break;
							default:
								VIEW::assign("VALUE_".strtoupper($field),$var);
							break;
						}
					}

					VIEW::assign('VALUE_NUMBER',PAGE::$start + (++$i));
				}
			}else{
				VIEW::newBlock("TAG_NONE");
			}
		}

		# 分類新增
		private static function cate_add(){
			$rsnum = CRUD::dataFetch('gallery_cate',array("langtag" => CORE::$langtag));
			CRUD::args_output(true,true);
			VIEW::assignGlobal("VALUE_SORT",++$rsnum);
		}

		# 執行分類新增
		private static function cate_insert(){
			CHECK::is_must($_POST["callback"],$_POST["subject"]);

			if(CHECK::is_pass()){
				CRUD::dataInsert('gallery_cate',$_POST,true,true);
				if(!empty(DB::$error)){
					CRUD::args_output();
					$msg = DB::$error;
					$path = CORE::$manage.'gallery/cate-add/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage.'gallery/cate/';
				}
			}else{
				CRUD::args_output();
				$msg = CHECK::$alert;
				$path = CORE::$manage.'gallery/cate-add/';
			}

			CORE::msg($msg,$path);
		}

		# 詳細
		private static function cate_detail($id){
			$rsnum = CRUD::dataFetch('gallery_cate',array('id' => $id));
			if(!empty($rsnum)){
				list($row) = CRUD::$data;
				foreach($row as $field => $var){
					switch($field){
						case "status":
							VIEW::assignGlobal("VALUE_".strtoupper($field)."_CK".$var,'selected');
						break;
						default:
							VIEW::assignGlobal("VALUE_".strtoupper($field),$var);
						break;
					}
				}

				$last_page = SESS::get("PAGE");
				if(!empty($last_page)){
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."gallery/cate/page-{$last_page}/");
				}else{
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."gallery/cate/");
				}

				SEO::load($row["seo_id"]);
				SEO::output();

				parent::$langID = $row['lang_id'];
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["no_args"],CORE::$manage.'gallery/cate/');
			}
		}

		# 修改
		private static function cate_modify(){
			CHECK::is_must($_POST["callback"],$_POST["id"],$_POST["subject"]);
			$check = CHECK::is_pass();
			$rsnum = CRUD::dataFetch('gallery_cate',array('id' => $_POST["id"]));

			if($check && !empty($rsnum)){
				CRUD::dataUpdate('gallery_cate',$_POST,true);
				if(!empty(DB::$error)){
					$msg = DB::$error;
					$path = CORE::$manage.'gallery/cate/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage."gallery/cate-detail/{$_POST['id']}/";
				}
			}else{
				if(empty($rsnum)){
					$msg = self::$lang["no_data"];
					$path = CORE::$manage.'gallery/cate/';
				}

				if(!$check){
					$msg = CHECK::$alert;
					$path = CORE::$manage.'gallery/cate/';
				}
			}

			CORE::msg($msg,$path);
		}

		# 刪除
		private static function cate_delete($id){
			$rs = CRUD::dataDel('gallery_cate',array('id' => $id));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'gallery/cate/';
			}

			if(!$rs){
				$msg = self::$lang["del_error"];
				$path = CORE::$manage.'gallery/cate/';
			}else{
				$msg = self::$lang["del_done"];
				$path = CORE::$manage.'gallery/cate/';
			}

			CORE::msg($msg,$path);
		}

		# 分類
		################################################################################################################################
		# 項目

		# 分類選單
		private static function cate_select($parent=null){
			$rsnum = CRUD::dataFetch('gallery_cate',array("langtag" => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					$selected = (!is_null($parent) && $parent == $row["id"])?'selected':'';
					$option_array[] = '<option value="'.$row["id"].'" '.$selected.'>'.$row["subject"].'</option>';
				}

				if(is_array($option_array)){
					return implode("",$option_array);
				}
			}

			return false;
		}

		# 分類選單列表
		private static function cate_list($id=false){
			$rsnum = CRUD::dataFetch('gallery_cate',array('langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			if($rsnum > 1){
				$parent = CRUD::$data;
				VIEW::newBlock("TAG_PARENT_BLOCK");
				foreach($parent as $key => $row){
					VIEW::newBlock("TAG_PARENT_LIST");
					foreach($row as $field => $var){
						VIEW::assign('VALUE_'.strtoupper($field),$var);
					}

					if($id == $row["id"]){
						$current = true;
						VIEW::assign("VALUE_CURRENT",'theme');
					}
				}
			}

			if(!$current) VIEW::assignGlobal("NONE_CURRENT",'theme');
		}

		# 列表
		private static function row($parent=false){
			if(!empty($parent)){
				$sk = array('langtag' => CORE::$langtag,'parent' => $parent);
			}else{
				$sk = array('langtag' => CORE::$langtag);
			}

			self::cate_list($parent); # 分類選單

			$rsnum = CRUD::dataFetch('gallery',$sk,false,array('sort' => CORE::$cfg["sort"]),false,true);
			if(!empty($rsnum)){
				$data = CRUD::$data;
				VIEW::newBlock("TAG_GALLERY_BLOCK");	

				foreach($data as $key => $row){
					VIEW::newBlock("TAG_GALLERY_LIST");
					foreach($row as $field => $var){
						switch($field){
							case "parent":
								CRUD::dataFetch('gallery_cate',array('id' => $var),array('subject'));
								list($parent) = CRUD::$data;
								VIEW::assign("VALUE_".strtoupper($field),$parent["subject"]);
							break;
							case "status":
								$status = ($var)?self::$lang["status_on"]:self::$lang["status_off"];
								if(empty($var)) VIEW::assign("CLASS_STATUS_RED",'red');
								VIEW::assign("VALUE_".strtoupper($field),$status);
							break;
							default:
								VIEW::assign("VALUE_".strtoupper($field),$var);
							break;
						}
					}

					VIEW::assign('VALUE_NUMBER',PAGE::$start + (++$i));
				}
			}else{
				VIEW::newBlock("TAG_NONE");
			}
		}

		# 新增
		private static function add(){
			$rsnum = CRUD::dataFetch('gallery',array("langtag" => CORE::$langtag));
			CRUD::args_output(true,true);
			VIEW::assignGlobal(array(
				"VALUE_SORT" => '1', #++$rsnum,
				"VALUE_SHOWDATE" => date("Y-m-d"),
				"VALUE_PARENT_OPTION" => self::cate_select(),
			));
		}

		# 執行新增
		private static function insert(){
			CHECK::is_must($_POST["callback"],$_POST["subject"],$_POST["dirpath"],$_POST["parent"]);

			if(CHECK::is_pass()){

				if(is_array($_POST['related'])){
					$_POST['related'] = json_encode($_POST['related']);
				}else{
					$_POST['related'] = '';
				}

				CRUD::dataInsert('gallery',$_POST,true,true,true);
				if(!empty(DB::$error)){
					CRUD::args_output();
					$msg = DB::$error;
					$path = CORE::$manage.'gallery/add/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage.'gallery/';
				}
			}else{
				CRUD::args_output();
				$msg = CHECK::$alert;
				$path = CORE::$manage.'gallery/add/';
			}

			CORE::msg($msg,$path);
		}

		# 詳細
		private static function detail($id){
			$rsnum = CRUD::dataFetch('gallery',array('id' => $id));
			if(!empty($rsnum)){
				list($row) = CRUD::$data;
				foreach($row as $field => $var){
					switch($field){
						case "related":
							GALLERY::related($var);
						break;
						case "parent":
							$field = $field.'_OPTION';
							$var = self::cate_select($var);
						break;
						case "status":
							$field = $field.'_ck'.$var;
							$var = 'selected';
						break;
					}

					$output["VALUE_".strtoupper($field)] = $var;
				}

				VIEW::assignGlobal($output);

				IMAGES::output('gallery',$row["id"]);
				foreach(IMAGES::$data as $images){
					VIEW::newBlock('TAG_IMAGES_LIST');
					foreach($images as $field => $var){
						$images['IMAGES_'.strtoupper($field)] = $var;
					}

					VIEW::assign($images);
				}

				SEO::load($row["seo_id"]);
				SEO::output();

				$last_page = SESS::get("PAGE");
				if(!empty($last_page)){
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."gallery/page-{$last_page}/");
				}else{
					VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$manage."gallery/");
				}

				parent::$langID = $row['lang_id'];
			}else{
				self::$temp["MAIN"] = self::$temp_option["MSG"];
				CORE::msg(self::$lang["no_args"],CORE::$manage.'gallery/');
			}
		}

		# 修改
		private static function modify(){
			CHECK::is_must($_POST["callback"],$_POST["id"],$_POST["subject"],$_POST["dirpath"],$_POST["parent"]);
			$check = CHECK::is_pass();
			$rsnum = CRUD::dataFetch('gallery',array('id' => $_POST["id"]));

			if($check && !empty($rsnum)){

				if(is_array($_POST['related'])){
					$_POST['related'] = json_encode($_POST['related']);
				}else{
					$_POST['related'] = '';
				}

				CRUD::dataUpdate('gallery',$_POST,true,true);
				if(!empty(DB::$error)){
					$msg = DB::$error;
					$path = CORE::$manage.'gallery/';
				}else{
					$msg = self::$lang["modify_done"];
					$path = CORE::$manage."gallery/detail/{$_POST['id']}/";
				}
			}else{
				if(empty($rsnum)){
					$msg = self::$lang["no_data"];
					$path = CORE::$manage.'gallery/';
				}

				if(!$check){
					$msg = CHECK::$alert;
					$path = CORE::$manage.'gallery/';
				}
			}

			CORE::msg($msg,$path);
		}

		# 刪除
		private static function delete($id){
			$rs = CRUD::dataDel('gallery',array('id' => $id));
			if(!empty(DB::$error)){
				$msg = DB::$error;
				$path = CORE::$manage.'gallery/';
			}

			if(!$rs){
				$msg = self::$lang["del_error"];
				$path = CORE::$manage.'gallery/';
			}else{
				$msg = self::$lang["del_done"];
				$path = CORE::$manage.'gallery/';
			}

			CORE::msg($msg,$path);
		}

		# 取得圖片資料與列表
		private static function images(){
			$rs = 'NONE';

			if(!empty($_POST['call'])){
				$args = GALLERY::dirLoad($_POST['call']);
				CHECK::is_array_exist($args);
				if(CHECK::is_pass()){
					foreach($args as $filePath){
						VIEW::newBlock('TAG_GALLERY_IMAGE');
						VIEW::assign('IMAGES_PATH',CORE::$cfg['dns'].$filePath);
					}

					new VIEW('ogs-admin-gallery-image-tpl.html',array('IMAGE' => self::$temp_option["IMAGE"]),true,1);
					$rs = VIEW::$output;
				}
			}

			echo $rs;
		}

		# 刪除已經建立的圖片
		private static function imagesDel(){
			if(empty($_POST['call'])) return false;
			IMAGES::del('gallery',$_POST['call']);
		}

		# 搜尋關聯相簿
		private static function seek($id=false){
			if(empty($_POST['call'])) echo 'NONE';
			$seekStr = $_POST['call'];

			if(!empty($id)){
				$rsnum = CRUD::dataFetch('gallery',array('id' => $id),array('related'));
				if(!empty($rsnum)){
					list($nowRow) = CRUD::$data;
					if(!empty($nowRow['related'])){
						$relatedArray = json_decode($nowRow['related'],true);
						$seekFilter = "id NOT IN('".implode("','",$relatedArray)."','".$id."')";

						$sk = array('status' => '1','langtag' => CORE::$langtag,'subject' => '%'.$seekStr.'%','custom' => $seekFilter);
					}else{
						$sk = array('status' => '1','langtag' => CORE::$langtag,'subject' => '%'.$seekStr.'%','id' => "!{$id}");
					}
				}
			}

			if(empty($sk) || !is_array($sk)) $sk = array('status' => '1','langtag' => CORE::$langtag,'subject' => '%'.$seekStr.'%');

			$rsnum = CRUD::dataFetch('gallery',$sk);
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					foreach($row as $field => $var){
						$output[$key][$field] = rawurlencode($var);
					}

					IMAGES::load('gallery',$row["id"]);
					list($image) = IMAGES::$data;
					$output[$key]['image'] = '<img src="'.$image['path'].'" style="width: 100px;">';
					$output[$key]['link'] = GALLERY::dataLink($row['parent'],$row);
				}

				if(is_array($output)){
					echo json_encode($output);
				}
			}else{
				echo 'NONE';
			}
		}
	}

?>