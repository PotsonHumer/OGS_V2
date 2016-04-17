<?php

	# 前台部落格功能

	class BLOG_FRONTEND extends BLOG{

		private static 
			$temp,
			$cate, #分類 id
			$id; # 資料 id

		function __construct(){

			list($args) = CORE::$args;
			self::$temp = CORE::$temp_main;
			
			CORE::common_resource();
			CORE::res_init('news/data_list','css');
			
			self::$temp["MAIN"] = 'ogs-news-tpl.html';

			if(!empty($cate)){
				self::$cate = SEO::origin('news_cate',$cate);
				self::$temp["MAIN"] = 'ogs-news-tpl.html';
				$func++;
			}
			
			if(!empty($args)){
				self::$id = SEO::origin('news',$args);
				self::$temp["MAIN"] = 'ogs-news-detail-tpl.html';
				$func++;
			}

			if($func < 2){
				self::row();
			}else{
				self::detail();
			}

			self::nav();

			new VIEW(CORE::$temp_option["HULL"],self::$temp,false,false);
		}


		# 顯示
		private static function row(){
			CORE::res_init('fix','css');

			if(!empty(self::$cate)){
				$rsnum = CRUD::dataFetch('news',array('parent' => self::$cate,'status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			}else{
				$rsnum = CRUD::dataFetch('news',array('status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			}

			if(!empty($rsnum)){
				#VIEW::newBlock("TAG_NEWS_BLOCK");
				$dataRow = CRUD::$data;

				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_NEWS_LIST");
					foreach($row as $field => $var){
						switch($field){
							case "showdate":
								$varSec = strtotime($var);
								$month = date('M',$varSec);
								$day = date('jS',$varSec);
								$year = date('Y',$varSec);

								$dateStr = $month.' '.$day.' , '.$year;

								VIEW::assign("VALUE_".strtoupper($field),$dateStr);
							break;
							case "content":
								$var = strip_tags($var);
							default:
								VIEW::assign("VALUE_".strtoupper($field),$var);
							break;
						}
					}
					
					IMAGES::load('news',$row["id"]);
					list($images) = IMAGES::$data;
					VIEW::assign(array(
						"VALUE_LINK" => self::dataLink($row["parent"],$row),
						"VALUE_IMAGE" => $images["path"],
						"VALUE_ALT" => $images["alt"],
						"VALUE_TITLE" => $images["title"],
					));
				}

				# SEO
				$cate_rsnum = CRUD::dataFetch('news_cate',array('id' => self::$cate));
				if(!empty($cate_rsnum)){
					list($cate_row) = CRUD::$data;
					SEO::load($cate_row["seo_id"]);
					if(empty(SEO::$data["h1"])) SEO::$data["h1"] = $cate_row["subject"];
				}else{
					SEO::load('news');
					if(empty(SEO::$data["h1"])) SEO::$data["h1"] = CORE::$lang["news"];
				}

				SEO::output();

				CRUMBS::fetch('news',$cate_row);
			}else{
				VIEW::newBlock("TAG_NONE");
			}
		}

		# 選單
		private static function nav(){
			VIEW::assignGlobal("NAV_CATE_TITLE",'NEWS');
			$rsnum = CRUD::dataFetch('news_cate',array('status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_NAV_LIST");
					VIEW::assign(array(
						"VALUE_NAV_SUBJECT" => $row["subject"],
						"VALUE_NAV_LINK" => CORE::$root.'news/'.SEO::link($row).'/',
						"VALUE_NAV_CURRENT" => (self::$id == $row["id"])?'current':'',
					));
				}
			}
		}

		# 顯示內容
		private static function detail(){
			$rsnum = CRUD::dataFetch('news',array('id' => self::$id));
			if(!empty($rsnum)){
				list($row) = CRUD::$data;
				foreach($row as $field => $var){
					VIEW::assignGlobal("VALUE_".strtoupper($field),$var);
				}

				VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$root.'news/');

				SEO::load($row["seo_id"]);
				if(empty(SEO::$data["h1"])) SEO::$data["h1"] = $row["subject"];
				SEO::output();

				CRUMBS::fetch('news',$row);
				self::hot(self::$id);
			}
		}

		# 熱門文章列表
		private static function hot($nowID){
			$rsnum = CRUD::dataFetch('news',array('status' => '1','hot' => '1','id' => '!'.$nowID),false,array('rand()' => ''));
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_HOT_BLOCK");
				$dataRow = CRUD::$data;
				foreach($dataRow as $row){
					VIEW::newBlock("TAG_HOT_LIST");
					foreach($row as $field => $var){
						switch($field){
							case "content":
								$maxLength = 15;
								$var = (mb_strlen($var,'UTF-8') > $maxLength)?mb_substr(strip_tags($var), 0, $maxLength, 'UTF-8').'...':$var;
							break;
						}

						VIEW::assign("VALUE_".strtoupper($field),$var);
					}

					IMAGES::load('news',$row["id"]);
					list($images) = IMAGES::$data;
					VIEW::assign(array(
						"VALUE_IMAGE" => $images["path"],
						"VALUE_ALT" => $images["alt"],
						"VALUE_TITLE" => $images["title"],
						'VALUE_LINK' => self::dataLink($row["parent"],$row),
					));
				}
			}
		}
	}

?>