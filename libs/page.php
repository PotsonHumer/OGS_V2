<?php

	# 分頁功能

	class PAGE{
		
		public static 
			$now = 1, # 現在頁次
			$start = 0, # 列數起始
			$num = 0; # 單頁顯示數
		private static $all; # 總頁數
		
		function __construct(){} # No need
		
		# 啟動頁次功能
		public static function handle($select,$sql,$rsnum){

			if(empty(self::$num)) self::$num = CORE::$cfg["item_num"];
			if(!empty($rsnum) && $rsnum > self::$num){
				self::$all = ceil($rsnum / self::$num); # 總頁數

				if(empty(self::$now)) self::$now = 1;
				if(self::$now < 0 || self::$now > self::$all) self::$now = 1;

				self::$start = $limit_start = self::$num * self::$now - self::$num; # limit 開始資料列數
				$new_limit = $limit_start.",".self::$num; # limit 組合完成

				$select['limit'] = $new_limit;
				$sql_page = DB::select($select);
				$page = array($sql_page,DB::num($sql_page));

				self::row();
			}else{
				self::$now = 1;
				$page = array($sql,$rsnum);
			}

			SESS::write('PAGE',self::$now);
			return $page;
		}

		# 取得連結
		private static function get_link(){
			$uri_array = explode("/",ROUTER::$uri);
			if(empty($uri_array[count($uri_array) - 1])){
				array_pop($uri_array);
				$link = implode("/",$uri_array);
			}else{
				$link = ROUTER::$uri;
			}

			return $link;
		}
		
		# 頁次選單
		private static function row(){
			if(!empty(self::$all)){

				$link = self::get_link();
				VIEW::newBlock("TAG_PAGE_BLOCK");

				# 分頁分段顯示
				if(self::$all > CORE::$cfg["page_num"]){
					$zone_num = ceil(self::$all / CORE::$cfg["page_num"]); # 總段數
					$zone_now = ceil(self::$now / CORE::$cfg["page_num"]); # 現在段數

					$page_min = ($zone_now * CORE::$cfg["page_num"] - CORE::$cfg["page_num"]) + 1; # 目前段數起始頁數
					$page_max = (($zone_now * CORE::$cfg["page_num"]) <= self::$all)?($zone_now * CORE::$cfg["page_num"]):self::$all; # 目前段數結尾頁數

					self::zone($zone_num,$zone_now);
				}else{
					$page_min = 1;
					$page_max = self::$all;
				}

				for($i=$page_min;$i<=$page_max;$i++){
					VIEW::newBlock("TAG_PAGE_LIST");
					VIEW::assign(array(
						"VALUE_PAGE_SUBJECT" => $i,
						"VALUE_PAGE_CURRENT" => ($i == self::$now)?'current':'',
						'VALUE_PAGE_LINK' => CORE::$root."{$link}/page-{$i}/",
					));
				}

				self::next_prev();
			}
		}

		# 上下頁連結
		private static function next_prev(){

			$link = self::get_link();

			# next
			if(self::$all > self::$now){
				$next = self::$now + 1;
				VIEW::gotoBlock("TAG_PAGE_BLOCK");
				VIEW::newBlock("TAG_PAGE_NEXT");
				VIEW::assign("VALUE_PAGE_NEXT",CORE::$root."{$link}/page-{$next}/");
			}

			# prev
			if(self::$now > 1){
				$prev = self::$now - 1;
				VIEW::gotoBlock("TAG_PAGE_BLOCK");
				VIEW::newBlock("TAG_PAGE_PREV");
				VIEW::assign("VALUE_PAGE_PREV",CORE::$root."{$link}/page-{$prev}/");
			}
		}

		# 上下段連結
		private static function zone($zone_num,$zone_now){

			$link = self::get_link();

			# next
			if($zone_num > $zone_now){
				$next = (($zone_now + 1) * CORE::$cfg["page_num"] - CORE::$cfg["page_num"]) + 1;
				VIEW::gotoBlock("TAG_PAGE_BLOCK");
				VIEW::newBlock("TAG_ZONE_NEXT");
				VIEW::assign("VALUE_ZONE_NEXT",CORE::$root."{$link}/page-{$next}/");
			}

			# prev
			if($zone_now > 1){
				$prev = ($zone_now - 1) * CORE::$cfg["page_num"];
				VIEW::gotoBlock("TAG_PAGE_BLOCK");
				VIEW::newBlock("TAG_ZONE_PREV");
				VIEW::assign("VALUE_ZONE_PREV",CORE::$root."{$link}/page-{$prev}/");
			}
		}
	}

?>