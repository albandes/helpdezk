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


    <!-- Custom and plugin javascript -->
    {head_item type="js"  src="$path/js/" files="inspinia.js"}
    {head_item type="js"  src="$path/js/plugins/pace/" files="pace.min.js"}

    {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
    {head_item type="css" src="$path/css/" files="animate.css"}

    {head_item type="css" src="$path/css/gn-menu/css/" files="component.css"}
    {head_item type="js"  src="$path/includes/js/plugins/gnmenu/" files="classie.js"}
    {head_item type="js"  src="$path/includes/js/plugins/gnmenu/" files="gnmenu.js"}

    {head_item type="js"  src="$path/includes/js/plugins/modernizr/" files="modernizr.custom.js"}

    <!-- Helpdezk -->
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/app/modules/fin/views/js/" files="main.js"}


    <!-- Personalized style -->
    {head_item type="css" src="$path/css/" files="$theme.css"}
    <!-- Last to be included therefore overwrite others css -->
    {literal}

    <script type="text/javascript">
        var default_lang = "{/literal}{$lang}{literal}",
            path         = "{/literal}{$path}{literal}",
            langName     = '{/literal}{$smarty.config.Name}{literal}',
            theme        = '{/literal}{$theme}{literal}',
            timesession  = '{/literal}{$timesession}{literal}',
            id_mask      = '{/literal}{$id_mask}{literal}',
            ein_mask     = '{/literal}{$ein_mask}{literal}',
            zip_mask     = '{/literal}{$zip_mask}{literal}',
            phone_mask     = '{/literal}{$phone_mask}{literal}',
            cellphone_mask     = '{/literal}{$cellphone_mask}{literal}',
            demoVersion = '{/literal}{$demoversion}{literal}';
    </script>

    <style>
        .panel-footer{
            background-color:#fff;
            border-color: #E7EAEC;
            color: #000;
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

        <!--<div class="wrapper wrapper-content">
            <div class="row  border-bottom white-bg dashboard-header">

                    <div class="col-sm-5">
                        <h2>Recursos Humanos</h2>
                        <small>Última atualização: {$dtatualiza}</small>
                        <ul class="list-group clear-list m-t">
                            <li class="list-group-item fist-item">
                                <span class="pull-right">
                                    {$total_mq}
                                </span>
                                <span class="label label-success">1</span> INSTITUTO EDUCACIONAL MQ - EIRELI
                            </li>
                            <li class="list-group-item">
                                <span class="pull-right">
                                    {$total_mario}
                                </span>
                                <span class="label label-info">2</span> SOC EDUCACIONAL MARIO QUINTANA LTDA
                            </li>
                            <li class="list-group-item">
                                <span class="pull-right">
                                    {$total_tresv}
                                </span>
                                <span class="label label-primary">3</span> SOCIEDADE EDUCACIONAL TRES VENDAS LTDA
                            </li>


                        </ul>
                    </div>

                </div>
        </div>-->

        <div class="footer">
            {include file=$footer}
        </div>

    </div>
</div>


</body>