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
    {*
    {head_item type="js"  src="$path/includes/js/plugins/jqGrid/i18n/" files="grid.locale-pt-br.js"}
    {head_item type="js"  src="$path/includes/js/plugins/jqGrid/" files="jquery.jqGrid.min.js"}
    {head_item type="css" src="$path/css/plugins/jqGrid/" files="ui.jqgrid.css"}
    *}
    <!-- Custom and plugin javascript -->
    <!-- {head_item type="js"  src="$path/includes/js/" files="inspinia.js"} -->
    {head_item type="js"  src="$path/includes/js/plugins/pace/" files="pace.min.js"}

    {head_item type="css" src="$path/css/gn-menu/css/" files="component.css"}
    {head_item type="js"  src="$path/includes/js/plugins/gnmenu/" files="classie.js"}
    {head_item type="js"  src="$path/includes/js/plugins/gnmenu/" files="gnmenu.js"}

    {head_item type="js"  src="$path/includes/js/plugins/modernizr/" files="modernizr.custom.js"}

    {head_item type="css" src="$path/css/" files="admmenu.css"}
    <!-- Helpdezk -->
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/app/modules/emq/views/js/" files="sendemail.js"}
    <!-- Font Awesome -->
    {*head_item type="css" src="$path/css/font-awesome/css/" files="font-awesome.css"*}
    {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
    <!-- animate -->
    {head_item type="css" src="$path/css/" files="animate.css"}
    <!-- Icheck, used in checkbox and radio -->
    {head_item type="css" src="$path/css/plugins/iCheck/" files="custom.css"}
    {head_item type="js"  src="$path/includes/js/plugins/iCheck/" files="icheck.min.js"}
    <!-- Bootstrap3 Dialog  -->
    {head_item type="css" src="$path/includes/js/plugins/bootstrap3-dialog/src/css/" files="bootstrap-dialog.css"}
    {head_item type="js"  src="$path/includes/js/plugins/bootstrap3-dialog/src/js/" files="bootstrap-dialog.js"}
    <!-- Dropzone  -->
    {head_item type="js"  src="$path/includes/js/plugins/dropzone/" files="dropzone.js"}
    {head_item type="css" src="$path/css/plugins/dropzone/" files="basic.css"}
    {head_item type="css" src="$path/css/plugins/dropzone/" files="pipe.dropzone.css"}
    <!-- Jquery Validate -->
    {head_item type="js"  src="$path/includes/js/plugins/validate/" files="jquery.validate.min.js"}
    <!-- Input Mask-->
    {head_item type="js" src="$path/includes/js/plugins/jquery-mask/" files="jquery.mask.min.js"}
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
    <!-- Clockpicker  -->
    {head_item type="css" src="$path/css/plugins/clockpicker/" files="clockpicker.css"}
    {head_item type="js"  src="$path/includes/js/plugins/clockpicker/" files="clockpicker.js"}
    <!-- Helpdezk CSS -->
    {head_item type="css" src="$path/css/" files="$theme.css"}
    <!-- Summernote Editor -->
    {head_item type="css" src="$path/css/plugins/summernote/$summernote_version/" files="summernote.css"}
    {head_item type="css" src="$path/css/plugins/summernote/" files="summernote-bs3.css"}
    {head_item type="js"  src="$path/includes/js/plugins/summernote/$summernote_version/" files="summernote.js"}
    {head_item type="js"  src="$path/includes/js/plugins/summernote/$summernote_version/lang/" files="summernote-{$lang|replace:'_':'-'}.js"}

    {literal}
    <script type="text/javascript">
        var default_lang = "{/literal}{$lang}{literal}",
            path = "{/literal}{$path}{literal}",
            langName = '{/literal}{$smarty.config.Name}{literal}',
            theme = '{/literal}{$theme}{literal}',
            mascDateTime = '{/literal}{$mascdatetime}{literal}',
            timesession = '{/literal}{$timesession}{literal}',
            noteAttMaxFiles = '{/literal}{$noteattmaxfiles}{literal}',
            noteAcceptedFiles = '{/literal}{$noteacceptedfiles}{literal}',
            ticketAttMaxFiles = '{/literal}{$ticketattmaxfiles}{literal}',
            ticketAcceptedFiles = '{/literal}{$ticketacceptedfiles}{literal}',
            hdkMaxSize = '{/literal}{$hdkMaxSize}{literal}',
            demoVersion = '{/literal}{$demoversion}{literal}';


    </script>

    <style>
        /* Additional style to fix warning dialog position */
        #alertmod_table_list_tickets {
            top: 900px !important;
        }

        hr {
            height: 1px;
            margin-left: 15px;
            margin-bottom:-5px;
        }
        .hr-warning{
            background-image: -webkit-linear-gradient(left, rgba(210,105,30,.8), rgba(210,105,30,.6), rgba(0,0,0,0));
        }
        .hr-success{
            background-image: -webkit-linear-gradient(left, rgba(15,157,88,.8), rgba(15, 157, 88,.6), rgba(0,0,0,0));
        }
        .hr-primary{
            background-image: -webkit-linear-gradient(left, rgba(66,133,244,.8), rgba(66, 133, 244,.6), rgba(0,0,0,0));
        }
        .hr-danger{
            background-image: -webkit-linear-gradient(left, rgba(244,67,54,.8), rgba(244,67,54,.6), rgba(0,0,0,0));
        }

        .breadcrumb {
            background: rgba(245, 245, 245, 0);
            border: 0px solid rgba(245, 245, 245, 1);
            border-radius: 25px;
            display: block;
        }

        /*
         * Adjust Bootstrap Tooltip Width
         * https://stackoverflow.com/questions/36263249/adjust-bootstrap-tooltip-width
         */
        .tooltip-inner {
            max-width: 100% !important;
        }

        .scrollable-panel-body {
            position: relative;
            max-height: 600px;
            overflow: auto;
            display: block;
            padding-bottom: 0;
        }

        .tab-sections {
            padding-bottom: 0;
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

            <div class="row border-bottom"> </div>


            <div class="wrapper wrapper-content  ">
                <div class="row wrapper white-bg ibox-title">
                    <div class="col-sm-4">
                        <h4>{$smarty.config.cat_utils} / {$smarty.config.emq_pgr_email} / <strong>{$smarty.config.emq_compose_email}</strong></h4>
                    </div>
                </div>

                <div class="row wrapper  border-bottom white-bg ">
                    &nbsp;
                </div>

                <!-- First Line -->


                <div class="col-xs-12 white-bg" style="height:10px;"></div>

                <!-- Form area -->
                <form method="get" class="form-horizontal" id="create-mac-form">

                    <!-- Hidden -->
                    <input type="hidden" name="_token" id= "_token" value="{$token}">

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label class="col-sm-1 control-label">{$smarty.config.itm_hosttype}:&nbsp;</label>
                                <div class="col-sm-4">
                                    <div class='checkbox i-checks'><label><input type="checkbox" name="typeSend" class="checkSend" value="1" id="typeSend_1"> {$smarty.config.emq_type_employees}</label></div>
                                    <div class='checkbox i-checks'><label><input type="checkbox" name="typeSend" class="checkSend" value="2" id="typeSend_2"> {$smarty.config.emq_type_parents}</label></div>
                                    <div class='checkbox i-checks'><label><input type="checkbox" name="typeSend" class="checkSend" value="3" id="typeSend_3"> {$smarty.config.emq_type_parents_new}</label></div>
                                </div>
                                <div class="col-sm-4">
                                    <div class='checkbox i-checks'><label><input type="checkbox" name="typeSend" class="checkSend" value="4" id="typeSend_4"> {$smarty.config.emq_type_teachers}</label></div>
                                    <!--<div class='checkbox i-checks'><label><input type="checkbox" name="typeSend" class="checkSend" value="5" id="typeSend_5"> {$smarty.config.emq_type_sports_experiences}</label></div>-->
                                    <div class='checkbox i-checks'><label><input type="checkbox" name="typeSend" class="checkSend" value="6" id="typeSend_6"> {$smarty.config.emq_type_bilingual}</label></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-4 b-l sectionsLine hide">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4 class="panel-title">{$smarty.config.emq_send_filters}</h4>
                                </div>

                                <div class="scrollable-panel-body">
                                    <table class="table table-bordered tab-sections">
                                        <tbody id="bodySections"></tbody>
                                    </table>
                                </div>

                                <div class="panel-footer text-right">
                                    <button id="btnSelAllSections" type="button" class="btn btn-primary btn-xs">
                                        {$smarty.config.emq_select_all}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8 b-l">
                            <div id="loaderList" class="text-center"></div>
                            <div id="emailsList" class="scrollable-panel-body emailsList hide">
                                <table class="table table-striped">
                                    <tbody id="bodyRecipients">
                                    </tbody>
                                </table>
                            </div>
                            <div class="emailsList text-right hide">&nbsp;</div>
                            <div class="emailsList text-right hide">
                                <button id="btnSelAllRecip" type="button" class="btn btn-primary btn-xs">
                                    {$smarty.config.emq_select_all}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div id="alert-send-email"></div>
                        </div>
                    </div>

                    <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>

                    <div class="col-xs-12 white-bg" style="height:10px;"></div>

                    <div class="row wrapper  white-bg text-center">
                        <div class="col-sm-12 form-group">
                            <a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa-arrow-alt-circle-left" aria-hidden="true"></i> Volta </a>
                            <button type="button" class="btn btn-primary btn-md " id="btnCreateMsg" >
                                <span class="fa fa-pen-nib"></span>  &nbsp;{$smarty.config.emq_compose_email}
                            </button>
                        </div>
                    </div>

                </form>
                <!-- End form area -->

                <div class="row border-bottom white-bg ">
                    <div class="footer">
                        {include file=$footer}
                    </div>
                </div>

            </div>
        </div>
    </div>


    {*include file='modals/main/modal-alert.tpl'*}
    {include file='modals/emails/modal-email.tpl'}


</body>

</html>

