<?php

use Latte\Runtime as LR;

/** source: C:\xampp\htdocs\helpdezk/app/modules/main/views/footer.latte */
final class Template625095ddd6 extends Latte\Runtime\Template
{

	public function main(): array
	{
		extract($this->params);
		echo '<div class="row">
    <div class="footer">                    
        <div class="float-start">
            <strong>Copyright</strong> Pipegrep &copy; 2018-';
		echo LR\Filters::escapeHtmlText(date('Y')) /* line 4 */;
		echo '
        </div>
        <div class="float-end">
            <strong>';
		echo LR\Filters::escapeHtmlText($version) /* line 7 */;
		echo '</strong>
        </div>
    </div>
</div>';
		return get_defined_vars();
	}

}
