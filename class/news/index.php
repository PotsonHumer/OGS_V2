<?php

	# 最新消息功能

	class NEWS{

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
			$rsnum = CRUD::dataFetch('news_cate',array('id' => $parent));
			if(!empty($rsnum)){
				list($cate) = CRUD::$data;
				$parentLink = SEO::link($cate);
			}
			
			if(!$data){
				return CORE::$root."news/{$parentLink}/";
			}else{
				$link = SEO::link($data);
				return CORE::$root."news/{$parentLink}/{$link}/";
			}
		}

		# 首頁列表
		public static function idx_row(){
			$rsnum = CRUD::dataFetch('news',array('status' => '1','langtag' => CORE::$langtag),false,array('sort' => CORE::$cfg["sort"]),'0,4');
			if(!empty($rsnum)){
				foreach(CRUD::$data as $key => $row){
					VIEW::newBlock("IDX_NEWS_LIST");
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
	}

?>