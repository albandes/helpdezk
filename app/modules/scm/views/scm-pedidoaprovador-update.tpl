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
    {head_item type="js" src="$path/app/modules/scm/views/js/" files="createpedidoaprovador.js"}
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

        .itenscotacaolayout{
            border: 1px solid black;
            border-radius: 10px;
            padding: 2px;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .table-bordered {
            border: 0;
            border-radius: 10px;
        }

        .table-bordered > thead > tr > th,
        .table-bordered > tbody > tr > th,
        .table-bordered > tfoot > tr > th,
        .table-bordered > thead > tr > td,
        .table-bordered > tbody > tr > td,
        .table-bordered > tfoot > tr > td {
            border-top: 0;
            border-right: 0;
            border-bottom: 1px solid #ddd;
            border-left: 0;
        }

        .table-bordered > thead > tr > th,
        .table-bordered > thead > tr > td {
            border-bottom: 2px solid #ddd;
        }

        .itenslayout{

            width: 50px;
            display:inline;
        }
        .quantidadelayout{

            margin-left: 5px;
            width: 50px;
            display:inline;
        }

        .divitenslayout{
            float:left;
            display:inline;
        }

        hr {
            color: black;
            height: 5px;
            margin-bottom: 10px;
            width: auto;
        }

        .layout{
            padding: 5px 0px 0px 0px;
        }

        .statusslectlayout{
            padding: 0px;
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
                        <h4>Cadastros / Pedido / <strong>Editar</strong></h4>
                    </div>
                </div>


                <div class="row wrapper  border-bottom white-bg">&nbsp;</div>

                <!-- First Line -->


                <div class="col-xs-12 white-bg" style="height:10px;"></div>

                <!-- Form area -->
                <form method="get" class="form-horizontal" id="update-pedidoaprovador-form" enctype="multipart/form-data">

                    <!-- Hidden -->
                    <input type="hidden" id="idpedidoaprovador" value="{$hidden_idpedidoaprovador}" />
                    <input type="hidden" name="_token" id= "_token" value="{$token}">

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-1 b-l">
                        </div>

                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Pedido N&ordm;:</label>
                                <div class="col-sm-5">
                                    <input type="text" id="numpedido" name="numpedido" class="form-control input-sm"  value="{$hidden_idpedidoaprovador}" readonly />
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-1 b-l">
                        </div>

                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Solicitante:</label>
                                <div class="col-sm-5">
                                    <input type="text" id="personname" name="personname" class="form-control input-sm"  value="{$personname}" readonly />
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row wrapper  white-bg ">

                        <div class="col-sm-1 b-l">
                            <div class="text-center" style="height:50px;">

                            </div>
                        </div>

                        <div class="col-sm-11 b-l">

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Data Entrega:</label>
                                <div class="col-sm-3">
                                    <input type="date" id="dataentrega" name="dataentrega" class="form-control input-sm" placeholder="{$plh_dataentrega}" value="{$dataentrega|date_format:"%Y-%m-%d"}" readonly >
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="row wrapper  white-bg {$flagdisplay}" >

                        <div class="col-sm-1 b-l">

                        </div>

                        <div class="col-sm-11 b-l">

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Turma:</label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-sm" value="{$turmadesc}" readonly />
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="row wrapper  white-bg {$flagdisplay}" >
                        <div class="col-sm-1 b-l"> </div>

                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Aula:</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" value="{$aula}" readonly />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">

                        <div class="col-sm-1 b-l">

                        </div>

                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Motivo da Compra:</label>
                                <div class="col-sm-5">
                                    <textarea rows="6" cols="100" id="motivo" name="motivo" class="form-control input-sm" required placeholder="{$plh_motivo}" readonly>{$motivo}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">

                        <div class="col-sm-1 b-l">

                        </div>

                        <div class="col-sm-11 b-l">

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Centro de Custo:</label>
                                <div class="col-sm-5">
                                    <input type="text" id="codigonomecentrodecusto" name="codigonomecentrodecusto" class="form-control input-sm"  value="{$codigonomecentrodecusto}" readonly >
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="row wrapper  white-bg ">

                        <div class="col-sm-1 b-l">

                        </div>

                        <div class="col-sm-11 b-l">

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Conta Contábil:</label>
                                <div class="col-sm-5">
                                    <input type="text" id="codigonomecontacontabil" name="codigonomecontacontabil" class="form-control input-sm"  value="{$codigonomecontacontabil}" readonly >
                                </div>


                            </div>

                        </div>

                    </div>

                    <div class="row wrapper  white-bg ">

                        <div class="col-sm-1 b-l">

                        </div>

                        <div class="col-sm-11 b-l">

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Status do pedido:</label>
                                <div class="col-sm-5">
                                    <select class="form-control input-sm status" id="idstatus" name="idstatus" >
                                        {html_options values=$statusids output=$statusvals selected=$idstatus}
                                    </select>
                                </div>


                            </div>

                        </div>

                    </div>

                    <div id="line_motivo" class="row wrapper white-bg hidden">

                        <div class="col-sm-1 b-l">

                        </div>

                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label  class="col-sm-2 control-label">Motivo:</label>
                                <div class="col-sm-8">
                                    <textarea rows="6" cols="100"  id="motivorejeicao" name="motivorejeicao" class="form-control input-sm" ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 white-bg" style="height:30px;"></div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l pedido" id="pedido">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="col-sm-6 text-center"><h3><strong>{$smarty.config.scm_Item}</strong></h3></th>
                                        <th class="col-sm-1 text-center"><h3><strong>{$smarty.config.scm_Quantidade}</strong></h3></th>
                                        <th class="col-sm-4 text-center"><h3><strong>{$smarty.config.Grid_status}</strong></h3></th>
                                        <th class="col-sm-4 text-center"><h3><strong>&nbsp;</strong></h3></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $arrItens as $key => $value}
                                    <tr>
                                        <td>
                                            <input type="hidden" id="iditempedidos" name="iditempedidos" value="{$value.iditempedido}" readonly>
                                            <h4>{$value.nome} - {$value.unidade}</h4>
                                        </td>
                                        <td>
                                            <input type="number" id="quantidades" name="quantidades[{$value.iditempedido}]" class="form-control text-center" placeholder="{$plh_quantidade}" step="0.25" value="{$value.quantidade}"/>
                                        </td>
                                        <td>
                                            <select class="form-control input-sm status" id="idstatusitens" name="idstatusitens[{$value.iditempedido}]" >
                                                {html_options values=$statusitensids output=$statusitensvals selected=$value.idstatus}
                                            </select>
                                        </td>
                                        <td class="btn-group">
                                            <button class="btn btnViewPicture" id="btnViewPicture" type="button" data-pedido="{$value.idproduto}"><i class="fa fa-image" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                    {if $rsCotacao[$value.iditempedido]|@count gt 0}
                                    <tr>
                                        <td colspan="4">
                                            <div class="itenscotacaolayout">
                                                <table id="cotacao-{$value.iditempedido}" class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class="col-sm-4 text-center"><h5><strong>{$smarty.config.scm_pgr_fornecedor}</strong></h5></th>
                                                            <th class="col-sm-2 text-center"><h5><strong>{$smarty.config.ERP_Unit_value}</strong></h5></th>
                                                            <th class="col-sm-2 text-center"><h5><strong>{$smarty.config.scm_total_value}</strong></h5></th>
                                                            <th class="col-sm-2 text-center"><h5><strong>{$smarty.config.scm_shipping_value}</strong></h5></th>
                                                            <th class="col-sm-1 text-center"><h5><strong>{$smarty.config.scm_Pdf}</strong></h5></th>
                                                            <th class="col-sm-1 text-center"><h5><strong>{$smarty.config.scm_Approve}</strong></h5></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    {foreach from=$rsCotacao[$value.iditempedido] key="key1" item="itenscotacao"}
                                                        <tr>
                                                            <td style="vertical-align: middle">
                                                                <input type="hidden" id="idcotacao" name="idcotacao[{$itenscotacao.idcotacao}]" value="{$itenscotacao.idcotacao}" >
                                                                {$itenscotacao.nomefornecedor}
                                                            </td>
                                                            <td class="text-center valoresunitarios" style="vertical-align: middle">{$itenscotacao.valor_unitario}</td>
                                                            <td class="text-center valorestotais" style="vertical-align: middle">{$itenscotacao.valor_total}</td>
                                                            <td class="text-center valoresfrete" style="vertical-align: middle">{$itenscotacao.valor_frete}</td>
                                                            <td class="text-center" style="vertical-align: middle">{if $itenscotacao.arquivo}<a href="{$caminho}{$itenscotacao.arquivo}" target="_blank">Download</a>{else}&nbsp;{/if}</td>
                                                            <td class="text-center" style="vertical-align: middle">
                                                                <label class="radio-inline i-checks"><input type="radio" id="flg_aprovado" name="flg_aprovado[{$value.iditempedido}]" value="{$itenscotacao.idcotacao}" {if $itenscotacao.flg_aprovado == 1}checked{/if} ></label>
                                                            </td>
                                                        </tr>
                                                    {/foreach}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    {/if}
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="col-sm-11 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-2 control-label">{$smarty.config.scm_item_total}:</label>
                                    <div class="col-sm-2">
                                        <input type="text" id="totalitens" data-valoritens="1" name="totalitens" class="form-control input-sm" value="{$totalitens}" readonly>
                                    </div>
                                    <label  class="col-sm-2 control-label">{$smarty.config.scm_shipping_total}:</label>
                                    <div class="col-sm-2">
                                        <input type="text" id="totalfrete" data-valorfrete="1" name="totalfrete" class="form-control input-sm" value="{$totalfrete}" readonly>
                                    </div>
                                    <label  class="col-sm-2 control-label">{$smarty.config.scm_request_total}:</label>
                                    <div class="col-sm-2">
                                        <input type="text" id="totalpedido" data-valortotal="1" name="totalpedido" class="form-control input-sm" value="{$totalpedido}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">

                        <div class="col-sm-1 b-l">

                        </div>

                        <div class="col-sm-11 b-l">
                            <div id="alert-update-pedidoaprovador"></div>
                        </div>
                    </div>

                    <div class="row wrapper  border-bottom white-bg "> &nbsp;</div>

                    <div class="row wrapper white-bg ">&nbsp;</div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa-arrow-alt-circle-left" aria-hidden="true"></i> Volta </a>
                                    <button type="button" class="btn btn-primary btn-md " id="btnUpdatePedidoAprovador" >
                                        <span class="fa fa-check"></span>  &nbsp;Envia
                                    </button>
                                </div>

                                <div class="col-sm-6 text-right {$flagRepass}">
                                    <button class="btn btn-warning btn-md btnNote" id="btnRepass" type="button" tabindex="-1"><span class="fa fa-external-link-alt"></span>&nbsp;Repassar</button>
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
                </div>


                {include file='modals/pedidoaprovador/modal-alert.tpl'}
                {include file='modals/pedidoaprovador/modal-repass.tpl'}
                {include file='modals/pedidocompra/modal-produto-picture.tpl'}
            </div>
        </div>
    </div>
</body>

</html>
