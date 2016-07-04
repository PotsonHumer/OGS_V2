<?php

	# 網站地圖 xml 產生器

	class SITEMAP{

		private static $url,$handle,$link;

		function __construct(){
			set_time_limit(0);

			self::$url = CORE::$cfg['host'];
			self::fetch(self::$url);
			self::sort();
			$rs = self::output();

			$msg = ($rs)?'sitemap.xml 輸出完成':'sitemap.xml 輸出失敗';
			echo '<script>alert("'.$msg.'"); location.href="'.CORE::$root.'sitemap.xml";</script>';
		}

		# crul
		private static function curl($url){
			$ch = curl_init();

			$options = array(
				CURLOPT_URL => $url,
				CURLOPT_HEADER => 0,
				CURLOPT_VERBOSE => 0,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 5.1; rv:10.0.2) Gecko/20100101 Firefox/10.0.2",
				CURLOPT_POST => 0,
				CURLOPT_POSTFIELDS => 0,
				#CURLOPT_COOKIEJAR => 0,
				#CURLOPT_COOKIEFILE => 0,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,
			);

			curl_setopt_array($ch, $options);
			// CURLOPT_RETURNTRANSFER = true 會傳回網頁回應,
			// false 時只回傳成功與否
			$result = curl_exec($ch);
			curl_close($ch);
			
			return $result;
		}

		# 取得連結
		private static function getHref($data){
			if(preg_match_all('/<a[^>]*?(href="([^#]*?)")[^>]*?>/si', $data, $dataArray)){
				list($all,$str,$links) = $dataArray;
				foreach($links as $link){
					if(preg_match('/^\//',$link)){
						$linkKey = md5($link);

						if(!isset(self::$link[$linkKey]['check']) || !self::$link[$linkKey]['check']){
							$fullLink = preg_replace('/^\//',self::$url,$link);
							self::$link[$linkKey] = array('url' => $fullLink,'check' => true,'layer' => count(explode('/',$link)));

							self::fetch($fullLink);
						}
					}
				}
			}else{
				return false;
			}
		}

		# 抓取網站連結
		private static function fetch($url=false){
			static $count;

			if(empty($url)) return false;

			$data = self::curl($url);
			if(!empty($data)){
				self::getHref($data);
			}
		}

		# 依照層次排序
		private static function sort(){
			if(!is_array(self::$link)) return false;

			foreach(self::$link as $linkData){
				$sort[$linkData['layer']][] = $linkData['url'];
			}

			ksort($sort);

			foreach($sort as $sortData){
				sort($sortData);
				foreach($sortData as $urlData){
					$newSort[] = $urlData;
				}
			}

			self::$handle = (is_array($newSort))?$newSort:false;
		}

		# 輸出
		private static function output(){
			if(!is_array(self::$handle)) return false;

			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset></urlset>');
			$urlset = $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

			foreach(self::$handle as $path){
				$url = $xml->addChild('url');
				$loc = $url->addChild('loc',$path);
			}

			#header('Content-type: text/xml');
			$output = $xml->asXML();

			$filePath = HOME_PATH.'sitemap.xml';
			$fileHandle = true;

			if(file_exists($filePath)){
				$chmodRs = chmod($filePath,0777);
				if(!$chmodRs) $fileHandle = unlink($filePath);
			}

			if(!$fileHandle) exit('程式沒有權限修改原本的 sitemap.xml 檔案，請先手動移除後再執行一次。');

			$file = fopen($filePath,"w");
			fwrite($file,$output);
			fclose($file);

			return true;
		}
	}

?>