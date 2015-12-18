<?php

	$config["root"] = "/V2/";
	$config["url"] = "59.126.99.159";
	$config["host"] = 'http://'.$config["url"].$config["root"];
	$config["manage"] = "ogsadmin/";
	
	# 初始路徑
	$config["images"] = $config["root"].'images/'; # 圖片路徑
	$config["noimg"] = $config["host"].'images/no-thumb.jpg'; # 預設圖片路徑
	$config["css"] = $config["root"].'css/'; # css 路徑
	$config["js"] = $config["root"].'js/'; # js 路徑
	$config["file"] = $config["root"].'files/'; # 檔案路徑
	$config["temp_path"] = 'temp'; # 樣板位置
	$config["admin_temp"] = 'temp_admin/'; # 後台樣板位置

	# 預設樣板 (主要)
	$config["temp"]['main'] = array(
		'INDEX_HEADER' => 'ogs-fn-index-header-tpl.html', # 首頁頁首 (head)
		'HEADER' => 'ogs-fn-header-tpl.html', # 頁首 (head)
		'TOP' => 'ogs-fn-top-tpl.html', # 主選單
		'NAV' => 'ogs-fn-nav-tpl.html', # 側邊選單
		'FOOTER' => 'ogs-fn-footer-tpl.html', # 頁腳 (footer)
		'PAGE' => 'ogs-fn-page-tpl.html', # 分頁連結
		"MAIN" => '', # 內頁
	);

	# 預設樣板 (其他選項)
	$config["temp"]['option'] = array(
		'MSG' => 'ogs-fn-msg-tpl.html', # 警告、訊息
		'INDEX' => 'ogs-fn-index-tpl.html', # 預設首頁
		'HULL' => 'ogs-fn-main-tpl.html', # 預設底板
	);

	# 後台預設樣板
	$config["temp"]['admin'] = array(
		'HEADER' => 'ogs-admin-header-tpl.html', # 頁首 (head)
		'TOP' => 'ogs-admin-top-tpl.html', # 主選單
		'NAV' => 'ogs-admin-nav-tpl.html', # 側邊選單
		'FOOTER' => 'ogs-admin-footer-tpl.html', # 頁腳 (footer)
		'PAGE' => 'ogs-admin-page-tpl.html', # 分頁連結
		"MAIN" => '', # 內頁
	);

	# 後台預設樣板 (其他選項)
	$config["temp"]['admin_option'] = array(
		'MSG' => 'ogs-admin-msg-tpl.html', # 警告、訊息
		'INDEX' => 'ogs-admin-index-tpl.html', # 預設首頁
		'HULL' => 'ogs-admin-main-tpl.html', # 預設底板
		'SEO' => 'ogs-admin-seo-tpl.html', # 預設 SEO 表單
		'IMAGE' => 'ogs-admin-images-tpl.html', # 預設圖片表單
		'TREE' => 'ogs-admin-tree-tpl.html', # 預設樹狀圖
	);

	# 雜項設定
	$config["sort"]	= 'asc'; # 資料庫排序
	$config["item_num"]	= 12; # 每分頁幾個項目
	$config["page_num"]	= 10; # 最多顯示幾個分頁連結
	
	# key : 語系目錄參數 , value : 語系檔案參數 ,資料庫語系參數
	$config["lang"] = array(
		'cht' => array('cht','cht'),
		#'eng' => array('eng','eng'),
	);


	#### DEFAULT FUNCTION ####
	$config["default_function"]	= array(
		'index','news','products','faq','member','sitemap','contact'
	);
	##############################

	
	#### DON'T CHANGE THIS ####
	$lang_keys = array_keys($config["lang"]);

	$config["router"] = $lang_keys[0]; # 初始目錄參數
	$config["langfix"] = $config["lang"][$lang_keys[0]][0]; # 初始語言參數
	$config["langtag"] = $config["lang"][$lang_keys[0]][1]; # 初始資料庫標籤
	###########################
	
	
	#### DB connect ####
	$config['prefix'] = 'ogs'; # 資料表前贅參數
	$config["connect"] = array(
		'host' => 'localhost',
		'user' => 'root',
		'pass' => 'studiodd',
		'db' => 'ogs_v2'
	);
	####################
	
	
	#### autoload filter ####
	$config["file_filter"] = array("index","core","watermark");
	$config["dir_filter"] = array();
	$config["class_filter"] = array();
	#########################


	#### AD ####
	# 廣告分類
	/*
	$config["ad_cate"] = array(
		1 => '首頁 Banner',
		2 => '內頁 Banner',
		3 => '上方圖示',
		4 => '首頁型錄連結',
	);
	*/
	############
	

	$config["sess"] = 'OGSV2';

	
	#### country ####
	$config["country"] = array(
		"Afghanistan",
		"Albania", 
		"Algeria", 
		"American Samoa", 
		"Andorra", 
		"Angola", 
		"Anguilla", 
		"Antarctica", 
		"Antigua And Barbuda",
		"Argentina", 
		"Armenia", 
		"Aruba", 
		"Australia", 
		"Austria", 
		"Azerbaijan",
		"Bahamas",
		"Bahrain", 
		"Bangladesh", 
		"Barbados", 
		"Belarus", 
		"Belgium",
		"Belize",
		"Benin", 
		"Bermuda", 
		"Bhutan", 
		"Bolivia", 
		"Bosnia Hercegovina",
		"Botswana", 
		"Bouvet Island", 
		"Brazil", 
		"British Indian Ocean Territory", 
		"Brunei Darussalam",
		"Bulgaria", 
		"Burkina Faso",
		"Burundi", 
		"Byelorussian SSR",
		"Cambodia", 
		"Cameroon", 
		"Canada", 
		"Cape Verde",
		"Cayman Islands",
		"Central African Republic",
		"Chad", 
		"Chile", 
		"China", 
		"Christmas Island",
		"Cocos Islands",
		"Colombia", 
		"Comoros", 
		"Congo", 
		"Congo, The Democratic Republic Of",
		"Cook Islands",
		"Costa Rica",
		"Cote D'Ivoire",
		"Croatia", 
		"Cuba", 
		"Cyprus", 
		"Czech Republic",
		"Czechoslovakia",
		"Denmark", 
		"Djibouti", 
		"Dominica", 
		"Dominican Republic",
		"East Timor",
		"Ecuador", 
		"Egypt", 
		"El Salvador",
		"England", 
		"Equatorial Guinea",
		"Eritrea", 
		"Estonia", 
		"Ethiopia", 
		"Falkland Islands",
		"Faroe Islands",
		"Fiji", 
		"Finland", 
		"France", 
		"French Guiana",
		"French Polynesia",
		"French Southern Territories",
		"Gabon", 
		"Gambia", 
		"Georgia", 
		"Germany", 
		"Ghana", 
		"Gibraltar", 
		"Great Britain",
		"Greece", 
		"Greenland", 
		"Grenada", 
		"Guadeloupe", 
		"Guam", 
		"Guatemela", 
		"Guernsey", 
		"Guinea", 
		"Guinea-Bissau",
		"Guyana", 
		"Haiti", 
		"Heard and McDonald Islands",
		"Honduras", 
		"Hong Kong",
		"Hungary", 
		"Iceland", 
		"India", 
		"Indonesia", 
		"Iran",
		"Iraq", 
		"Ireland", 
		"Isle Of Man",
		"Israel", 
		"Italy", 
		"Jamaica", 
		"Japan", 
		"Jersey", 
		"Jordan", 
		"Kazakhstan", 
		"Kenya", 
		"Kiribati", 
		"Korea, Democratic People's Republic Of",
		"Korea, Republic Of",
		"Kuwait", 
		"Kyrgyzstan", 
		"Lao People's Democratic Republic",
		"Latvia", 
		"Lebanon", 
		"Lesotho", 
		"Liberia", 
		"Libyan Arab Jamahiriya",
		"Liechtenstein", 
		"Lithuania", 
		"Luxembourg", 
		"Macau", 
		"Macedonia", 
		"Madagascar", 
		"Malawi", 
		"Malaysia", 
		"Maldives", 
		"Mali", 
		"Malta", 
		"Marshall Islands",
		"Martinique", 
		"Mauritania", 
		"Mauritius", 
		"Mayotte", 
		"Mexico", 
		"Micronesia, Federated States Of",
		"Moldova, Republic Of",
		"Monaco", 
		"Mongolia", 
		"Montserrat", 
		"Morocco", 
		"Mozambique", 
		"Myanmar", 
		"Namibia", 
		"Nauru", 
		"Nepal", 
		"Netherlands", 
		"Netherlands Antilles",
		"Neutral Zone",
		"New Caledonia",
		"New Zealand",
		"Nicaragua", 
		"Niger", 
		"Nigeria", 
		"Niue", 
		"Norfolk Island",
		"Northern Mariana Islands",
		"Norway", 
		"Oman", 
		"Pakistan", 
		"Palau", 
		"Panama", 
		"Papua New Guinea",
		"Paraguay", 
		"Peru", 
		"Philippines", 
		"Pitcairn", 
		"Poland", 
		"Portugal", 
		"Puerto Rico",
		"Qatar", 
		"Reunion", 
		"Romania", 
		"Russian Federation",
		"Rwanda", 
		"Saint Helena",
		"Saint Kitts And Nevis",
		"Saint Lucia",
		"Saint Pierre and Miquelon",
		"Saint Vincent and The Grenadines",
		"Samoa", 
		"San Marino",
		"Sao Tome and Principe",
		"Saudi Arabia",
		"Senegal", 
		"Seychelles", 
		"Sierra Leone",
		"Singapore", 
		"Slovakia", 
		"Slovenia", 
		"Solomon Islands",
		"Somalia", 
		"South Africa",
		"South Georgia and The Sandwich Islands",
		"Spain", 
		"Sri Lanka",
		"Sudan", 
		"Suriname", 
		"Svalbard and JanMayen Islands",
		"Swaziland", 
		"Sweden", 
		"Switzerland", 
		"Syrian Arab Republic",
		"Taiwan", 
		"Tajikista", 
		"Tanzania, United Republic Of",
		"Thailand", 
		"Togo", 
		"Tokelau", 
		"Tonga", 
		"Trinidad and Tobago",
		"Tunisia", 
		"Turkey", 
		"Turkmenistan", 
		"Turks and Caicos Islands",
		"Tuvalu", 
		"Uganda", 
		"Ukraine", 
		"United Arab Emirates",
		"United Kingdom",
		"United States",
		"United States Minor Outlying Islands",
		"Uruguay", 
		"USSR", 
		"Uzbekistan", 
		"Vanuatu", 
		"Vatican City State",
		"Venezuela", 
		"Vietnam", 
		"Virgin Islands (British)",
		"Virgin Islands (U.S.)",
		"Wallis and Futuna Islands",
		"Western Sahara",
		"Yemen, Republic of",
		"Yugoslavia", 
		"Zaire", 
		"Zambia", 
		"Zimbabwe",
	);
	#################
	
	return $config;
	
?>