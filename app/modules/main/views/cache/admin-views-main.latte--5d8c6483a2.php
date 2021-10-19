<?php

use Latte\Runtime as LR;

/** source: /home/htdocs/versiontwo/helpdezk/app/modules/admin/views/main.latte */
final class Template5d8c6483a2 extends Latte\Runtime\Template
{
	protected const BLOCKS = [
		['title' => 'blockTitle', 'content' => 'blockContent'],
	];


	public function main(): array
	{
		extract($this->params);
		if ($this->getParentName()) {
			return get_defined_vars();
		}
		$this->renderBlock('title', get_defined_vars()) /* line 2 */;
		echo '

';
		$this->renderBlock('content', get_defined_vars()) /* line 4 */;
		echo "\n";
		return get_defined_vars();
	}


	public function prepare(): void
	{
		extract($this->params);
		$this->parentName = $layout;
		
	}


	/** {block title} on line 2 */
	public function blockTitle(array $ʟ_args): void
	{
		extract($this->params);
		extract($ʟ_args);
		unset($ʟ_args);
		echo LR\Filters::escapeHtmlText($_ENV['PAGE_TITLE']) /* line 2 */;
		
	}


	/** {block content} on line 4 */
	public function blockContent(array $ʟ_args): void
	{
		extract($this->params);
		extract($ʟ_args);
		unset($ʟ_args);
		echo '
<body class="top-navigation">
	<div id="page-wrapper" class="gray-bg">		
		<div class="row border-bottom white-bg">
';
		$this->createTemplate($navBar, $this->params, 'include')->renderToContentType('html') /* line 9 */;
		echo '        </div>

        <div class="wrapper wrapper-content">
            <div class="row border-bottom white-bg dashboard-header">
                <div class="col-sm-5">
                    <h2>Admin</h2>
                </div>
            </div>
        </div>

';
		$this->createTemplate($footer, $this->params, 'include')->renderToContentType('html') /* line 20 */;
		echo '	</div>
</body>

';
	}

}
