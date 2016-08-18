<?php

	# 圖片處理

	class IMAGES{

		public static $data; # 儲存取得資料

		function __construct(){} # no need..

		# 取得圖片資料
		public static function load($tb_name,$id=false){
			self::$data = array();
			$rsnum = CRUD::dataFetch('images',array('sheet' => $tb_name,'related' => $id),false,array('id' => 'asc'));
			if(!empty($rsnum)){
				$cropField = (CORE::$mobile)?'crop_m':'crop';

				foreach(CRUD::$data as $key => $row){
					$row["exist"] = (!empty($row["path"]))?true:false;
					$row["path"] = (!empty($row["path"]))?self::absolute_path($row["path"]):self::absolute_path(CORE::$cfg["noimg"]);
					if(!CORE::$bgend) $row["path"] = (!empty($row[$cropField]))?$row[$cropField]:$row['path'];
					self::$data[] = $row;
				}

				return count(self::$data);
			}else{
				return false;
			}
		}

		# 輸出圖片資料
		public static function output($tb_name,$id,$setnum=0,$tag=false,$tagRow=false){
			$rsnum = IMAGES::load($tb_name,$id);

			# 指定數量
			if(!empty($setnum)){
				for($i=0;$i<$setnum;$i++){
					$row = IMAGES::$data[$i];

					if(!$tag) $tag = 'IMAGES_BLOCK';

					if($tagRow){
						VIEW::newBlock($tag);
					}else{
						VIEW::newBlock($tag.$i);
					}

					if(is_array($row)){
						foreach($row as $field => $var){
							VIEW::assign("IMAGES_".strtoupper($field),$var);
						}
					}
				}
				return true;
			}

			# 自動偵測
			switch($rsnum){
				case "0":
					return false;
				break;
				case "1":
					list($row) = IMAGES::$data;
					foreach($row as $field => $var){
						VIEW::assignGlobal("IMAGES_".strtoupper($field),$var);
					}
				break;
				default:
					if(!$tag) $tag = 'IMAGES_BLOCK';
					foreach(IMAGES::$data as $key => $row){

						if($tagRow){
							VIEW::newBlock($tag);
						}else{
							VIEW::newBlock($tag.$key);
						}

						foreach($row as $field => $var){
							VIEW::assign("IMAGES_".strtoupper($field),$var);
						}
					}
				break;
			}
		}

		# 紀錄圖片
		public static function add($tb_name,array $args,$related){
			foreach($args["id"] as $key => $id){
				list($width,$height) = IMAGES::size($args["path"][$key]);
				$images = array(
					'path' => $args["path"][$key],
					'alt' => $args["alt"][$key],
					'title' => $args["title"][$key],
					'width_o' => $width,
					'height_o' => $height,
					'width' => $args['width'][$key],
					'height' => $args['height'][$key],
					'width_m' => $args['width_m'][$key],
					'height_m' => $args['height_m'][$key],
					'info' => $args['info'][$key],
					'sheet' => $tb_name,
					'related' => $related
				);

				CRUD::dataInsert('images',$images);

				self::crop(CRUD::$id,$images['path'],$images['width'],$images['height'],$images['width_m'],$images['height_m']);
			}
		}

		# 更新圖片
		public static function modify(array $args,$tb_name=false,$related=false){
			foreach($args["id"] as $key => $id){
				$rsnum = CRUD::dataFetch('images',array('id' => $id));
				if(!empty($rsnum)){
					list($imgRow) = CRUD::$data;

					list($width,$height) = IMAGES::size($args["path"][$key]);
					$images = array(
						'id' => $id,
						'path' => $args["path"][$key],
						'alt' => $args["alt"][$key],
						'title' => $args["title"][$key],
						'width_o' => $width,
						'height_o' => $height,
						'width' => $args['width'][$key],
						'height' => $args['height'][$key],
						'width_m' => $args['width_m'][$key],
						'height_m' => $args['height_m'][$key],
						'info' => $args['info'][$key],
					);

					if(empty($id)){
						$images = array_merge($images,array('sheet' => $tb_name,'related' => $related));
						CRUD::dataInsert('images',$images);
						$ID = CRUD::$id;
					}else{
						CRUD::dataUpdate('images',$images);
						$ID = $images['id'];
					}

					self::crop($ID,$images['path'],$images['width'],$images['height'],$images['width_m'],$images['height_m'],$imgRow['crop'],$imgRow['crop_m']);
				}
			}
		}

		# 刪除圖片
		public static function del($tb_name,$id){
			$rsnum = IMAGES::load($tb_name,$id);
			if(!empty($rsnum)){
				foreach(IMAGES::$data as $key => $row){
					DB::delete(CORE::$prefix.'_images',array('id' => $row["id"]));
					self::delCrop($row['crop'],$row['crop_m']);
				}
			}
		}

		# 刪除縮圖
		private static function delCrop($crop,$crop_m){
			if(!empty($crop)){
				$cropPath = str_replace(CORE::$cfg['host'],ROOT_PATH,$crop);
				if(file_exists($cropPath)) unlink($cropPath);
			}

			if(!empty($crop_m)){
				$cropPath_m = str_replace(CORE::$cfg['host'],ROOT_PATH,$crop_m);
				if(file_exists($cropPath_m)) unlink($cropPath_m);
			}
		}

		# 取得圖片絕對路徑
		private static function absolute_path($path=false){
			#static $e;

			if(!empty($path) && !preg_match('/http/',$path)/* && ++$e == 1*/){
				return preg_replace("/^".addcslashes('/',CORE::$cfg["root"])."/",CORE::$cfg["host"],$path,1);
			}else{
				return $path;
			}
		}

		# 製作小圖
		public static function crop($id,$path=false,$width=false,$height=false,$width_m=false,$height_m=false,$crop=false,$crop_m=false){
			if(empty($id)) return false;

			if(!empty($crop) || !empty($crop_m)){
				self::delCrop($crop,$crop_m);
			}

			if(!empty($path) && $path != CORE::$cfg['noimg']){
				$nowDate = strtotime(date('Y-m-d H:i:s'));
				$filePath = str_replace(CORE::$cfg['host'],ROOT_PATH,$path);
				$fileName = basename($filePath);
				$extension = pathinfo($filePath, PATHINFO_EXTENSION);

				$cropName = 'crop_'.$id.'_'.$nowDate.'.'.$extension;

				$cropDir = ROOT_PATH."files/crop/";
				$cropDir_m = ROOT_PATH."files/crop_m/";
				$cropHostPath = CORE::$cfg['host'].'files/crop/'.$cropName;
				$cropHost_mPath = CORE::$cfg['host'].'files/crop_m/'.$cropName;

				if(!is_dir($cropDir)){
					mkdir($cropDir, 0777, true) || die("can't create dir in '{$cropDir}'");
					chmod($cropDir, 0777);
				}

				if(!is_dir($cropDir_m)){
					mkdir($cropDir_m, 0777, true) || die("can't create dir in '{$cropDir_m}'");
					chmod($cropDir_m, 0777);
				}

				$cropPath = $cropDir.$cropName;
				$cropPath_m = $cropDir_m.$cropName;

				switch(true){
					case (!empty($width) && !empty($height)):
						self::resize($filePath,$width,$height,$cropPath);
					break;
					case (!empty($width) || !empty($height)):
						if(empty($width)) $width = 'auto';
						if(empty($height)) $height = 'auto';
						self::resize($filePath,$width,$height,$cropPath);
					break;
					default:
						$cropNone = true;
					break;
				}

				switch(true){
					case (!empty($width_m) && !empty($height_m)):
						self::resize($filePath,$width_m,$height_m,$cropPath_m);
					break;
					case (!empty($width_m) || !empty($height_m)):
						if(empty($width_m)) $width_m = 'auto';
						if(empty($height_m)) $height_m = 'auto';
						self::resize($filePath,$width_m,$height_m,$cropPath_m);
					break;
					default:
						$cropNone = true;
					break;
				}
			}else{
				$cropNone = true;
			}

			if(!$cropNone){
				$cropArgs = array('crop' => $cropHostPath,'crop_m' => $cropHost_mPath,'id' => $id);
			}else{
				$cropArgs = array('crop' => 'null','crop_m' => 'null','id' => $id);
			}

			CRUD::dataUpdate('images',$cropArgs);
		}

		# 縮放圖片
		public static function resize($path=false,$width=false,$height=false,$save=false){
			require_once(ROOT_PATH."class/editor/filemanager/include/php_image_magician.php");
			require_once(ROOT_PATH."class/editor/filemanager/include/utils.php");

			switch(true){
				case ((empty($width) || $width == 'auto') && (empty($height) || $height == 'auto')):
					return false;
				break;
				case (empty($width) || $width == 'auto'):
					$option = 1;
				break;
				case (empty($height) || $height == 'auto'):
					$option = 2;
				break;
				default:
					$option = 0;
				break;
			}

			$path = str_replace(CORE::$cfg['host'],ROOT_PATH,$path);

			$magicianObj = new imageLib($path);
			#$magicianObj->setForceStretch(false);
			$magicianObj->resizeImage($width, $height, $option);

			#$imagick = new Imagick($path);
			#$imagick->resizeImage($width,$height,Imagick::FILTER_LANCZOS,false);
			#$imagick->scaleImage($width,$height);

			$filename = basename($path);
			$file_extension = strtolower(substr(strrchr($filename,"."),1));

			/*
			switch($file_extension){
			    case "gif": $ctype="image/gif"; break;
			    case "png": $ctype="image/png"; break;
			    case "jpeg":
			    case "jpg": $ctype="image/jpeg"; break;
			    default:
			}

			header('Content-type: '.$ctype);
			*/

			#echo $imagick->getImageBlob();
			if(empty($save)){
				$magicianObj->displayImage($file_extension);
			}else{
				$magicianObj->saveImage($save, 100);
			}
		}

		# 縮放路徑處裡
		public static function resizePath($path=false,$width=false,$height=false){
			if(empty($path)) return self::absolute_path(CORE::$cfg["noimg"]);

			$extension = pathinfo($path, PATHINFO_EXTENSION);
			$baseName = basename($path,'.'.$extension);
			$urlPath = base64_encode($path);
			return CORE::$root.'imghandle/resize/'.$width.'/'.$height.'/'.$urlPath.'/'.$baseName.'/';

			/*
			if(preg_match('/^http/',$path)){
				if(preg_match('/^http:\/\/'.CORE::$cfg["url"].'/',$path)){
					$relativePath = str_replace(CORE::$cfg['host'],'/',$path);
					return CORE::$root.'imghandle/resize/'.$width.'/'.$height.'/'.$relativePath.'/';
				}else{
					return $path;
				}
			}else{

			}
			*/
		}

		# 快速取得圖片大小
		public static function size($path=false){
			if(empty($path)) return array(0,0);

			require_once ROOT_PATH.'libs/Fastimage.php';
			$image = new FastImage($path);
			return $image->getSize();
		}
	}

?>