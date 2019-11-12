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
    {head_item type="js" src="$path/app/modules/helpdezk/views/js/" files="email-template.js"}
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
    <!-- Input Mask-->
    {head_item type="js" src="$path/includes/js/plugins/jquery-mask/" files="jquery.mask.min.js"}
    <!-- Autocomplete -->
    {head_item type="js" src="$path/includes/js/plugins/autocomplete/" files="jquery.autocomplete.js"}
    {head_item type="css" src="$path/includes/js/plugins/autocomplete/" files="jquery.autocomplete.css"}
    <!-- Combo Autocomplete -->
    {head_item type="js" src="$path/includes/js/plugins/chosen/" files="chosen.jquery.js"}
    {head_item type="css" src="$path/css/plugins/chosen/" files="chosen.css"}
    <!-- Summernote Editor -->
    {head_item type="css" src="$path/css/plugins/summernote/$summernote_version/" files="summernote.css"}
    {head_item type="css" src="$path/css/plugins/summernote/" files="summernote-bs3.css"}
    {head_item type="js"  src="$path/includes/js/plugins/summernote/$summernote_version/" files="summernote.js"}
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

        .scrollable-table {
            max-height: 331px;
            overflow: auto;
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
            <div class="row wrapper    white-bg ibox-title">
                <div class="col-sm-4">
                    <h4>Cadastros / {$smarty.config.pgr_erp_emailtemplate} / <strong>{$smarty.config.Add}</strong></h4>
                </div>
                <div class="col-sm-8 text-right">
                    &nbsp;
                </div>
            </div>

            <div class="row wrapper  border-bottom white-bg ">
                &nbsp;
            </div>

            <!-- First Line -->


            <div class="col-xs-12 white-bg" style="height:10px;"></div>


            <!-- Form area -->
            <form method="get" class="form-horizontal" id="update-emailconf-form">

                <input type="hidden" name="_token" id= "_token" value="{$token}">

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-2 b-l"></div>

                    <div class="col-sm-10 b-l">
                        <div class="form-group">
                            <label class="col-sm-3 control-label text-right">{$smarty.config.Subject}:</label>
                            <div class="col-sm-5">
                                <input type="text" id="templateName" name="templateName" class="form-control input-sm" value="" >
                            </div>
                        </div>

                        
                    </div>

                </div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-12 b-l pull-center">
                        <div class="col-sm-6">
                            <div id="description" name="description" ></div>
                        </div>

                        <div class="col-sm-6">
                            <div class="panel-group">
                                <div id="panelVars" class="panel panel-primary">
                                    <div id="tab-type-itens" class="scrollable-table">
                                        <table id='browser' class="table table-hover">
                                            <colgroup>
                                                <col width='25%'/>
                                                <col width='75%'/>
                                            </colgroup>
                                            <tbody>
                                                <tr>
                                                    <td>$USER</td>
                                                    <td>{$smarty.config.Var_user}</td>
                                                </tr>
                                                <tr>
                                                    <td>$PHONE</td>
                                                    <td>{$smarty.config.Var_phone}</td>
                                                </tr>
                                                <tr>
                                                    <td>$BRANCH</td>
                                                    <td>{$smarty.config.Var_branch}</td>
                                                </tr>
                                                <tr>
                                                    <td>$DATE</td>
                                                    <td>{$smarty.config.Var_date}</td>
                                                </tr>
                                                <tr>
                                                <td>$REQUEST</td>
                                                    <td>{$smarty.config.Var_request}</td>
                                                </tr>
                                                <tr>
                                                    <td>$SUBJECT</td>
                                                    <td>{$smarty.config.Var_subject}</td>
                                                </tr>
                                                <tr>
                                                    <td>$DESCRIPTION</td>
                                                    <td>{$smarty.config.Var_description}</td>
                                                </tr>
                                                <tr>
                                                    <td>$RECORD</td>
                                                    <td>{$smarty.config.Var_record}</td>
                                                </tr>
                                                <tr>
                                                    <td>$EXPIRE_DATE</td>
                                                    <td>{$smarty.config.Var_expire}</td>
                                                </tr>
                                                <tr>
                                                    <td>$ASSUME</td>
                                                    <td>{$smarty.config.Var_assume}</td>
                                                </tr>
                                                <tr>
                                                    <td>$FINISH_DATE</td>
                                                    <td>{$smarty.config.Var_finish}</td>
                                                </tr>
                                                <tr>
                                                    <td>$REJECTION</td>
                                                    <td>{$smarty.config.Var_rejection}</td>
                                                </tr>
                                                <tr>
                                                    <td>$STATUS</td>
                                                    <td>{$smarty.config.Var_status}</td>
                                                </tr>
                                                <tr>
                                                    <td>$REQUESTER</td>
                                                    <td>{$smarty.config.Var_requester}</td>
                                                </tr>
                                                <tr>
                                                    <td>$INCHARGE</td>
                                                    <td>{$smarty.config.Var_incharge}</td>
                                                </tr>
                                                <tr>
                                                    <td>$NT_OPERATOR</td>
                                                    <td>{$smarty.config.Var_nt_operator}</td>
                                                </tr>
                                                <tr>
                                                    <td>$NT_USER</td>
                                                    <td>{$smarty.config.Var_nt_user}</td>
                                                </tr>
                                                <tr>
                                                    <td>$LINK_OPERATOR</td>
                                                    <td>{$smarty.config.Var_link_operator}</td>
                                                </tr>
                                                <tr>
                                                    <td>$LINK_USER</td>
                                                    <td>{$smarty.config.Var_link_user}</td>
                                                </tr>
                                                <tr>
                                                    <td>$EVALUATION</td>
                                                    <td>{$smarty.config.Var_evaluation}</td>
                                                </tr>
                                                <tr>
                                                    <td>$LINK_EVALUATE</td>
                                                    <td>{$smarty.config.Var_link_evaluation}</td>
                                                </tr>                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-2 b-l"></div>

                    <div class="col-sm-10 b-l">
                        <div id="alert-update-template"></div>
                    </div>
                    
                </div>

                <div class="row wrapper  border-bottom white-bg ">
                    &nbsp;
                </div>

                <div class="col-xs-12 white-bg" style="height:10px;"></div>

                <div class="row wrapper  white-bg text-center">
                    <div class="col-sm-12 b-l">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa-arrow-alt-circle-left" aria-hidden="true"></i> Volta </a>
                                <button type="button" class="btn btn-primary btn-md" id="btnUpdateTemplate" >
                                    <span class="fa fa-save"></span>  &nbsp;{$smarty.config.Save}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
            <!-- End form area -->


            <div class="row border-bottom white-bg ">
            <div class="row border-bottom">


            <div class="footer">
                {include file=$footer}
            </div>
</div>


                {include file='modals/main/modal-alert.tpl'}




</body>

</html>
