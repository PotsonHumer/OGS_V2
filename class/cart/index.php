<?php

	# 購物車功能

	class CART{

		private static 
			$id, // 會員 id
			$temp, // 預設樣板
			$option_temp, // 預設選項樣板
			$stock, // 庫存相關功能
			$ship, // 運費
			$subtotal, // 價格小計
			$total, // 總價
			$first, // 第一次購物標籤
			$verify; // 會員驗證路徑

		function __construct(){

			$func = array_shift(CORE::$args);
			self::$id = SESS::get('m_id');
			self::$temp = CORE::$temp_main;
			self::$option_temp = CORE::$temp_option;

			switch($func){
				case "add":
					self::$temp["MAIN"] = self::$option_temp["MSG"];
					self::add();
				break;
				case "modify":
					self::$temp["MAIN"] = self::$option_temp["MSG"];
					self::modify();
				break;
				case "del":
					self::$temp["MAIN"] = self::$option_temp["MSG"];
					self::del();
				break;
				case "detail":
					$first = array_shift(CORE::$args);
					self::$first = ($first == 'first-shop')?true:false;
					if(!empty(self::$id) || self::$first){
						self::$temp["MAIN"] = 'ogs-cart-detail-tpl.html';
					}else{
						self::$temp["MAIN"] = 'ogs-cart-login-tpl.html';
					}

					self::detail();
				break;
				case "finish":
					self::$temp["MAIN"] = self::$option_temp["MSG"];
					self::finish();
				break;
				default:
					self::$temp["MAIN"] = 'ogs-cart-car-tpl.html';
					self::car();
				break;
			}

			if(!CHECK::is_ajax()){
				new VIEW(self::$option_temp["HULL"],self::$temp,false,false);
			}
		}

		# 取得產品資訊
		private static function p_data($id){
			$rsnum = CRUD::dataFetch('products',array('id' => $id));
			if(!empty($rsnum)){
				list($row) = CRUD::$data;
				return $row;
			}else{
				return false;
			}

			/*
			$select = array(
				'table' => 'bg_product_list',
				'field' => '*',
				'where' => "id = '{$p_id}'",
				//'order' => '',
				//'limit' => '',
			);

			$sql = DB::select($select);
			$rsnum = DB::num($sql);

			if(!empty($rsnum)){
				return DB::fetch($sql);
			}else{
				return false;
			}
			*/
		}

		# 計算未出貨庫存量
		/*
		private static function stock_unship($id){
			$select = "SELECT oi.amount FROM ".CORE::$prefix."_order_item as oi 
						LEFT JOIN ".CORE::$prefix."_order as o on o.serial = oi.serial 
						WHERE o.status <= '2' and oi.stock_id = '{$id}'";
			
			$sql = DB::select(false,$select);
			$rsnum = DB::num($sql);

			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					$amount_array[] = $row["amount"];
				}

				return array_sum($amount_array);
			}else{
				return 0;
			}
		}
		*/

		# 加入購物車
		private static function add(){
			#$stock_id = array_shift(CORE::$args);
			#$stock_row = STOCK::fetch($stock_id);
			$p_row = self::p_data($_POST["id"]);

			#$unship_num = self::stock_unship($stock_row["id"]); # 未出貨庫存
			#$real_num = $stock_row["amount"] - $unship_num; # 可用庫存
			$num = (empty($_POST["amount"]) || $_POST["amount"]=="null")?1:$_POST["amount"]; # 訂購數量
			IMAGES::load('products',$_POST["id"]);
			list($images) = IMAGES::$data;

			$cart = array(
				'id' => $p_row["id"],
				'name' => $p_row["subject"],
				'image' => $images["path"],
				'price' => (!empty($p_row["discount"]))?$p_row["discount"]:$p_row["price"],
				#'spec' => $stock_row["name"],
				#'stock' => $real_num,
				'amount' => $num,
				'subtotal' => $num * $p_row["price"],
			);

			SESS::write('cart',$cart["id"],$cart);

			/*
			if($real_num >= $num){
				SESS::write('cart',$cart["id"],$cart);
			}else{
				$cart["amount"] = $real_num;
				$cart["subtotal"] = $real_num * $stock_row["price"];
				SESS::write('cart',$stock_row["id"],$cart);
			}
			*/

			header('location: '.CORE::$root.'cart/');
		}

		# 數量選單
		private static function amount_select($max=99,$now=false,$min=1){
			for($i=$min;$i<=$max;$i++){
				$selected = (!empty($now) && $now == $i)?'selected':'';
				$option_array[] = '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
			}

			return implode("",$option_array);
		}

		# 更新購物車
		private static function modify(){
			$id = array_shift(CORE::$args);
			$amount = array_shift(CORE::$args);

			$cart = SESS::get('cart',$id);
			$cart["amount"] = $amount;
			$cart["subtotal"] = $cart["price"] * $amount;
			SESS::write('cart',$id,$cart);

			header("location: ".CORE::$root.'cart/');
		}

		# 購物車顯示
		private static function car(){
			$cartRow = SESS::get('cart');
			CHECK::is_array_exist($cartRow);

			if(CHECK::is_pass()){
				foreach($cartRow as $id => $cart){
					VIEW::newBlock("TAG_CAR_LIST");
					foreach($cart as $field => $var){
						VIEW::assign("VALUE_".strtoupper($field),$var);
					}

					VIEW::assign(array(
						"VALUE_ID" => $id,
						"VALUE_ROW" => ++$i,
						"VALUE_AMOUNT_OPTION" => self::amount_select(99,$cart["amount"]),
						"VALUE_AMOUNT" => $cart["amount"],
					));

					$subtotal_array[] = $cart["subtotal"];
				}

				self::$subtotal = array_sum($subtotal_array);
				self::$total = self::$subtotal + self::$ship;
				VIEW::assignGlobal("VALUE_TOTAL",self::$total);
			}else{
				self::$temp["MAIN"] = self::$option_temp["MSG"];
				CORE::msg(CORE::$lang["cart_none"],CORE::$root."products/");
			}
		}

		# 刪除訂購產品
		private static function del(){
			$id = array_shift(CORE::$args);
			SESS::del('cart',$id);

			CORE::msg(CORE::$lang["del_done"],CORE::$root."cart/");
		}

		# 訂單詳細
		private static function detail(){
			self::car();

			if(self::$first){
				# 無登入
				VIEW::newBlock("TAG_FIRST_BLOCK");
				VIEW::assignGlobal(array(
					"VALUE_FIRST" => 1,
				));
			}else{
				# 已登入
				CRUD::dataFetch('member',array('id' => self::$id));
				list($m_row) = CRUD::$data;

				foreach($m_row as $field => $var){
					VIEW::assignGlobal('VALUE_'.strtoupper($field),$var);
				}

				VIEW::assignGlobal(array(
					"VALUE_FIRST" => 0,
				));

				VIEW::newBlock("TAG_MAIL_BLOCK");
			}
		}

		# 新會員設定
		private static function new_member(){
			$args = array(
				'account' => $_POST["account"],
				'password' => md5($_POST["password"]),
				'name' => $_POST["name"],
				'address' => $_POST["address"],
				'tel' => $_POST["tel"],
				'cell' => $_POST["cell"],
				'createdate' => date("Y-m-d H:i:s"),
			);

			CRUD::dataInsert('member',$args);
			if(!empty(DB::$error)){
				CORE::msg(DB::$error,CORE::$root);
				return false;
			}else{
				self::$id = DB::get_id();
				$verify_code = md5($args["account"].$args["password"].$args["createdate"].self::$id); # 組合認證碼
				self::$verify = 'http://'.CORE::$cfg["url"].CORE::$root."member/verify/{$verify_code}/"; # 組合認證路徑
				CRUD::dataUpdate('member',array('verify_code' => $verify_code,'id' => self::$id)); # 儲存認證碼
				return true;
			}
		}

		# 訂單編號生成
		private static function new_serial(){
			$rsnum = CRUD::dataFetch('order');
			return date("YmdHis".str_pad(++$rsnum, 10, "0", STR_PAD_LEFT));
		}

		# 完成訂購
		private static function finish(){
			self::car();
			#$account_check = CRUD::dataFetch('member',array('account' => $_POST["account"]));
			CHECK::is_must($_POST["name"],$_POST["tel"],$_POST["cell"],$_POST["add_name"],$_POST["add_tel"],$_POST["add_address"]);

			if(!empty($_POST["first"])){
				# 未登入
				/*
				CHECK::is_email($_POST["account"]);
				CHECK::is_password($_POST["password"]);
				CHECK::is_same($_POST["password"],$_POST["match_password"]);

				$pass = CHECK::is_pass();

				if($pass && empty($account_check)){
					$new_member_rs = self::new_member();
				}else{
					CORE::msg(CORE::$lang["account_exist"].'或'.CORE::$lang["args_error"],CORE::$root);
				}
				*/
			}else{
				# 已登入
				$pass = CHECK::is_pass();
			}

			if($pass /*&& (!empty($_POST["first"]) && $new_member_rs || empty($_POST["first"]))*/){
				$serial = self::new_serial();
				#$email = (empty($_POST["first"]))?$_POST["email"]:$_POST["account"];
				$email = $_POST["email"];
				$name = $_POST["name"];
				$order = array(
					'm_id' => self::$id,
					'serial' => $serial,
					'status' => (!empty($_POST["first"]))?11:0,
					'createdate' => date("Y-m-d H:i:s"),
					'subtotal' => self::$subtotal,
					'ship' => self::$ship,
					'total' => self::$total,
					'email' => $email,
				);

				$order = array_merge($order,$_POST);
				CRUD::dataInsert('order',$order);
				$o_id = DB::get_id();

				if(empty(DB::$error)){
					$cartRow = SESS::get('cart');
					foreach($cartRow as $id => $cart){
						$item = array(
							'serial' => $serial,
							'p_id' => $cart["p_id"],
							#'stock_id' => $id,
							'name' => $cart["name"],
							#'spec' => $cart["spec"],
							'amount' => $cart["amount"],
							'price' => $cart["price"],
						);

						CRUD::dataInsert('order_item',$item);
						if(!empty(DB::$error)) break;
					}
				}

				if(empty(DB::$error)){
					# 訂購確認信
					foreach($order as $field => $var){
						VIEW::assignGlobal("VALUE_".strtoupper($field),$var);
					}
					new VIEW('ogs-mail-cart-tpl.html',false,true,false);
					CORE::mail_handle(SYSTEM::$setting["email"],$email,VIEW::$output,CORE::$lang["cart_mail"],SYSTEM::$setting["name"]);
					CORE::mail_handle($email,SYSTEM::$setting["email"],VIEW::$output,CORE::$lang["cart_mail"],$name);

					# 會員認證信
					/*
					VIEW::assignGlobal('VALUE_VERIFY_PATH',self::$verify);
					new VIEW('ogs-mail-verify-tpl.html',false,true,false);
					CORE::mail_handle(SYSTEM::$setting["email"],$email,VIEW::$output,CORE::$lang["regist_mail"],SYSTEM::$setting["name"]);
					*/

					SESS::del('cart');
					CORE::msg(CORE::$lang["cart_done"],CORE::$root."member/order/{$o_id}/");
				}else{
					# 發生錯誤
					CORE::msg(DB::$error,CORE::$root);
				}
			}else{
				if(!$pass || empty($_POST["first"])){
					CORE::msg(CHECK::$alert,CORE::$root);
				}
			}
		}
	}

?>