<?php

	# 結構標記化產生程式

	class SCHEMA{

		private static $schema;

		function __construct(){
			
		}

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

		public static function output($view=false){
			$schemaFull = '<script type="application/ld+json">'.self::jsonEncode().'</script>';

			if($view){
				VIEW::assignGlobal('TAG_SCHEMA',$schemaFull);
			}else{
				return $schemaFull;
			}
		}
	}

?> 