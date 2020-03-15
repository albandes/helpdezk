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
    <!-- Helpdezk -->
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/app/modules/admin/views/js/" files="createprogram.js"}
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
                        <h4>Cadastros / {$smarty.config.Programs} / <strong>{$smarty.config.Add}</strong></h4>
                    </div>
                </div>            

                <div class="row wrapper border-bottom white-bg "></div>

                <!-- First Line -->
                <div class="col-xs-12 white-bg" style="height:10px;"></div>

                <!-- Form area -->
                <form method="get" class="form-horizontal" id="create-program-form">

                    <!-- Hidden -->
                    <input type="hidden" name="_token" id= "_token" value="{$token}">

                    <div class="row wrapper  white-bg ">

                        <div class="col-sm-2 b-l">                    
                        </div>

                        <div class="col-sm-10 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Module}:</label>
                                <div class="col-sm-5">
                                    <select class="form-control input-sm"  id="cmbModule" name="cmbModule" data-placeholder="{$plh_module_select}" >
                                        {html_options values=$moduleids output=$modulevals selected=$idmodule}
                                    </select>
                                </div>
                                <div class="col-sm-1 ">
                                    <button class="btn btn-default" id="btnAddModule" type="button" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Category}:</label>
                                <div class="col-sm-5">
                                    <select class="form-control input-sm"  id="cmbCategory" name="cmbCategory" data-placeholder="{$plh_category_select}" >
                                        {html_options values=$categoryids output=$categoryvals selected=$idcategory}
                                    </select>
                                </div>
                                <div class="col-sm-1 ">
                                    <button class="btn btn-default" id="btnAddCategory" type="button" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Name}:</label>
                                <div class="col-sm-5">
                                    <input type="text" id="txtName" name="txtName" class="form-control input-sm" required placeholder="{$plh_program_description}" value="" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Controller}:</label>
                                <div class="col-sm-5">
                                    <input type="text" id="txtController" name="txtController" class="form-control input-sm" required placeholder="{$plh_controller_description}" value="" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Smarty}:</label>
                                <div class="col-sm-5 lbltooltip" data-toggle="tooltip" data-placement="right" title="{$smarty.config.Alert_add_program_title}">
                                    <input type="text" id="txtSmarty" name="txtSmarty" class="form-control input-sm" required placeholder="{$plh_module_smartyvar}" value="" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Operations}:</label>
                                <div class="checkbox i-checks"><label> <input type="checkbox" name="ope[]" id="N" value="2"> <i></i> &nbsp;{$smarty.config.New}</label></div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">&nbsp;</label>
                                <div class="checkbox i-checks"><label> <input type="checkbox" name="ope[]" id="E" value="3"> <i></i> &nbsp;{$smarty.config.edit}</label></div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">&nbsp;</label>
                                <div class="checkbox i-checks"><label> <input type="checkbox" name="ope[]" id="D" value="4"> <i></i> &nbsp;{$smarty.config.Delete}</label></div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">&nbsp;</label>
                                <div class="checkbox i-checks"><label> <input type="checkbox" name="ope[]" id="X" value="5"> <i></i> &nbsp;{$smarty.config.Export}</label></div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">&nbsp;</label>
                                <div class="checkbox i-checks"><label> <input type="checkbox" name="ope[]" id="M" value="6"> <i></i> &nbsp;{$smarty.config.email}</label></div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">&nbsp;</label>
                                <div class="checkbox i-checks"><label> <input type="checkbox" name="ope[]" id="S" value="7"> <i></i> &nbsp;{$smarty.config.SMS}</label></div>
                            </div>

                        </div>

                    </div>

                    <div class="row wrapper  white-bg ">

                        <div class="col-sm-1 b-l">

                        </div>

                        <div class="col-sm-11 b-l">
                            <div id="alert-create-program"></div>
                        </div>

                    </div>

                    <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>

                    <div class="col-xs-12 white-bg" style="height:10px;"></div>


                    <div class="row wrapper  white-bg text-center">
                        <div class="col-sm-12 form-group">
                            <a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa-arrow-alt-circle-left" aria-hidden="true"></i> {$smarty.config.Back_btn} </a>
                            <button type="button" class="btn btn-primary btn-md " id="btnCreateProgram" >
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


    {include file='modals/programs/modal-alert-create.tpl'}
    {include file='modals/programs/modal-module.tpl'}
    {include file='modals/programs/modal-category.tpl'}



</body>

</html>

