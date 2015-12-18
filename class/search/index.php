<?php

	# 全文檢索

	class SEARCH{

		private static 
			$kw,
			$rsnum,
			$temp;

		function __construct(){

			self::$temp = CORE::$temp_main;
			self::$kw = $_POST["kw"];

			self::$temp["NAV"] = '';
			self::$temp["MAIN"] = 'ogs-search-tpl.html';
			self::row();

			new AD();

			VIEW::assignGlobal(array(
				"NAV_CATE_TITLE" => CORE::$lang["search"],
				"SEO_H1" => CORE::$lang["search"],
				"VALUE_KW" => self::$kw,
				"VALUE_RSNUM" => array_sum(self::$rsnum),
			));

			CRUMBS::fetch('search');

			new VIEW(CORE::$temp_option["HULL"],self::$temp,false,false);
		}

		private static function row(){
			self::intro();
			self::products();
			self::news();
		}

		private static function intro(){
			self::$rsnum[] = $rsnum = CRUD::dataFetch('intro',array('langtag' => CORE::$langtag,'status' => '1','custom' => "(subject like '%".self::$kw."%' or content like '%".self::$kw."%')"));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_SEARCH_LIST");
					VIEW::assign(array(
						"VALUE_SUBJECT" => $row["subject"],
						"VALUE_CONTENT" => mb_substr(strip_tags($row["content"]), 0, 50, 'UTF-8'),
						"VALUE_LINK" => CORE::$root.'intro/'.SEO::link($row).'/'
					));
				}
			}
		}

		private static function products(){
			self::$rsnum[] = $rsnum = CRUD::dataFetch('products',array('langtag' => CORE::$langtag,'status' => '1','custom' => "(subject like '%".self::$kw."%' or info like '%".self::$kw."%' or content like '%".self::$kw."%')"));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					$content = (empty($row["content"]))?$row["info"]:$row["content"];

					VIEW::newBlock("TAG_SEARCH_LIST");
					VIEW::assign(array(
						"VALUE_SUBJECT" => $row["subject"],
						"VALUE_CONTENT" => mb_substr(strip_tags($content), 0, 50, 'UTF-8'),
						"VALUE_LINK" => PRODUCTS::dataLink($row["parent"],$row),
					));
				}
			}
		}

		private static function news(){
			self::$rsnum[] = $rsnum = CRUD::dataFetch('news',array('langtag' => CORE::$langtag,'status' => '1','custom' => "(subject like '%".self::$kw."%' or content like '%".self::$kw."%')"));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_SEARCH_LIST");
					VIEW::assign(array(
						"VALUE_SUBJECT" => $row["subject"],
						"VALUE_CONTENT" => mb_substr(strip_tags($row["content"]), 0, 50, 'UTF-8'),
						"VALUE_LINK" => NEWS::dataLink($row["parent"],$row),
					));
				}
			}
		}
	}

?>