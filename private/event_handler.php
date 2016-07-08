#!/usr/bin/php
<?php

/*
 * private/event_handler.php
 */

require_once("db.php");

if(count($argv)>1){
	if($argv[1] == "RECEIVED" || $argv[1] == "REPORT"){
	
		wait_read_gsm(PATH_INCOMING,PATH_RECEIVED);
	
	}
	if($argv[1] == "SENT"){
		
		check_sent_gsm($argv[2]);
		
	}
}

function wait_read_gsm($path_incoming,$path_received){

	global $db;

	$d = dir($path_incoming);
	while (false !== ($entry = $d->read())) {
		if($entry != "." && $entry != ".."){
			$file = $entry;
			$full_msg = file_get_contents($path_incoming.$file);

			// парсим файл
			$smsfile = parse_sms_file($full_msg);
			$phone = $smsfile['From'];
			$dt = $smsfile['Sent'];
			$alphabet = $smsfile['Alphabet'];

			if(isset($alphabet) && $alphabet == "UCS2"){
				$msg = iconv("UCS-2BE", "UTF-8", $smsfile['Msg']);
			}
			else{
				$msg = $smsfile['Msg'];
			}

			$full_msg = $db->real_escape_string($full_msg);
			$msg = $db->real_escape_string($msg);
				
			if($msg != "SMS STATUS REPORT"){
				$insert = "insert into sms set
					dt = from_unixtime(unix_timestamp('{$dt}')),
					phonenumber = '{$phone}',
					msg = '{$msg}',
					full_msg = '{$full_msg}';";
				if($db->query($insert)){
					rename($path_incoming.$file, $path_received.$file);
				}
			}
			else{
				$int_id = $smsfile['Message_id'];
				$state = $smsfile['Status']['code'];
				$update = "update `sms` set `result` = 'OK', `message_state` = 2, `error_code` = '{$state}' where `method` = 'gsm' and `int_id` = '{$int_id}';";
				if($db->query($update)){
					rename($path_incoming.$file, $path_received.$file);
				}
			}
		}
	}
	$d->close();
}

function parse_sms_file($filecontent){

	$result = array();
	$result['Msg'] = "";

	$lines = explode("\n", $filecontent);
	$i=0;
	if(strpos($filecontent,"SMS STATUS REPORT") === false){
		foreach ($lines as $line){
			if($line != ""){
				if($i < 12){
					$parse = explode(":",$line);
					$result[$parse[0]] = trim($parse[1]);
				}
				else{
					$result['Msg'] .= $line."\n";
				}
			}
			$i++;
		}
		$result['Msg'] = substr($result['Msg'],0,-1);
	}
	else{
		foreach ($lines as $line){
			if($line != ""){
				if($i == 12){
					$result['Msg'] .= $line;
				}
				else{
					$parse = explode(":",$line);
					if($parse[0] != "Status"){
						$result[$parse[0]] = trim($parse[1]);
					}
					else{
						$stats = explode(",",trim($parse[1]));
						$result[$parse[0]] = array(
								"code" => $stats[0],
								"text" => $stats[1],
								"descr" => $stats[2],
						);
					}
				}
			}
			$i++;
		}
	}

	return $result;

}

function check_sent_gsm($full_path_sent_file){
	
	global $db;
	if(file_exists($full_path_sent_file)){
		preg_match("/(\D+)(\d+)/", $full_path_sent_file,$matches);
		$id = $matches[2];
		$file = file_get_contents($full_path_sent_file);
		$lines = explode("\n", $file);
		foreach($lines as $line){
			if($line != "" && strpos($line,": ") > 0){
				list($param,$value) = explode(": ",$line);
				if($param == "Message_id") $int_id = $value;
			}
		}
		$fields = [
				'full_msg' => $file,
				'result' => 'SENT',
		];
		if (isset($int_id)) {
			$fields['int_id'] = $int_id;
		}
		$sql_array = [];
		foreach ($fields as $field => $new_value){
			$sql_array[] = "`{$field}` = '{$new_value}' ";
		}
		$sql_string = implode(",",$sql_array);
		if(!$db->query("update `sms` set {$sql_string} where id = '{$id}';")){
			print_r($db->error);
			return false;
		}
		return true;
	}
	return false;
}
