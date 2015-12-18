<?php

	# 前台介紹頁功能

	class INTRO_FRONTEND extends INTRO{

		private static 
			$temp,
			$id; # 資料 id

		function __construct(){

			list($args) = CORE::$args;
			self::$temp = CORE::$temp_main;

			self::$id = SEO::origin('intro',$args);
			self::$temp["MAIN"] = 'ogs-intro-tpl.html';
			self::row();
			self::nav();

			new VIEW(CORE::$temp_option["HULL"],self::$temp,false,false);
		}


		# 介紹頁顯示
		private static function row(){
			if(!empty(self::$id)){
				$rsnum = CRUD::dataFetch('intro',array('id' => self::$id,'status' => '1','langtag' => CORE::$langtag));
			}else{
				$rsnum = CRUD::dataFetch('intro',array('status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]),'0,1');
			}

			if(!empty($rsnum)){
				list($row) = CRUD::$data;

				foreach($row as $field => $var){
					VIEW::assignGlobal("VALUE_".strtoupper($field),$var);
				}

				SEO::load($row["seo_id"]);
				if(empty(SEO::$data["h1"])) SEO::$data["h1"] = $row["subject"];
				SEO::output();

				CRUMBS::fetch('intro',$row);
			}
		}

		# 介紹頁選單
		private static function nav(){
			VIEW::assignGlobal("NAV_CATE_TITLE",'ABOUT');
			$rsnum = CRUD::dataFetch('intro',array('status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_NAV_LIST");
					VIEW::assign(array(
						"VALUE_NAV_SUBJECT" => $row["subject"],
						"VALUE_NAV_LINK" => CORE::$root.'intro/'.SEO::link($row).'/',
						"VALUE_NAV_CURRENT" => (self::$id == $row["id"])?'current':'',
					));
				}
			}
		}
	}

?>