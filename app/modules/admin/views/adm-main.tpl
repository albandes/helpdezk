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

    {*head_item type="css" src="$path/css/font-awesome/css/" files="font-awesome.css"*}
    {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
    {head_item type="css" src="$path/css/" files="animate.css"}

    {head_item type="css" src="$path/css/gn-menu/css/" files="component.css"}
    {head_item type="js"  src="$path/includes/js/plugins/gnmenu/" files="classie.js"}
    {head_item type="js"  src="$path/includes/js/plugins/gnmenu/" files="gnmenu.js"}

    {head_item type="js"  src="$path/includes/js/plugins/modernizr/" files="modernizr.custom.js"}

    {head_item type="css" src="$path/css/" files="admmenu.css"}

    <!-- Helpdezk -->
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/app/modules/admin/views/js/" files="main.js"}


    <!-- Personalized style -->
    {head_item type="css" src="$path/css/" files="$theme.css"}
    <!-- Last to be included therefore overwrite others css -->
    <!-- Input Mask-->
    {head_item type="js" src="$path/includes/js/plugins/jquery-mask/" files="jquery.mask.min.js"}

    <!-- Autocomplete -->
    {head_item type="js" src="$path/includes/js/plugins/autocomplete/" files="jquery.autocomplete.js"}
    {head_item type="css" src="$path/includes/js/plugins/autocomplete/" files="jquery.autocomplete.css"}

    <!-- Combo Autocomplete -->
    {head_item type="js" src="$path/includes/js/plugins/chosen/" files="chosen.jquery.js"}
    {head_item type="css" src="$path/css/plugins/chosen/" files="chosen.css"}

    <!-- Jquery Validate -->
    {head_item type="js"  src="$path/includes/js/plugins/validate/" files="jquery.validate.min.js"}

    <!-- Dropzone  -->
    {head_item type="js"  src="$path/includes/js/plugins/dropzone/" files="dropzone.js"}
    {head_item type="css" src="$path/css/plugins/dropzone/" files="basic.css"}
    {head_item type="css" src="$path/css/plugins/dropzone/" files="pipe.dropzone.css"}
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
                cellphone_mask     = '{/literal}{$cellphone_mask}{literal}' ;
        </script>

        <style>
            .panel-footer{
                background-color:#fff;
                border-color: #E7EAEC;
                color: #000;
            }

            .homeDash {
                -moz-border-bottom-colors: none;
                -moz-border-left-colors: none;
                -moz-border-right-colors: none;
                -moz-border-top-colors: none;
                background-color: #ffffff;
                border-color: #e7eaec;
                border-image: none;
                border-style: solid solid none;
                border-width: 4px 0px 0;
                color: inherit;
                margin-bottom: 0;
                padding: 14px 15px 7px;
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

            <div class="wrapper wrapper-content">
                <div class="row  border-bottom white-bg dashboard-header">
                    <div class="col-sm-5">
                        <h2>{$smarty.config.admin_dashboard}</h2>
                    </div>
                </div>
            </div>

            <div class="footer">
                {include file=$footer}
            </div>

        </div>
    </div>


</body>