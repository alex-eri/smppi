<?php

/*
 * index.php
 */
include_once ("includes/session.inc.php");
include_once ("includes/db.php");
include_once ("includes/site.class.php");
include_once ("lang/lang.{$site_lang}.php");

$csms = new SmppiSite ();

include ("includes/auth.inc.php");

$webuser = "";

$part = (isset ( $_REQUEST ['part'] )) ? $_REQUEST ['part'] : "incoming";

// navigation classes
$incoming_active = ($part == "incoming") ? "active" : "";
$outgoing_active = ($part == "outgoing") ? "active" : "";
$send_active = ($part == "send") ? "active" : "";
$adm_active = ($part == "adm") ? "active" : "";

// menu access
$send_menu = (in_array ( "SMS_WEBSEND", $user_rights )) ? "<li class=\"{$send_active}\"><a href=\"/send/\">" . MENU_SEND_SMS . "</a></li>" : "";
$adm_menu = (in_array ( "SMS_ADMIN", $user_rights )) ? "<li class=\"{$adm_active}\"><a href=\"/adm/\">" . MENU_MANAGE . "</a></li>" : "";

// make web content
$content_page = "";

if ($part == "incoming") {
	
	$direction = "0";
	
	// pagination
	$page = (isset ( $_REQUEST ['page'] )) ? $_REQUEST ['page'] : 1;
	$count = $csms->getsms_count ( $direction );
	$page_count = ceil ( $count / $csms->limit );
	
	$pagination = $csms->pagination ( $page, $page_count, 3 );
	
	$content_page .= "
			{$pagination}
			<table class=\"table table-bordered table-striped table-hover\">
				<thead>
				<tr>
					<th class=\"date-10\">" . THEAD_DATE . "</th>
					<th class=\"phone-10\">" . THEAD_PHONE . "</th>
					<th class=\"nsg-80\">" . THEAD_MSG . "<span class=\"label label-default pull-right\">" . THEAD_METHOD . "</span></th>
				</tr>
				</thead>
			";
	$content_page .= "
				<tbody>
			";
	
	if ($getsms = $csms->getsms ( 0, $page )) {
		foreach ( $getsms as $sms ) {
			
			$sms ['phonenumber'] = ($demo_number != "") ? $demo_number : $sms ['phonenumber'];
			
			$msg = htmlspecialchars ( $sms ['msg'] );
			$content_page .= "
						<tr>
							<td class=\"date-10\">{$sms['tstamp']}</td>
							<td class=\"phone-10\">{$sms['phonenumber']}</td>
							<td class=\"msg-80\">{$msg}<span class=\"label label-default pull-right\">{$sms['method']}</span></td>
						</tr>
					";
		}
	}
	
	$content_page .= "
				</tbody>
			</table>
			{$pagination}
			";
}

if ($part == "outgoing") {
	
	$direction = "1";
	
	// pagination
	$page = (isset ( $_REQUEST ['page'] )) ? $_REQUEST ['page'] : 1;
	$count = $csms->getsms_count ( $direction );
	$page_count = ceil ( $count / $csms->limit );
	
	$pagination = $csms->pagination ( $page, $page_count, 3 );
	
	$content_page .= "
			{$pagination}
			<table class=\"table table-bordered table-striped table-hover\">
				<thead>
				<tr>
					<th class=\"date-10\">" . THEAD_DATE . "</th>
					<th class=\"phone-10\">" . THEAD_PHONE . "</th>
					<th class=\"sent-10\">" . THEAD_STATE . "</th>
					<th class=\"msg-70\">" . THEAD_MSG . "<span class=\"label label-default pull-right\">" . THEAD_METHOD . "</span></th>
				</tr>
				</thead>
			";
	$content_page .= "
				<tbody>
			";
	
	if ($getsms = $csms->getsms ( 1, $page )) {
		foreach ( $getsms as $sms ) {
			
			$sms ['phonenumber'] = ($demo_number != "") ? $demo_number : $sms ['phonenumber'];
			
			$msg = htmlspecialchars ( $sms ['msg'] );
			if ($sms ['result'] == "OK") {
				$process = SENT_YES;
			} elseif ($sms ['result'] == "ERROR") {
				$process = SENT_ERROR;
			} else {
				$process = SENT_NO;
			}
			
			$smpp_state_html = "";
			
			if ($sms ['result'] == "OK" && $sms ['full_msg'] != "") {
				try {
					if ($sms ['message_state'] == 2) {
						$smpp_result = [ ];
						$smpp_result ['message_state'] = $sms ['message_state'];
						$smpp_result ['error_code'] = $sms ['error_code'];
					} elseif( $sms ['method'] == 'smpp' ) {
						$smpp_result = $csms->smpp_check ( $sms ['full_msg'] );
					}
					if ($smpp_result ['message_state'] == 1) {
						$smpp_state_html = "<span class=\"label label-default\">" . IN_PROCESS . "</span>";
					} elseif ($smpp_result ['message_state'] == 2 && $smpp_result ['error_code'] == 0) {
						$smpp_state_html = "<span class=\"label label-success\">" . DELIVERED . "</span>";
						$csms->update_operator_state ( $sms ['id'], $smpp_result ['message_state'], $smpp_result ['error_code'] );
					} else {
						$smpp_state_html = "<span class=\"label label-danger\">" . DELIVER_ERROR . "</span>";
						$csms->update_operator_state ( $sms ['id'], $smpp_result ['message_state'], $smpp_result ['error_code'] );
					}
				} catch ( Exception $e ) {
					$smpp_state_html = "";
				}
			}
			
			$content_page .= "
					<tr>
						<td class=\"date-10\">{$sms['tstamp']}</td>
						<td class=\"phone-10\">{$sms['phonenumber']}</td>
						<td class=\"sent-10 small\"
							data-container=\"body\"
							data-toggle=\"popover\"
							data-placement=\"top\"
							data-content=\"To: {$sms['phonenumber']}<br />Datetime: {$sms['dt']}<br />{$sms['result']}: {$sms['full_msg']}<br />Error code: {$sms['error_code']}\">
							{$process}
							{$smpp_state_html}
						</td>
						<td class=\"msg-70\">{$msg}<span class=\"label label-default pull-right\">{$sms['method']}</span>	</td>
					</tr>
			";
		}
	}
	
	$content_page .= "
				</tbody>
			</table>
			{$pagination}
			";
}

if ($part == "send" && in_array ( "SMS_WEBSEND", $user_rights )) {
	
	if (isset ( $_SESSION ['send_error'] )) {
		$send_error = "<span class=\"label label-danger\">{$_SESSION['send_error']}</span>";
		unset ( $_SESSION ['send_error'] );
	} else {
		$send_error = "";
	}
	
	include ("templates/sendsms_form.php");
	
	$content_page = $sendsms_html;
}

if ($part == "adm" && in_array ( "SMS_ADMIN", $user_rights )) {
	
	include ("templates/user_modal.php");
	
	$content_page = $modal_html;
	
	$user_error = (isset ( $_SESSION ['user_error'] )) ? "<p><label class=\"label label-danger\">{$_SESSION['user_error']}</label></p>" : "";
	
	unset ( $_SESSION ['user_error'] );
	
	$content_page .= $user_error;
	
	$label_send = ($csms->check_pid_file ( "sms_send" )) ? "<span class=\"label label-success\">" . PC_SENDING_ON . "</span>" : "<span class=\"label label-danger\">" . PC_SENDING_OFF . "</span>";
	$label_receive = ($csms->check_pid_file ( "sms_receive" )) ? "<span class=\"label label-success\">" . PC_RECEIVING_ON . "</span>" : "<span class=\"label label-danger\">" . PC_RECEIVING_OFF . "</span>";
	
	$content_page .= "
			<div class=\"panel panel-default\">
				<div class=\"panel-body\">
					{$label_send}&nbsp;{$label_receive}<span class=\"label label-info pull-right\">My number: {$my_number}</span>
				</div>
			</div>
	";
	
	$content_page .= "
			<table class=\"table table-bordered table-striped table-hover\">
			<thead>
			<tr>
					<th>" . THEAD_LOGIN . "</th>
					<th>" . THEAD_IP . "</th>
					<th>" . THEAD_INTERFACE . "</th>
					<th>" . THEAD_RIGHTS . "</th>
			</tr>
			</thead>
	";
	$content_page .= "
			<tbody>
	";
	if ($users = $csms->get_users ()) {
		foreach ( $users as $user ) {
			
			$get_rights = $csms->get_rights ( $user ['id'] );
			$rights = "";
			foreach ( $get_rights as $right ) {
				if ($right ['checked'] != "") {
					$rights .= "<span class=\"label label-default\">{$right['descr']}</span> ";
				}
			}
			
			$content_page .= "
					<tr>
							<td><button class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#create_user\" onclick=\"modal_show({$user['id']});\">{$user['login']}</button></td>
							<td>{$user['ip']}</td>
							<td>{$user['interface']}</td>
							<td>{$rights}</td>
					</tr>
			";
		}
	}
	
	$content_page .= "
			</tbody>
			</table>
	";
	
	$content_page .= "<p><button class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#create_user\" onclick=\"modal_show(0);\">" . BTN_CREATE_USER . "</button></p>";
}

if ($content_page != "") {
	include ("templates/main.tpl.php");
} else {
	include ("templates/404.tpl.php");
}

echo $html;
