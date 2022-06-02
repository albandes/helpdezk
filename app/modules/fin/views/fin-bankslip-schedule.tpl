<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Helpdezk | Parracho</title>

        <!-- Mainly scripts -->
        {head_item type="js" src="$path/includes/js/" files="$jquery_version"}
        {head_item type="css" src="$path/includes/bootstrap/css/" files="bootstrap.min.css"}
        {head_item type="js"  src="$path/includes/bootstrap/js/" files="bootstrap.min.js"}
        <!-- jqGrid -->
        {head_item type="js"  src="$path/includes/js/plugins/jqGrid/i18n/" files="grid.locale-pt-br.js"}
        {head_item type="js"  src="$path/includes/js/plugins/jqGrid/" files="jquery.jqGrid.min.js"}
        <!-- Custom and plugin javascript -->
        {head_item type="js"  src="$path/js/" files="inspinia.js"}
        {head_item type="js"  src="$path/js/plugins/pace/" files="pace.min.js"}

        {head_item type="css" src="$path/css/gn-menu/css/" files="component.css"}
        {head_item type="js"  src="$path/includes/js/plugins/gnmenu/" files="classie.js"}
        {head_item type="js"  src="$path/includes/js/plugins/gnmenu/" files="gnmenu.js"}

        {head_item type="js"  src="$path/includes/js/plugins/modernizr/" files="modernizr.custom.js"}

        {head_item type="css" src="$path/css/" files="admmenu.css"}
        <!-- Jquery UI -->
        {head_item type="js"  src="$path/js/plugins/jquery-ui/" files="jquery-ui.min.js"}
        <!-- Helpdezk -->
        {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
        {head_item type="js" src="$path/includes/js/" files="default.js"}
        {head_item type="js" src="$path/app/modules/fin/views/js/" files="bankslipschedule.js"}

        {*head_item type="css" src="$path/css/font-awesome/css/" files="font-awesome.css"*}
        {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
        {head_item type="css" src="$path/css/" files="animate.css"}

        {head_item type="css" src="$path/css/plugins/jQueryUI/" files="jquery-ui-1.10.4.custom.min.css"}
        {head_item type="css" src="$path/css/plugins/jqGrid/" files="ui.jqgrid.css"}
        {head_item type="css" src="$path/css/" files="$theme.css"}

        <!-- Input Mask-->
        {head_item type="js" src="$path/includes/js/plugins/jquery-mask/" files="jquery.mask.min.js"}

        <!-- Datapicker  -->
        {head_item type="css" src="$path/css/plugins/datepicker/" files="datepicker3.css"}
        {head_item type="js"  src="$path/includes/js/plugins/datepicker/" files="bootstrap-datepicker.js"}
        {head_item type="js"  src="$path/includes/js/plugins/datepicker/locales/" files="bootstrap-datepicker.pt-BR.min.js"}

        {literal}
        <script type="text/javascript">
            var default_lang = "{/literal}{$lang}{literal}",
                path = "{/literal}{$path}{literal}",
                langName = '{/literal}{$smarty.config.Name}{literal}',
                theme = '{/literal}{$theme}{literal}',
                mascDateTime = '{/literal}{$mascdatetime}{literal}',
                timesession = '{/literal}{$timesession}{literal}',
                demoVersion = '{/literal}{$demoversion}{literal}',
                string_array = '{/literal}{$string_array}{literal}',
                access = {/literal}{$access}{literal};
        </script>
        <style>
            /* Additional style to fix warning dialog position */
            #alertmod_table_list_tickets {
                top: 900px !important;
            }
            .modal-dialog {
                width: 90%;
            }

        </style>

        {/literal}

    </head>

    <body class="top-navigation">
        <div id="wrapper">

            <div id="page-wrapper" class="gray-bg">

                <div class="row border-bottom white-bg">
                    {include file=$navBar}
                </div>

                <div class="wrapper wrapper-content animated fadeInRight ">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="ibox ">

                                <div class="ibox-title">
                                    <h5>{$smarty.config.cat_utils} / {$smarty.config.pgr_fin_bankslip_email} / <strong>{$smarty.config.Home}</strong></h5>

                                </div>

                                <div class="ibox-content">
                                    <div class="row wrapper">
                                        <div class="col-lg-6">
                                            <input type="hidden" name="_token" id= "_token" value="{$token}">
                                            <button id="btnCreate" type="button" class="btn btn-primary btn-xs"><i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;{$smarty.config.schedule_email_sending}</button>
                                            <button id="btnListEmail" type="button" class="btn btn-primary btn-xs"><i class="fa fa-list-alt" aria-hidden="true"></i>&nbsp;{$smarty.config.list_emails}</button>
                                        </div>
                                    </div>

                                    <div class="row wrapper">&nbsp;</div>

                                    <div class="row wrapper">
                                        <div class="jqGrid_wrapper">
                                            <table id="table_list_schedules"></table>
                                            <div id="pager_list_schedules"></div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="footer">
                    {include file=$footer}
                </div>

            </div>

        </div>

        <!-- Modal -->
        {include file='modals/main/modal-alert.tpl'}
        {include file='modals/emails/modal-view-email.tpl'}
        {include file='modals/emails/modal-email-company.tpl'}

    </body>
</html>