<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Helpdezk | Parracho</title>

    {head_item type="js" src="$path/includes/js/" files="$jquery_version"}

    {head_item type="css" src="$path/includes/bootstrap/css/" files="bootstrap.min.css"}
    {head_item type="js"  src="$path/includes/bootstrap/js/" files="bootstrap.min.js"}

   <!-- {head_item type="js"  src="$path/js/" files="inspinia.js"}-->

    <!-- <link href="/parracho/font-awesome/css/font-awesome.css" rel="stylesheet"> -->



    <link href="/parracho/css/animate.css" rel="stylesheet">


    {head_item type="css" src="$path/css/" files="$theme.css"}

    {head_item type="js" src="$path/app/modules/admin/views/js/" files="login.js"}

    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}

    {literal}
    <script type="text/javascript">

        var default_lang = "{/literal}{$lang}{literal}",
                path = "{/literal}{$path}{literal}",
                langName = '{/literal}{$smarty.config.Name}{literal}',
                theme = '{/literal}{$theme}{literal}',
                timesession = '{/literal}{$timesession}{literal}';


    </script>
    {/literal}

</head>

<body class="gray-bg">

    <div class="loginColumns animated fadeInDown">
        <div class="row">

            <div class="col-lg-7">

                <img src="{$path}/app/uploads/logos/{$loginlogo}" height="{$height}px" width="{$width}px" />




                <h2 class="font-bold">{$smarty.config.important_notices}</h2>



                <!-- Grid -->

                <div class="row">
                    <div class="col-lg-3 parr_row5">
                        {$smarty.config.Topic}
                    </div>
                    <div class="col-lg-9 parr_row5">
                        {$smarty.config.Subject}
                    </div>
                </div>

                <hr class="parr_line1">

                <!--      -->
                {if $warning|@count > 0}
                    {foreach from=$warning key=k item=v}
                        <div class="row">
                            <div class="col-lg-3">
                                <a href="{$v.idmessage}" id="sign_up" data-toggle="modal" class="just_for_reference">{$v.title_topic}</a> &nbsp;
                            </div>
                            <div class="col-lg-7 text-leftt">
                                <a href="{$v.idmessage}" id="sign_up-1" data-toggle="modal" class="just_for_reference">{$v.title_warning}</a>
                            </div>
                        </div>
                    {/foreach}
                {else}
                    {$smarty.config.No_result}
                {/if}

                <!--     -->

            </div>
            <div class="col-lg-5">

                <div class="ibox-content">

                    <div id="response"></div>

                    <form class="m-t" role="form" id="frm-login" action="action="javascript:;">
                        <div class="form-group">
                            <input name="login" type="text" class="form-control" placeholder="{$smarty.config.Login}" required="">
                        </div>
                        <div class="form-group">
                            <input name="password" type="password" class="form-control" placeholder="{$smarty.config.Password}" required="">
                        </div>


                        <div id="secret" class="form-group"></div>

                        <br><br><br><br><br><br>
                        <button  type="submit" class="btn btn-primary block full-width m-b">Login</button>

                        <p class="text-muted text-right ">
                            <a href="#modal-form-lost-password" data-toggle="modal"   id="lost_password">
                                <small>{$smarty.config.Lost_password}?</small>
                            </a>
                        </p>
                        <!--
                        <p class="text-muted text-center ">
                            <small>Do not have an account?</small>
                        </p>

                        <a class="btn btn-sm btn-white btn-block" href="register.html" >Create an account</a>
                        -->

                    </form>
                    <p class="m-t text-center">
                        <small>Version: {$version}</small>
                    </p>
                </div>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-6">
                Copyright Pipegrep IP Connectivity
            </div>
            <div class="col-md-6 text-right">
               <small>© 2018-2020</small>
            </div>
        </div>
    </div>

    {include file='modals/login/modalWarning.tpl'}
    {include file='modals/login/lostPassword.tpl'}


</body>

</html>
