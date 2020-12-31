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

    <!-- Helpdezk -->
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/app/modules/helpdezk/views/js/" files="main.js"}

    <!-- Data Tables -->
    {head_item type="js" src="$path/includes/js/plugins/dataTables/" files="jquery.dataTables.js"}
    {head_item type="js" src="$path/includes/js/plugins/dataTables/" files="dataTables.bootstrap.js"}
    {head_item type="js" src="$path/includes/js/plugins/dataTables/" files="dataTables.responsive.js"}
    {head_item type="js" src="$path/includes/js/plugins/dataTables/" files="dataTables.tableTools.min.js"}
    {head_item type="css" src="$path/css/plugins/dataTables/" files="dataTables.bootstrap.css"}
    {head_item type="css" src="$path/css/plugins/dataTables/" files="dataTables.responsive.css"}
    {head_item type="css" src="$path/css/plugins/dataTables/" files="dataTables.tableTools.min.css"}

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

    <!-- Personalized style -->
    {head_item type="css" src="$path/css/" files="$theme.css"}
    <!-- Last to be included therefore overwrite others css -->

    <!-- Dropzone  -->
    {head_item type="js"  src="$path/includes/js/plugins/dropzone/" files="dropzone.js"}
    {head_item type="css" src="$path/css/plugins/dropzone/" files="basic.css"}
    {head_item type="css" src="$path/css/plugins/dropzone/" files="pipe.dropzone.css"}

    {literal}

        <script type="text/javascript">
            var default_lang    = "{/literal}{$lang}{literal}",
                path            = "{/literal}{$path}{literal}",
                langName        = '{/literal}{$smarty.config.Name}{literal}',
                theme           = '{/literal}{$theme}{literal}',
                timesession     = '{/literal}{$timesession}{literal}',
                id_mask         = '{/literal}{$id_mask}{literal}',
                ein_mask        = '{/literal}{$ein_mask}{literal}',
                zip_mask        = '{/literal}{$zip_mask}{literal}',
                phone_mask      = '{/literal}{$phone_mask}{literal}',
                cellphone_mask  = '{/literal}{$cellphone_mask}{literal}',
                typeuser        = '{/literal}{$typeuser}{literal}',
                demoVersion     = '{/literal}{$demoversion}{literal}';
        </script>

        <style>

            .panel-footer{
                background-color:#fff;
                border-color: #E7EAEC;
                color: #000;
            }


            body.DTTT_Print {
                background: #fff;

            }
            .DTTT_Print #page-wrapper {
                margin: 0;
                background:#fff;
            }

            button.DTTT_button, div.DTTT_button, a.DTTT_button {
                border: 1px solid #e7eaec;
                background: #fff;
                color: #676a6c;
                box-shadow: none;
                padding: 6px 8px;
            }
            button.DTTT_button:hover, div.DTTT_button:hover, a.DTTT_button:hover {
                border: 1px solid #d2d2d2;
                background: #fff;
                color: #676a6c;
                box-shadow: none;
                padding: 6px 8px;
            }

            .dataTables_filter label {
                margin-right: 5px;

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

                {if $show_dashboard == true}
                    {include file='dash-user.tpl'}
                {/if}

            </div>


            <div class="footer">
                {include file=$footer}
            </div>
        </div>
    </div>

    {include file='modals/main/modalPersonData.tpl'}
    {include file='modals/main/modal-approve-require.tpl'}

</body>