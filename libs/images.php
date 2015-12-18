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
				foreach(CRUD::$data as $key => $row){
					$row["exist"] = (!empty($row["path"]))?true:false;
					$row["path"] = (!empty($row["path"]))?self::absolute_path($row["path"]):self::absolute_path(CORE::$cfg["noimg"]);
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
				$images = array(
					'path' => $args["path"][$key],
					'alt' => $args["alt"][$key],
					'title' => $args["title"][$key],
					'sheet' => $tb_name,
					'related' => $related
				);

				CRUD::dataInsert('images',$images);
			}
		}

		# 更新圖片
		public static function modify(array $args,$tb_name=false,$related=false){
			foreach($args["id"] as $key => $id){
				$images = array(
					'id' => $id,
					'path' => $args["path"][$key],
					'alt' => $args["alt"][$key],
					'title' => $args["title"][$key],
				);

				if(empty($id)){
					$images = array_merge($images,array('sheet' => $tb_name,'related' => $related));
					CRUD::dataInsert('images',$images);
				}else{
					CRUD::dataUpdate('images',$images);
				}
			}
		}

		# 刪除圖片
		public static function del($tb_name,$id){
			$rsnum = IMAGES::load($tb_name,$id);
			if(!empty($rsnum)){
				foreach(IMAGES::$data as $key => $row){
					DB::delete(CORE::$prefix.'_images',array('id' => $row["id"]));
				}
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
	}

?>