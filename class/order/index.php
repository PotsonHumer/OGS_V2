<?php

	# 訂單功能

	class ORDER extends OGSADMIN{
		function __construct(){

			$func = array_shift(CORE::$args);

			switch($func){
				case "detail":
					self::$temp["MAIN"] = 'ogs-admin-order-detail-tpl.html';
					self::detail();
				break;
				case "modify":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::modify();
				break;
				case "del":
					self::$temp["MAIN"] = self::$temp_option["MSG"];
					self::del();
				break;
				default:
					self::$temp["MAIN"] = 'ogs-admin-order-list-tpl.html';
					self::row();
					$func = false;
				break;
			}

			self::nav_current('ORDER');
		}

		# 訂單列表
		private static function row($serial=false){
			$sk = (!empty($serial))?array('serial' => $serial,'del' => '0'):array('del' => '0');
			$rsnum = CRUD::dataFetch('order',$sk,false,array('createdate' => 'desc'));
			if(!empty($rsnum)){
				if(empty($serial)) VIEW::newBlock("TAG_ORDER_BLOCK");
				foreach(CRUD::$data as $key => $row){
					if(empty($serial)) VIEW::newBlock("TAG_ORDER_LIST");
					$assign = (empty($serial))?'assign':'assignGlobal';
					foreach($row as $field => $var){
						switch($field){
							case "payment_type":
								VIEW::$assign("VALUE_".strtoupper($field),self::$lang["payment"][$var]);
							break;
							case "status":
								VIEW::$assign("VALUE_".strtoupper($field),self::$lang["order_status"][$var]);
							break;
							default:
								VIEW::$assign("VALUE_".strtoupper($field),$var);
							break;
						}
					}

					VIEW::assign("VALUE_ROW",++$i);
				}

				if(!empty($serial)) return CRUD::$data[0];
			}else{
				VIEW::newBlock("TAG_NONE");
			}
		}

		# 訂單詳細
		private static function detail(){
			$serial = array_shift(CORE::$args);
			$o_row = self::row($serial);

			if(is_array(self::$lang["order_status"])){
				foreach(self::$lang["order_status"] as $status => $status_str){
					VIEW::newBlock("TAG_STATUS_LIST");
					VIEW::assign(array(
						"VALUE_STATUS_STR" => $status_str,
						"VALUE_STATUS" => $status,
						"VALUE_STATUS_CK" => ($status == $o_row["status"])?'checked':''
					));
				}
			}

			$rsnum = CRUD::dataFetch('order_item',array('serial' => $serial),false,array('id' => 'desc'));
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_ITEM_BLOCK");
				foreach(CRUD::$data as $key => $row){
					VIEW::newBlock("TAG_ITEM_LIST");
					foreach($row as $field => $var){
						VIEW::assign("VALUE_".strtoupper($field),$var);
					}

					VIEW::assign("VALUE_ROW",++$i);
				}
			}
		}

		# 訂單修改 (狀態)
		private static function modify(){
			CHECK::is_must($_POST["id"],$_POST["serial"]);

			if(CHECK::is_pass() && isset($_POST["status"])){
				CRUD::dataUpdate('order',array('status' => $_POST["status"],'id' => $_POST["id"]));

				# 訂單完成，刪除庫存數
				/*
				if($_POST["status"] == 3){
					$rsnum = CRUD::dataFetch('order_item',array('serial' => $_POST["serial"]));
					if(!empty($rsnum)){
						$itemArray = CRUD::$data;
						foreach($itemArray as $key => $row){
							CRUD::dataFetch('stock_bind',array('id' => $row["stock_id"]));
							$remain_amount = CRUD::$data[0]["amount"] - $row["amount"];

							CRUD::dataUpdate('stock_bind',array('id' => $row["stock_id"],'amount' => $remain_amount));
						}
					}
				}
				*/
				
				CORE::msg(self::$lang["modify_done"],CORE::$manage.'order/detail/'.$_POST["serial"].'/');
			}else{
				CORE::msg(CHECK::$alert,CORE::$manage.'order/detail/'.$_POST["serial"].'/');
			}
		}

		# 訂單刪除
		private static function del(){
			$id = array_shift(CORE::$args);
			CRUD::dataUpdate('order',array('del' => '1','id' => $id));

			if(!empty(DB::$error)){
				CORE::msg(DB::$error,CORE::$manage.'order/');
			}else{
				CORE::msg(self::$lang["del_done"],CORE::$manage.'order/');
			}
		}
	}

?>