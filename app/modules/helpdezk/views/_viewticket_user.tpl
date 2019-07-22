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
    {head_item type="css" src="$path/css/plugins/jqGrid/" files="ui.jqgrid.css"}
    <!-- Custom and plugin javascript -->
    <!-- {head_item type="js"  src="$path/includes/js/" files="inspinia.js"}-->
    {head_item type="js"  src="$path/includes/js/plugins/pace/" files="pace.min.js"}

    {head_item type="css" src="$path/css/gn-menu/css/" files="component.css"}
    {head_item type="js"  src="$path/includes/js/plugins/gnmenu/" files="classie.js"}
    {head_item type="js"  src="$path/includes/js/plugins/gnmenu/" files="gnmenu.js"}

    {head_item type="js"  src="$path/includes/js/plugins/modernizr/" files="modernizr.custom.js"}
    <!-- Jquery UI -->
    {*
     *
     Incompatible with the "sumernote". If it is being used the title of the buttons do not work
     *
    {head_item type="js"  src="$path/includes/js/plugins/jquery-ui/" files="jquery-ui.min.js"}
    {head_item type="css" src="$path/css/plugins/jQueryUI/" files="jquery-ui-1.10.4.custom.min.css"}
    *}
    <!-- Helpdezk -->
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/app/modules/helpdezk/views/js/" files="viewticket.js"}
    <!-- Font Awesome -->
    {head_item type="css" src="$path/css/font-awesome/css/" files="font-awesome.css"}
    <!-- animate -->
    {head_item type="css" src="$path/css/" files="animate.css"}
    <!-- Helpdezk CSS -->
    {head_item type="css" src="$path/css/" files="$theme.css"}
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
    {literal}
    <script type="text/javascript">
        var default_lang = "{/literal}{$lang}{literal}",
                path = "{/literal}{$path}{literal}",
                langName = '{/literal}{$smarty.config.Name}{literal}',
                theme = '{/literal}{$theme}{literal}',
                mascDateTime = '{/literal}{$mascdatetime}{literal}',
                timesession = '{/literal}{$timesession}{literal}';
                noteAttMaxFiles = '{/literal}{$noteattmaxfiles}{literal}',
                noteAcceptedFiles = '{/literal}{$noteacceptedfiles}{literal}',
                ticketAttMaxFiles = '{/literal}{$ticketattmaxfiles}{literal}',
                ticketAcceptedFiles = '{/literal}{$ticketacceptedfiles}{literal}';
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
        <div class="row border-bottom">


        </div>

        <div class="wrapper wrapper-content  ">
            <div class="col-xs-12 white-bg" style="height:10px;"></div>
            <div class="row wrapper    white-bg ">
                <div class="col-sm-4">

                    <h3>{$smarty.config.Request_code}: {$request_code}</h3>

                </div>
                <div class="col-sm-8 text-right"">

                        <button type="button" class="btn btn-default btn-sm" id="button-reload">
                            <span class="fa fa-refresh"></span> &nbsp;{$smarty.config.reload_request}
                        </button>

                </div>
            </div>

            <div class="row wrapper  border-bottom white-bg ">
                &nbsp;
            </div>
            <!-- -->
            <div class="col-xs-12 white-bg" style="height:10px;"></div>
            <div class="row wrapper  white-bg ">
                <div class="col-sm-6 ">
                    <div class="wrapper">
                        <div >
                            <div class="col-lg-4">
                                <h4 >{$smarty.config.Request_owner}:</h4>
                            </div>
                            <div class="col-sm-8 ">
                                {$owner}
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-6">
                    <div class="wrapper">
                        <div >
                            <div class="col-lg-4">
                                <h4 >{$smarty.config.status}:</h4>
                            </div>
                            <div class="col-sm-8 ">

                                {$status}

                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="row wrapper  white-bg ">
                <div class="col-sm-6 ">
                    <div class="wrapper">
                        <div >
                            <div class="col-lg-4">
                                <h4 >{$smarty.config.Department}:</h4>
                            </div>
                            <div class="col-sm-8 ">
                                {$department}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 ">
                    <div class="wrapper">
                        <div >
                            <div class="col-lg-4">
                                <h4 >{$smarty.config.Opening_date}:</h4>
                            </div>
                            <div class="col-sm-8 ">
                                {$entry}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row wrapper  white-bg ">
                <div class="col-sm-6 ">
                    <div class="wrapper">
                        <div >
                            <div class="col-lg-4">
                                <h4 >{$smarty.config.Source}:</h4>
                            </div>
                            <div class="col-sm-8 ">
                                {$source}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 ">
                    <div class="wrapper">
                        <div >
                            <div class="col-lg-4">
                                <h4 >{$smarty.config.Tckt_incharge}:</h4>
                            </div>
                            <div class="col-sm-8 ">
                                {$inchargename}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- -->


            <!-- Form area -->
            <form method="get" class="form-horizontal">

                <input name="idstatus" type="hidden" id="idstatus" value="{$hidden_idstatus}" />
                <input type="hidden" id="coderequest" value="{$hidden_coderequest}" />

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-6 b-l">

                        <div class="form-group"><label class="col-sm-2 control-label">{$smarty.config.Area}: </label>
                            {*<div class="col-sm-10"><input type="text" class="form-control input-sm"></div>*}
                            <div class="col-sm-10">
                                <select class="form-control  m-b" name="area" id="area" disabled="disabled">
                                    <option value="0">{$smarty.config.Select} </option>
                                    {html_options values=$areaids output=$areavals selected=$idarea}
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-6">
                    </div>
                </div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-6 b-l">

                        <div class="form-group"><label class="col-sm-2 control-label">{$smarty.config.Type}:</label>
                            {*<div class="col-sm-10"><input type="text" class="form-control input-sm"></div>*}
                            <div class="col-sm-10">
                                <select class="form-control  m-b" name="type-view" id="type-view" disabled="disabled">
                                    <option value="0">{$smarty.config.Select} </option>
                                    {html_options values=$typeids output=$typevals selected=$idtype}
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-6">
                        <div class="form-group"><label class="col-sm-2 control-label">{$smarty.config.Priority}:</label>
                            <div class="col-sm-10">
                                <select class="form-control  m-b" name="priority" id="priority" disabled="disabled">
                                    <option value="0">{$smarty.config.Select} </option>
                                    {html_options values=$priorityids output=$priorityvals selected=$idpriority}
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-6 b-l">

                        <div class="form-group"><label class="col-sm-2 control-label">{$smarty.config.Item}:</label>
                            <div class="col-sm-10">
                                <select class="form-control  m-b" name="item" id="item" disabled="disabled">
                                    <option value="0">{$smarty.config.Select} </option>
                                    {html_options values=$itemids output=$itemvals selected=$iditem}
                                </select>
                            </div>
                        </div>


                    </div>
                    <div class="col-lg-6 b-l">
                        <div class="form-group"><label class="col-sm-2 control-label">{$smarty.config.Att_way}:</label>
                            <div class="col-sm-10">
                                <select class="form-control  m-b" name="way" id="way" disabled="disabled">
                                    <option value="0">{$smarty.config.Select} </option>
                                    {html_options values=$wayids output=$wayvals selected=$idway}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-6 b-l">

                        <div class="form-group"><label class="col-sm-2 control-label">{$smarty.config.Service}:</label>
                            <div class="col-sm-10">
                                <select class="form-control  m-b" name="service" id="service" disabled="disabled">
                                    <option value="0">{$smarty.config.Select} </option>
                                    {html_options values=$serviceids output=$servicevals selected=$idservice}
                                </select>
                            </div>
                        </div>


                    </div>
                    <div class="col-lg-6 b-l">
                        <div class="form-group"><label class="col-sm-2 control-label">{$smarty.config.Reason}:</label>
                            <div class="col-sm-10">
                                <select class="form-control  m-b" name="reason" id="reason"  disabled="disabled">
                                    <option value="0">{$smarty.config.Select} </option>
                                    {html_options values=$reasonids output=$reasonvals selected=$idreason}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-12 b-l">

                        <div class="form-group"><label class="col-sm-1 control-label">{$smarty.config.Subject}:</label>
                            <div class="col-sm-11"><input type="text" disabled id="subject" name="subject" value="{$subject}" class="form-control input-sm"></div>
                        </div>

                    </div>
                </div>



                {if $hasattach == 1}
                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 ">
                            <div class="wrapper">
                                <div >
                                    <div class="col-lg-1">
                                        <h4 >{$smarty.config.Attachments}:</h4>
                                    </div>
                                    <div class="col-sm-11 text-left">
                                        {$attach_files}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}


                <div class="row wrapper  white-bg ">
                    <div class="col-sm-12 ">
                        <div >
                            <div class="col-lg-2">
                                <h4 >{$smarty.config.Description}:</h4>
                            </div>
                            <div class="row wrapper  white-bg ">
                                <div class="col-sm-10 b-l">
                                    <div id="summernote">{$description}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </form>
            <!-- End form area -->
        <!-- Icons -->
        {if $displayprint != 0 || $displayreopen != 0 || $displaycancel != 0 ||  $displayevaluate != 0}
            <div class="row wrapper  white-bg " >
                <hr style="margin-bottom:3px !important; margin-top:2px !important; margin-right:20px !important; margin-left:2px !important;"/>
            </div>

            <div class="row wrapper  white-bg ">
                {if $displayprint == 1}
                <button type="button" class="btn btn-default btn-sm" id="btnPrint">
                    <span class="glyphicon glyphicon-print"></span> &nbsp;{$smarty.config.Print}
                </button>
                {/if}

                {if $displaycancel == 1}
                <button type="button" class="btn btn-default btn-sm" id="btnCancel">
                    <span class="glyphicon glyphicon-remove"></span>  &nbsp;{$smarty.config.Cancel_btn}
                </button>
                {/if}

                {if $displayreopen == 1}
                <button type="button" class="btn btn-default btn-sm" id="btnReopen">
                    <span class="fa fa-file-text-o"></span>  &nbsp;{$smarty.config.btn_reopen}
                </button>
                {/if}
                {if $displayevaluate == 1}
                <button type="button" class="btn btn-default btn-sm" id="btnEvaluate">
                    <span class="fa fa-comment-o"></span>  &nbsp;{$smarty.config.Btn_evaluate}
                </button>
                {/if}

            </div>

            <div class="row wrapper  white-bg " >
                <hr style="margin-bottom:2px !important; margin-top:4px !important; margin-right:20px !important; margin-left:2px !important;"/>
            </div>

        {/if}

        <div class="row wrapper  white-bg ">
            <br>
        </div>

        {if $displaynote == 1}

            <div class="row wrapper  white-bg " >
                <hr class="hr-pipe-black"/>
                <br>
            </div>

            <div class="row wrapper  white-bg ">
                <div class="col-sm-12 ">
                    <div >
                        <div class="col-lg-2">
                            <h4 >{$smarty.config.Insert_note}:</h4>
                        </div>
                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-10 b-l">
                                <div id="requestnote"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Note Attachments -->
            <div class="row wrapper  white-bg ">
                <div class="col-sm-12 ">
                    <div >
                        <div class="col-lg-2">
                            <h4 >{$smarty.config.Attachments}:</h4>
                        </div>
                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-10 b-l">
                                <!-- This is the dropzone element -->
                                <div id="myDropzone" class="dropzone dz-default dz-message" >


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- -->

            <div class="col-xs-12 white-bg" style="height:10px;"></div>

            <div class="row wrapper  white-bg ">
                <div class="col-sm-12 ">
                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-primary" id="btnSendNote" >
                            <i class='fa fa-check'></i>
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


        <!-- Notes -->
        <div class="col-xs-12 white-bg" style="height:10px;"></div>

        <div class="row wrapper  white-bg ">
            <div class="col-sm-12 ">
                <h4 >{$smarty.config.Added_notes}:</h4>
            </div>
        </div>

        <div class="col-xs-12 white-bg" style="height:10px;"></div>

        {*<div id="ticket_notes" class="row wrapper  white-bg ">
            <div class="timeline-item  ">
                <div class="row">
                    <div class="col-xs-3 date">
                        <i class="fa fa-file-text"></i>
                        7:00 am
                        <br/>
                        <small class="text-navy">3 hour ago</small>
                    </div>
                    <div class="col-xs-9 content">
                        <p class="m-b-xs"><strong>Send documents to Mike</strong></p>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since.</p>
                    </div>
                </div>
            </div>
        </div>*}

            <div id="ticket_notes">
                {$notes}
            </div>


        <div class="row border-bottom white-bg ">
        <div class="row border-bottom">


        <div class="footer">
            {include file=$footer}
        </div>
</div>



    {include file='modals/viewticket/modalCancel.tpl'}
    {include file='modals/viewticket/modalReopen.tpl'}
    {include file='modals/viewticket/modalEvaluate.tpl'}
    {include file='modals/viewticket/modalDeleteNote.tpl'}



</body>

</html>
