<!-- TEMPLATE DA PÁGINA HOME DO PROGRAMA RELATÓRIO SOLICITAÇÕES -->

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
    {head_item type="css" src="$path/css/" files="admmenu.css"}

    {head_item type="js"  src="$path/includes/js/plugins/modernizr/" files="modernizr.custom.js"}
    <!-- Helpdezk -->
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/app/modules/helpdezk/views/js/" files="report.js"}
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
    {if $dtpickerLocale != ''}
        {head_item type="js"  src="$path/includes/js/plugins/datepicker/locales/" files="$dtpickerLocale"}
    {/if}
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
            ticketAcceptedFiles = '{/literal}{$ticketacceptedfiles}{literal}',
            demoVersion = '{/literal}{$demoversion}{literal}',
            datepickerOpts = {/literal}{$datepickerOpts}{literal};

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
                    <h4>{$smarty.config.cat_reports} / <strong>{$smarty.config.pgr_hdk_report}</strong></h4>
                </div>
            </div>


            <div class="row wrapper  border-bottom white-bg ">
                &nbsp;
            </div>

            <!-- First Line -->

            <div class="col-xs-12 white-bg" style="height:10px;"></div>


            <!-- FORMULÁRIO DO RELATÓRIO ----------------->

            <form method="get" class="form-horizontal" id="inv-report-form" name="inv-report-form">

                <!-- Hidden -->
                <input type="hidden" name="_token" id= "_token" value="{$token}">

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-1 b-l"></div>
                    
                    <!-- campos formulario relatorio solicitacoes -->

                    <!-- Campo Tipo de Relatório -->
                    <div id = "campoRelType" class="col-sm-11 b-l">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.rel_tiporel}:</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" name="cmbRelType" id="cmbRelType" >
                                    <option value="" disabled hidden selected>{$smarty.config.Select}</option>
                                    {html_options values=$reltypeids output=$reltypevals selected=$idreltype}
                                </select>
                            </div>
                        </div>
                    </div>  
                </div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-1 b-l"></div>

                    <!-- Campo Empresa -->
                    <div id = "campoCompany" class="col-sm-11 b-l relDep hide">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.Company}:</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" name="cmbEmpresa" id="cmbEmpresa" >
                                    <option value="">{$smarty.config.Select}</option>
                                    <option value="ALL">{$smarty.config.all}</option>
                                    {html_options values=$adviserids output=$adviservals selected=$idcompany}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-1 b-l"></div>

                    <!-- Campo Atendente || depende de tipo de relatorio -->
                    <div id = "campoOperator" class="col-sm-11 b-l relDep hide">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.Operator}:</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" name="cmbAtendente" id="cmbAtendente" >
                                    <option value="">{$smarty.config.Select}</option>
                                    <option value="ALL">{$smarty.config.all}</option>
                                    {html_options values=$operatorids output=$operatorvals selected=$idattendance}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row wrapper  white-bg">
                    <div class="col-sm-1 b-l"></div>

                    <!-- Campo Área || depende do tipo de relatorio -->
                    <div  id = "campoArea" class="col-sm-11 b- relDep hide">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.APP_areaLabel}:</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" name="cmbArea" id="cmbArea" >
                                    <option value="">{$smarty.config.Select}</option>
                                    <option value="ALL">{$smarty.config.all}</option>
                                    {html_options values=$areaids output=$areavals selected=$idarea}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-1 b-l"></div>

                    <!-- Campo Tipo -->
                    <div id = "campoType" class="col-sm-11 b-l relDep hide">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.APP_typeLabel}:</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" name="cmbTipo" id="cmbTipo" disabled>
                                    <option value="">{$smarty.config.Select}</option>
                                    <option value="ALL">{$smarty.config.all}</option>
                                    {html_options values=$typeids output=$typevals selected=$idtype}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-1 b-l"></div>

                    <!-- Campo Item -->
                    <div id = "campoItem" class="col-sm-11 b-l relDep hide">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.APP_itemLabel}:</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" name="cmbItem" id="cmbItem" disabled>
                                    <option value="">{$smarty.config.Select}</option>
                                    {html_options values=$itemids output=$itemvals selected=$iditem}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-1 b-l"></div>

                    <!-- Campo Serviço -->
                    <div id = "campoService" class="col-sm-11 b-l relDep hide">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.APP_serviceLabel}:</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" name="cmbServico" id="cmbServico" disabled>
                                    <option value="">{$smarty.config.Select}</option>
                                    {html_options values=$serviceids output=$servicevals selected=$idservice}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-1 b-l"></div>

                    <!-- Campo Motivo -->
                    <div id = "campoReason" class="col-sm-11 b-l relDep hide">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.Reason}:</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" name="cmbMotivo" id="cmbMotivo" disabled>
                                    <option value="">{$smarty.config.Select}</option>
                                    {html_options values=$reasonids output=$reasonvals selected=$idreason}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-1 b-l"></div>

                    <!-- Campo atendimento -->
                    <div id = "campoAttendance" class="col-sm-11 b-l relDep hide">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.Attendance}:</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" name="cmbTipoatend" id="cmbTipoatend" >
                                    <option value="">{$smarty.config.Select}</option>
                                    <option value="ALL">{$smarty.config.all}</option>
                                     <option value="">{$smarty.config.Select}</option>
                                    {html_options values=$attendanceids output=$attendancevals selected=$idattendance}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                 <div class="row wrapper  white-bg ">
                    <div class="col-sm-1 b-l"></div>

                    <!-- Campo escolha tempo -->
                    <div id = "campoTimeSelect" class="col-sm-11 b-l relDep hide">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Período:</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" name="cmbTipoPeriodo" id="cmbTipoPeriodo" >
                                    <option value="">{$smarty.config.Select}</option>
                                    {html_options values=$timeids output=$timevals selected=$idtime}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                 <div class="row wrapper  white-bg ">
                    <div class="col-sm-1 b-l"></div>

                <!-- Campo Calendários -->
                <div id="campoCalendars" class="col-sm-11 b-l hide">
                    <div class="form-group campoRel">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">De:</label>
                                <div class="col-sm-3">
                                    <div class="input-group date">
                                        <input type="text" class="input-sm form-control" id="dtstart" name="dtstart" value=""/>
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                                <label class="col-sm-1 control-label">até:</label>
                                <div class="col-sm-3">
                                    <div class="input-group date">
                                        <input type="text" class="input-sm form-control" id="dtfinish" name="dtfinish" value=""/>
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-1 b-l"></div>

                    <div class="col-sm-11 b-l">
                        <div id="alert-inv-report"></div>
                    </div>
                </div>

                <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>

                <div class="col-xs-12 white-bg" style="height:10px;"></div>

                <div class="row wrapper  white-bg ">

                <!-- BOTÃO SUBMIT ----------------------------------->

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

                <!-- OUTPUT DA BUSCA ---->

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

                                    <!-- cabeçalho --> 

                                </thead>
                                <tbody>

                                    <!-- corpo -->

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-------------------------------->


            </form>
            <!-- FINAL DO FORMULÁRIO RELATÓRIO -->
        </div>


        <div class="footer">
            {include file=$footer}
        </div>
    </div>
</div>

{include file='modals/reports/modal-export.tpl'}
{*include file='modals/reports/modal-web-print.tpl'*}



</body>

</html>
</html>

