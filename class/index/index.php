<?php

	# 首頁

	class INDEX{
		function __construct(){

			CHECK::is_array_exist(CORE::$args);
			if(CHECK::is_pass()){
				self::notFound();
				exit;	
			}

			$temp = CORE::$temp_main;
			$temp_option = CORE::$temp_option;

			SEO::load('index');
			SEO::output();

			NEWS::idx_row();

			new AD;

			CORE::common_resource();

			new VIEW('ogs-index-tpl.html',$temp,false,false);
		}

		public static function notFound(){
			CORE::common_resource();

			SEO::load('nofound');
			SEO::output();

			VIEW::assignGlobal('SEO_H1',(empty(SEO::$data['h1']))?'404 not found':SEO::$data['h1']);

			$temp = CORE::$temp_main;
			$temp["MAIN"] = 'ogs-fn-404-tpl.html';
			new VIEW(CORE::$temp_option["HULL"],$temp,false,false);
		}
	}

?>