<?php

/*
 * httpsoocs/smpp.php
 */

function smpp_send($smpp_hosts,$smpp_port,$smpp_login,$smpp_password,$smpp_from,$smpp_to,$message){

	require_once '../httpsdocs/includes/smpp/smppclient.class.php';
	require_once '../httpsdocs/includes/smpp/gsmencoder.class.php';
	require_once '../httpsdocs/includes/smpp/sockettransport.class.php';
	
	// Construct transport and client
	$transport = new SocketTransport($smpp_hosts,$smpp_port);
	$transport->setRecvTimeout(10000);
	$smpp = new SmppClient($transport);
	
	// Activate binary hex-output of server interaction
	$smpp->debug = false;
	$transport->debug = false;
	
	$transport->open();
	$smpp->bindTransmitter($smpp_login,$smpp_password);
	
	// Optional connection specific overrides
	SmppClient::$sms_null_terminate_octetstrings = false;
	SmppClient::$csms_method = SmppClient::CSMS_PAYLOAD;
	SmppClient::$sms_registered_delivery_flag = SMPP::REG_DELIVERY_SMSC_BOTH;
	
	// Prepare message
	$tags = "CSMS_16BIT_TAGS";
	
	if(preg_match ('/^[\p{Cyrillic}\p{Common}]+$/u', $message)){
		$data_coding = SMPP::DATA_CODING_UCS2;
		$encodedMessage = iconv("UTF-8","UCS-2BE",$message);
	}
	else{
		$data_coding = SMPP::DATA_CODING_ISO8859_1;
	}
	
	if(is_null($encodedMessage)) $encodedMessage = $message;
	
	$from = new SmppAddress($smpp_from,SMPP::TON_ALPHANUMERIC);
	$to = new SmppAddress($smpp_to,SMPP::TON_INTERNATIONAL,SMPP::NPI_E164);
	
	// Send
	if($smpp_id = $smpp->sendSMS($from,$to,$encodedMessage,$tags,$data_coding)){
		// Close connection
		$smpp->close();
		return trim($smpp_id);
	}
	else{
		// Close connection
		$smpp->close();
		return false;
	}
}

function smpp_check($smpp_hosts,$smpp_port,$smpp_login,$smpp_password,$smpp_from,$smpp_id){

	require_once '../httpsdocs/includes/smpp/smppclient.class.php';
	require_once '../httpsdocs/includes/smpp/sockettransport.class.php';

	$transport = new SocketTransport($smpp_hosts,$smpp_port);
	$transport->setRecvTimeout(10000);
	$smpp = new SmppClient($transport);

	$smpp->debug = false;
	$transport->debug = false;

	$transport->open();
	$smpp->bindTransmitter($smpp_login,$smpp_password);

	$source = new SmppAddress($smpp_from,SMPP::TON_ALPHANUMERIC);

	if($smpp_res = $smpp->queryStatus($smpp_id,$source)){
		$smpp->close();
		return $smpp_res;
	}
	else{
		$smpp->close();
		return false;
	}
}

