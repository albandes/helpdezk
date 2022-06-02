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
    {head_item type="js" src="$path/app/modules/hur/views/js/" files="candidate.js"}

    {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
    {head_item type="css" src="$path/css/" files="animate.css"}

    {head_item type="css" src="$path/css/plugins/jQueryUI/" files="jquery-ui-1.10.4.custom.min.css"}
    {head_item type="css" src="$path/css/plugins/jqGrid/" files="ui.jqgrid.css"}
    {head_item type="css" src="$path/css/" files="$theme.css"}

    <!-- Autocomplete -->
    {head_item type="js" src="$path/includes/js/plugins/autocomplete/" files="jquery.autocomplete.js"}
    {head_item type="css" src="$path/includes/js/plugins/autocomplete/" files="jquery.autocomplete.css"}

    <!-- Combo Autocomplete -->
    {head_item type="js" src="$path/includes/js/plugins/chosen/" files="chosen.jquery.js"}
    {head_item type="css" src="$path/css/plugins/chosen/" files="chosen.css"}

    <!-- Datapicker  -->
    {head_item type="css" src="$path/css/plugins/datepicker/" files="datepicker3.css"}
    {head_item type="js"  src="$path/includes/js/plugins/datepicker/" files="bootstrap-datepicker.js"}
    {head_item type="js"  src="$path/includes/js/plugins/datepicker/locales/" files="bootstrap-datepicker.pt-BR.min.js"}

    <!-- Moment -->
    {head_item type="js"  src="$path/includes/js/plugins/moment/" files="moment-with-locales.min.js"}

    <!-- Input Mask-->
    {head_item type="js" src="$path/includes/js/plugins/jquery-mask/" files="jquery.mask.min.js"}


    {literal}
        <script type="text/javascript">
            var default_lang = "{/literal}{$lang}{literal}",
                path = "{/literal}{$path}{literal}",
                langName = '{/literal}{$smarty.config.Name}{literal}',
                theme = '{/literal}{$theme}{literal}',
                mascDateTime = '{/literal}{$mascdatetime}{literal}',
                mascDate = '{/literal}{$mascdate}{literal}',
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
                                <h5>Cadastros / Curr&iacute;culos / <strong>Home</strong> </h5>

                            </div>

                            <div class="ibox-content">

                                <div class="row">
                                    <div class="col-sm-1" style="padding-right: 0px">
                                        <button id="btnAll" type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh" aria-hidden="true""></i>&nbsp;Todos</button>
                                    </div>
                                    <div class="col-sm-2" style="padding-right: 5px;padding-left: 1px;">
                                        <select class="form-control input-sm" id="cmbArea" name="cmbArea" data-placeholder="Área" >
                                            {html_options values=$areaids output=$areavals selected=$idarea}
                                        </select>
                                    </div>
                                    <div class="col-sm-2" style="padding-right: 7px;padding-left: 5px;">
                                        <select class="form-control input-sm" id="cmbRole" name="cmbRole" data-placeholder="Cargo" >
                                            {html_options values=$roleids output=$rolevals selected=$idrole}
                                        </select>
                                    </div>
                                    <!--<button id="btnPrint" type="button" class="btn btn-default btn-sm"><i class="fa fa-print" aria-hidden="true"></i>&nbsp;Imprimir</button>-->
                                    <button id="btnView" type="button" class="btn btn-default btn-sm"><i class="fa fa-eye" aria-hidden="true"></i>&nbsp;Visualizar</button>
                                </div>

                                <p></p>

                                <div class="jqGrid_wrapper">
                                    <table id="table_list_tickets"></table>
                                    <div id="pager_list_persons"></div>
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
    {include file='modals/aso/modal-alert-refresh.tpl'}



</body>

</html>
