<?php

	# 社群分享功能

	class SHARE{

		private static 
			$prefix,
			$url,
			$meta;

		function __construct($args=false){
			self::$url = CORE::$cfg['dns'].$_SERVER['REQUEST_URI'];

			self::facebook($args);
			self::twitter($args);

			self::output();
		}

		# facebook 分享
		private static function facebook($args=false){
			self::$prefix = 'og:http://ogp.me/ns# fb:http://ogp.me/ns/fb#';
			$property = array(
				'locale' => 'zh_TW',
				'description' => SEO::$output['description'],
				'title' => SEO::$output['title'],
				'type' => 'website',
				'url' => self::$url,
				'image' => '',
				'site_name' => SYSTEM::$setting['name'],
			);

			if(is_array($args)){
				$property = array_merge($property,$args);
			}

			self::assemble($property,__function__);
		}

		# twitter 分享
		private static function twitter($args=false){
			$property = array(
				'card' => 'summary',
				'description' => SEO::$output['description'],
				'title' => SEO::$output['title'],
				'image' => '',
			);

			if(is_array($args)){
				$property = array_merge($property,$args);
			}

			self::assemble($property,__function__);
		}

		# 組成 meta
		private static function assemble(array $property,$function=false){
			switch($function){
				case "facebook":
					$prefix = 'ogs:';
				break;
				case "twitter":
					$prefix =  'twitter:';
				break;
				default:
					$prefix = '';
				break;
			}

			foreach($property as $argKey => $argVar){
				self::$meta[] = '<meta property="'.$prefix.$argKey.'" content="'.$argVar.'"/>';
			}
		}

		# 輸出
		private static function output(){
			VIEW::assignGlobal(array(
				'SHARE_PREFIX' => 'prefix="'.self::$prefix.'"',
				'SHARE_META' => (is_array(self::$meta))?implode("\n",self::$meta):'',
				'SHARE_LINE' => "javascript: void(window.open('http://line.naver.jp/R/msg/text/?'.concat(encodeURIComponent(location.href)) ));",
				'SHARE_FACEBOOK' => "javascript: void(window.open('http://www.facebook.com/share.php?u=".self::$url."'));",
				'SHARE_TWITTER' => "javascript: void(window.open('http://twitter.com/home/?status=".self::$url."'));",
				'SHARE_GPLUS' => "javascript: void(window.open('https://plus.google.com/share?url=".self::$url."'));",
			));
		}
	}

?>