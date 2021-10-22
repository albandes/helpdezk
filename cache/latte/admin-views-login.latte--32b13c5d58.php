<?php

use Latte\Runtime as LR;

/** source: /home/htdocs/versiontwo/helpdezk/app/modules/admin/views/login.latte */
final class Template32b13c5d58 extends Latte\Runtime\Template
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
		if (!$this->getReferringTemplate() || $this->getReferenceType() === "extends") {
			foreach (array_intersect_key(['k' => '21', 'v' => '21'], $this->params) as $ʟ_v => $ʟ_l) {
				trigger_error("Variable \$$ʟ_v overwritten in foreach on line $ʟ_l");
			}
		}
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
		echo '<body class="gray-bg">
    
    <div class="loginColumns animated fadeInDown">
        <div class="row">
            <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                <img src="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($loginLogoUrl)) /* line 10 */;
		echo '" height="';
		echo LR\Filters::escapeHtmlAttr($loginheight) /* line 10 */;
		echo 'px" width="';
		echo LR\Filters::escapeHtmlAttr($loginwidth) /* line 10 */;
		echo 'px">
                <h2 class="font-bold">';
		echo LR\Filters::escapeHtmlText(($this->filters->translate)('Important_notices')) /* line 11 */;
		echo '</h2>

                <div class="row">
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 parr_row5">';
		echo LR\Filters::escapeHtmlText(($this->filters->translate)('Topic')) /* line 14 */;
		echo '</div>
                    <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 parr_row5">';
		echo LR\Filters::escapeHtmlText(($this->filters->translate)('Subject')) /* line 15 */;
		echo '</div>
                </div>

                <hr class="parr_line1">

';
		if (($this->filters->length)($warning) > 0) /* line 20 */ {
			$iterations = 0;
			foreach ($warning as $k=>$v) /* line 21 */ {
				echo '                        <div class="row">
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                <a href="';
				echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($v->idmessage)) /* line 24 */;
				echo '" id="sign_up" data-toggle="modal" class="just_for_reference">';
				echo LR\Filters::escapeHtmlText($v->title_topic) /* line 24 */;
				echo '</a> &nbsp;
                            </div>
                            <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 text-start">
                                <a href="';
				echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($v->idmessage)) /* line 27 */;
				echo '" id="sign_up-1" data-toggle="modal" class="just_for_reference">';
				echo LR\Filters::escapeHtmlText($v->title_warning) /* line 27 */;
				echo '</a>
                            </div>
                        </div>
';
				$iterations++;
			}
		}
		else /* line 31 */ {
			echo '                    ';
			echo LR\Filters::escapeHtmlText(($this->filters->translate)('No_notices')) /* line 32 */;
			echo "\n";
		}
		echo '            </div>
            
            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">

                <div class="ibox-content">

                    <div id="response"></div>

                    <form class="m-t" role="form" id="frm-login" action="javascript:;" >
                        <div class="form-group">
                            <input name="login" type="text" class="form-control" placeholder="';
		echo LR\Filters::escapeHtmlAttr(($this->filters->translate)('Login')) /* line 44 */;
		echo '" required="">
                        </div>
                        <div class="form-group">
                            <input name="password" type="password" class="form-control" placeholder="';
		echo LR\Filters::escapeHtmlAttr(($this->filters->translate)('Password')) /* line 47 */;
		echo '" required="">
                        </div>
                        
                        <button  type="submit" class="btn btn-primary block full-width m-b">Login</button>

                        <p class="text-muted text-end">
                            <a href="#modal-form-lost-password" data-toggle="modal"   id="lost_password">
                                <small>';
		echo LR\Filters::escapeHtmlText(($this->filters->translate)('Lost_password')) /* line 54 */;
		echo '?</small>
                            </a>
                        </p>

                        <!-- -->
';
		if ($demoversion == 1) /* line 59 */ {
			echo '                        <div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="panel panel-success">
                                        <div class="panel-heading">
                                            Demo Version
                                        </div>
                                        <div class="panel-body">
                                            <b>Demo User</b><br>
                                            login: user<br>
                                            password: 1234<br>
                                            <br>
                                            <b>Operator User</b><br>
                                            login: operator<br>
                                            password: 1234<br>
                                            <br>
                                            <b>Admin User</b><br>
                                            login: admin<br>
                                            password: 1234<br>

                                            <br>
                                            <p class="text-muted text-center ">
                                                <small>In the demo version, password exchange, some features and email sending are disabled.</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
';
		}
		else /* line 89 */ {
			echo '                        <br><br><br><br><br><br>
';
		}
		echo '                    </form>
                    <p class="m-t text-center">
                        <small>Version: ';
		echo LR\Filters::escapeHtmlText($version) /* line 94 */;
		echo '</small>
                    </p>
                </div>
            </div>

            <hr>
            <div class="row">
                <div class="col-md-6">Copyright Pipegrep IP Connectivity</div>
                <div class="col-md-6 text-end"><small>© 2018-';
		echo LR\Filters::escapeHtmlText(date('Y')) /* line 102 */;
		echo '</small></div>
            </div>

        </div>
    </div>
    <script src="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($path)) /* line 107 */;
		echo '/app/modules/admin/views/js/login.js"></script>
</body>
';
	}

}
