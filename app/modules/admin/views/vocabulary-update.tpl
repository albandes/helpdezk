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
    {head_item type="js" src="$path/app/modules/admin/views/js/" files="createvocabulary.js"}
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
                ticketAcceptedFiles = '{/literal}{$ticketacceptedfiles}{literal}',
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



        <div class="wrapper wrapper-content  ">

            <div class="row wrapper white-bg ibox-title">
                <div class="col-sm-4">
                    <h4>{$smarty.config.records} / {$smarty.config.pgr_vocabulary} / <strong>{$smarty.config.edit}</strong></h4>
                </div>
            </div>


            <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>

            <!-- First Line -->


            <div class="col-xs-12 white-bg" style="height:10px;"></div>


            <!-- Form area -->
            <form method="get" class="form-horizontal" id="update-vocabulary-form">

                <!-- Hidden -->
                <input type="hidden" name="_token" id= "_token" value="{$token}">
                <input type="hidden" name="hidden_vocabulary" id= "hidden_vocabulary" value="{$hidden_vocabulary}">
                <input type="hidden" id= "typeAction" value="upd">

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-1 b-l">
                    </div>

                    <div class="col-sm-10 b-l">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.Module}:</label>
                            <div class="col-sm-4">
                                <select class="form-control input-sm" id="cmbModule" name="cmbModule" >
                                    <option value="">{$smarty.config.Select}</option>
                                    {html_options values=$moduleids output=$modulevals selected=$idmodule}
                                </select>
                            </div>
                            <div class="col-sm-1 ">
                                <button class="btn btn-default lbltooltip" id="btnAddModule" type="button" tabindex="-1" data-toggle="tooltip" data-placement="right" title="{$smarty.config.tt_add_module_btn}">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.vocabulary_key_name}:</label>
                            <div class="col-sm-7">
                                <input type="text" id="keyName" name="keyName" class="form-control input-sm" value="{$keyName}" >
                            </div>
                        </div>

                    </div>

                </div>

                <div class="col-xs-12 white-bg" style="height:30px;"></div>

                <div class="row wrapper  white-bg textcenter">
                    <div class="col-sm-12 b-l">
                        <div class="col-sm-2 b-l"></div>
                        <div class="col-sm-8 b-l">
                            <table id="localeTab" class="table table-hover">
                                <thead>
                                <tr>
                                    <th class="col-sm-3 text-center"><h4><strong>{$smarty.config.vocabulary_locale}</strong></h4></th>
                                    <th class="col-sm-8 text-center"><h4><strong>{$smarty.config.vocabulary_key_value}</strong></h4></th>
                                    <th class="col-sm-1 text-center"><h4><strong>{$smarty.config.TMS_Delete}</strong></h4></th>
                                </tr>
                                </thead>
                                <tbody>
                                {foreach $aItens as $key => $value}
                                <tr>
                                    <td>
                                        <select class="form-control input-sm cmbLocale" name="localeID[]" id="localeID_{($key + 1)}">
                                            {html_options values=$localeids output=$localevals selected=$value.idlocale}
                                        </select>
                                        <input type="hidden" id="numId" value="{($key + 1)}"/>
                                        <input type="hidden" name="vocabularyID[]" id="vocabularyID_{($key + 1)}" value="{$value.idvocabulary}"/>
                                    </td>
                                    <td>
                                        <input type="text" name="keyValue[]" id="keyValue_{($key + 1)}" class="form-control input-sm" value="{$value.key_value}" />
                                    </td>
                                    <td class="text-center"><a href="javascript:;" onclick="removeRow(this,'localeTab','upd')" class="btn btn-danger bt-xs"><i class="fa fa-times"></i></a></td>
                                </tr>
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-sm-12 b-l">
                        <div class="col-sm-2 b-l"></div>
                        <div class="col-sm-8 b-l">
                            <button type="button" class="btn btn-primary btn-md " id="btnAddKeyValue" >
                                <span class="fa fa-plus"></span>  &nbsp;{$smarty.config.Add} {$smarty.config.pgr_vocabulary}
                            </button>
                        </div>
                    </div>

                </div>

                <div class="col-xs-12 white-bg" style="height:10px;"></div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-12 b-l">
                        <div id="alert-update-vocabulary"></div>
                    </div>
                </div>

                <div class="row wrapper  border-bottom white-bg ">
                    &nbsp;
                </div>

                <div class="col-xs-12 white-bg" style="height:10px;"></div>


                <div class="row wrapper  white-bg text-center">

                    <div class="col-sm-12">
                        <div class="form-group">
                            <a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa-arrow-alt-circle-left" aria-hidden="true"></i> {$smarty.config.Back_btn} </a>
                            <button type="button" class="btn btn-primary btn-md" id="btnUpdateVocabulary" >
                                <span class="fa fa-save"></span>  &nbsp;{$smarty.config.Save}
                            </button>
                        </div>
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

        {include file='modals/main/modal-alert.tpl'}
        {include file='modals/programs/modal-module.tpl'}

    </div>
</div>

</body>

</html>

