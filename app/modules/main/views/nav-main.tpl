<!-- Input Mask-->
{head_item type="js" src="$path/includes/js/plugins/jquery-mask/" files="jquery.mask.min.js"}

<!-- Autocomplete -->
{head_item type="js" src="$path/includes/js/plugins/autocomplete/" files="jquery.autocomplete.js"}
{head_item type="css" src="$path/includes/js/plugins/autocomplete/" files="jquery.autocomplete.css"}

<!-- Jquery Validate -->
{head_item type="js"  src="$path/includes/js/plugins/validate/" files="jquery.validate.min.js"}

<!-- Dropzone  -->
{head_item type="js"  src="$path/includes/js/plugins/dropzone/" files="dropzone.js"}
{head_item type="css" src="$path/css/plugins/dropzone/" files="basic.css"}
{head_item type="css" src="$path/css/plugins/dropzone/" files="pipe.dropzone.css"}

<!-- Combo Autocomplete -->
{head_item type="js" src="$path/includes/js/plugins/chosen/" files="chosen.jquery.js"}
{head_item type="css" src="$path/css/plugins/chosen/" files="chosen.css"}

{head_item type="js" src="$path/app/modules/main/views/js/" files="nav-main.js"}

{literal}
<script type="text/javascript">
    var     id_mask      = '{/literal}{$id_mask}{literal}',
        ein_mask     = '{/literal}{$ein_mask}{literal}',
        zip_mask     = '{/literal}{$zip_mask}{literal}',
        phone_mask     = '{/literal}{$phone_mask}{literal}',
        cellphone_mask     = '{/literal}{$cellphone_mask}'{literal},
        changepass = '{/literal}{$changepass}'{literal};


</script>
{/literal}

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
                                            <img style="max-width:100px;" src="{$headerlogo_url}"  >
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
        <a class="nav navbar-nav" href="{$path}/{$modulePath}/home" ><img style="max-width:90px; margin-top: 5px;" src="{$moduleLogo_url}" height="{$headerheight}px" width="{$headerwidth}px"  > </a>

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
                    <a aria-expanded='false' role='button' href='#' class='dropdown-toggle' data-toggle='dropdown'>{$smarty.config.Modules}<span class='caret'></span></a>
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


            <!--<li>
                <a href="{$logout}">
                    <i class="fa fa-sign-out-alt"></i> {$smarty.config.logout}
                </a>
            </li>-->
            <li class="dropdown">
                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                    </span> <span class="text-muted text-xs block">{$navlogin} <b class="caret"></b></span> </span>
                    <!--<img alt="image" class="img-circle img-thumbnail" src="{$person_photo}" />-->
                </a>
                <ul class="dropdown-menu animated fadeInRight m-t-xs">
                    {if !$isroot}
                        <li><a href="#" class="btnEditUserProfile"><i class="far fa-user"></i> {$smarty.config.user_profile}</a></li>
                        <li><a href="#" id="btnEditUserConfigExternal"><i class="fas fa-user-cog"></i> {$smarty.config.user_external_settings}</a></li>
                        <li><a href="#" class="btnEditUserPass"><i class="fa fa-key"></i> {$smarty.config.Change_password}</a></li>
                    {else}
                        <li>&nbsp;</li>
                    {/if}
                    <!--<li><a href=""> </a></li>
                    <li><a href=""> </a></li>-->
                    <li class="divider"></li>
                    <li>
                        <a href="{$logout}"> <i class="fa fa-sign-out-alt"></i> {$smarty.config.logout}</a>
                    </li>
                </ul>
            </li>


        </ul>


    </div>

</nav>

