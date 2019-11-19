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
    {head_item type="js" src="$path/app/modules/helpdezk/views/js/" files="ticket.js"}

    {*head_item type="css" src="$path/css/font-awesome/css/" files="font-awesome.css"*}
    {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
    {head_item type="css" src="$path/css/" files="animate.css"}

    {head_item type="css" src="$path/css/plugins/jQueryUI/" files="jquery-ui-1.10.4.custom.min.css"}
    {head_item type="css" src="$path/css/plugins/jqGrid/" files="ui.jqgrid.css"}
    {head_item type="css" src="$path/css/" files="$theme.css"}

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
                timesession = '{/literal}{$timesession}{literal}',
                typeuser = '{/literal}{$typeuser}{literal}',
                flgoperator = '{/literal}{$flgoperator}{literal}';
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
        <div class="row border-bottom">


        </div>

        {*
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>jqGrid</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="index.html">Home</a>
                    </li>
                    <li>
                        <a>Tables</a>
                    </li>
                    <li class="active">
                        <strong>jqGrid</strong>
                    </li>
                </ol>
            </div>
        </div>
*}
        <div class="wrapper wrapper-content  ">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">

                        <div class="ibox-title">
                            <h3>{$smarty.config.Tck_title}</h3>
                        </div>

                        <div class="ibox-content">
                            {if $typeuser == 3}
                            <div class="row">
                                    <div class="col-sm-3" style="padding-right: 5px;padding-left: 15px;">
                                        <select class="form-control input-sm" id="cmbTypeExpireDate" name="cmbTypeDate" data-placeholder={$smarty.config.Expire_date} >
                                            {html_options values=$typeexpdateids output=$typeexpdatevals selected=$idtypeexpdate}
                                        </select>
                                    </div>
                                    <div class="col-sm-3" style="padding-right: 7px;padding-left: 5px;">
                                        <select class="form-control input-sm" id="cmbTypeView" name="cmbTypeView">
                                            {html_options values=$typeviewids output=$typeviewvals selected=$idtypeview}
                                        </select>
                                    </div>
                            </div>
                            <p></p>
                            {/if}

                            <div class="jqGrid_wrapper">
                                <table id="table_list_tickets"></table>
                                <div id="pager_list_tickets"></div>
                            </div>
                            <!--
                            <div class="jqGrid_wrapper">
                                <table id="table_list_2"></table>
                                <div id="pager_list_2"></div>
                            </div>
                            -->
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

{include file='modals/main/modal-info.tpl'}
{include file='modals/main/modal-approve-require.tpl'}



<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Modal header</h3>
    </div>
    <div class="modal-body">
        <p>One fine body…</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary">Save changes</button>
    </div>
</div>

</body>

</html>
