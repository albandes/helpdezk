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
    {head_item type="js"  src="$path/includes/js/plugins/jqGrid/i18n/" files="$jqgrid_i18nFile"}
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
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/app/modules/helpdezk/views/js/" files="viewticket-operator.js"}
    <!-- Font Awesome -->
    {*head_item type="css" src="$path/css/font-awesome/css/" files="font-awesome.css"*}
    {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
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
    <!-- Combo Autocomplete -->
    {head_item type="js" src="$path/includes/js/plugins/chosen/" files="chosen.jquery.js"}
    {head_item type="css" src="$path/css/plugins/chosen/" files="chosen.css"}
    <!-- Datapicker  -->
    {head_item type="css" src="$path/css/plugins/datepicker/" files="datepicker3.css"}
    {head_item type="js"  src="$path/includes/js/plugins/datepicker/" files="bootstrap-datepicker.js"}
    {head_item type="js"  src="$path/includes/js/plugins/datepicker/locales/" files="bootstrap-datepicker.pt-BR.min.js"}
    <!-- Moment -->
    {head_item type="js"  src="$path/includes/js/plugins/moment/" files="moment-with-locales.min.js"}
    <!-- Stopwatch - Count timer  -->
    {head_item type="js"  src="$path/includes/js/plugins/countimer/" files="countimer.js"}
    <!-- Clockpicker  -->
    {head_item type="css" src="$path/css/plugins/clockpicker/" files="clockpicker.css"}
    {head_item type="js"  src="$path/includes/js/plugins/clockpicker/" files="clockpicker.js"}
    <!-- Jquery Validate -->
    {head_item type="js"  src="$path/includes/js/plugins/validate/" files="jquery.validate.min.js"}
    {* <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>*}
    <!-- Input Mask-->
    {head_item type="js" src="$path/includes/js/plugins/jquery-mask/" files="jquery.mask.min.js"}
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
                obtime =  '{/literal}{$obrigatorytime}{literal}',
                emptynote = '{/literal}{$emptynote}{literal}',
                hdkMaxSize = '{/literal}{$hdkMaxSize}{literal}';
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

        /*
         * Adjust Clockpicker on modal
         */
        .clockpicker-popover {
            z-index: 999999 !important;
        }

        .confirmBox {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
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
            <div class="row wrapper    white-bg dashboard-header">
                <div class="col-sm-4">
                    <h3>{$smarty.config.Request_code}: {$request_code}</h3>
                </div>
                <div class="col-sm-8 text-right">
                        <button type="button" class="btn btn-default btn-sm" id="button-reload">
                            <span class="fa fa-sync-alt"></span> &nbsp;{$smarty.config.reload_request}
                        </button>
                </div>
            </div>

            <div class="row wrapper  border-bottom white-bg "></div>
            <!-- -->
            <div class="col-xs-12 white-bg" style="height:10px;"></div>
            <div class="row wrapper  white-bg ">
                <div class="col-sm-6 ">
                    <div class="wrapper">
                        <div class="form-group">
                            <label class="col-lg-4 control-label text-right">{$smarty.config.Request_owner}: </label>
                            <div class="col-lg-8 control-text">
                                {$owner}&nbsp;
                                <!--<button type="button" class="btn btn-default btn-sm fa fa-envelope" id="btnEmail">-->
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="wrapper">
                        <div class="form-group">
                            <label class="col-lg-4 control-label text-right">{$smarty.config.status}: </label>
                            <div class="col-lg-8 control-text">
                                {$status}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row wrapper  white-bg ">
                <div class="col-sm-6 ">
                    <div class="wrapper">
                        <div class="form-group">
                            <label class="col-lg-4 control-label text-right">{$smarty.config.Department}: </label>
                            <div class="col-lg-8 control-text">
                                {$department}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 ">
                    <div class="wrapper">
                        <div class="form-group">
                            <label class="col-lg-4 control-label text-right">{$smarty.config.Opening_date}: </label>
                            <div class="col-lg-8 control-text">
                                {$entry}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row wrapper  white-bg ">
                <div class="col-sm-6 ">
                    <div class="wrapper">
                        <div class="form-group">
                            <label class="col-lg-4 control-label text-right">{$smarty.config.Source}: </label>
                            <div class="col-lg-8 control-text">
                                {$source}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 ">
                    <div class="wrapper">
                        <div class="form-group">
                            <label class="col-lg-4 control-label text-right">{$smarty.config.Tckt_incharge}: </label>
                            <div class="col-lg-8 control-text">
                                {$inchargename}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row wrapper  white-bg ">
                <div class="col-sm-6 ">
                    <div class="wrapper">
                        <div class="form-group">
                            <label class="col-lg-4 control-label text-right">{$smarty.config.Opened_by}: </label>
                            <div class="col-lg-8 control-text">
                                {$author}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 ">
                    <div class="wrapper">
                        <div class="form-group">
                            <label class="col-lg-4 control-label text-right">{$smarty.config.Expire_date}: </label>
                            <div class="col-lg-8 control-text">
                                <span id="txtExpireDate">{$expire_date}</span>
                                {if $show_btn_change_expire == 1}
                                &nbsp;
                                <button type="button" class="btn btn-default btn-sm" id="btnChangeExpireDate">
                                    <span class="fa fa-calendar"></span>  &nbsp;{$smarty.config.Change_date}
                                </button>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div id="auxopelist" class="row wrapper  white-bg {$flgauxopelist}">
                <div class="col-sm-6 pull-right">
                    <div class="wrapper">
                        <div class="form-group">
                            <label class="col-lg-4 control-label  text-right">{$smarty.config.lbl_auxiliary_operator}: </label>
                            <div id="auxopediv"class="col-lg-8 control-text">
                                {foreach from=$usersaux item=foo}
                                    <div>{$foo}</div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- -->
            <div class="col-xs-12 white-bg" style="height:10px;"></div>

            <!-- Form area -->
            <form method="get" class="form-horizontal" id="editticket-form">

                <input type="hidden" name="idstatus" id="idstatus" value="{$hidden_idstatus}" />
                <input type="hidden" name="coderequest" id="coderequest" value="{$hidden_coderequest}" />
                <input name="incharge" type="hidden" id="incharge" value="{$incharge}" />
                <input name="typeincharge" type="hidden" id="typeincharge" value="{$typeincharge}" />

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-6 b-l">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.Area}: </label>
                            {*<div class="col-sm-10"><input type="text" class="form-control input-sm"></div>*}
                            <div class="col-sm-10">
                                <select class="form-control  m-b" name="area" id="area">
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

                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.Type}:</label>
                            {*<div class="col-sm-10"><input type="text" class="form-control input-sm"></div>*}
                            <div class="col-sm-10">
                                <select class="form-control  m-b" name="cmbType" id="cmbType">
                                    <option value="0">{$smarty.config.Select} </option>
                                    {html_options values=$typeids output=$typevals selected=$idtype}
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label text-right">{$smarty.config.Priority}:</label>
                            <div class="col-sm-9">
                                <select class="form-control  m-b" name="priority" id="priority">
                                    <option value="0">{$smarty.config.Select} </option>
                                    {html_options values=$priorityids output=$priorityvals selected=$idpriority}
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-6 b-l">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.Item}:</label>
                            <div class="col-sm-10">
                                <select class="form-control  m-b" name="item" id="item">
                                    <option value="0">{$smarty.config.Select} </option>
                                    {html_options values=$itemids output=$itemvals selected=$iditem}
                                </select>
                            </div>
                        </div>


                    </div>
                    <div class="col-lg-6 b-l">
                        <div class="form-group">
                            <label class="col-sm-3 control-label text-right">{$smarty.config.Att_way}:</label>
                            <div class="col-sm-7">
                                <select class="form-control  m-b" name="way" id="way">
                                    <option value="0">{$smarty.config.Select} </option>
                                    {html_options values=$wayids output=$wayvals selected=$idway}
                                </select>
                            </div>
                            <div class="col-sm-2 text-right">
                                <button class="btn btn-default" id="btnAddWay" type="button" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-6 b-l">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.Service}:</label>
                            <div class="col-sm-10">
                                <select class="form-control  m-b" name="service" id="service">
                                    <option value="0">{$smarty.config.Select} </option>
                                    {html_options values=$serviceids output=$servicevals selected=$idservice}
                                </select>
                            </div>
                        </div>


                    </div>
                    <div class="col-lg-6 b-l">
                        <div class="form-group">
                            <label class="col-sm-3 control-label text-right">{$smarty.config.Reason}:</label>
                            <div class="col-sm-9">
                                <select class="form-control  m-b" name="reason" id="reason">
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
                            <div class="form-group">
                                <label class="col-sm-1 control-label">{$smarty.config.Attachments}:</label>
                                <div class="col-sm-11">
                                    {$attach_files}
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}


                <div class="row wrapper  white-bg ">
                    <div class="col-sm-12  b-l">
                        <div class="form-group">
                            <label class="col-sm-1 control-label">{$smarty.config.Description}:</label>
                            <div class="col-sm-11">
                                <div id="summernote">{$description}</div>
                            </div>
                        </div>
                    </div>
                </div>


            </form>
            <!-- End form area -->
        <!-- Icons -->
            {if $displayprint != 0 || $displaychanges != 0 || $displayassume != 0 ||  $displayrepass != 0 || $displayreject != 0 || $displayclose != 0 || $displayreopen != 0 || $displayapprove != 0 || $displayreturn != 0 || $displayreprove != 0}
            <div class="row wrapper  white-bg " >
                <hr style="margin-bottom:3px !important; margin-top:2px !important; margin-right:20px !important; margin-left:2px !important;"/>
            </div>

            <div class="row wrapper  white-bg text-left">
                {if $displaychanges == 1}
                <button type="button" class="btn btn-default btn-sm" id="btnSaveChanges">
                    <span class="fa fa-save"></span>  &nbsp;{$smarty.config.btn_save_changes}
                </button>
                {/if}

                {if $displayassume == 1}
                <button type="button" class="btn btn-default btn-sm" id="btnAssume">
                    <span class="fa fa-check-square"></span>  &nbsp;{$smarty.config.btn_assume}
                </button>
                {/if}

                {if $displayopaux == 1}
                <button type="button" class="btn btn-default btn-sm" id="btnOpAux">
                    <span class="fa fa-user-plus"></span>  &nbsp;{$smarty.config.btn_ope_aux}
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
                    <button type="button" class="btn btn-default btn-sm" id="btnClose">
                        <span class="fa fa-times-circle"></span>  &nbsp;{$smarty.config.btn_close}
                    </button>
                {/if}

                {if $displayreopen == 1}
                    <button type="button" class="btn btn-default btn-sm" id="btnReopen">
                        <span class="fa fa-sync"></span>  &nbsp;{$smarty.config.btn_reopen}
                    </button>
                {/if}

                {if $displayevaluate == 1}
                    <button type="button" class="btn btn-default btn-sm" id="btnEvaluate">
                        <span class="fa fa-comment-o"></span>  &nbsp;{$smarty.config.Btn_evaluate}
                    </button>
                {/if}

                {if $displayapprove == 1}
                    <button type="button" class="btn btn-default btn-sm" id="btnRequestApproveApp">
                        <span class="fa fa-thumbs-up"></span>  &nbsp;{$smarty.config.Request_approve_app}
                    </button>
                {/if}

                {if $displayreturn == 1}
                    <button type="button" class="btn btn-default btn-sm" id="btnRequestReturnApp">
                        <span class="fa fa-undo-alt"></span>  &nbsp;{$smarty.config.Request_return_app}
                    </button>
                {/if}

                {if $displayreprove == 1}
                    <button type="button" class="btn btn-default btn-sm" id="btnRequestDisapproveApp">
                        <span class="fa fa-thumbs-down"></span>  &nbsp;{$smarty.config.Request_reprove_app}
                    </button>
                {/if}

                {if $displayprint == 1}
                    <button type="button" class="btn btn-default btn-sm" id="btnPrint">
                        <span class="glyphicon glyphicon-print"></span> &nbsp;{$smarty.config.Print}
                    </button>
                    <!--<button type="button" class="btn btn-default btn-sm" id="btnPrint">
                        <span class="glyphicon glyphicon-print"></span> &nbsp;{$smarty.config.Print}
                    </button>-->
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
                        <div class="col-lg-3">
                            <h4 >{$smarty.config.Time_expended}:</h4>
                        </div>
                        <div class="col-lg-9 row wrapper form-group">
                            <label class="col-lg-3 control-label">{$smarty.config.Execution_date}: </label>
                            <div class="col-lg-3">
                                <div class="input-group date">
                                    <input type="text" id="execdate" name="execdate" class="form-control input-sm" value="{$now}" readonly />
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div >
                        <div class="col-lg-3">
                            <h4 ></h4>
                        </div>
                        <div class="col-lg-9 row wrapper form-group">
                            <label class="col-lg-1">{$smarty.config.Started}: </label>
                            <div class="col-lg-2 control-text">
                                <input type="text" id="started" name="started" class="form-control input-sm" value="{$smarty.now|date_format:'%H:%M:%S'}"  />
                            </div>
                            <label class="col-lg-1 control-label">{$smarty.config.Finished_alt}: </label>
                            <div class="col-lg-2">
                                <input type="text" id="finished" name="finished" class="form-control input-sm" value="{$datedefault}"  />
                            </div>
                            <label class="col-lg-2 control-label">{$smarty.config.Total_minutes}: </label>
                            <div class="col-lg-2 input-group">
                                <input id="totalminutes" name="totalminutes" class="form-control input-sm timer" value=""/>
                                <span class="input-group-addon" id="btnTimer"><i class="glyphicon glyphicon-time"></i></span>
                            </div>

                        </div>
                    </div>
                    <div >
                        <div class="col-lg-3">
                            <h4 ></h4>
                        </div>
                        <div class="col-lg-9 row wrapper form-group">
                            <label class="col-lg-1">{$smarty.config.Hour}: </label>
                            <div class="col-lg-2">
                                <select class="form-control  m-b" name="typehour" id="typehour">
                                    {html_options values=$typehourids output=$typehourvals selected=$idtypehour}
                                </select>
                            </div>
                            <label class="col-lg-1">{$smarty.config.Visible}: </label>
                            <div class="col-lg-2">
                                <select class="form-control  m-b" name="typenote" id="typenote">
                                    {html_options values=$typenoteids output=$typenotevals selected=$idtypenote}
                                </select>
                            </div>
                            <div class="col-lg-5">
                                <label class="checkbox-inline i-checks"> <input type="checkbox" id="callback" name="callback" value="1">&nbsp;{$smarty.config.Time_return}</label>
                            </div>

                        </div>
                    </div>
                </div>
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
                            <div class="col-sm-10 b-l text-center">
                                <!-- This is the dropzone element -->
                                <div id="myDropzone" class="dropzone dz-default dz-message" style="max-width:750px;">


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- -->

            <div class="col-xs-12 white-bg" style="height:10px;"></div>

            <div class="row wrapper  white-bg ">
                <div class="col-sm-12 text-center">
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-primary" id="btnSendNote" >
                            <i class='fa fa-paper-plane' aria-hidden="true"></i>
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

    {include file='modals/viewticket/modal-change-expire.tpl'}
    {include file='modals/viewticket/modal-add-attway.tpl'}
    {include file='modals/viewticket/modal-assume.tpl'}
    {include file='modals/newticket/modal-repass.tpl'}
    {include file='modals/viewticket/modal-reject.tpl'}
    {include file='modals/viewticket/modal-close.tpl'}
    {include file='modals/viewticket/modal-aux-operator.tpl'}
    {include file='modals/viewticket/modalCancel.tpl'}
    {include file='modals/viewticket/modalReopen.tpl'}
    {include file='modals/viewticket/modalEvaluate.tpl'}
    {include file='modals/viewticket/modalDeleteNote.tpl'}
    {include file='modals/main/modal-alert.tpl'}



</body>

</html>
