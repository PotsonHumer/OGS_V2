<?php

	# 前台產品功能

	class PRODUCTS_FRONTEND extends PRODUCTS{

		private static 
			$temp,
			$cate, #分類 id
			$id; # 資料 id

		function __construct(){

			list($cate,$args) = CORE::$args;
			self::$temp = CORE::$temp_main;
			
			if(!empty($cate)){
				self::$cate = SEO::origin('products_cate',$cate);
				$func++;
			}
			
			if(!empty($args)){
				self::$id = SEO::origin('products',$args);
				$func++;
			}

			switch($func){
				case 0:
					self::$temp["MAIN"] = 'ogs-products-tpl.html';
					AD::$cate = 4;
					new AD();

					SEO::load('products');
					if(empty(SEO::$data["h1"])) SEO::$data["h1"] = CORE::$lang["products"];
					SEO::output();
					CRUMBS::fetch('products');
				break;
				case 1:
					self::$temp["MAIN"] = 'ogs-products-list-tpl.html';
					self::row();
				break;
				default:
					self::$temp["MAIN"] = 'ogs-products-detail-tpl.html';
					self::detail();
				break;
			}

			self::nav();

			CORE::res_init('fix','css');
			new VIEW(CORE::$temp_option["HULL"],self::$temp,false,false);
		}


		# 分類列表
		private static function row(){
			CORE::res_init('fix','css');

			$rsnum = CRUD::dataFetch('products_cate',array('parent' => self::$cate,'status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]));

			if(!empty($rsnum)){
				#VIEW::newBlock("TAG_PRODUCTS_CATE_BLOCK");
				$dataRow = CRUD::$data;

				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_PRODUCTS_CATE_LIST");
					foreach($row as $field => $var){
						switch($field){
							default:
								VIEW::assign("VALUE_".strtoupper($field),$var);
							break;
						}
					}
					
					IMAGES::load('products_cate',$row["id"]);
					list($images) = IMAGES::$data;
					VIEW::assign(array(
						"VALUE_LINK" => self::dataLink($row["parent"]),
						"VALUE_IMAGE" => $images["path"],
						"VALUE_ALT" => $images["alt"],
						"VALUE_TITLE" => $images["title"],
					));
				}

				SEO::output();
			}else{
				if(!self::itemRow()) VIEW::newBlock("TAG_NONE");
			}

			# SEO
			$cate_rsnum = CRUD::dataFetch('products_cate',array('id' => self::$cate));
			if(!empty($cate_rsnum)){
				list($cate_row) = CRUD::$data;
				SEO::load($cate_row["seo_id"]);
				if(empty(SEO::$data["h1"])) SEO::$data["h1"] = $cate_row["subject"];
				SEO::output();
			}

			CRUMBS::fetch('products',$cate_row);
		}

		# 產品列表
		private static function itemRow(){
			$rsnum = CRUD::dataFetch('products',array('parent' =>  self::$cate,'status' => '1'),false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_PRODUCTS_LIST");
					foreach($row as $field => $var){
						VIEW::assign("VALUE_".strtoupper($field),$var);
					}

					IMAGES::load('products',$row["id"]);
					list($images) = IMAGES::$data;
					VIEW::assign(array(
						"VALUE_LINK" => self::dataLink($row["parent"],$row),
						"VALUE_IMAGE" => $images["path"],
						"VALUE_ALT" => $images["alt"],
						"VALUE_TITLE" => $images["title"],
					));
				}

				return true;
			}else{
				return false;
			}
		}

		# 顯示內容
		private static function detail(){
			$rsnum = CRUD::dataFetch('products',array('id' => self::$id));
			if(!empty($rsnum)){
				list($row) = CRUD::$data;
				foreach($row as $field => $var){
					VIEW::assignGlobal("VALUE_".strtoupper($field),$var);
				}

				VIEW::assignGlobal("VALUE_BACK_LINK",self::dataLink(self::$cate));

				IMAGES::load('products',$row["id"]);
				foreach(IMAGES::$data as $key => $images){
					switch($key){
						case 0:
						break;
						case 1:
							foreach($images as $field => $var){
								VIEW::assignGlobal("IMAGE_".strtoupper($field),$var);
							}
						default:
							VIEW::newBlock("TAG_IMAGES");
							foreach($images as $field => $var){
								VIEW::assign("IMAGES_".strtoupper($field),$var);
							}
						break;
					}
				}

				if(empty($row["discount"])) VIEW::assignGlobal("DISCOUNT_NONE",'style="display: none;"');

				SEO::load($row["seo_id"]);
				if(empty(SEO::$data["h1"])) SEO::$data["h1"] = $row["subject"];
				SEO::output();

				CRUMBS::fetch('products',$row);

				while(++$i <= 99){
					VIEW::newBlock("TAG_AMOUNT_LIST");
					VIEW::assign("VALUE_AMOUNT",$i);
				}
			}
		}
	}

?>