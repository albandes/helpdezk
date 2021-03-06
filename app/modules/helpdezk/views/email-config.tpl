<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{$title|default:'Helpdezk | Open Source'}</title>

    <!-- Mainly scripts -->
    {head_item type="js" src="$path/includes/js/" files="$jquery_version"}
    {head_item type="css" src="$path/includes/bootstrap/css/" files="bootstrap.min.css"}
    {head_item type="js"  src="$path/includes/bootstrap/js/" files="bootstrap.min.js"}
    <!-- jqGrid -->
    {head_item type="js"  src="$path/includes/js/plugins/jqGrid/i18n/" files="$jqgrid_i18nFile"}
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
    {head_item type="js" src="$path/app/modules/helpdezk/views/js/" files="email-config.js"}

    {*head_item type="css" src="$path/css/font-awesome/css/" files="font-awesome.css"*}
    {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
    {head_item type="css" src="$path/css/" files="animate.css"}

    {head_item type="css" src="$path/css/plugins/jQueryUI/" files="jquery-ui-1.10.4.custom.min.css"}
    {head_item type="css" src="$path/css/plugins/jqGrid/" files="ui.jqgrid.css"}
    {head_item type="css" src="$path/css/" files="$theme.css"}

    <!-- Icheck, used in checkbox and radio -->
    {head_item type="css" src="$path/css/plugins/iCheck/" files="custom.css"}
    {head_item type="js"  src="$path/includes/js/plugins/iCheck/" files="icheck.min.js"}

    <!-- Combo Autocomplete -->
    {head_item type="js" src="$path/includes/js/plugins/chosen/" files="chosen.jquery.js"}
    {head_item type="css" src="$path/css/plugins/chosen/" files="chosen.css"}

    <!-- Jquery Validate -->
    {head_item type="js"  src="$path/includes/js/plugins/validate/" files="jquery.validate.min.js"}

    {literal}
        <script type="text/javascript">
            var default_lang    = "{/literal}{$lang}{literal}",
                path            = "{/literal}{$path}{literal}",
                langName        = '{/literal}{$smarty.config.Name}{literal}',
                theme           = '{/literal}{$theme}{literal}',
                mascDateTime    = '{/literal}{$mascdatetime}{literal}',
                timesession     = '{/literal}{$timesession}{literal}',
                demoVersion     = '{/literal}{$demoversion}{literal}';
        </script>
        <style>
            /* Additional style to fix warning dialog position */
            #alertmod_table_list_tickets {
                top: 900px !important;
            }

            .scrollable-panel {
                height: 350px;
                overflow: auto;
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
                                <h5>{$smarty.config.cat_records} / {$smarty.config.Email_config} / <strong>{$smarty.config.Home}</strong></h5>
                            </div>

                            <div class="ibox-content">

                                <input type="hidden" name="_token" id= "_token" value="{$token}">
                                <button id="btnDisable" type="button" class="btn btn-primary btn-xs disabled"><i class="fa fa-lock" aria-hidden="true"></i>&nbsp;{$smarty.config.Deactivate}</button>
                                <button id="btnEnable" type="button" class="btn btn-primary btn-xs disabled"><i class="fa fa-unlock" aria-hidden="true"></i>&nbsp;{$smarty.config.Activate}</button>
                                <!--<button id="btnCreate" type="button" class="btn btn-primary btn-xs"><i class="fa fa-plus-circle" aria-hidden="true""></i>&nbsp;{$smarty.config.Template_add}</button>-->
                                <button id="btnUpdate" type="button" class="btn btn-primary btn-xs"><i class="fa fa-edit" aria-hidden="true""></i>&nbsp;{$smarty.config.Template_edit}</button>
                                

                                <p></p>

                                <div class="jqGrid_wrapper">
                                    <input type="hidden" name="_token" id= "_token" value="{$token}">
                                    <table id="table_list_email_confs"></table>
                                    <div id="pager_list_email_confs"></div>
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
    {include file='modals/hdkgroup/modal-attendant-group.tpl'}
    {include file='modals/hdkgroup/modal-group-service.tpl'}
    {include file='modals/hdkgroup/modal-group-repass.tpl'}


</body>

</html>
