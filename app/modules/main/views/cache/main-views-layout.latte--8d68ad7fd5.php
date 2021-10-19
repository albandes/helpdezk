<?php

use Latte\Runtime as LR;

/** source: /home/htdocs/versiontwo/helpdezk/app/modules/main/views/layout.latte */
final class Template8d68ad7fd5 extends Latte\Runtime\Template
{
	protected const BLOCKS = [
		['title' => 'blockTitle', 'content' => 'blockContent'],
	];


	public function main(): array
	{
		extract($this->params);
		echo '<!doctype html>
<html lang="';
		echo LR\Filters::escapeHtmlAttr($lang) /* line 2 */;
		echo '">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<title>';
		if ($this->getParentName()) {
			return get_defined_vars();
		}
		$this->renderBlock('title', get_defined_vars()) /* line 7 */;
		echo '</title>
	
	<!-- bootstrap -->
	<link href="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($path)) /* line 10 */;
		echo '/public/assets/css/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<script src="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($path)) /* line 11 */;
		echo '/public/assets/css/bootstrap/js/bootstrap.bundle.min.js" defer></script>

	<!-- helpdezk theme -->
	<link href="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($path)) /* line 14 */;
		echo '/public/assets/css/';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(!isset($theme) ? $_ENV['THEME'] : $theme)) /* line 14 */;
		echo '.css" rel="stylesheet">
	<link href="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($path)) /* line 15 */;
		echo '/public/assets/css/animate.css" rel="stylesheet">

	<!-- menu -->
	<link href="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($path)) /* line 18 */;
		echo '/public/assets/css/gn-menu/css/component.css" rel="stylesheet">
	<script src="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($path)) /* line 19 */;
		echo '/public/assets/js/plugins/gnmenu/classie.js" defer></script>
    <script src="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($path)) /* line 20 */;
		echo '/public/assets/js/plugins/gnmenu/gnmenu.js" defer></script>

	<!-- fontawesome -->
	<link rel="stylesheet" href="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($path)) /* line 23 */;
		echo '/public/assets/css/fontawesome/css/all.css" rel="stylesheet">
	
	<!-- jquery -->
	<script src="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($path)) /* line 26 */;
		echo '/public/assets/js/jquery-3.6.0.min.js"></script>

</head>

';
		$this->renderBlock('content', get_defined_vars()) /* line 30 */;
		echo '

</html>';
		return get_defined_vars();
	}


	/** {block title} on line 7 */
	public function blockTitle(array $ʟ_args): void
	{
		
	}


	/** {block content} on line 30 */
	public function blockContent(array $ʟ_args): void
	{
		
	}

}
