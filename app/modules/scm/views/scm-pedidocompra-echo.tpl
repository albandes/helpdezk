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
    {head_item type="js" src="$path/app/modules/scm/views/js/" files="createpedidocompra.js"}
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
                    <h4>Cadastros / Pedido / <strong>Visualizar</strong></h4>
                </div>
                <div class="col-sm-8 text-right"">

            </div>
        </div>

        <div class="row wrapper  border-bottom white-bg "></div>

        <!-- First Line -->


        <div class="col-xs-12 white-bg" style="height:10px;"></div>

        <!-- Form area -->
        <form method="get" class="form-horizontal" id="update-pedidocompra-form">

            <!-- Hidden -->
            <input type="hidden" name="iduserrole" id="iduserrole" value="{$iduserrole}">

            <div class="row wrapper  white-bg ">
                <div class="col-sm-1 b-l">
                </div>

                <div class="col-sm-11 b-l">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Pedido N&ordm;:</label>
                        <div class="col-sm-5">
                            <input type="text" id="numpedido" name="numpedido" class="form-control input-sm"  value="{$hidden_idpedidocompra}" readonly />
                        </div>
                    </div>
                </div>

            </div>

            <div id="ownerLine" class="row wrapper white-bg ">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">{$smarty.config.Request_owner}:</label>
                        <div class="col-sm-5 control-text">
                            <p class="form-control-static">{$ownerName}</p>
                            <input type="hidden" name="idowner" id="idowner" value="{$idowner}">
                        </div>
                    </div>

                </div>

            </div>

            <div class="row wrapper  white-bg ">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Data Entrega:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm" value="{$dataentrega}" readonly />

                        </div>
                        <div class="col-sm-4">
                            <span class="form-control input-sm" readonly="readonly">{$nomestatus} </span>
                        </div>

                    </div>

                    <div class="form-group {$flagdisplay}">
                        <label class="col-sm-2 control-label">Turma:</label>
                        <div class="col-sm-3">
                            <select class="form-control input-sm"  id="cmbTurma" name="cmbTurma" disabled >
                                {html_options values=$turmaids output=$turmavals selected=$idturma}
                            </select>
                        </div>

                    </div>

                    <div id="line_turma" class="row wrapper  white-bg {$flagdisplay}" >
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Aula:</label>
                            <div class="col-sm-6">
                                <input type="text" id="txtAula" name="txtAula" class="form-control input-sm" value="{$aula}" readonly >
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Motivo:</label>
                        <div class="col-sm-6">
                            <textarea rows="6" cols="100" id="motivo" name="motivo" class="form-control input-sm" required readonly>{$motivo}</textarea>
                        </div>
                    </div>

                </div>

            </div>

            <div class="col-xs-12 white-bg" style="height:20px;"></div>

            <div class="row wrapper  white-bg textcenter">
                <div class="col-sm-12 b-l">
                    <div class="col-sm-2 b-l"></div>
                    <div class="col-sm-8 b-l">
                        <table id="itemList" class="table table-hover">
                            <thead>
                            <tr>
                                <th class="col-sm-8 text-center"><h4><strong>{$smarty.config.Item}</strong></h4></th>
                                <th class="col-sm-2 text-center"><h4><strong>{$smarty.config.availability}</strong></h4></th>
                                <th class="col-sm-2 text-center"><h4><strong>{$smarty.config.scm_Quantidade}</strong></h4></th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach $arrItens as $key => $value}
                                <tr>
                                    <td>
                                        {$value.nome}
                                    </td>
                                    <td class="text-center">
                                        <span id="availability_1" class="form-control-static {$value.lblType}"><strong>{$value.disponibilidade}</strong></span>
                                    </td>
                                    <td>
                                        <input type="number" name="quantidades[]" class="form-control input-sm" placeholder="{$plh_quantidade}" value="{$value.quantidade}" readonly />
                                    </td>

                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <div class="col-xs-12 white-bg" style="height:20px;"></div>

            {if $idstatus == 9}
                <div class="row wrapper  white-bg ">

                    <div class="col-sm-1 b-l">

                    </div>

                    <div class="col-sm-11 b-l">

                        <div class="form-group">
                            <label class="col-sm-2 text-danger control-label">Motivo Cancelamento:</label>
                            <div class="col-sm-5">
                                <textarea class="form-control input-sm" readonly>{$motivocancelamento}</textarea>
                            </div>
                        </div>

                    </div>

                </div>
            {/if}

            <div class="row wrapper  white-bg ">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">
                    <div id="alert-update-pedidocompra"></div>
                </div>
            </div>

            <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>

            <div class="col-xs-12 white-bg" style="height:10px;"></div>

            <div class="row wrapper  white-bg text-center">
                <div class="col-sm-12 b-l">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa fa-arrow-alt-circle-left" aria-hidden="true"></i> Volta </a>
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


            {include file='modals/pedidocompra/modal-alert.tpl'}


</body>

</html>