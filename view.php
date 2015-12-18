<?php

	class VIEW extends CORE{
		
		public static $output;
		public static $parameter;
		private static $tpl;
		
		// 主要樣板,附加樣板,輸出方式 (false => 直接輸出 , true => 回傳輸出),樣板類型 (false => 一般樣板, 1 => 後台樣板, 2 => 自訂樣板)
		function __construct($main_tpl='',$assing_tpl=array(),$output_type=false,$temp_type=false){
			
			// 輸出樣板路徑以外的檔案
			switch($temp_type){
				case 2:
					$temp_router = '';
				break;
				case 1:
					$temp_router = CORE::$admin_temp;
				break;
				default:
					$temp_router = CORE::$temp;
				break;
			}
			
			self::$tpl = new TemplatePower($temp_router.$main_tpl); // 註冊主要樣板
			
			// 附加樣板 (陣列輸入)
			if(is_array($assing_tpl) && count($assing_tpl) > 0){
				foreach($assing_tpl as $tpl_title => $tpl_path){
	        		self::$tpl->assignInclude($tpl_title,$temp_router.$tpl_path);
				}
			}
			
	        self::$tpl->prepare();
			
			// 建立輸出功能
			if(is_array(self::$parameter) && count(self::$parameter) > 0){
				foreach(self::$parameter as $tpl_key => $tpl_array){
					$tpl_type = array_keys($tpl_array);
					$tpl_value = $tpl_array[$tpl_type[0]];
					
					switch($tpl_type[0]){
						case 0:
							self::assign_do($tpl_value,false);
						break;
						case 1:
							self::block_do($tpl_value,false);
						break;
						case 2:
							self::block_do($tpl_value,true);
						break;
						case 3:
							self::assign_do($tpl_value,true);
						break;
					}
				}
			}
			
			// 輸出
			if(!$output_type){
				if(CORE::$cfg["langfix"] == 'chs' && !CORE::$bgend){
					self::$output = self::$tpl->getOutputContent();
					echo BIG2GB::go(self::$output);
				}else{
					self::$tpl->printToScreen();
				}

				self::$parameter = array();
				exit;
			}else{
				self::$output = self::$tpl->getOutputContent();
			}
		}
		
		#######################################################
		# 實際使用樣板功能
		
		// 啟動 Block 功能
		private static function block_do($tag_name='',$switch=false){
			if($switch){
				self::$tpl->gotoBlock($tag_name);
			}else{
				self::$tpl->newBlock($tag_name);
			}
		}
		
		// 啟動 assign 功能
		private static function assign_do(array $array,$switch=false){
			if($switch){
				self::$tpl->assignGlobal($array);
			}else{
				self::$tpl->assign($array);
			}
		}
		
		
		#######################################################
		# 組建樣板參數
		
		public function assign($value,$value_sec=''){
			if(is_array($value)){
				self::$parameter[][0] = $value;
			}else{
				self::$parameter[][0] = array($value => $value_sec);
			}
		}
		
		public function newBlock($tag=''){
			if(!empty($tag)){
				self::$parameter[][1] = $tag;
			}
		}
		
		public function gotoBlock($tag=''){
			if(!empty($tag)){
				self::$parameter[][2] = $tag;
			}
		}
		
		public function assignGlobal($value,$value_sec=''){
			if(is_array($value)){
				self::$parameter[][3] = $value;
			}else{
				self::$parameter[][3] = array($value => $value_sec);
			}
		}
	}
?>