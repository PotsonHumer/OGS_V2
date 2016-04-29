<?php

	# 讀取畫面功能

	class LOAD{

		public static
			$bgClor = '#E6BD20',
			$css,
			$js,
			$html;

		function __construct(){
			if(!CORE::$cfg['loadScreen']) return false;
			VIEW::assignGlobal(array(
				'TAG_LOAD_SCREEN_CSS' => self::loadCss(),
				'TAG_LOAD_SCREEN_HTML' => self::loadHtml(),
				'TAG_LOAD_SCREEN_JS' => self::loadJs(),
			));
		}

		# 預設的 css
		private static function loadCss(){
			if(empty(self::$css)) self::$css = '.spinner{margin:100px auto 0;width:70px;text-align:center}.spinner>div{width:18px;height:18px;background-color:#333;border-radius:100%;display:inline-block;-webkit-animation:sk-bouncedelay 1.4s infinite ease-in-out both;animation:sk-bouncedelay 1.4s infinite ease-in-out both}.spinner .bounce1{-webkit-animation-delay:-.32s;animation-delay:-.32s}.spinner .bounce2{-webkit-animation-delay:-.16s;animation-delay:-.16s}@-webkit-keyframes sk-bouncedelay{0%,100%,80%{-webkit-transform:scale(0)}40%{-webkit-transform:scale(1)}}@keyframes sk-bouncedelay{0%,100%,80%{-webkit-transform:scale(0);transform:scale(0)}40%{-webkit-transform:scale(1);transform:scale(1)}}';
			return '<style type="text/css">#loadCover{position:fixed;top:0;left:0;width:100%;height:100%;background:'.self::$bgClor.';z-index:99999999}'.self::$css.'</style>';
		}

		# 預設的 js
		private static function loadJs(){
			if(empty(self::$js)) self::$js = '$(function(){$(\'#loadCover\').hide();});';
			return '<script>'.self::$js.'</script>';
		}

		# 預設的樣式
		private static function loadHtml(){
			if(empty(self::$html)) self::$html = '<div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>';
			return '<div id="loadCover">'.self::$html.'</div>';
		}
	}

?>