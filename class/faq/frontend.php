<?php

	# 前台問與答功能

	class FAQ_FRONTEND extends FAQ{

		private static 
			$temp,
			$id; # 資料 id

		function __construct(){

			list($args) = CORE::$args;
			self::$temp = CORE::$temp_main;

			if(!empty($args)){
				self::$id = SEO::origin('faq',$args);
			}

			self::$temp["MAIN"] = 'ogs-faq-tpl.html';
			self::row();
			self::nav();

			new VIEW(CORE::$temp_option["HULL"],self::$temp,false,false);
		}


		# 顯示
		private static function row(){
			if(!empty(self::$id)){
				$rsnum = CRUD::dataFetch('faq',array('parent' => self::$id,'status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			}else{
				$rsnum = CRUD::dataFetch('faq',array('status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			}

			if(!empty($rsnum)){
				VIEW::newBlock("TAG_FAQ_BLOCK");
				$dataRow = CRUD::$data;

				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_FAQ_LIST");
					foreach($row as $field => $var){
						VIEW::assignGlobal("VALUE_".strtoupper($field),$var);
					}
				}
			}else{
				VIEW::newBlock("TAG_NONE");
			}

			# SEO
			$parent_rsnum = CRUD::dataFetch('faq_cate',array('id' => self::$id));
			if(!empty($parent_rsnum)){
				list($parent_row) = CRUD::$data;
				SEO::load($parent_row["seo_id"]);
				if(empty(SEO::$data["h1"])) SEO::$data["h1"] = $parent_row["subject"];
			}else{
				SEO::load('faq');
				if(empty(SEO::$data["h1"])) SEO::$data["h1"] = CORE::$lang["faq"];
			}

			SEO::output();
			CRUMBS::fetch('faq',$parent_row);
		}

		# 選單
		private static function nav(){
			VIEW::assignGlobal("NAV_CATE_TITLE",'SHOPPING');
			$rsnum = CRUD::dataFetch('faq_cate',array('status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_NAV_LIST");
					VIEW::assign(array(
						"VALUE_NAV_SUBJECT" => $row["subject"],
						"VALUE_NAV_LINK" => CORE::$root.'faq/'.SEO::link($row).'/',
						"VALUE_NAV_CURRENT" => (self::$id == $row["id"])?'current':'',
					));
				}
			}
		}
	}

?>