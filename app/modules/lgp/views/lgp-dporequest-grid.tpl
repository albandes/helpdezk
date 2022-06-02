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
    <!-- Jquery UI -->
    {head_item type="js"  src="$path/js/plugins/jquery-ui/" files="jquery-ui.min.js"}
    <!-- Helpdezk -->
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/app/modules/lgp/views/js/" files="lgp-dporequest-grid.js"}

    {*head_item type="css" src="$path/css/font-awesome/css/" files="font-awesome.css"*}
    {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
    {head_item type="css" src="$path/css/" files="animate.css"}

    {head_item type="css" src="$path/css/plugins/jQueryUI/" files="jquery-ui-1.10.4.custom.min.css"}
    {head_item type="css" src="$path/css/plugins/jqGrid/" files="ui.jqgrid.css"}
    {head_item type="css" src="$path/css/" files="$theme.css"}

    <!-- Input Mask-->
    {head_item type="js" src="$path/includes/js/plugins/jquery-mask/" files="jquery.mask.min.js"}

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
                access = {/literal}{$access}{literal},
                datepickerOpts = {/literal}{$datepickerOpts}{literal},
                dtSearchFmt = '{/literal}{$dtSearchFmt}{literal}';
        </script>
        <style>
            /* Additional style to fix warning dialog position */
            #alertmod_table_list_tickets {
                top: 900px !important;
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
                                <h5>{$smarty.config.cat_records} / {$smarty.config.pgr_dpo_request} / <strong>{$smarty.config.Home}</strong></h5>

                            </div>

                            <div class="ibox-content">

                                <input type="hidden" name="_token" id= "_token" value="{$token}">
                                
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="input-group input-group-sm">
                                            <input type="text" id="txtSearch" name="txtSearch" class="form-control" placeholder="{$smarty.config.insert_name_ssn_email}">
                                            <span class="input-group-btn">
                                                <button id="btnSearch" type="button" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <button id="btnNewTicket" type="button" class="btn btn-primary btn-sm"><i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;{$smarty.config.Tck_new_ticket}</button>
                                    </div>                                
                                    
                                </div> <br>
                                
                                <div class="jqGrid_wrapper">
                                    <table id="table_list_lgpticket"></table>
                                    <div id="pager_list_lgpticket"></div>
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


</body>

</html>
