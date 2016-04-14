<?php

	# 首頁

	class INDEX{
		function __construct(){

			$temp = CORE::$temp_main;
			$temp_option = CORE::$temp_option;

			SEO::load('index');
			SEO::output();

			NEWS::idx_row();

<<<<<<< HEAD
			new AD;

			CORE::res_init('super_slide','marquee','box');
=======
			CORE::common_resource();
			CORE::res_init('index','css');
>>>>>>> 435bcb6... 修改共通資源載入方法

			new VIEW('ogs-index-tpl.html',$temp,false,false);
		}
	}

?>