<?php

	# PHPmailer
	class MAIL{

		private static $call;

		function __construct(){

			CORE::summon(__FILE__);

			self::$call = new PHPMailer();
			self::$call->IsSMTP();

			self::$call->SMTPAuth = true;
			self::$call->SMTPSecure = "ssl"; # 設定 SSL 連線
			self::$call->Host = "smtp.gmail.com"; # 主機位置
			self::$call->Port = 465; # Gmail 是 465

			# 信件內容的編碼方式       
			self::$call->CharSet = "utf-8";

			# 信件處理的編碼方式
			self::$call->Encoding = "base64";

			# SMTP 驗證的使用者資訊
			self::$call->Username = 'ogs.notice@gmail.com';  # mail 帳號
			self::$call->Password = "ntjrfzntaoyulddc";  # mail 密碼
		}

		public static function handle($from,$to,$mail_content,$mail_subject,$mail_name){
			# 信件內容設定
			self::$call->From = $from; # 此處為寄出後收件者顯示寄件者的電子郵件 (請設成與上方驗証電子郵件一樣的位址)
			self::$call->FromName = $mail_name; # 此處為寄出後收件者顯示寄件者的名稱

			#self::$call->SetFrom($from,$mail_name);
			self::$call->Subject = "=?utf-8?B?".base64_encode($mail_subject)."?="; # 此處為寄出後收件者顯示寄件者的電子郵件標題
			self::$call->Body = $mail_content;   //信件內容 
			self::$call->IsHTML(true);

			# BCC (隱密副件)
			# self::$call->AddBCC($bcc_mail);

			# 收件人
			$to_array = explode(",",$to);

			foreach($to_array as $to_nuit){
				self::$call->AddAddress($to_nuit,""); # 此處為收件者的電子信箱及顯示名稱

				if(!self::$call->Send()){
					return self::$call->ErrorInfo;
				}
			}

			return false;
		}

	}


?>