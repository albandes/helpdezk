<?php

use Latte\Runtime as LR;

/** source: C:\xampp\htdocs\helpdezk/app/modules/main/views/nav-main.latte */
final class Template8e53dfc500 extends Latte\Runtime\Template
{

	public function main(): array
	{
		extract($this->params);
		echo '<nav class="navbar navbar-expand-sm static-top" role="navigation">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>

        <ul id="gn-menu" class="navbar-nav gn-menu-main">
            <li class="gn-trigger">
                <a class="gn-icon gn-icon-menu "><span>Menu</span></a>
                <div class="gn-menu-wrapper">
                    <div class="gn-scroller">
                        <ul class="gn-menu">
                        </ul>
                    </div><!-- /gn-scroller -->
                </div>
            </li>
        </ul>

        <div class="collapse navbar-collapse " id="navbar">


        </div>
    </div>    

</nav>';
		return get_defined_vars();
	}

}
