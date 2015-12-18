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

			CORE::res_init('super_slide','marquee','box');

			new VIEW('ogs-index-tpl.html',$temp,false,false);
		}
	}

?>