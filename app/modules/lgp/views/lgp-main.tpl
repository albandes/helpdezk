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
    {head_item type="js" src="$path/app/modules/lgp/views/js/" files="main.js"}


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

    <!-- jqGrid -->
    {head_item type="js"  src="$path/includes/js/plugins/jqGrid/i18n/" files="$jqgrid_i18nFile"}
    {head_item type="js"  src="$path/includes/js/plugins/jqGrid/" files="jquery.jqGrid.min.js"}
    {head_item type="css" src="$path/css/plugins/jqGrid/" files="ui.jqgrid.css"}

    <!-- Jquery UI -->
    {head_item type="css" src="$path/css/plugins/jQueryUI/" files="jquery-ui-1.10.4.custom.min.css"}
    {head_item type="js"  src="$path/includes/js/plugins/jquery-ui/" files="jquery-ui.min.js"}

     <!-- Datapicker  -->
    {head_item type="css" src="$path/css/plugins/datepicker/" files="datepicker3.css"}
    {head_item type="js"  src="$path/includes/js/plugins/datepicker/" files="bootstrap-datepicker.js"}
    {if $dtpickerLocale != ''}
        {head_item type="js"  src="$path/includes/js/plugins/datepicker/locales/" files="$dtpickerLocale"}
    {/if}

    <!-- Dropzone  -->
    {head_item type="js"  src="$path/includes/js/plugins/dropzone/" files="dropzone.js"}
    {head_item type="css" src="$path/css/plugins/dropzone/" files="basic.css"}
    {head_item type="css" src="$path/css/plugins/dropzone/" files="pipe.dropzone.css"}
    {literal}

        <script type="text/javascript">
            var default_lang   = "{/literal}{$lang}{literal}",
                path           = "{/literal}{$path}{literal}",
                langName       = '{/literal}{$smarty.config.Name}{literal}',
                theme          = '{/literal}{$theme}{literal}',
                timesession    = '{/literal}{$timesession}{literal}',
                id_mask        = '{/literal}{$id_mask}{literal}',
                ein_mask       = '{/literal}{$ein_mask}{literal}',
                zip_mask       = '{/literal}{$zip_mask}{literal}',
                phone_mask     = '{/literal}{$phone_mask}{literal}',
                cellphone_mask = '{/literal}{$cellphone_mask}{literal}',
                demoVersion    = '{/literal}{$demoversion}{literal}';

            $(document).ready(function(){
                <!-- Enable portlets -->
                WinMove();
            });

            // Dragable panels
            window.WinMove = function() {
                var element = "[class*=col]";
                var handle = ".ibox-title";
                var connect = "[class*=col]";
                $(element).sortable(
                    {
                        handle: handle,
                        connectWith: connect,
                        tolerance: 'pointer',
                        forcePlaceholderSize: true,
                        opaclassification: 0.8,
                    })
                    .disableSelection();
            };

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

            .bs-callout {
                padding: 20px;
                margin: 20px 0;
                border: 1px solid #eee;
                border-left-width: 5px;
                border-radius: 3px;
            }
            .bs-callout h4 {
                margin-top: 0;
                margin-bottom: 5px;
            }
            .bs-callout p:last-child {
                margin-bottom: 0;
            }
            .bs-callout code {
                border-radius: 3px;
            }
            .bs-callout+.bs-callout {
                margin-top: -5px;
            }
            .bs-callout-default {
                border-left-color: #777;
            }
            .bs-callout-default h4 {
                color: #777;
            }
            .bs-callout-primary {
                border-left-color: #428bca;
            }
            .bs-callout-primary h4 {
                color: #428bca;
            }
            .bs-callout-success {
                border-left-color: #5cb85c;
            }
            .bs-callout-success h4 {
                color: #5cb85c;
            }
            .bs-callout-danger {
                border-left-color: #d9534f;
            }
            .bs-callout-danger h4 {
                color: #d9534f;
            }
            .bs-callout-warning {
                border-left-color: #f0ad4e;
            }
            .bs-callout-warning h4 {
                color: #f0ad4e;
            }
            .bs-callout-info {
                border-left-color: #5bc0de;
            }
            .bs-callout-info h4 {
                color: #5bc0de;
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
                        <h2>{$smarty.config.lgp_Navbar_name}</h2>
                    </div>
                </div>

                <div class="row wrapper wrapper-content white-bg">
                    
                </div>


            </div>

            <div class="footer">
                {include file=$footer}
            </div>

        </div>
    </div>


</body>