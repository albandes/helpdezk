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
    <!-- Jquery UI -->
    {head_item type="js"  src="$path/js/plugins/jquery-ui/" files="jquery-ui.min.js"}
    <!-- Helpdezk -->
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/app/modules/acd/views/js/" files="student.js"}

    {head_item type="css" src="$path/css/font-awesome/css/" files="font-awesome.css"}
    {head_item type="css" src="$path/css/" files="animate.css"}

    {head_item type="css" src="$path/css/plugins/jQueryUI/" files="jquery-ui-1.10.4.custom.min.css"}
    {head_item type="css" src="$path/css/plugins/jqGrid/" files="ui.jqgrid.css"}
    {head_item type="css" src="$path/css/" files="$theme.css"}

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
                                <h5>Cadastros / Atleta / <strong>Home</strong></h5>

                            </div>

                            <div class="ibox-content">

                                <button id="btnCreate" type="button" class="btn btn-primary btn-xs"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Novo</button>
                                <button id="btnUpdate" type="button" class="btn btn-primary btn-xs"><i class="fa fa-pencil" aria-hidden="true""></i>&nbsp;Editar</button>
                                <button id="btnDisable" type="button" class="btn btn-primary btn-xs"><i class="fa fa-lock" aria-hidden="true"></i>&nbsp;Desativar</button>
                                <button id="btnEnable" type="button" class="btn btn-primary btn-xs"><i class="fa fa-unlock" aria-hidden="true"></i>&nbsp;Ativar</button>
                                <button type="button" class="btn btn-primary btn-xs"><i class="fa fa-cog" aria-hidden="true"></i>&nbsp;Permissões</button>

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
                <div class="pull-right">
                    10GB of <strong>250GB</strong> Free.
                </div>
                <div>
                    <strong>Copyright</strong> Example Company &copy; 2014-2015
                </div>
            </div>

        </div>

    </div>

    <!-- Modal -->




</body>

</html>
