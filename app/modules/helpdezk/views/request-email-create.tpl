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
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/app/modules/helpdezk/views/js/" files="createrequestemail.js"}
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

        #btnCancel{
            margin-left: 150px;
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

            <div class="wrapper wrapper-content">

                <div class="row wrapper white-bg ibox-title">
                    <div class="col-sm-4">
                        <h4>Cadastros / {$smarty.config.pgr_email_request} / <strong>{$smarty.config.Add}</strong></h4>
                    </div>
                </div>            

                <div class="row wrapper border-bottom white-bg "></div>

                <!-- First Line -->
                <div class="col-xs-12 white-bg" style="height:10px;"></div>

                <!-- Form area -->
                <form method="get" class="form-horizontal" id="create-request-email-form">

                    <!-- Hidden -->
                    <input type="hidden" name="_token" id= "_token" value="{$token}">

                    <div class="row wrapper  white-bg ">

                        <div class="col-sm-2 b-l">                    
                        </div>

                        <div class="col-sm-10 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Server}:</label>
                                <div class="col-sm-5">
                                    <input type="text" id="txtServer" name="txtServer" class="form-control input-sm" placeholder="{$plh_program_description}" value="" >
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Type}:</label>
                                <div class="col-sm-5">
                                    <select class="form-control input-sm"  id="cmbSrvType" name="cmbSrvType" data-placeholder="{$plh_module_select}" >
                                        {html_options values=$srvtypeids output=$srvtypevals selected=$idsrvtype}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Port}:</label>
                                <div class="col-sm-5">
                                    <input type="text" id="txtPort" name="txtPort" class="form-control input-sm" required placeholder="{$plh_program_description}" value="" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.email}:</label>
                                <div class="col-sm-5">
                                    <input type="text" id="txtEmail" name="txtEmail" class="form-control input-sm" required placeholder="{$plh_controller_description}" value="" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Password}:</label>
                                <div class="col-sm-5 lbltooltip" data-toggle="tooltip" data-placement="right" title="{$smarty.config.Alert_add_program_title}">
                                    <input type="password" id="txtPassword" name="txtPassword" class="form-control input-sm" required placeholder="{$plh_module_smartyvar}" value="" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Area}:</label>
                                <div class="col-sm-5">
                                    <select class="form-control input-sm"  id="cmbArea" name="cmbArea" data-placeholder="{$plh_category_select}" >
                                        <option value="">{$smarty.config.Select}</option>
                                        {html_options values=$areaids output=$areavals selected=$idarea}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Type}:</label>
                                <div class="col-sm-5">
                                    <select class="form-control input-sm"  id="cmbType" name="cmbType" data-placeholder="{$plh_category_select}" >
                                        <option value="">{$smarty.config.Select}</option>
                                        {html_options values=$typeids output=$typevals selected=$idtype}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Item}:</label>
                                <div class="col-sm-5">
                                    <select class="form-control input-sm"  id="cmbItem" name="cmbItem" data-placeholder="{$plh_category_select}" >
                                        <option value="">{$smarty.config.Select}</option>
                                        {html_options values=$itemids output=$itemvals selected=$iditem}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Service}:</label>
                                <div class="col-sm-5">
                                    <select class="form-control input-sm"  id="cmbService" name="cmbService" data-placeholder="{$plh_category_select}" >
                                        <option value="">{$smarty.config.Select}</option>
                                        {html_options values=$serviceids output=$servicevals selected=$idservice}
                                    </select>
                                </div>
                            </div>

                             <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Filter_by_sender}:</label>
                                <div class="col-sm-5">
                                    <input type="text" id="txtFilterSender" name="txtFilterSender" class="form-control input-sm" placeholder="{$plh_program_description}" value="" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Filter_by_subject}:</label>
                                <div class="col-sm-5">
                                    <input type="text" id="txtFilterSubject" name="txtFilterSubject" class="form-control input-sm" placeholder="{$plh_controller_description}" value="" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Create_user}:</label>
                                <div class="checkbox i-checks"><label> <input type="checkbox" name="checkCreateUser" id="checkCreateUser" value="1"> <i></i> &nbsp;{$smarty.config.Create_user_msg}</label></div>
                            </div>

                            <div class="form-group createNewUser hide">
                                <label class="col-sm-2 control-label">{$smarty.config.Company}:</label>
                                <div class="col-sm-5">
                                    <select class="form-control input-sm"  id="cmbCompany" name="cmbCompany" data-placeholder="{$plh_category_select}" >
                                        <option value="">{$smarty.config.Select}</option>
                                        {html_options values=$companyids output=$companyvals selected=$idcompany}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group createNewUser hide">
                                <label class="col-sm-2 control-label">{$smarty.config.Department}:</label>
                                <div class="col-sm-5">
                                    <select class="form-control input-sm"  id="cmbDepartment" name="cmbDepartment" data-placeholder="{$plh_category_select}" >
                                        <option value="">{$smarty.config.Select}</option>
                                        {html_options values=$departmentids output=$departmentvals selected=$iddepartment}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Delete_emails}:</label>
                                <div class="checkbox i-checks"><label> <input type="checkbox" name="checkDeleteEmails" id="checkDeleteEmails" value="1"> <i></i> &nbsp;{$smarty.config.Delete_emails_msg}</label></div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Login_layout}:</label>
                                <div class="col-sm-5">
                                    <select class="form-control input-sm"  id="cmbLoginLayout" name="cmbLoginLayout" data-placeholder="{$plh_category_select}" >
                                        {html_options values=$loginlayoutids output=$loginlayoutvals selected=$idloginlayout}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Note}:</label>
                                <div class="checkbox i-checks"><label> <input type="checkbox" name="checkNote" id="checkNote" value="1"> <i></i> &nbsp;{$smarty.config.Note_msg}</label></div>
                            </div>

                        </div>

                    </div>

                    <div class="row wrapper  white-bg ">

                        <div class="col-sm-1 b-l">

                        </div>

                        <div class="col-sm-11 b-l">
                            <div id="alert-create-request-email"></div>
                        </div>

                    </div>

                    <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>

                    <div class="col-xs-12 white-bg" style="height:10px;"></div>


                    <div class="row wrapper  white-bg text-center">
                        <div class="col-sm-12 form-group">
                            <a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa-arrow-alt-circle-left" aria-hidden="true"></i> Volta </a>
                            <button type="button" class="btn btn-primary btn-md " id="btnCreateReqEmail" >
                                <span class="fa fa-save"></span>  &nbsp;{$smarty.config.Save}
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


    {include file='modals/main/modal-alert.tpl'}



</body>

</html>

