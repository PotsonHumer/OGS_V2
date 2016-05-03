<?php

	# 首頁

	class INDEX{
		function __construct(){

			$temp = CORE::$temp_main;
			$temp_option = CORE::$temp_option;

			SEO::load('index');
			SEO::output();

			NEWS::idx_row();

			new AD;

			CORE::common_resource();

			new VIEW('ogs-index-tpl.html',$temp,false,false);
		}
	}

?>