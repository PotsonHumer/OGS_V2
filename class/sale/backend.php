<?php

	# 後台金字塔功能

	class SALE_BACKEND extends OGSADMIN{

		private static $id;

		function __construct(){

			list($func) = CORE::$args;
			$nav_class = 'SALE';

			MANAGER::level_check(1);
			CORE::res_init('pyramid','css');

			switch($func){
				case "bonus": # 未發送獎金列表
					$nav_func = 'BONUS';
					self::$temp["MAIN"] = 'ogs-admin-sale-bonus-tpl.html';
					self::bonus();
				break;
				case "grant": # 獎金發送
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::grant();
				break;
				case "granted": # 已發送獎金列表
					$nav_func = 'GRANTED';
					self::$temp["MAIN"] = 'ogs-admin-sale-granted-tpl.html';
					self::granted();
				break;
				default:
					self::$id = $func;
					self::$temp["MAIN"] = 'ogs-admin-sale-tpl.html';
					self::row();
				break;
			}

			self::nav_current($nav_class,$nav_func);
		}


		# 金字塔顯示
		private static function row(){
			if(SALE::pyramid(self::$id)){
				/*
				if(count(SALE::$group) > 1){
					# 多個群組
				}
				*/
				
				$group_array = SALE::$group;
				foreach($group_array as $group => $data){
					VIEW::newBlock("TAG_SALE_BLOCK");

					$level_count = count($data) - 1; # 共有幾層
					$max_unit = pow(2,$level_count);

					VIEW::assign("PYRAMID_WIDTH",($max_unit * 150));

					foreach($data as $level => $level_data){
						VIEW::newBlock("TAG_SALE_LEVEL");

						if(empty($level)){
							VIEW::newBlock("TAG_SALE_UNIT");
							VIEW::assign(array(
								"PYRAMID_SPLIT" => pow(2,$level),
								"TAG_USED" => "used",
							));

							list($top_parent) = array_keys($level_data);
							$top_level = SALE::member_level($level_data[$top_parent]);

							self::output($level,$level_data[$top_parent],$data); # 顯示會員資訊
							$zone[0] = $level_data[$top_parent]["id"]; # 紀錄父層區域 id
						}else{
							$unit_count = pow(2,$level);

							for($i=0;$i<$unit_count;$i++){
								VIEW::newBlock("TAG_SALE_UNIT");
								VIEW::assign("PYRAMID_SPLIT",pow(2,$level));

								$zone_key = ceil(($i + 1) / 2) - 1; # 取得父層區域 key
								$unit_key = (empty($i) || $i % 2 == 0)?0:1;
								$row = $level_data[$zone[$zone_key]][$unit_key]; # 取得資料

								if(isset($row) && is_array($row)){
									# 如果有資料
									VIEW::assign("TAG_USED","used");
									self::output($level,$row,$data); # 顯示會員資訊
									$new_zone[$i] = $row["id"]; # 紀錄父層區域 id									
								}else{
									VIEW::assign("CONTENT_EMPTY","empty");
								}
							}

							unset($zone);
							$zone = $new_zone;
						}
						
						VIEW::gotoBlock("TAG_SALE_LEVEL");
						VIEW::assign("VALUE_LEVEL",$top_level++);
					}
				}
			}
		}


		# 輸出資料
		private static function output($level,array $row,array $data){
			foreach($data[$level] as $parent => $unit_array){
				$unit_count[] = (!empty($level))?count($unit_array):'1';
			}

			if(is_array($unit_count)){
				$level_count = array_sum($unit_count);
			}else{
				$level_count = 0;
			}

			VIEW::newBlock("TAG_SALE_CONTENT");
			foreach($row as $field => $var){
				VIEW::assign("VALUE_".strtoupper($field),$var);
			}
			
			# 獎金計算
			$bonus = SALE::bonus($row["id"]);
			if(!empty($bonus)){
				VIEW::newBlock("TAG_SALE_BONUS");
				VIEW::assign("VALUE_BONUS",$bonus);
			}
		}


		# 未發送獎金列表
		private static function bonus(){
			$rsnum = CRUD::dataFetch('member',array('status' => '1','verify' => '1'),false,array('createdate' => 'asc'),false,false);

			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					$bonus = SALE::bonus($row["id"]);

					if(!empty($bonus)){
						if(++$count == 1){
							VIEW::newBlock("TAG_MEMBER_BLOCK");
						}

						VIEW::newBlock("TAG_MEMBER_LIST");
						foreach($row as $field => $var){
							VIEW::assign("VALUE_".strtoupper($field),$var);
						}

						VIEW::assign(array(
							"VALUE_NUMBER" => PAGE::$start + (++$i),
							"VALUE_LEVEL" => SALE::member_level($row),
							"VALUE_BONUS" => $bonus,
						));
					}
				}

				if(empty($count)){
					VIEW::newBlock("TAG_NONE");
				}
			}else{
				VIEW::newBlock("TAG_NONE");
			}
		}


		# 獎金發送
		private static function grant(){
			if(is_array($_POST["id"])){
				foreach($_POST["id"] as $m_id){
					CRUD::dataInsert('grant',array(
						'm_id' => $m_id,
						'granted' => SALE::bonus($m_id),
						'date' => date("Y-m-d H:i:s")
					));

					if(!empty(DB::$error)){
						$msg = DB::$error;
						break;
					}
				}

				if(empty($msg)){
					$msg = self::$lang["modify_done"];
				}
			}else{
				$msg = self::$lang["no_args"];
			}

			CORE::msg($msg,CORE::$manage.'sale/bonus/');
		}


		# 已發送獎金時間區間選單
		private static function granted_timer_select($date=false){
			$rsnum = CRUD::dataFetch('grant',false,false,array('date' => 'desc'));
			if(!empty($rsnum)){
				foreach(CRUD::$data as $key => $row){
					if(empty($last_timer) || $last_timer != $row["date"]){
						$selected = ($date == $row["date"])?'selected':'';
						$option_array[] = '<option value="'.$row["date"].'" '.$selected.'>'.$row["date"].'</option>';
					}

					$last_timer = $row["date"];
				}

				return implode("",$option_array);
			}else{
				return false;
			}
		}

		# 已發送獎金會員選單
		private static function granted_member_select($m_id=false){
			$rsnum = CRUD::dataFetch('grant',false,false,array('m_id' => 'asc'));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					if(empty($last_m_id) || $last_m_id != $row["m_id"]){
						CRUD::dataFetch('member',array("id" => $row["m_id"]));
						list($m_row) = CRUD::$data;

						$selected = ($m_id == $m_row["id"])?'selected':'';
						$option_array[] = '<option value="'.$m_row["id"].'" '.$selected.'>'.$m_row["name"].'</option>';
					}

					$last_m_id = $row["m_id"];
				}

				return implode("",$option_array);
			}else{
				return false;
			}
		}

		# 已發送獎金列表
		private static function granted(){

			VIEW::assignGlobal(array(
				"VALUE_DATE_OPTION" => self::granted_timer_select($_POST['date']),
				"VALUE_M_ID_OPTION" => self::granted_member_select($_POST['m_id']),
			));

			if(!empty($_POST['m_id'])){
				$sk['m_id'] = $_POST['m_id'];
			}

			if(!empty($_POST['date'])){
				$sk['date'] = $_POST["date"];
			}

			$rsnum = CRUD::dataFetch('grant',$sk,false,array($_POST["date"] => 'desc'));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				VIEW::newBlock("TAG_GRANT_BLOCK");
				foreach($dataRow as $key => $row){
					CRUD::dataFetch('member',array('m_id' => $row["m_id"]));
					list($m_row) = CRUD::$data;

					VIEW::newBlock("TAG_GRANT_LIST");
					VIEW::assign(array(
						"VALUE_NUMBER" => ++$i,
						"VALUE_ACCOUNT" => $m_row["account"],
						"VALUE_NAME" => $m_row["name"],
						"VALUE_LEVEL" => SALE::member_level($m_row),
						"VALUE_GRANTED" => $row["granted"],
					));
				}
			}else{
				VIEW::newBlock("TAG_NONE");
			}
		}
	}

?>