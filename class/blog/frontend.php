<?php

	# 前台部落格功能

	class BLOG_FRONTEND extends BLOG{

		private static 
			$temp,
			$cate, #分類 id
			$id; # 資料 id

		function __construct(){

			list($cate,$args) = CORE::$args;
			self::$temp = CORE::$temp_main;
			
			CORE::common_resource();
			CORE::res_init('blog/data_list','css');
			
			self::$temp["MAIN"] = 'ogs-blog-tpl.html';

			if(!empty($cate)){
				self::$cate = SEO::origin('blog_cate',$cate);
				self::$temp["MAIN"] = 'ogs-blog-tpl.html';
				$func++;
			}
			
			if(!empty($args)){
				self::$id = SEO::origin('blog',$args);
				self::$temp["MAIN"] = 'ogs-blog-detail-tpl.html';
				$func++;
			}

			if($func <= 1){
				self::row();
			}else{
				self::detail();
			}

			self::nav();
			self::hot(self::$id);

			self::$temp['NAV'] = '';
			new VIEW(CORE::$temp_option["HULL"],self::$temp,false,false);
		}


		# 顯示
		private static function row(){
			CORE::res_init('fix','css');

			if(!empty(self::$cate)){
				$rsnum = CRUD::dataFetch('blog',array('parent' => self::$cate,'status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]),false,true);
			}else{
				$rsnum = CRUD::dataFetch('blog',array('status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]),false,true);
			}

			if(!empty($rsnum)){
				#VIEW::newBlock("TAG_BLOG_BLOCK");
				$dataRow = CRUD::$data;

				SCHEMA::make('blog_list',$dataRow);

				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_BLOG_LIST");
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
								$maxLength = 100;
								$var = (mb_strlen($var,'UTF-8') > $maxLength)?mb_substr(strip_tags($var), 0, $maxLength, 'UTF-8').'...':$var;
							default:
								VIEW::assign("VALUE_".strtoupper($field),$var);
							break;
						}
					}

					CRUD::dataFetch('blog_cate',array('id' => $row['parent']));
					list($cate) = CRUD::$data;
					
					IMAGES::load('blog',$row["id"]);
					list($images) = IMAGES::$data;
					VIEW::assign(array(
						"VALUE_LINK" => self::dataLink($row["parent"],$row),
						"VALUE_IMAGE" => $images["path"],
						"VALUE_ALT" => $images["alt"],
						"VALUE_TITLE" => $images["title"],
						"VALUE_PARENT" => $cate['subject'],
					));

					# 顯示評分星星數
					if(!empty($row['score'])){
						VIEW::newBlock('TAG_SCORE_BLOCK');
						VIEW::assign('VALUE_SCORE',$row['score']);
						$score = 0;

						while(++$score <= $row['score']){
							VIEW::newBlock('TAG_SCORE_STAR');
						}
					}

					# 顯示瀏覽數
					if(!empty($row['view_number']) || !empty($row['view_custom'])){
						VIEW::newBlock('TAG_VIEW_BLOCK');
						VIEW::assign('VALUE_VIEW_TOTAL',number_format($row['view_number'] + $row['view_custom']));
					}
				}

				# SEO
				$cate_rsnum = CRUD::dataFetch('blog_cate',array('id' => self::$cate));
				if(!empty($cate_rsnum)){
					list($cate_row) = CRUD::$data;
					SEO::load($cate_row["seo_id"]);
					if(empty(SEO::$data["h1"])) SEO::$data["h1"] = $cate_row["subject"];
				}else{
					SEO::load('blog');
					if(empty(SEO::$data["h1"])) SEO::$data["h1"] = CORE::$lang["blog"];
				}

				SEO::output();

				CRUMBS::fetch('blog',$cate_row);
			}else{
				VIEW::newBlock("TAG_NONE");
			}
		}

		# 選單
		private static function nav(){
			VIEW::assignGlobal("NAV_CATE_TITLE",'BLOG');
			$rsnum = CRUD::dataFetch('blog_cate',array('status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_NAV_LIST");
					VIEW::assign(array(
						"VALUE_NAV_SUBJECT" => $row["subject"],
						"VALUE_NAV_LINK" => CORE::$root.'blog/'.SEO::link($row).'/',
						"VALUE_NAV_CURRENT" => (self::$cate == $row["id"])?'active':'',
					));
				}
			}
		}

		# 顯示內容
		private static function detail(){
			$rsnum = CRUD::dataFetch('blog',array('id' => self::$id));
			if(!empty($rsnum)){
				list($row) = CRUD::$data;
				foreach($row as $field => $var){
					VIEW::assignGlobal("VALUE_".strtoupper($field),$var);
				}

				VIEW::assignGlobal("VALUE_BACK_LINK",CORE::$root.'blog/');

				SEO::load($row["seo_id"]);
				if(empty(SEO::$data["h1"])) SEO::$data["h1"] = $row["subject"];
				SEO::output();

				CRUMBS::fetch('blog',$row);

				MESSAGE::$dataID = $row['id'];
				MESSAGE::$func = 'blog';
				new MESSAGE();
				VIEW::assignGlobal('TAG_MESSAGE_BLOCK',MESSAGE::$output);

				SCHEMA::make('blog_detail',$row);
			}
		}

		# 熱門文章列表
		private static function hot($nowID){
			$rsnum = CRUD::dataFetch('blog',array('status' => '1','hot' => '1','id' => '!'.$nowID),false,array('rand()' => ''),'0,5');
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

					IMAGES::load('blog',$row["id"]);
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