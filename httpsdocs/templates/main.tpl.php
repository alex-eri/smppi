<?php

/*
 * templates/main.tpl.php
 */

include_once("templates/header.tpl.php");
include_once("templates/footer.tpl.php");

$html = $header_html;

$exit = BTN_EXIT;
$base_path = BASE_PATH;

$html .= <<<HTML
		
		<div class="navbar navbar-inverse" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Навигация</span> <span class="icon-bar"></span>
						<span class="icon-bar"></span> <span class="icon-bar"></span>
					</button>
					<span class="navbar-brand" href="{$base_path}">{$title_head}</span>
				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						{$incoming_menu}
						{$outgoing_menu}
						{$send_menu}
						{$adm_menu}
					</ul>
					<form class="navbar-form navbar-right" role="exit" method="post">
							<span class="label label-primary">{$webuser}</span>
							<input type="hidden" name="exit" value="exit">
							<button type="submit" class="btn btn-danger">{$exit}</button>
					</form>
					</div>
				</div>
				<!--/.nav-collapse -->
			</div>
		</div>
	<div class="container-fluid theme-showcase" role="main">
		{$content_page}
	</div>

HTML;

$html .= $footer_html;
