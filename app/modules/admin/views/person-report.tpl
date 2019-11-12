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
    {head_item type="css" src="$path/css/" files="admmenu.css"}

    {head_item type="js"  src="$path/includes/js/plugins/modernizr/" files="modernizr.custom.js"}
    <!-- Helpdezk -->
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/app/modules/admin/views/js/" files="personreport.js"}
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
    <!-- Helpdezk CSS -->
    {head_item type="css" src="$path/css/" files="$theme.css"}
    <!-- Datapicker  -->
    {head_item type="css" src="$path/css/plugins/datepicker/" files="datepicker3.css"}
    {head_item type="js"  src="$path/includes/js/plugins/datepicker/" files="bootstrap-datepicker.js"}
    {head_item type="js"  src="$path/includes/js/plugins/datepicker/locales/" files="bootstrap-datepicker.pt-BR.min.js"}
    <!-- Moment -->
    {head_item type="js"  src="$path/includes/js/plugins/moment/" files="moment-with-locales.min.js"}
    <!-- FileDownload -->
    {head_item type="js"  src="$path/includes/js/plugins/jquery-filedownload/" files="jquery.filedownload.js"}

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

        /*
         * Adicional styles to make table scrollable
         */
        .hdk-custom-scrollbar {
            position: relative;
            height: 600px;
            overflow: auto;
        }
        .hdk-table-wrapper-scroll-y {
            display: block;
        }

        /*
         * Adicional styles to print modal content
         */

        @media screen {
            #printSection {
                display: none;
            }
        }

        @media print {
            body * {
                visibility:hidden;
            }
            #printSection, #printSection * {
                visibility:visible;
            }
            #printSection {
                position:absolute;
                left:0;
                top:0;
            }
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
                    <h4>{$smarty.config.cat_reports} / <strong>{$smarty.config.pgr_person_report}</strong></h4>
                </div>
            </div>


            <div class="row wrapper  border-bottom white-bg ">
                &nbsp;
            </div>

            <!-- First Line -->

            <div class="col-xs-12 white-bg" style="height:10px;"></div>


            <!-- Form area -->
            <form method="get" class="form-horizontal" id="person-report-form">

                <!-- Hidden -->
                <input type="hidden" name="_token" id= "_token" value="{$token}">

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-1 b-l"></div>

                    <div class="col-sm-11 b-l">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.type}:</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" name="cmbTypePerson" id="cmbTypePerson" >
                                    <option value="">{$smarty.config.Select}</option>
                                    <option value="ALL">{$smarty.config.all}</option>
                                    {html_options values=$typepersonids output=$typepersonvals selected=$idtypeperson}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-1 b-l"></div>

                    <div class="col-sm-11 b-l">
                        <div id="alert-person-report"></div>
                    </div>
                </div>

                <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>

                <div class="col-xs-12 white-bg" style="height:10px;"></div>

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-12 b-l">
                        <div class="form-group text-center">
                            <!--<a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa-times" aria-hidden="true"></i> Cancela </a>-->
                            <button type="button" class="btn btn-primary btn-md btnSearch " id="btnSearch" >
                                <span class="fa fa-search"></span>  &nbsp;{$smarty.config.Search}
                            </button>

                        </div>
                    </div>
                </div>

                <div class="row wrapper  border-bottom white-bg returnBox hide">&nbsp;</div>

                <div class="row wrapper border-bottom white-bg returnBox hide">
                    <div class="col-sm-12 white-bg" style="height:15px;"></div>
                    <div class="col-sm-12 b-l">
                        <div class="form-group text-right">
                            <button type="button" class="btn btn-primary btn-md btnSave " id="btnSave" >
                                <span class="fa fa-save"></span> {$smarty.config.Save}
                            </button>
                            <button type="button" class="btn btn-primary btn-md btnPrint " id="btnPrint" >
                                <span class="fa fa-print"></span> {$smarty.config.Print}
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-12 white-bg" style="height:15px;"></div>
                    <div class="col-sm-12 b-l">
                        <div id="divReturn" class="col-sm-12">
                            <table id="returnTable" class="table table-hover table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="col-sm-2 text-center"><h4><strong>{$smarty.config.Login}</strong></h4></th>
                                        <th class="col-sm-5 text-center"><h4><strong>{$smarty.config.Name}</strong></h4></th>
                                        <th class="col-sm-2 text-center"><h4><strong>{$smarty.config.type}</strong></h4></th>
                                        <th class="col-sm-3 text-center"><h4><strong>{$smarty.config.Company}</strong></h4></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


            </form>
            <!-- End form area -->
        </div>


        <div class="footer">
            {include file=$footer}
        </div>
    </div>
</div>

{include file='modals/reports/modal-export.tpl'}
{include file='modals/reports/modal-web-print.tpl'}



</body>

</html>
</html>

