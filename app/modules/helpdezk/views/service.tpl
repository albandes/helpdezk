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
    {head_item type="js" src="$path/app/modules/helpdezk/views/js/" files="service.js"}

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
            var default_lang = "{/literal}{$lang}{literal}",
                    path = "{/literal}{$path}{literal}",
                    langName = '{/literal}{$smarty.config.Name}{literal}',
                    theme = '{/literal}{$theme}{literal}',
                    mascDateTime = '{/literal}{$mascdatetime}{literal}',
                    timesession = '{/literal}{$timesession}{literal}';
        </script>
        <style>
            /* Additional style to fix warning dialog position */
            #alertmod_table_list_tickets {
                top: 900px !important;
            }

            .scrollable-services {
                max-height: 617px;
                overflow: auto;
            }

            .scrollable-types-itens {
                max-height: 200px;
                overflow: auto;
            }

            .btn-valign {
                padding-top:5px;
            }

            .scrollable-table-body {
                height: 200px !important;
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
                                <h5>Cadastros / <strong>{$smarty.config.pgr_services}</strong></h5>

                            </div>

                            <div class="ibox-content">

                                <input type="hidden" name="_token" id= "_token" value="{$token}">
                                <button id="btnNewArea" type="button" class="btn btn-primary btn-xs"><i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;{$smarty.config.Area_insert}</button>
                                <button id="btnNewType" type="button" class="btn btn-primary btn-xs"><i class="fa fa-plus-circle" aria-hidden="true""></i>&nbsp;{$smarty.config.Type_insert}</button>
                                <button id="btnConfigApprv" type="button" class="btn btn-primary btn-xs"><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;{$smarty.config.conf_approvals}</button>

                                <div class="col-sm-12 white-bg" style="height:20px;"></div>

                                <div class="row white-bg ">
                                    <div class="col-sm-6 b-l">
                                        <div class="panel-group">
                                            <div id="panelSearch" class="panel panel-primary">
                                                <div id= "tab-services" class="scrollable-services">{$tabservices}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 b-l">
                                        <div class="col-sm-12">
                                            <div class="panel-group">
                                                <div id="panelTypes" class="panel panel-primary hide">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title" id="typeTitle"></h4>
                                                    </div>
                                                    <div>&nbsp;</div>
                                                    <div style='padding-left:5px;'>
                                                        <input name="idtypeHide" type="hidden" id="idtypeHide" value="" />
                                                        <button id="btnNewItem" type="button" class="btn btn-default btn-xs">
                                                            <i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;{$smarty.config.Add_item}
                                                        </button>
                                                    </div>
                                                    <div>&nbsp;</div>
                                                    <div id="tab-type-itens" class="scrollable-types-itens"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="panel-group">
                                                <div id="panelItens" class="panel panel-primary hide">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title" id="itemTitle"></h4>
                                                    </div>
                                                    <div>&nbsp;</div>
                                                    <div style='padding-left:5px;'>
                                                        <input name="iditemHide" type="hidden" id="iditemHide" value="" />
                                                        <button id="btnNewService" type="button" class="btn btn-default btn-xs">
                                                            <i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;{$smarty.config.Add_service}
                                                        </button>
                                                    </div>
                                                    <div>&nbsp;</div>
                                                    <div id="tab-itens-services" class="scrollable-types-itens"></div>
                                                </div>
                                            </div>
                                        </div>
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
    {include file='modals/hdkservice/modal-area.tpl'}
    {include file='modals/hdkservice/modal-area-update.tpl'}
    {include file='modals/hdkservice/modal-type.tpl'}
    {include file='modals/hdkservice/modal-item.tpl'}
    {include file='modals/hdkservice/modal-service.tpl'}
    {include file='modals/hdkservice/modal-conf-approve.tpl'}
    {include file='modals/hdkservice/modal-dialog-delete.tpl'}


</body>

</html>
