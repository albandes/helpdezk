<nav class="navbar navbar-static-top" role="navigation">


    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="fa fa-chevron-circle-down"></span>
            <!--<span class="icon-bar"></span>
            <span class="icon-bar"></span>-->
        </button>

        <ul id="gn-menu" class="navbar-nav gn-menu-main">
            <li class="gn-trigger">
                <a class="gn-icon gn-icon-menu "><span>Menu</span></a>
                <div class="gn-menu-wrapper">
                    <div class="gn-scroller">
                        <ul class="gn-menu">
                            {if $isroot}
                                <li>
                                    <a class="gn-icon" href="{$adminhome}">
                                        <img style="max-width:60px;" src="{$path}/app/uploads/logos/{$adminlogo}"  >
                                        &nbsp;&nbsp;Admin
                                    </a>
                                </li>
                            {else}
                                {if $hashelpdezk }
                                    <li>
                                        <a class="gn-icon" href="{$helpdezkhome}">
                                            <img style="max-width:60px;" src="{$path}/app/uploads/logos/{$headerlogo}"  >
                                            &nbsp;&nbsp;HelpDEZK
                                        </a>
                                    </li>
                                {/if}
                                {if $hasadmin }
                                    <li>
                                        <a class="gn-icon" href="{$adminhome}">
                                            <img style="max-width:60px;" src="{$path}/app/uploads/logos/{$adminlogo}"  >
                                            &nbsp;&nbsp;Admin
                                        </a>
                                    </li>
                                {/if}
                                {foreach from=$modules key=myId item=i}
                                    <li>
                                        <a class="gn-icon" href="{$path}/{$i.path}/home">
                                            <img style="max-width:60px;" src="{$path}/app/uploads/logos/{$i.headerlogo}"  >
                                            &nbsp;&nbsp;{$i.varsmarty}
                                        </a>
                                    </li>
                                {/foreach}

                            {/if}
                        </ul>
                    </div><!-- /gn-scroller -->
                </div>
            </li>
        </ul>

    </div>

    <div class="navbar-collapse collapse" id="navbar">
        <!-- active module -->
        <a class="nav navbar-nav" href="{$path}/{$modulePath}/home" ><img style="max-width:90px; margin-top: 5px;" src="{$path}/app/uploads/logos/{$moduleLogo}"  > </a>

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
                    <a aria-expanded="false" role="button" id="btnNewTck" href="{$lnk_featured_2}"> {$featured_label_2} </a>
                </li>
            </ul>
        {/if}

        {if $featured_3 == 1}
            <ul class="nav navbar-nav">
                <li class="active">
                    <a aria-expanded="false" role="button" id="btnNewTck" href="{$lnk_featured_3}"> {$featured_label_3} </a>
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
            <!-- Modules Menu -->
            {$listMenu_1}

        </ul>

        {if $displayMenu_Adm == 1}
            <ul class="nav navbar-nav">
                <!-- Admin Menu  -->
                <li class='dropdown'>
                    <a aria-expanded='false' role='button' href='#' class='dropdown-toggle' data-toggle='dropdown'>Módulos<span class='caret'></span></a>
                    <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
                        {$listMenu_Adm}
                    </ul>
                </li>
            </ul>
        {/if}

        <ul class="nav navbar-top-links navbar-right">
            <li>
                {$smarty.config.timeouttext}: <span id="numberCountdown"></span>
            </li>

            <!--<li class="dropdown">
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
            </li>-->


            <li>
                <a href="{$logout}">
                    <i class="fa fa-sign-out"></i> {$smarty.config.logout}
                </a>
            </li>


        </ul>


    </div>

</nav>