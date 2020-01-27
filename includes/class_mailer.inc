<?php
class Mailer extends PHPMailer
{
	public function __construct($destinataire, $sujet, $corps, $html = false) {
		//$this->SMTPDebug = true;
		$this->IsSMTP();
		if(CONFIG_SMTP_SECURE == '') {
			$this->SMTPAuth = false;
		} else {
			$this->SMTPAuth = true;
			$this->SMTPSecure = CONFIG_SMTP_SECURE;
			if(CONFIG_SMTP_LOGIN != '') {
				$this->Username = CONFIG_SMTP_LOGIN;
			}
			if(CONFIG_SMTP_PASSWORD != '') {
				$this->Password = CONFIG_SMTP_PASSWORD;
			}

		}
		if(CONFIG_SMTP_PORT != '') {
			$this->Port = CONFIG_SMTP_PORT;
		}

		$this->Host = CONFIG_SMTP_HOST;
		$this->From = CONFIG_SMTP_FROM;
		$this->FromName = CONFIG_SMTP_FROM;

		if($html) {
			$this->IsHTML();
		}
		$this->Subject = $sujet;
		$this->Body = $corps;

		$this->SMTPOptions = array('ssl' => array("verify_peer"=>false, "verify_peer_name"=>false, "allow_self_signed"=>true));

		$this->AddAddress($destinataire);
	}
}
?>