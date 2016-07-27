<?php

	# 前台反饋功能

	class FEEDBACK_FRONTEND extends FEEDBACK{

		private static 
			$temp,
			$m_id, # 會員 id
			$id; # 資料 id

		function __construct(){

			list($args) = CORE::$args;
			self::$m_id = SESS::get('m_id');
			self::$temp = CORE::$temp_main;

			CRUMBS::fetch('feedback');

			SEO::load('feedback');
			if(empty(SEO::$data["h1"])) SEO::$data["h1"] = CORE::$lang['feedback'];

			SEO::output();

			switch($args){
				case "thankyou":
					new RESPONSE('feedback',CORE::$lang['feedbackThanks']);
				break;
				case "list": # 顯示反饋
					self::$temp["MAIN"] = 'ogs-feedback-list-tpl.html';
					self::row();
				break;
				case "add": # 執行增加反饋
					self::$temp["MAIN"] = CORE::$temp_option["MSG"];
					self::add();
				break;
				default:
					self::$temp["MAIN"] = 'ogs-feedback-tpl.html';
				break;
			}

			CORE::common_resource();

			new VIEW(CORE::$temp_option["HULL"],self::$temp,false,false);
		}

		# 初始畫面
		protected static function form($rowCount=5){
			CRUD::args_output(true,true);

			$rsnum = CRUD::dataFetch('feedback',array('status' => '1'),false,array('createdate' => 'desc'));
			if(!empty($rsnum)){
				$dataRow = CRUD::$data;
				foreach($dataRow as $row){
					$scoreArray[] = $row['score'];
					$socreCount[$row['score']]++;
				}

				# 計算平均總分
				$averge = round(array_sum($scoreArray) / $rsnum,1);
				list($mainScore,$subScore) = explode('.',$averge);
			}

			VIEW::assignGlobal(array(
				'VALUE_TOTAL' => (empty($rsnum))?0:$rsnum,
				'VALUE_AVERGE_MAIN' => (empty($mainScore))?0:$mainScore,
				'VALUE_AVERGE_SUB' => (empty($subScore))?0:$subScore,
			));

			if(is_array($socreCount)){
				$s = 6;
				while(--$s > 0){
					VIEW::newBlock('TAG_SOCRE_LINE');
					$number = (empty($socreCount[$s]))?'0':$socreCount[$s];
					VIEW::assign(array(
						'VALUE_SCORE_PERCENT' => round((100 / $rsnum) * $number).'%',
						'VALUE_SOCRE_COUNT' => $s,
						'VALUE_SCORE_NUMBER' => $number,
					));
				}
			}

			$schemaReview = self::row($rowCount);

			SCHEMA::make('feedback',array('count' => $rsnum,'score' => $averge,'review' => $schemaReview));
		}

		# 反饋顯示
		private static function row(){
			$rsnum = CRUD::dataFetch('feedback',array('status' => '1'),false,array('createdate' => 'desc'));
			if(!empty($rsnum)){
				VIEW::newBlock('TAG_FEEDBACK_BLOCK');
				$dataRow = CRUD::$data;
				foreach($dataRow as $key => $row){
					VIEW::newBlock('TAG_FEEDBACK_LIST');
					foreach($row as $field => $var){
						switch($field){
							case "gender":
								$var = ($var)?'先生':'小姐';
							break;
							case "createdate":
								$var = date('Y-m-d',strtotime($var));
							break;
						}

						$schemaReview[$key][$field] = $var;
						$output['VALUE_'.strtoupper($field)] = $var;
					}

					VIEW::assign($output);

					# 顯示評分星星數
					if(!empty($row['score'])){
						VIEW::newBlock('TAG_SCORE_BLOCK');
						VIEW::assign('VALUE_SCORE',$row['score']);
						$score = 0;

						while(++$score <= $row['score']){
							VIEW::newBlock('TAG_SCORE_STAR');
						}
					}
				}

				return $schemaReview;
			}
		}

		# 增加反饋
		private static function add(){
			CHECK::is_must($_POST["callback"],$_POST["name"],$_POST["content"]);
			CHECK::is_email($_POST["email"]);

			if(CHECK::is_pass()){
				foreach($_POST as $field => $var){
					switch($field){
						case "name":
						case "content":
						case "gender":
							$insert[$field] = strip_tags($var);
						break;
						default:
							$insert[$field] = $var;
						break;
					}
				}

				CRUD::dataInsert('feedback',$insert);
				if(!empty(DB::$error)){
					$msg = 'Error! '.DB::$error;
				}else{
					$msg = CORE::$lang['submit_done'];
				}
			}else{
				$msg = CHECK::$alert;
			}

			RESPONSE::register($msg,CORE::$root.'feedback/thankyou/');
		}
	}

?>