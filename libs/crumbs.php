<?php
	
	# 麵包屑功能

	class CRUMBS{

		private static $data;

		function __construct(){
			self::make(CORE::$lang["index"],CORE::$root);
		}

		# 輸入組合資訊
		public static function make($subject,$link){
			self::$data[] = array('subject' => $subject,'link' => $link);
		}

		# 清除訊息
		public static function clear(){
			self::$data = array();
		}

		# 輸出
		public static function output(){
			CHECK::is_array_exist(self::$data);

			if(CHECK::is_pass()){
				foreach(self::$data as $key => $args){
					VIEW::newBlock("TAG_CRUMBS_LIST");
					foreach($args as $field => $var){
						VIEW::assign("CRUMBS_".strtoupper($field),mb_substr($var, 0, 20, 'UTF-8'));
					}
				}
			}
		}

		# 依照功能產生資訊
		public static function fetch($func=false,$row=false){
			CHECK::is_must($func);

			if(CHECK::is_pass()){
				switch($func){
					case "intro":
						self::make($row["subject"],CORE::$root.'intro/'.SEO::link($row).'/');
					break;
					case "sitemap":
					case "contact":
						self::make(CORE::$lang[$func],CORE::$root.$func.'/');
					break;
					case "news":
						self::newsFetch($row);
					break;
					case "faq":
						self::faqFetch($row);
					break;
					case "products":
						self::productsFetch($row);
					break;
					case "search":
						self::make(CORE::$lang["search"],'#');
					break;
				}

				self::output();
			}
		}

		//-------------------------------------------------------------------------------------------------------------
		# 各項功能麵包屑

		# 最新消息
		private static function newsFetch($row=false){
			self::make(CORE::$lang["news"],CORE::$root.'news/');

			if($row["parent"]){
				# 項目
				CRUD::dataFetch("news_cate",array('id' => $row["parent"]));
				list($cateRow) = CRUD::$data;

				self::make($cateRow["subject"],NEWS::dataLink($cateRow["id"]));
				self::make($row["subject"],NEWS::dataLink($cateRow["id"],$row));
			}else{
				# 分類
				if(!empty($row["id"])) self::make($row["subject"],NEWS::dataLink($row["id"]));
			}
		}

		# 問與答
		private static function faqFetch($row=false){
			self::make(CORE::$lang["faq"],CORE::$root.'faq/');
			if(is_array($row)) self::make($row["subject"],CORE::$root.'faq/'.SEO::link($row).'/');
		}

		# 產品
		private static function productsFetch($row=false){
			self::make(CORE::$lang["products"],CORE::$root.'products/');

			if(is_array($row)){
				$rsnum = CRUD::dataFetch('products',array('id' => $row["id"]));
				if(!empty($rsnum)){
					list($row) = CRUD::$data;
					CRUD::dataFetch('products_cate',array('id' => $row["parent"]));
					list($cateRow) = CRUD::$data;

					self::productsCateFetch($cateRow);
					self::make($row["subject"],PRODUCTS::dataLink($row["parent"],$row));
				}else{
					self::productsCateFetch($row);
				}
			}
		}

		# 產品分類
		private static function productsCateFetch(array $row){
			if(!empty($row["parent"])){
				$rsnum = CRUD::dataFetch('products_cate',array('id' => $row["parent"]));
				if(!empty($rsnum)){
					list($parentRow) = CRUD::$data;
					self::productsCateFetch($parentRow);
				}
			}
			self::make($row["subject"],PRODUCTS::dataLink($row["id"]));
		}
	}

?>