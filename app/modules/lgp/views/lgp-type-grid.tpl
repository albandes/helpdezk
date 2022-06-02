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
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/app/modules/lgp/views/js/" files="lgp-type-grid.js"}

    {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
    {head_item type="css" src="$path/css/" files="animate.css"}

    {head_item type="css" src="$path/css/plugins/jQueryUI/" files="jquery-ui-1.10.4.custom.min.css"}
    {head_item type="css" src="$path/css/plugins/jqGrid/" files="ui.jqgrid.css"}
    {head_item type="css" src="$path/css/" files="$theme.css"}

     <!-- Datapicker  -->
    {head_item type="css" src="$path/css/plugins/datepicker/" files="datepicker3.css"}
    {head_item type="js"  src="$path/includes/js/plugins/datepicker/" files="bootstrap-datepicker.js"}
    {if $dtpickerLocale != ''}
        {head_item type="js"  src="$path/includes/js/plugins/datepicker/locales/" files="$dtpickerLocale"}
    {/if}

    <!-- Moment -->
    {head_item type="js"  src="$path/includes/js/plugins/moment/" files="moment-with-locales.min.js"}

    <!-- Input Mask-->
    {head_item type="js" src="$path/includes/js/plugins/jquery-mask/" files="jquery.mask.min.js"}
    

    {literal}
    <script type="text/javascript">
         var    default_lang = "{/literal}{$lang}{literal}",
                path = "{/literal}{$path}{literal}",
                langName = '{/literal}{$smarty.config.Name}{literal}',
                theme = '{/literal}{$theme}{literal}',
                mascDateTime = '{/literal}{$mascdatetime}{literal}',
                timesession = '{/literal}{$timesession}{literal}',
                noteAttMaxFiles = '{/literal}{$noteattmaxfiles}{literal}',
                noteAcceptedFiles = '{/literal}{$noteacceptedfiles}{literal}',
                ticketAttMaxFiles = '{/literal}{$ticketattmaxfiles}{literal}',
                ticketAcceptedFiles = '{/literal}{$ticketacceptedfiles}{literal}',
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

        hr {
            height: 1px;
            margin-left: 15px;
            margin-bottom:-5px;
        }
        .hr-warning{
            background-image: -webkit-linear-gradient(left, rgba(210,105,30,.8), rgba(210,105,30,.6), rgba(0,0,0,0));
        }
        .hr-success{
            background-image: -webkit-linear-gradient(left, rgba(15,157,88,.8), rgba(15, 157, 88,.6), rgba(0,0,0,0));
        }
        .hr-primary{
            background-image: -webkit-linear-gradient(left, rgba(66,133,244,.8), rgba(66, 133, 244,.6), rgba(0,0,0,0));
        }
        .hr-danger{
            background-image: -webkit-linear-gradient(left, rgba(244,67,54,.8), rgba(244,67,54,.6), rgba(0,0,0,0));
        }

        .breadcrumb {
            background: rgba(245, 245, 245, 0);
            border: 0px solid rgba(245, 245, 245, 1);
            border-radius: 25px;
            display: block;
        }

        /*
         * Adjust Bootstrap Tooltip Width
         * https://stackoverflow.com/questions/36263249/adjust-bootstrap-tooltip-width
         */
        .tooltip-inner {
            max-width: 100% !important;
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
                                <h5>{$smarty.config.cat_records} / {$smarty.config.pgr_type} / <strong>{$smarty.config.Home}</strong></h5>

                            </div>

                            <div class="ibox-content">

                                <button id="btnCreate" type="button" class="btn btn-primary btn-xs {$display_btn_add}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;{$smarty.config.New}</button>
                                <button id="btnUpdate" type="button" class="btn btn-primary btn-xs {$display_btn_edit}"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{$smarty.config.edit}</button>
                                <button id="btnEcho"   type="button" class="btn btn-primary btn-xs"><i class="fa fa-eye" aria-hidden="true"></i>&nbsp;{$smarty.config.Grid_view}</button>
                                <button id="btnDisable"type="button" class="btn btn-primary btn-xs disabled"><i class="fa fa-lock" aria-hidden="true"></i>&nbsp;{$smarty.config.Deactivate}</button>
                                <button id="btnEnable" type="button" class="btn btn-primary btn-xs disabled"><i class="fa fa-unlock" aria-hidden="true"></i>&nbsp;{$smarty.config.Activate}</button>

                                <p></p>

                                <div class="jqGrid_wrapper">
                                    <table id="table_list_type"></table>
                                    <div id="pager_list_type"></div>
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


    {include file='modals/main/modal-alert.tpl'}




</body>

</html>