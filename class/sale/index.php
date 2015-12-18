<?php

	# 直銷功能

	class SALE{

		private static $endClass;
		public static 
			$data, # 取得的金字塔資料
			$group; # 金字塔群組

		function __construct($end_switch=false){

			CORE::summon(__FILE__);

			if($end_switch){
				self::$endClass =  __CLASS__."_BACKEND";
			}else{
				self::$endClass =  __CLASS__."_FRONTEND";
			}
			
			new self::$endClass;
		}

		function __call($function,$args){
			CORE::call_function(self::$endClass,$function,$args);
		}


		# 金字塔顯示
		public static function pyramid($m_id=NULL){
			$custom = (is_null($m_id))?"referrals IS NULL":"id = '{$m_id}'";
			$rsnum = CRUD::dataFetch('member',array("custom" => $custom,'status' => '1','verify' => '1'));
			if(!empty($rsnum)){
				# 金字塔起頭 (多組或單組)
				$pyramid = CRUD::$data;
				foreach($pyramid as $key => $row){
					self::$data = array(); # 清空紀錄

					# 取得上層 id
					if(!is_null($row["referrals"])){
						$referrals_num = CRUD::dataFetch('member',array("account" => $row["referrals"],'status' => '1','verify' => '1'));
						if(!empty($referrals_num)) $referrals = CRUD::$data[0]["id"];
					}else{
						$referrals = 0;
					}

					self::$data[0][$referrals] = $row;
					self::pyramid_level($row);

					self::$group[$key] = self::$data;
				}

				return true;
			}else{
				# 查無資料
				return false;
			}
		}

		# 壘算階層
		private static function pyramid_level(array $parentRow,$level=0){
			if($account === false) return false;

			++$level;
			$rsnum = CRUD::dataFetch('member',array("referrals" => $parentRow["account"],'status' => '1','verify' => '1'),false,array('createdate' => 'asc','id' => 'asc'));
			if(!empty($rsnum)){
				foreach(CRUD::$data as $key => $row){
					self::$data[$level][$parentRow["id"]][$key] = $row;
					self::pyramid_level($row,$level);
				}
			}
		}

		# 計算選定會員階層
		public static function member_level(array $row,$level=0){
			$rsnum = CRUD::dataFetch('member',array("account" => $row["referrals"],'status' => '1','verify' => '1'));
			if(!empty($rsnum)){
				list($parentRow) = CRUD::$data;
				return self::member_level($parentRow,++$level);
			}else{
				return $level;
			}
		}
		
		# 計算獎金 - 取得各層是否滿員
		public static function bonus($m_id=false){
			SALE::pyramid($m_id);
			list($data) = self::$group;
			
			unset($data[0]);
			foreach($data as $level => $cate){
				$level_total = pow(2,$level);
				unset($member_count,$createdate);
				foreach($cate as $sub_member){
					$member_count[] = count($sub_member);

					# 取得會員創建時間
					foreach($sub_member as $memberRow){
						$createdate[] = $memberRow["createdate"];
					}
				}
				
				$member_total = array_sum($member_count); # 取得同階層總會員數

				if($level_total == $member_total){
					$level_done[$level] = true;

					# 取得會員最晚創建時間
					rsort($createdate);
					list($level_time[$level]) = $createdate;
				}
			}

			# 所有累計獎金
			$bonus = self::bonus_count($level_done,$level_time);

			# 扣除已經發送獎金
			$rsnum = CRUD::dataFetch('grant',array('m_id' => $m_id));
			if(!empty($rsnum)){
				foreach(CRUD::$data as $key => $row){
					$granted_array[] = $row["granted"];
				}

				$granted = array_sum($granted_array);
				return $bonus - $granted;
			}else{
				return $bonus;
			}
		}
		
		# 計算獎金 - 累算獎金
		private static function bonus_count($level_done=false,$level_time=false){
			if($level_done === false){
				# 沒有獎金
				return '0';
			}else{
				$level = count($level_done);

				if($level < 5){
					return '0';
				}else{
					list($real_level,$pass_days) = self::pass_data_count($level,$level_time);
				}

				if(empty($real_level) || $real_level <= 0){
					return 0;
				}
				
				switch($real_level){
					case "1":
						return 5000;
					break;
					case "2":
						return 9500;
					break;
					case "3":
						return 19500;
					break;
					case "4":
						return 44500;
					break;
					default:
						$bonus_level = $real_level - 4;
						$bonums_timer = ($pass_days >= $bonus_level)?$bonus_level:$pass_days;
						return (50000 * $bonums_timer);
					break;
				}
			}
		}

		# 計算經過周數 / 天數
		private static function pass_data_count($level,$level_time){
			$start_date = $level_time[5]; # 起始日期
			$last_date = $level_time[$level]; # 最後滿級日期

			$start_year = date("Y",strtotime($start_date)); # 起始年
			$start_month = date("n",strtotime($start_date)); # 起始月
			$start_day = date("j",strtotime($start_date)); # 起始日
			$start_week = date("W",strtotime($start_date)); # 起始周

			$last_year = date("Y",strtotime($last_date)); # 滿級年
			$last_week = date("W",strtotime($last_date)); # 滿級周

			if($last_year == $start_year){
				$cross_week = ($last_week - $start_week) + 1; # 經過幾周

				if($cross_week > 4){
					$week5_first_day = date("Y-m-d",mktime(0,0,0,$start_month,(($start_day + 4 * 7) + 1),$start_year)); # 第九周第一天
					$week5_first_sec = strtotime($week5_first_day);
					$last_date_sec = strtotime($last_date);
					$pass_days = ceil((($last_date_sec - $week5_first_sec) / 60 / 60 / 24));
				}

				$real_level = $cross_week;
			}else{
				# 計算跨年周數
				for($year=$start_year;$year<=$last_year;$year++){
					switch($year){
						case $start_year:
							$end_week = date("W",mktime(0,0,0,12,31,$start_year));
							$cross_week_array[] = ($end_week - $start_week) + 1;
						break;
						case $last_year:
							$cross_week_array[] = $last_week;
						break;
						default:
							$cross_week_array[] = date("W",mktime(0,0,0,12,31,$year));
						break;
					}
				}

				$cross_week = array_sum($cross_week_array); # 經過幾周

				if($cross_week > 4){
					$week5_first_day = date("Y-m-d",mktime(0,0,0,$start_month,(($start_day + 4 * 7) + 1),$start_year)); # 第五周第一天
					$week5_first_sec = strtotime($week5_first_day);
					$last_date_sec = strtotime($last_date);
					$pass_days = ceil((($last_date_sec - $week5_first_sec) / 60 / 60 / 24));
				}

				$real_level = $cross_week;
			}

			return array($real_level,$pass_days);
		}
	}

?>