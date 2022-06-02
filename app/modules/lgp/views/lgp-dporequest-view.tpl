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
    <!-- Helpdezk -->
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/app/modules/lgp/views/js/" files="lgp-dporequest-create.js"}
    <!-- Font Awesome -->
    {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
    <!-- animate -->
    {head_item type="css" src="$path/css/" files="animate.css"}
    <!-- Icheck, used in checkbox and radio -->
    {head_item type="css" src="$path/css/plugins/iCheck/" files="custom.css"}
    {head_item type="js"  src="$path/includes/js/plugins/iCheck/" files="icheck.min.js"}
    <!-- Bootstrap3 Dialog  -->
    {head_item type="css" src="$path/includes/js/plugins/bootstrap3-dialog/src/css/" files="bootstrap-dialog.css"}
    {head_item type="js"  src="$path/includes/js/plugins/bootstrap3-dialog/src/js/" files="bootstrap-dialog.js"}
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
    <!-- Helpdezk CSS -->
    {head_item type="css" src="$path/css/" files="$theme.css"}
    <!-- Summernote Editor -->
    {head_item type="css" src="$path/css/plugins/summernote/$summernote_version/" files="summernote.css"}
    {head_item type="css" src="$path/css/plugins/summernote/" files="summernote-bs3.css"}
    {head_item type="js"  src="$path/includes/js/plugins/summernote/$summernote_version/" files="summernote.js"}

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
            newsStorage = '{/literal}{$newsStorage}{literal}',
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

        .highlight {
            padding: 9px 14px;
            margin-bottom: 14px;
            border: 2px solid #e1e1e8;
            border-radius: 6px;
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

            <div class="row border-bottom"></div>

            <div class="wrapper wrapper-content ">
                <div class="row wrapper white-bg ibox-title">
                    <div class="col-sm-4">
                        <h4>{$smarty.config.Request_code}: {$request_code}</h4>
                    </div>
                    <div class="col-sm-8 text-right">
                        <button type="button" class="btn btn-default btn-xs" id="button-reload">
                            <span class="fa fa-sync-alt"></span> &nbsp;{$smarty.config.reload_request}
                        </button>
                    </div>
                </div>

                <div class="row wrapper  border-bottom white-bg "></div>

                <!-- First Line -->

                <div class="col-xs-12 white-bg" style="height:10px;"></div>

                <!-- Form area -->
                <form method="get" class="form-horizontal" id="lgp-viewticket-form">

                    <!-- Hidden -->
                    <input type="hidden" name="_token" id= "_token" value="{$token}">
                    <input type="hidden" name="idstatus" id="idstatus" value="{$hidden_idstatus}" />
                    <input type="hidden" name="coderequest" id="coderequest" value="{$hidden_coderequest}" />
                    <input type="hidden" name="incharge" id="incharge" value="{$incharge}" />
                    <input type="hidden" name="typeincharge" id="typeincharge" value="{$typeincharge}" />
                    <input type="hidden" name="isdpo" id="isdpo" value="{$isdpo}" />
                

                    <div class="row wrapper white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.available_note_holder}:</label>
                                <div class="col-sm-4 form-control-static">
                                    {$owner}
                                </div>
                                <label class="col-sm-2 control-label">{$smarty.config.status}:</label>
                                <div class="col-sm-4 form-control-static">
                                   {$status}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Opening_date}:</label>
                                <div class="col-sm-4 form-control-static">
                                    {$openingdate}
                                </div>
                                <label class="col-sm-2 control-label">{$smarty.config.Tckt_incharge}:</label>
                                <div class="col-sm-4 form-control-static">
                                    {$in_charge}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Subject}:</label>
                                <div class="col-sm-10 form-control-static">
                                    {$subject}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Description}:</label>
                                <div class="col-sm-8 form-control-static gray-bg highlight">
                                    <div id="attDescription" name="attDescription">
                                        <!--<textarea class="form-control input-sm">{$description}</textarea>-->
                                        {$description}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {if $hasattach == 1}
                    <div class="row wrapper white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Attachments}:</label>
                                <div class="col-sm-10 form-control-static">
                                    {$attach}
                                </div>
                            </div>
                        </div>
                    </div>
                    {/if}

                    <div class="row wrapper white-bg" >
                        <div class="col-sm-12 b-l">
                            <hr style="margin-bottom:3px !important; margin-top:2px !important; margin-right:20px !important; margin-left:2px !important;"/>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg text-left">
                        <div class="col-sm-12 b-l">
                        {if $displayassume == 1}
                            <button type="button" class="btn btn-default btn-sm" id="btnAssume">
                                <span class="fa fa-check-square"></span>  &nbsp;{$smarty.config.btn_assume}
                            </button>
                        {/if}

                        {if $displayrepass == 1}
                            <button type="button" class="btn btn-default btn-sm" id="btnRepass">
                                <span class="fa fa-share"></span>  &nbsp;{$smarty.config.Repass_btn}
                            </button>
                        {/if}

                        {if $displayreject == 1}
                            <button type="button" class="btn btn-default btn-sm" id="btnReject">
                                <span class="fa fa-times"></span>  &nbsp;{$smarty.config.btn_reject}
                            </button>
                        {/if}

                        {if $displayclose == 1}
                            <button type="button" class="btn btn-default btn-sm" id="btnCloseReq">
                                <span class="fa fa-times-circle"></span>  &nbsp;{$smarty.config.btn_close}
                            </button>
                        {/if}

                        {if $displayprint == 1}
                            <button type="button" class="btn btn-default btn-sm" id="btnPrint">
                                <span class="fa fa-print"></span> &nbsp;{$smarty.config.Print}
                            </button>
                        {/if}

                        {if $displaycancel == 1}
                            <button type="button" class="btn btn-default btn-sm" id="btnCancelReq">
                                <span class="fa fa-times"></span>  &nbsp;{$smarty.config.Cancel_btn}
                            </button>
                        {/if}

                        {if $displayreopen == 1}
                            <button type="button" class="btn btn-default btn-sm" id="btnReopen">
                                <span class="fa fa-sync"></span>  &nbsp;{$smarty.config.btn_reopen}
                            </button>
                        {/if}
                        </div>
                    </div>

                    <div class="col-xs-12 white-bg" style="height:10px;"></div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div id="alert-lgp-viewticket"></div>
                        </div>
                    </div>

                    <div class="row wrapper white-bg" >
                        <div class="col-sm-12 b-l">
                            <hr style="margin-bottom:3px !important; margin-top:2px !important; margin-right:20px !important; margin-left:2px !important;"/>
                        </div>
                    </div>

                    <div class="col-xs-12 white-bg" style="height:10px;"></div>

                    {if $displaynote == 1}
                    <div class="row wrapper  white-bg " >
                        <hr class="hr-pipe-black"/>
                        <br>
                    </div>
                    <form method="get" id="note-form-insert">
                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-12 ">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><h4>{$smarty.config.Visible}:</h4></label>
                                    <div class="col-sm-2 b-l">
                                        <select class="form-control  m-b" name="typenote" id="typenote">
                                            {html_options values=$typenoteids output=$typenotevals selected=$idtypenote}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-12 ">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><h4>{$smarty.config.Insert_note}:</h4></label>
                                    <div class="col-sm-8 b-l">
                                        <div id="requestnote"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Note Attachments -->
                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-12 ">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><h4>{$smarty.config.Attachments}:</h4></label>
                                    <div class="col-sm-8 b-l text-center">
                                        <!-- This is the dropzone element -->
                                        <div id="myDropzone" class="dropzone dz-default dz-message"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- -->

                    <div class="col-xs-12 white-bg" style="height:10px;"></div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 ">
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-primary" id="btnSendNote" >
                                    <i class='fa fa-paper-plane'></i>
                                    {$smarty.config.Send}
                                </button>
                            </div>
                            <div class="col-sm-6 ">
                                <div id="alert-noteadd"></div>
                            </div>
                            <div class="col-sm-4 ">
                                &nbsp;
                            </div>

                        </div>
                    </div>

                    <div class="row wrapper  white-bg " >
                        <hr class="hr-pipe-black"/>
                    </div>

                    {/if}

                    <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>

                    <div class="col-xs-12 white-bg" style="height:10px;"></div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 ">
                            <h4 >{$smarty.config.Added_notes}:</h4>
                        </div>
                    </div>

                    <div class="col-xs-12 white-bg" style="height:10px;"></div>

                    <div id="ticket_notes">
                        {$notes}
                    </div>

                </form>
                <!-- End form area -->


                <div class="row border-bottom white-bg ">
                    <div class="row border-bottom">
                        <div class="footer">
                            {include file=$footer}
                        </div>
                    </div>
                </div>
            </div>    
        </div>
    </div>

    {include file='modals/dporequest/modal-repass.tpl'}
    {include file='modals/dporequest/modal-next-step.tpl'}
    {include file='modals/main/modal-alert.tpl'}
    
    {include file='modals/dporequest/modal-assume.tpl'}
    {include file='modals/dporequest/modal-cancel.tpl'}
    {include file='modals/dporequest/modal-reopen.tpl'}
    {include file='modals/dporequest/modal-delete-note.tpl'}
    {include file='modals/dporequest/modal-reject.tpl'}
    {include file='modals/dporequest/modal-close.tpl'}

</body>

</html>