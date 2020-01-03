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
    {head_item type="js"  src="$path/includes/js/plugins/jqGrid/i18n/" files="$jqgrid_i18nFile"}
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
    {head_item type="js" src="$path/app/modules/admin/views/js/" files="features.js"}
    <!-- Font Awesome -->
    {*head_item type="css" src="$path/css/font-awesome/css/" files="font-awesome.css"*}
    {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
    <!-- animate -->
    {head_item type="css" src="$path/css/" files="animate.css"}
    <!-- Helpdezk CSS -->
    {head_item type="css" src="$path/css/" files="$theme.css"}
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
   {* <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>*}
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
    <!-- Summernote Editor -->
    {head_item type="css" src="$path/css/plugins/summernote/$summernote_version/" files="summernote.css"}
    {head_item type="css" src="$path/css/plugins/summernote/" files="summernote-bs3.css"}
    {head_item type="js"  src="$path/includes/js/plugins/summernote/$summernote_version/" files="summernote.js"}
    

    {literal}
    <script type="text/javascript">
         var     default_lang = "{/literal}{$lang}{literal}",
                 path = "{/literal}{$path}{literal}",
                 langName = '{/literal}{$smarty.config.Name}{literal}',
                 theme = '{/literal}{$theme}{literal}',
                 mascDateTime = '{/literal}{$mascdatetime}{literal}',
                 timesession = '{/literal}{$timesession}{literal}',
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

            <div class="row border-bottom"></div>

            <div class="wrapper wrapper-content">
                <div class="row wrapper white-bg ibox-title">
                    <div class="col-sm-4">
                        <h4>{$smarty.config.cat_config} / <strong>{$smarty.config.pgr_sys_features}</strong></h4>
                    </div>
                </div>

                <div class="row wrapper  border-bottom white-bg ">
                    &nbsp;
                </div>

                <div class="col-sm-12 white-bg" style="height:10px;"></div>

                <div class="row wrapper  white-bg ">
                    <div class="row col-sm-12 b-l">
                        <div class="form-group">
                            <label class="col-sm-3 control-label text-right">{$smarty.config.Type}:</label>
                            <div class="col-sm-3">
                                <select class="form-control input-sm"  id="cmbModule" name="cmbModule" >
                                    {html_options values=$moduleids output=$modulevals selected=$idmodule}
                                </select>
                            </div>
                            <div id="addConf" class="col-sm-6 text-right hide">
                                <div class="form-group">
                                    <div class="col-sm-5">
                                        <button type="button" class="btn btn-primary btn-md " id="btnAddNewConf" >
                                            <span class="fa fa-plus-square"></span>  &nbsp;{$smarty.config.new_feature}
                                        </button>
                                    </div>
                                    <div class="col-sm-2">&nbsp;</div>
                                    <div class="col-sm-5">
                                        <button type="button" class="btn btn-primary btn-md " id="btnAddConfCateg" >
                                            <span class="fa fa-plus-square"></span>  &nbsp;{$smarty.config.Add_category}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 white-bg" style="height:30px;"></div>

                <div id="hdkLoader"class="row wrapper white-bg "></div>

                <div class="row wrapper  white-bg "></div>

                <input type="hidden" name="_token" id= "_token" value="{$token}">
                <div class="row wrapper  white-bg moduleConfigs hide">

                </div>

                <!-- Form area - E-mail Config -->
                <form method="post" class="form-horizontal mainConfigs" name="formEmailConfig" id="formEmailConfig">
                    <div class="row wrapper  white-bg "></div>
                    <div class="row wrapper  white-bg ">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-at" aria-hidden="true"></i>&nbsp;{$smarty.config.pgr_email_config}
                            </div>
                            <div class="panel-body">
                                <div class="row col-sm-12 b-l">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.Title}:</label>
                                        <div class="col-sm-5 control-text">
                                            <input type="text" id="mailtitle" name="mailtitle" class="form-control input-sm" value="{$emtitle}" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.Email_host}:</label>
                                        <div class="col-sm-5 control-text">
                                            <input type="text" id="mailhost" name="mailhost" class="form-control input-sm" value="{$emhost}" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.Domain}:</label>
                                        <div class="col-sm-5 control-text">
                                            <input type="text" id="maildomain" name="maildomain" class="form-control input-sm" value="{$domain}" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.User_login}:</label>
                                        <div class="col-sm-5 control-text">
                                            <input type="text" id="mailuser" name="mailuser" class="form-control input-sm" value="{$emuser}" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.Password}:</label>
                                        <div class="col-sm-5 control-text">
                                            <input type="text" id="mailpass" name="mailpass" class="form-control input-sm" value="{$empassword}" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.Email_sender}:</label>
                                        <div class="col-sm-5 control-text">
                                            <input type="text" id="mailsender" name="mailsender" class="form-control input-sm" value="{$emsender}" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.Port}:</label>
                                        <div class="col-sm-5 control-text">
                                            <input type="text" id="mailport" name="mailport" class="form-control input-sm" value="{$emport}" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label  class="col-sm-3 control-label">{$smarty.config.Requires_Autentication}?</label>
                                        <div class="checkbox i-checks"><label> <input type="checkbox" id="authcheck" name="authcheck" value="S" {$auth} > </label></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.Header}:</label>
                                        <div class="col-sm-5">
                                            <div id="emailHeader" name="emailHeader" >{$emheader}</div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.Footer}:</label>
                                        <div class="col-sm-5">
                                            <div id="emailFooter" name="emailFooter" >{$emfooter}</div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label  class="col-sm-3 control-label">{$smarty.config.Success_logs}?</label>
                                        <div class="checkbox i-checks"><label> <input type="checkbox" id="successcheck" name="successcheck" value="1" {$successcheck} > </label></div>
                                    </div>
                                    <div class="form-group">
                                        <label  class="col-sm-3 control-label">{$smarty.config.Failure_logs}?</label>
                                        <div class="checkbox i-checks"><label> <input type="checkbox" id="failurecheck" name="failurecheck" value="1" {$failurecheck} > </label></div>
                                    </div>
                                    <div class="form-group">
                                        <label  class="col-sm-3 control-label">{$smarty.config.tracker_status}?</label>
                                        <div class="checkbox i-checks"><label> <input type="checkbox" id="trackercheck" name="trackercheck" value="1" {$trackercheck} > </label></div>
                                    </div>
                                    <div class="form-group">
                                        <label  class="col-sm-3 control-label">{$smarty.config.Email_byCron}?</label>
                                        <div class="checkbox i-checks"><label> <input type="checkbox" id="emailbycron" name="emailbycron" value="1" {$emailbycroncheck} > </label></div>
                                    </div>
                                </div>
                            </div>
                            <div  class="panel-footer text-center">
                                 <button type="button" class="btn btn-primary btn-md " id="btnSaveEmailConfig" >
                                    <span class="fa fa-save"></span>  &nbsp;{$smarty.config.Save}
                                </button>
                            </div>
                        </div>

                    </div>                    
                </form>

                <!-- Form area - Pop Server -->
                <form method="post" class="form-horizontal mainConfigs" name="formPopServer" id="formPopServer">
                    <div class="row wrapper  white-bg "></div>
                    <div class="row wrapper  white-bg ">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-at" aria-hidden="true"></i>&nbsp;{$smarty.config.Pop_server}
                            </div>
                            <div class="panel-body">
                                <div class="row col-sm-12 b-l">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Host:</label>
                                        <div class="col-sm-5 control-text">
                                            <input type="text" id="pophost" name="pophost" class="form-control input-sm" value="{$pophost}" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.Type}:</label>
                                        <div class="col-sm-5">
                                            <select class="form-control input-sm"  id="popType" name="popType" >
                                                {html_options values=$poptypeids output=$poptypevals selected=$idpoptype}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.Port}:</label>
                                        <div class="col-sm-5 control-text">
                                            <input type="text" id="popport" name="popport" class="form-control input-sm" value="{$popport}" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.Domain}:</label>
                                        <div class="col-sm-5 control-text">
                                            <input type="text" id="popdomain" name="popdomain" class="form-control input-sm" value="{$popdomain}" >
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div  class="panel-footer text-center">
                                 <button type="button" class="btn btn-primary btn-md " id="btnSavePopServer" >
                                    <span class="fa fa-save"></span>  &nbsp;{$smarty.config.Save}
                                </button>
                            </div>
                        </div>

                    </div>                    
                </form>

                <!-- Form area - LDAP Integration -->
                <form method="post" class="form-horizontal mainConfigs" name="formLDAP" id="formLDAP">
                    <div class="row wrapper  white-bg "></div>
                    <div class="row wrapper  white-bg ">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-network-wired" aria-hidden="true"></i>&nbsp;{$smarty.config.Integration_ldap}
                            </div>
                            <div class="panel-body">
                                <div class="row col-sm-12 b-l">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.Type}:</label>
                                        <div class="col-sm-5">
                                            <select class="form-control input-sm"  id="ldaptype" name="ldaptype" >
                                                {html_options values=$ldaptypeids output=$ldaptypevals selected=$ldaptype}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.ldap_server}:</label>
                                        <div class="col-sm-5 control-text">
                                            <input type="text" id="ldapserver" name="ldapserver" class="form-control input-sm" value="{$ldapserver}" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.ldap_dn}:</label>
                                        <div class="col-sm-5 control-text">
                                            <input type="text" id="ldapdn" name="ldapdn" class="form-control input-sm" value="{$ldapdn}" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.ldap_domain}:</label>
                                        <div class="col-sm-5 control-text">
                                            <input type="text" id="ldapdomain" name="ldapdomain" class="form-control input-sm" value="{$ldapdomain}" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.ldap_field}:</label>
                                        <div class="col-sm-5 control-text">
                                            <input type="text" id="ldapfield" name="ldapfield" class="form-control input-sm" value="{$ldapfield}" >
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="col-sm-12 alert alert-info">
                                                <small>{$smarty.config.ldap_field_obs}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div  class="panel-footer text-center">
                                 <button type="button" class="btn btn-primary btn-md " id="btnSaveLDAP" >
                                    <span class="fa fa-save"></span>  &nbsp;{$smarty.config.Save}
                                </button>
                            </div>
                        </div>

                    </div>                    
                </form>

                <!-- Form area - Maintenance -->
                <form method="post" class="form-horizontal mainConfigs" name="formMaintenance" id="formMaintenance">
                    <div class="row wrapper  white-bg "></div>
                    <div class="row wrapper  white-bg ">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-tools" aria-hidden="true"></i>&nbsp;{$smarty.config.Maintenance}
                            </div>
                            <div class="panel-body">
                                <div class="row col-sm-12 b-l">
                                    <div class="form-group">
                                        <label  class="col-sm-3 control-label">{$smarty.config.Maintenance}?</label>
                                        <div class="checkbox i-checks"><label> <input type="checkbox" id="maintenanceChk" name="maintenanceChk" value="S" {$maintenancecheck} > </label></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.Header}:</label>
                                        <div class="col-sm-5">
                                            <div id="maintenanceMsg" name="maintenanceMsg" >{$mainmsg}</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div  class="panel-footer text-center">
                                <button type="button" class="btn btn-primary btn-md " id="btnSaveMaintenance" >
                                    <span class="fa fa-save"></span>  &nbsp;{$smarty.config.Save}
                                </button>
                            </div>
                        </div>

                    </div>                    
                </form>

                <!-- Form area - Log -->
                <form method="post" class="form-horizontal mainConfigs" name="formLog" id="formLog">
                    <div class="row wrapper  white-bg "></div>
                    <div class="row wrapper  white-bg ">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-file-alt" aria-hidden="true"></i>&nbsp;{$smarty.config.ERP_Log}
                            </div>
                            <div class="panel-body">
                                <div class="row col-sm-12 b-l">
                                    <div class="form-group">
                                        <label  class="col-sm-3 control-label">{$smarty.config.log_general}?</label>
                                        <div class="checkbox i-checks"><label> <input type="checkbox" id="logGeneralChk" name="logGeneralChk" value="S" {$logGeneralChkd} > </label></div>
                                    </div>
                                    <div class="form-group">
                                        <label  class="col-sm-3 control-label">{$smarty.config.log_email}?</label>
                                        <div class="checkbox i-checks"><label> <input type="checkbox" id="logEmailChk" name="logEmailChk" value="S" {$logEmailChkd} > </label></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.log_host}:</label>
                                        <div class="col-sm-5">
                                            <select class="form-control input-sm"  id="logHostType" name="logHostType" >
                                                {html_options values=$loghosttypeids output=$loghosttypevals selected=$idloghosttype}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.log_remote_server}:</label>
                                        <div class="col-sm-5 control-text">
                                            <input type="text" id="logServer" name="logServer" class="form-control input-sm" value="{$srvremote}" {$srvremoteflg} >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{$smarty.config.log_level}:</label>
                                        <div class="col-sm-5 control-text">
                                            <input type="text" id="logLevel" name="logLevel" class="form-control input-sm" value="{$loglevel}" >
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div  class="panel-footer text-center">
                                <button type="button" class="btn btn-primary btn-md " id="btnSaveLog" >
                                    <span class="fa fa-save"></span>  &nbsp;{$smarty.config.Save}
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

                <!-- Form area - Miscellaneous -->
                <form method="post" class="form-horizontal mainConfigs" name="formMisc" id="formMisc">
                    <div class="row wrapper  white-bg "></div>
                    <div class="row wrapper  white-bg ">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-grip-vertical" aria-hidden="true"></i>&nbsp;{$smarty.config.Other_items}
                            </div>
                            <div class="panel-body">
                                <div class="row col-sm-12 b-l">
                                    <div class="form-group">
                                        <label  class="col-sm-3 control-label">{$smarty.config.sys_2FAuthentication}?</label>
                                        <div class="checkbox i-checks"><label> <input type="checkbox" id="TwoFAuthChk" name="TwoFAuthChk" value="S" {$TwoFAuthChkd} > </label></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{$smarty.config.country_default}:</label>
                                    <div class="col-sm-5">
                                        <select class="form-control input-sm"  id="cbmDefCountry" name="cbmDefCountry" >
                                            {html_options values=$defcountryids output=$defcountryvals selected=$iddefcountry}
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{$smarty.config.sys_session_time_lbl}:</label>
                                    <div class="col-sm-5 control-text">
                                        <input type="text" id="timeSession" name="timeSession" class="form-control input-sm" value="{$timesession}" >
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="col-sm-12 alert alert-info">
                                            <small>{$smarty.config.sys_time_session}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div  class="panel-footer text-center">
                                <button type="button" class="btn btn-primary btn-md " id="btnSaveMisc" >
                                    <span class="fa fa-save"></span>  &nbsp;{$smarty.config.Save}
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

                <div class="row wrapper  border-bottom white-bg ">
                    &nbsp;
                </div>

                <div class="footer">
                    {include file=$footer}
                </div>

            </div>
        </div>

    </div>


    {include file='modals/main/modal-alert.tpl'}
    {include file='modals/features/modal-add-feature.tpl'}
    {include file='modals/features/modal-add-category.tpl'}




</body>

</html>