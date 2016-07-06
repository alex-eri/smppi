<?php

/*
 * rename this file into config.php
 */

$dbhost = "";
$dbuser = "";
$dbpassword = "";
$dbname = "smstools";

$site_lang = "ru"; // en|ru # 
$title = "Smstools web interface";
$title_head = "Smstools web interface";

$demo_number = ""; // replace all phonenumbers in web interface
$my_number = "79123456789";

$smpp_hosts = array(
                "",
                "",
);
$smpp_login = "login";
$smpp_password = "password";

$smpp_port = "2775";

// hardcode vars for debug :)
$smpp_from = "";
$smpp_to = "";
$smpp_msg = "t";

// const
define("DEBUG","1");
define("PATH_LOG","/var/www/vhosts/smppi.net/log/");
define("PATH_INCOMING","/var/spool/sms/incoming/");
define("PATH_RECEIVED","/var/spool/sms/received/");
define("PATH_OUTGOUING","/var/spool/sms/outgoing/");
define("PATH_SENT","/var/spool/sms/sent/");
