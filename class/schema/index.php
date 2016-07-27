<?php

	# 結構標記化產生程式

	class SCHEMA{

		private static 
			$publisher = 'Icisco 愛思科',
			$schema;

		function __construct(){}

		# 基本項目
		private static function basic($type,array $args){
			$basic = array(
				'@context' => 'http://schema.org',
				'@type' => $type,
			);

			return array_merge($basic,$args);
		}

		# 生成 json
		private static function jsonEncode($args=false,$level=0){
			$args = (is_array($args))?$args:self::$schema;
			CHECK::is_array_exist($args);
			if(CHECK::is_pass()){
				foreach($args as $key => $item){
					if(is_array($item)){
						$urlArgs[$key] = self::jsonEncode($item,($level + 1));
					}else{
						$urlArgs[$key] = urlencode($item);
					}
				}

				if(is_array($urlArgs)){
					if(empty($level)){
						return urldecode(json_encode($urlArgs));
					}else{
						return $urlArgs;
					}
				}
			}
		}

		# 產生 schema
		public static function make($func=false,$args=false){
			if(empty($args)) return false;

			switch($func){
				case 'index':
				case 'intro':
				case 'news_list':
				case 'news_detail':
				case 'blog_list':
				case 'blog_detail':
				case 'products_list':
				case 'products_detail':
				case 'gallery_list':
				case 'gallery_detail':
				case 'feedback':
				case 'contact':
				case 'faq':
					self::$func($args);
				break;
				case 'custom':
				break;
			}
		}

		# index
		private static function index($args){

			$output = array(
				'url' => CORE::$cfg['host'],
				#'contactPoint' => 
			);

			CHECK::is_array_exist($args);
			if(CHECK::is_pass()){
				foreach(SYSTEM::$setting as $field => $var){
					if(empty($var)) continue;
					switch($field){
						case "name":
						case "email":
						case "address":
						case "logo":
							$output[$field] = $var;
						break;
						case "tel":
							$output['telephone'] = $var;
						break;
						case "facebook":
						case "gplus":
						case "twitter":
						case "instagram":
						case "linkedin":
							$output['sameAs'][] = $var;
						break;
					}
				}
			}

			self::$schema[] = self::basic('Organization',$output);
		}

		# intro
		private static function intro($args){
			switch(true){
				case (is_array($args)): # 依照來源資料
					$row = $args;
				break;
				case (is_numeric($args)): # 自行取得資料
					$rsnum = CRUD::dataFetch('intro',array('id' => $args,'status' => '1','langtag' => CORE::$langtag));
					if(!empty($rsnum)){
						list($row) = CRUD::$data;
					}
				break;
				default:
					$row = false;
				break;
			}

			if(is_array($row)){
				$output = array(
					'name' => $row['subject'],
					'articleBody' => strip_tags($row['content']),
					'publisher' => array('@type' => 'Organization','name' => self::$publisher)
				);

				self::$schema[] = self::basic('Article',$output);
			}
		}

		# news list
		private static function news_list($args){
			foreach($args as $row){
				self::news_detail($row);
			}
		}

		# news
		private static function news_detail($args){
			$output = array(
					'name' => $args['subject'],
					'startDate' => $args['showdate'],
					'description' => strip_tags($args['content']),
					#'location' => '',
				);

			self::$schema[] = self::basic('Event',$output);
		}

		# blog list
		private static function blog_list($args){
			foreach($args as $row){
				self::blog_detail($row);
			}
		}

		# blog
		private static function blog_detail($args){
			$output = array(
				'name' => CORE::$lang['blog'],
				#'logo' => '',
				'headline' => $args['subject'],
				'articleBody' => preg_replace('/\s(?=)/', '', strip_tags($args['content'])),
				'author' => SYSTEM::$setting['name'],
				'publisher' => array('@type' => 'Organization','name' => self::$publisher),
				'datePublished' => (!empty($args['createdate']))?$args['createdate']:date('Y-m-d'),
				'dateModified' => (!empty($args['modifydate']))?$args['modifydate']:date('Y-m-d'),
				'mainEntityOfPage' => CORE::$cfg['host'].'blog/',
			);

			IMAGES::load('blog',$args['id']);
			if(is_array(IMAGES::$data)){
				list($image) = IMAGES::$data;
				$output['image'] = self::image($image['path']);
			}

			self::$schema[] = self::basic('BlogPosting',$output);
		}

		# products list
		private static function products_list($args){
			foreach($args as $row){
				self::products_detail($row);
			}
		}

		# products
		private static function products_detail($args){
			$output = array(
				'name' => $args['subject'],
				'description' => preg_replace('/\s(?=)/', '', strip_tags($args['content'])),
			);

			IMAGES::load('products',$args['id']);
			if(is_array(IMAGES::$data)){
				list($image) = IMAGES::$data;
				$output['image'] = self::image($image['path']);
			}

			self::$schema[] = self::basic('Product',$output);
		}

		# 麵包屑生成
		public static function breadcrumb(array $args){
			foreach($args as $key => $data){
				$items['itemListElement'][] = array(
					'@type' => 'ListItem',
					'position' => ($key + 1),
					'item' => array(
						'@id' => 'http://'.CORE::$cfg['url'].$data['link'],
						'name' => $data['subject'],
					),
				);
			}

			self::$schema[] = self::basic('BreadcrumbList',$items);
		}

		# 輸出
		public static function output($view=false){
			$schemaJson = self::jsonEncode();

			if(!empty($schemaJson)){
				$schemaFull = '<script type="application/ld+json">'.$schemaJson.'</script>';

				if($view){
					VIEW::assignGlobal('TAG_SCHEMA',$schemaFull);
				}else{
					return $schemaFull;
				}
			}
		}
	}

?>