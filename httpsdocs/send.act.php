<?php

include_once("includes/session.inc.php");
include_once("includes/db.php");
include_once("includes/site.class.php");
include_once("lang/lang.{$site_lang}.php");

$csms = new SmppiSite();

include("includes/auth.inc.php");

if(in_array("SMS_WEBSEND", $user_rights)){

	if(isset($_REQUEST['phone']) && isset($_REQUEST['msg'])){
		$phone = $csms->check_phone($_REQUEST['phone']);
		$msg = $db->real_escape_string($_REQUEST['msg']);
		$translit = (isset($_REQUEST['translit']) && $_REQUEST['translit'] == 1) ? 1 : 0;
		$method = (isset($_REQUEST['method'])) ? $_REQUEST['method'] : "gsm";
		try{
			$sms_id = $csms->sendsms($phone,$msg,$translit,$method);
			$csms->users_log($user_id, "sendsms:{$sms_id}", $_SERVER['REMOTE_ADDR']);
			header("location: /outgoing/");
		}
		catch (Exception $e){
			$_SESSION['send_error'] = SEND_ERROR;
			header("location: /send/");
		}
	}
	else{
		$_SESSION['send_error'] = FIELDS_ERROR;
		header("location: /send/");
	}
		
}
else{
	header("location: /");
}
