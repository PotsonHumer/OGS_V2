<?php

	# 產品功能

	class PRODUCTS{

		private static $endClass;

		function __construct($end_switch=false){

			CORE::summon(__FILE__);

			if($end_switch){
				self::$endClass =  __CLASS__."_BACKEND";
			}else{
				self::$endClass =  __CLASS__."_FRONTEND";
			}
			
			new self::$endClass;
		}

		function __call($function,$args){
			CORE::call_function(self::$endClass,$function,$args);
		}

		# 資料項目連結
		public static function dataLink($parent,$data=false){
			$rsnum = CRUD::dataFetch('products_cate',array('id' => $parent));
			if(!empty($rsnum)){
				list($cate) = CRUD::$data;
				$parentLink = SEO::link($cate);
			}
			
			if(!$data){
				return CORE::$root."products/{$parentLink}/";
			}else{
				$link = SEO::link($data);
				return CORE::$root."products/{$parentLink}/{$link}/";
			}
		}

		# 選單
		public static function nav(){
			list($cate) = CORE::$args;
			VIEW::assignGlobal("NAV_CATE_TITLE",'PRODUCTS CATE');
			$rsnum = CRUD::dataFetch('products_cate',array('status' => '1','parent' => 'null','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_NAV_LIST");
					VIEW::assign(array(
						"VALUE_NAV_SUBJECT" => $row["subject"],
						"VALUE_NAV_LINK" => CORE::$root.'products/'.SEO::link($row).'/',
						"VALUE_NAV_CURRENT" => ($cate == $row["id"])?'current':'',
					));
				}
			}
		}

		# 分類樹狀結構清單
		public static function tree($from='pc',$parent='null',$level=0){
			static $i;

			SK::fetch();
			$allRsnum = CRUD::dataFetch('products_cate');
			$rsnum = CRUD::dataFetch('products_cate',array('parent' => $parent),false,array('sort' => CORE::$cfg["sort"]));
			if($allRsnum > 1){
				if(++$i == 1) VIEW::newBlock("TAG_TREE_BLOCK");
				if(empty($rsnum)) return false;

				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){

					switch($from){
						case "pc":
							$link = CORE::$manage."products/cate/sk-parent:{$row["id"]}/";
						break;
						case "p":
							$link = CORE::$manage."products/sk-parent:{$row["id"]}/";
						break;
					}

					VIEW::newBlock("TAG_TREE_LIST");
					VIEW::assign(array(
						"TREE_SUBJECT" => $row["subject"],
						"TREE_LINK" => $link,
						"TREE_LEVEL" => $level,
						"TREE_CURRENT" => (SK::$args["parent"] == $row["id"])?'theme':'',
					));

					self::tree($from,$row["id"],($level + 1));
				}
			}
		}

		# 關聯產品顯示
		public static function related($related=false){
			if(empty($related)) return false;

			$relatedArray = json_decode($related,true);
			$rsnum = CRUD::dataFetch('products',array('id' => $relatedArray,'status' => '1','langtag' => CORE::$langtag));
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_RELATED_BLOCK");

				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_RELATED_LIST");

					IMAGES::load('products',$row["id"]);
					list($image) = IMAGES::$data;
					
					VIEW::assign(array(
						"VALUE_ID" => $row['id'],
						"VALUE_SUBJECT" => $row['subject'],
						"VALUE_IMAGE" => $image['path'],
						"VALUE_ALT" => $image['alt'],
						"VALUE_TITLE" => $image['title'],
						"VALUE_LINK" => PRODUCTS::dataLink($row['parent'],$row),
						"VALUE_PRICE" => $row['price'],
					));
				}
			}
		}		
	}

?>