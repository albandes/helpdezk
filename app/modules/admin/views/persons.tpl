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
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/app/modules/admin/views/js/" files="persons.js"}

    {*head_item type="css" src="$path/css/font-awesome/css/" files="font-awesome.css"*}
    {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
    {head_item type="css" src="$path/css/" files="animate.css"}

    {head_item type="css" src="$path/css/plugins/jQueryUI/" files="jquery-ui-1.10.4.custom.min.css"}
    {head_item type="css" src="$path/css/plugins/jqGrid/" files="ui.jqgrid.css"}
    {head_item type="css" src="$path/css/" files="$theme.css"}

    <!-- Combo Autocomplete -->
    {head_item type="js" src="$path/includes/js/plugins/chosen/" files="chosen.jquery.js"}
    {head_item type="css" src="$path/css/plugins/chosen/" files="chosen.css"}

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

    {literal}
        <script type="text/javascript">
            var default_lang = "{/literal}{$lang}{literal}",
                    path = "{/literal}{$path}{literal}",
                    langName = '{/literal}{$smarty.config.Name}{literal}',
                    theme = '{/literal}{$theme}{literal}',
                    mascDateTime = '{/literal}{$mascdatetime}{literal}',
                    timesession = '{/literal}{$timesession}{literal}',
                id_mask      = '{/literal}{$id_mask}{literal}',
                ein_mask     = '{/literal}{$ein_mask}{literal}',
                zip_mask     = '{/literal}{$zip_mask}{literal}',
                phone_mask     = '{/literal}{$phone_mask}{literal}',
                cellphone_mask     = '{/literal}{$cellphone_mask}{literal}';
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
                                <h5>Cadastros / {$smarty.config.people} / <strong>{$smarty.config.Home}</strong></h5>

                            </div>

                            <div class="ibox-content">

                                <button id="btnCreate" type="button" class="btn btn-primary btn-xs"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;{$smarty.config.New}</button>
                                <button id="btnUpdate" type="button" class="btn btn-primary btn-xs"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{$smarty.config.edit}</button>
                                <button id="btnDisable" type="button" class="btn btn-primary btn-xs"><i class="fa fa-lock" aria-hidden="true"></i>&nbsp;{$smarty.config.Deactivate}</button>
                                <button id="btnEnable" type="button" class="btn btn-primary btn-xs"><i class="fa fa-unlock" aria-hidden="true"></i>&nbsp;{$smarty.config.Activate}</button>
                                <button id="btnPerms" type="button" class="btn btn-primary btn-xs"><i class="fa fa-cog" aria-hidden="true"></i>&nbsp;{$smarty.config.permissions}</button>
                                <button id="btnGroups" type="button" class="btn btn-primary btn-xs"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;{$smarty.config.View_groups}</button>

                                <p></p>

                                <div class="jqGrid_wrapper">
                                    <table id="table_list_persons"></table>
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
    {include file='modals/programs/modal-alert.tpl'}
    {include file='modals/person/modal-attendant-groups.tpl'}


</body>

</html>
