<?php

	# 自動排序功能

	class SORT{
		function __construct(){} # No need

		# 執行自動排序
		public static function auto($tb_name,$langtag,$id,$sort=1){
			$parentExist = CRUD::dataFetch($tb_name,array('id' => $id),array('parent'));
			list($selfRow) = CRUD::$data;

			if($parentExist){
				$addon = (!empty($selfRow["parent"]))?" and parent = '{$selfRow["parent"]}'":" and parent IS NULL";
			}

			$rsnum = CRUD::dataFetch($tb_name,array('langtag' => $langtag,'custom' => "id != '{$id}' {$addon}"),false,array('sort' => CORE::$cfg["sort"]));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){

					# 讓出預定排序位置
					$autoSort = (++$i == $sort)?++$i:$i;
					$sort_args[] = array(
						'id' => $row["id"],
						'sort' => $autoSort,
					);
				}
			}

			$sort_args[] = array(
				'id' => $id,
				'sort' => $sort,
			);

			# 重置所有相關資料排序
			if(is_array($sort_args)){
				foreach($sort_args as $args){
					DB::update(CORE::$prefix."_".$tb_name,array('sort' => $args["sort"],'id' => $args["id"]));
				}
			}
		}
	}

?>