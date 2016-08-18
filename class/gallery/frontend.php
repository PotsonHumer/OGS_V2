<?php

	# 前台相簿功能

	class GALLERY_FRONTEND extends GALLERY{

		private static 
			$temp,
			$cate, #分類 id
			$id; # 資料 id

		function __construct(){

			list($cate,$args) = CORE::$args;
			self::$temp = CORE::$temp_main;
			
			CORE::common_resource();
			
			self::$temp["MAIN"] = 'ogs-gallery-tpl.html';

			if(!empty($cate)){
				self::$cate = SEO::origin('gallery_cate',$cate);
				self::$temp["MAIN"] = 'ogs-gallery-tpl.html';
				$func++;
			}
			
			if(!empty($args)){
				self::$id = SEO::origin('gallery',$args);
				self::$temp["MAIN"] = 'ogs-gallery-detail-tpl.html';
				$func++;
			}

			if($func <= 1){
				self::row();
			}else{
				self::detail();
			}

			VIEW::assignGlobal(array(
				'TAG_FUNC_STR_ENG' => 'GALLERY',
				'TAG_GALLERY_ACTIVE' => 'class="li1"',
			));

			self::nav();

			new VIEW(CORE::$temp_option["HULL"],self::$temp,false,false);
		}


		# 顯示
		private static function row(){
			CORE::res_init('fix','css');

			if(!empty(self::$cate)){
				$rsnum = CRUD::dataFetch('gallery',array('parent' => self::$cate,'status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			}else{
				$rsnum = CRUD::dataFetch('gallery',array('status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			}

			if(!empty($rsnum)){
				#VIEW::newBlock("TAG_GALLERY_BLOCK");
				$dataRow = CRUD::$data;

				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_GALLERY_LIST");
					foreach($row as $field => $var){
						switch($field){
							case "dirpath":
								#list($var) = self::dirLoad($var,1);
							break;
							case "content":
								$strLength = 30;
								$var = (mb_strlen(strip_tags($var)) > $strLength)?mb_substr(strip_tags($var),0,$strLength,'UTF-8').'...':$var;
							break;
						}

						$output["VALUE_".strtoupper($field)] = $var;
					}

					IMAGES::load('gallery',$row['id']);
					list($image) = IMAGES::$data;

					$output['VALUE_IMAGE'] = $image['path'];
					$output['VALUE_LINK'] = self::dataLink($row["parent"],$row);

					VIEW::assign($output);
				}

				# SEO
				$cate_rsnum = CRUD::dataFetch('gallery_cate',array('id' => self::$cate));
				if(!empty($cate_rsnum)){
					list($cate_row) = CRUD::$data;
					SEO::load($cate_row["seo_id"]);
					if(empty(SEO::$data["h1"])) SEO::$data["h1"] = $cate_row["subject"];
				}else{
					SEO::load('gallery');
					if(empty(SEO::$data["h1"])) SEO::$data["h1"] = CORE::$lang["gallery"];
				}

				SEO::output();

				CRUMBS::fetch('gallery',$cate_row);
			}else{
				VIEW::newBlock("TAG_NONE");
			}
		}

		# 選單
		private static function nav(){
			VIEW::assignGlobal("NAV_CATE_TITLE",'GALLERY');
			$rsnum = CRUD::dataFetch('gallery_cate',array('status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_NAV_LIST");
					VIEW::assign(array(
						"VALUE_NAV_SUBJECT" => $row["subject"],
						"VALUE_NAV_LINK" => CORE::$root.'gallery/'.SEO::link($row).'/',
						"VALUE_NAV_CURRENT" => (self::$cate == $row["id"])?'current':'',
					));
				}
			}
		}

		# 顯示內容
		private static function detail(){
			$rsnum = CRUD::dataFetch('gallery',array('id' => self::$id));
			if(!empty($rsnum)){
				list($row) = CRUD::$data;
				foreach($row as $field => $var){
					switch($field){
						case "dirpath":
							#$images = self::dirLoad($var);
							#continue;
						break;
					}

					$output["VALUE_".strtoupper($field)] = $var;
				}

				IMAGES::load('gallery',$row['id']);
				if(is_array(IMAGES::$data)){
					foreach(IMAGES::$data as $images){
						VIEW::newBlock('TAG_GALLERY_LIST');
						foreach($images as $field => $var){
							$imgOutput['IMAGE_'.strtoupper($field)] = $var;
						}

						VIEW::assign($imgOutput);
					}
				}

				$output['VALUE_BACK_LINK'] = self::dataLink(self::$cate);
				VIEW::assignGlobal($output);

				SEO::load($row["seo_id"]);
				if(empty(SEO::$data["h1"])) SEO::$data["h1"] = $row["subject"];
				SEO::output();

				CRUMBS::fetch('gallery',$row);

				self::other($row['id']);

				MESSAGE::$func = 'gallery';
				MESSAGE::$dataID = $row['id'];
				new MESSAGE;
				VIEW::assignGlobal("TAG_MESSAGE_BLOCK",MESSAGE::$output);

				GALLERY::related($row['related']);
			}else{
				header('location: '.CORE::$root.'gallery/');
			}
		}

		# 其它相簿
		private static function other($id=false){
			$rsnum = CRUD::dataFetch('gallery',array('id' => '!'.$id,'status' => '1','langtag' => CORE::$langtag),array('id','subject','showdate','parent','seo_id'),array('sort' => CORE::$cfg['sort']));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;

				VIEW::newBlock("TAG_OTHER_BLOCK");
				foreach($dataRow as $row){
					VIEW::newBlock("TAG_OTHER_LIST");
					VIEW::assign(array(
						'VALUE_SUBJECT' => $row['subject'],
						'VALUE_SHOWDATE' => date('Y/m/d',strtotime($row['showdate'])),
						"VALUE_LINK" => self::dataLink($row["parent"],$row),
					));
				}
			}
		}
	}

?>