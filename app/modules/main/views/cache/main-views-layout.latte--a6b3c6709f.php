<?php

use Latte\Runtime as LR;

/** source: C:\xampp\htdocs\helpdezk/app/modules/main/views/layout.latte */
final class Templatea6b3c6709f extends Latte\Runtime\Template
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
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous" defer></script>

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
	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous">

</head>

';
		$this->renderBlock('content', get_defined_vars()) /* line 27 */;
		echo '

</html>';
		return get_defined_vars();
	}


	/** {block title} on line 7 */
	public function blockTitle(array $ʟ_args): void
	{
		
	}


	/** {block content} on line 27 */
	public function blockContent(array $ʟ_args): void
	{
		
	}

}
