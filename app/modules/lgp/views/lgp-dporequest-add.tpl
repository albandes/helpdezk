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
    {head_item type="js" src="$path/app/modules/lgp/views/js/" files="lgp-dporequest-create.js"}
    <!-- Font Awesome -->
    {*head_item type="css" src="$path/css/font-awesome/css/" files="font-awesome.css"*}
    {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
    <!-- animate -->
    {head_item type="css" src="$path/css/" files="animate.css"}
    <!-- Summernote Editor -->
    {head_item type="css" src="$path/css/plugins/summernote/$summernote_version/" files="summernote.css"}
    {head_item type="css" src="$path/css/plugins/summernote/" files="summernote-bs3.css"}
    {head_item type="js"  src="$path/includes/js/plugins/summernote/$summernote_version/" files="summernote.js"}
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
            demoVersion = '{/literal}{$demoversion}{literal}',
            string_array = '{/literal}{$string_array}{literal}',
            access = {/literal}{$access}{literal};


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
                        <h4>{$smarty.config.cat_records} / {$smarty.config.pgr_dpo_request} / <strong>{$smarty.config.New_request}</strong></h4>
                    </div>
                </div>

                <div class="row wrapper  border-bottom white-bg ">
                    &nbsp;
                </div>

                <!-- First Line -->


                <div class="col-xs-12 white-bg" style="height:10px;"></div>

                <!-- Form area -->
                <form method="get" class="form-horizontal" id="lgp-newticket-form">

                    <!-- Hidden -->
                    <input type="hidden" name="_token" id= "_token" value="{$token}">

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-1 b-l">&nbsp;</div>

                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{$smarty.config.available_note_holder}:</label>
                                <div class="col-sm-6">
                                    <select class="form-control input-sm" id="cmbRequester" name="cmbRequester"  >
                                        <option value="" disabled selected hidden>{$smarty.config.Select}</option>
                                        {html_options values=$requesterids output=$requestervals selected=$idrequester}
                                    </select>
                                </div>
                                <div class="col-sm-1 ">
                                    <button class="btn btn-default" id="btnAddRequester" type="button" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">{$smarty.config.Subject}:</label>
                                <div class="col-sm-7">
                                    <input type="text" id="reqSubject" name="reqSubject" class="form-control input-sm" value="" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">{$smarty.config.Description}:</label>
                                <div class="col-sm-7">
                                    <div id="reqDescription" name="reqDescription" ></div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">{$smarty.config.Attachments}:</label>
                                <div class="col-sm-7 text-center">
                                    <div id="reqAttachs" class="dropzone dz-default dz-message" ></div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div id="alert-lgp-newticket"></div>
                        </div>
                    </div>

                    <div class="row wrapper border-bottom white-bg ">&nbsp;</div>

                    <div class="col-xs-12 white-bg" style="height:10px;"></div>

                    <div class="row wrapper white-bg text-center">
                        <div class="col-sm-12 form-group">
                            <button type="button" class="btn btn-white btn-md" id="btnCancel">
                                <span class="fa fa-arrow-alt-circle-left"></span>  &nbsp;{$smarty.config.Back_btn}
                            </button>
                            <button type="button" class="btn btn-primary btn-md" id="btnSaveTicket">
                                <span class="fa fa-save"></span>  &nbsp;{$smarty.config.Register_btn}
                            </button>
                            <button type="button" class="btn btn-success btn-md" id="btnRepassTicket">
                                <span class="fa fa-share"></span>  &nbsp;{$smarty.config.Repass_btn}
                            </button>
                            <!--<button type="button" class="btn btn-danger btn-md" id="btnFinishTicket">
                                <span class="fa fa-times-circle"></span>  &nbsp;{$smarty.config.Finish_btn}
                            </button>-->
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


    {include file='modals/dporequest/modalAlert.tpl'}
    {include file='modals/dporequest/modal-repass.tpl'}
    {include file='modals/dporequest/modal-next-step.tpl'}
    {include file='modals/dporequest/modal-requester-add.tpl'}


</body>

</html>

