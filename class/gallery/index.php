<?php

	# 相簿功能

	class GALLERY{

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
			$rsnum = CRUD::dataFetch('gallery_cate',array('id' => $parent));
			if(!empty($rsnum)){
				list($cate) = CRUD::$data;
				$parentLink = SEO::link($cate);
			}
			
			if(!$data){
				return CORE::$root."gallery/{$parentLink}/";
			}else{
				$link = SEO::link($data);
				return CORE::$root."gallery/{$parentLink}/{$link}/";
			}
		}

		# 取得相簿圖片
		public static function dirLoad($dirPath=false,$loadNum=false){
			if(empty($dirPath)) return false;

			$realPath = ROOT_PATH.'files/'.$dirPath;
			if(file_exists($realPath)){
				$allFiles = glob($realPath.'/*.{jpg,png,gif,jpeg,JPG,PNG,GIF,JPEG}',GLOB_BRACE);
				CHECK::is_array_exist($allFiles);
				if(CHECK::is_pass()){
					foreach($allFiles as $key => $filePath){
						$output[$key] = str_replace(ROOT_PATH,CORE::$root,$filePath);
						if($loadNum !== false && is_numeric($loadNum) && ++$i >= $loadNum) break;
					}
				}
			}

			if(is_array($output)){
				return $output;
			}else{
				return false;
			}
		}

		# 首頁列表
		public static function idx_row(){
			$rsnum = CRUD::dataFetch('gallery',array('status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]),'0,4');
			if(!empty($rsnum)){
				foreach(CRUD::$data as $key => $row){
					VIEW::newBlock("IDX_GALLERY_LIST");
					foreach($row as $field => $var){
						switch($field){
							case "showdate":
								VIEW::assign("VALUE_".strtoupper($field),date("Y.m.d",strtotime($var)));
							break;
							default:
								VIEW::assign("VALUE_".strtoupper($field),$var);
							break;
						}
					}

					VIEW::assign("VALUE_LINK",self::dataLink($row["parent"],$row));
				}
			}
		}

		# 關聯產品顯示
		public static function related($related=false){
			if(empty($related)) return false;

			$relatedArray = json_decode($related,true);
			$rsnum = CRUD::dataFetch('gallery',array('id' => $relatedArray,'status' => '1','langtag' => CORE::$langtag));
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_RELATED_BLOCK");

				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					VIEW::newBlock("TAG_RELATED_LIST");

					IMAGES::load('gallery',$row["id"]);
					list($image) = IMAGES::$data;
					
					VIEW::assign(array(
						"VALUE_ID" => $row['id'],
						"VALUE_SUBJECT" => $row['subject'],
						"VALUE_IMAGE" => $image['path'],
						"VALUE_ALT" => $image['alt'],
						"VALUE_TITLE" => $image['title'],
						"VALUE_LINK" => GALLERY::dataLink($row['parent'],$row),
					));
				}
			}
		}
	}

?>