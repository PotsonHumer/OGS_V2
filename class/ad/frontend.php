<?php

	# 前台廣告功能

	class AD_FRONTEND extends AD{
		function __construct(){
			self::row();
		}

		# 廣告列表
		public static function row(){
			$args = (!self::$cate)?array('status' => '1'):array('status' => '1','id' => self::$cate);
			$rsnum = CRUD::dataFetch('ad_cate',$args);
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				$nowDate = date("Y-m-d");

				foreach($dataRow as $key => $cate){
					$rsnum = CRUD::dataFetch('ad',array('parent' => $cate["id"],'custom' => "status = '1' or (status = '2' and startdate <= '{$nowDate}' and limitdate >= '{$nowDate}')"));
					if(!empty($rsnum)){
						VIEW::newBlock("TAG_AD_BLOCK".$cate["id"]);
						foreach(CRUD::$data as $key => $row){
							VIEW::newBlock("TAG_AD_LIST".$cate["id"]);
							foreach($row as $field => $var){
								switch($field){
									case "link":
										if(empty($var)) $var = '#';
									default:
										VIEW::assign("VALUE_".strtoupper($field),$var);
									break;
								}
							}

							IMAGES::load('ad',$row["id"]);
							list($images) = IMAGES::$data;

							VIEW::assign(array(
								"VALUE_IMAGE" => $images["path"],
								"VALUE_ALT" => $images["alt"],
								"VALUE_TITLE" => $images["title"],
							));
						}
					}
				}
			}
		}

	}

?>