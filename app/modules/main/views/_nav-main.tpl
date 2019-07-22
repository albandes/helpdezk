<nav class="navbar navbar-static-top" role="navigation">

    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

        {if $hashelpdezk }
            <a class="navbar-brand" href="{$helpdezkhome}" ><img style="max-width:90px; margin-top: -7px;" src="{$path}/app/uploads/logos/{$headerlogo}"  > </a>
        {/if}

        {* Aditional Modules *}
        {foreach from=$modules key=myId item=i}
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{$path}/{$i.path}/home" ><img style="max-width:90px; margin-top: -7px;" src="{$path}/app/uploads/logos/{$i.headerlogo}"  > </a>
        {/foreach}
    </div>

    <div class="navbar-collapse collapse" id="navbar">

        {if $featured_1 == 1}
            <ul class="nav navbar-nav">
                <li class="active">
                    <a aria-expanded="false" role="button" href="{$lnk_featured_1}"> {$featured_label_1} </a>
                </li>
            </ul>
        {/if}

        {if $featured_2 == 1}
            <ul class="nav navbar-nav">
                <li class="active">
                    <a aria-expanded="false" role="button" href="{$lnk_featured_2}"> {$featured_label_2} </a>
                </li>
            </ul>
        {/if}
        {*
        <ul class="nav navbar-top-links navbar-left">
            <li>
                <a href="{$logout}">
                    <i class="fa fa-sign-out"></i> Novo Ticket
                </a>
            </li>
        </ul>
        *}
        <ul class="nav navbar-nav">

            {if $displayMenu_1 == 1}
                <li class="dropdown">
                    <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Cadastros <span class="caret"></span></a>
                    <ul role="menu" class="dropdown-menu">
                        {$listMenu_1}
                    </ul>
                </li>
            {/if}

            <!--<li class="dropdown">
                <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Cadastros <span class="caret"></span></a>
                <ul role="menu" class="dropdown-menu">
                    <li><a href="http://10.42.44.200/helpdezk/helpdezk/hdkWarning/index">Cadastro de Avisos</a></li>
                    <li><a href="">Menu item</a></li>
                    <li><a href="">Menu item</a></li>
                    <li><a href="">Menu item</a></li>
                </ul>
            </li>-->


            {*
            <li class="dropdown">
                <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Menu item <span class="caret"></span></a>
                <ul role="menu" class="dropdown-menu">
                    <li><a href="">Menu item</a></li>
                    <li><a href="">Menu item</a></li>
                    <li><a href="">Menu item</a></li>
                    <li><a href="">Menu item</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Menu item <span class="caret"></span></a>
                <ul role="menu" class="dropdown-menu">
                    <li><a href="">Menu item</a></li>
                    <li><a href="">Menu item</a></li>
                    <li><a href="">Menu item</a></li>
                    <li><a href="">Menu item</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Menu item <span class="caret"></span></a>
                <ul role="menu" class="dropdown-menu">
                    <li><a href="">Menu item</a></li>
                    <li><a href="">Menu item</a></li>
                    <li><a href="">Menu item</a></li>
                    <li><a href="">Menu item</a></li>
                </ul>
            </li>
            *}

        </ul>


        <ul class="nav navbar-top-links navbar-right">
            <li>
                {$smarty.config.timeouttext}: <span id="numberCountdown"></span>
            </li>

            <li class="dropdown">
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                    <i class="fa fa-bell"></i>  <span class="label label-primary">{$total_warnings}</span>
                </a>
                <ul class="dropdown-menu dropdown-alerts">
                    <li>
                        <a href="mailbox.html">
                            <div>
                                <i class="fa fa-envelope fa-fw"></i> You have {$total_warnings} messages
                                <span class="pull-right text-muted small">4 minutes ago</span>
                            </div>
                        </a>
                    </li>
                    {*
                    <li class="divider"></li>
                    <li>
                        <a href="profile.html">
                            <div>
                                <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                                <span class="pull-right text-muted small">12 minutes ago</span>
                            </div>
                        </a>
                    </li>
                    *}
                    <li class="divider"></li>
                    <li>
                        <a href="grid_options.html">
                            <div>
                                <i class="fa fa-upload fa-fw"></i> Server Rebooted
                                <span class="pull-right text-muted small">2d 4h 12min ago</span>
                            </div>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <div class="text-center link-block">
                            <a href="notifications.html">
                                <strong>See All Alerts</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>


            <li>
                <a href="{$logout}">
                    <i class="fa fa-sign-out"></i> {$smarty.config.logout}
                </a>
            </li>


        </ul>


    </div>

</nav>