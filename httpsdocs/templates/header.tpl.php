<?php

$base_path = BASE_PATH;
$header_html = <<<HTML
<html>
<head>
	<title>{$title}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="{$base_path}images/favicon.ico">

	<link href="{$base_path}css/bootstrap.min.css" rel="stylesheet">
	<link href="{$base_path}css/theme.css" rel="stylesheet">

	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

	<script type="text/javascript" src="{$base_path}js/jquery.min.js"></script>
	<script type="text/javascript" src="{$base_path}js/script.js"></script>
	<script type="text/javascript" src="{$base_path}js/bootstrap.min.js"></script>
</head>
<body>
HTML;

