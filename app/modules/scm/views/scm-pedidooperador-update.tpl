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
    {head_item type="js" src="$path/app/modules/scm/views/js/" files="createpedidooperador.js"}
    <!-- Font Awesome -->
    {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
    <!-- animate -->
    {head_item type="css" src="$path/css/" files="animate.css"}

    <!-- Icheck, used in checkbox and radio -->
    {head_item type="css" src="$path/css/plugins/iCheck/" files="custom.css"}
    {head_item type="js"  src="$path/includes/js/plugins/iCheck/" files="icheck.min.js"}
    <!-- Summernote Editor -->
    {head_item type="css" src="$path/css/plugins/summernote/$summernote_version/" files="summernote.css"}
    {head_item type="css" src="$path/css/plugins/summernote/" files="summernote-bs3.css"}
    {head_item type="js"  src="$path/includes/js/plugins/summernote/$summernote_version/" files="summernote.min.js"}
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
        .itenscotacaolayout{
            border: 1px solid black;
            border-radius: 10px;
            padding-bottom: 10px;
            margin-top: 5px;
            margin-bottom: 2px;
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

        .btnAddRemovelayout{
            margin-top: 6px;
            text-align: center;
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

                    <h4>Cadastros / Pedido / <strong>Editar</strong></h4>

                </div>
                <div class="col-sm-8 text-right"">

                &nbsp;

            </div>
        </div>

        <div class="row wrapper  border-bottom white-bg ">
            &nbsp;
        </div>

        <!-- First Line -->


        <div class="col-xs-12 white-bg" style="height:10px;"></div>

        <!-- Form area -->
        <form method="get" class="form-horizontal" id="update-pedidooperador-form" enctype="multipart/form-data">

            <!-- Hidden -->
            <input type="hidden" name="idpedidooperador" id="idpedidooperador" value="{$hidden_idpedidooperador}" />
            <input type="hidden" name="_token" id= "_token" value="{$token}">
            <input type="hidden" name="flgGC" id= "flgGC" value="{$flgGC}">

            <div class="row wrapper  white-bg ">
                <div class="col-sm-1 b-l">
                </div>

                <div class="col-sm-11 b-l">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Pedido N&ordm;:</label>
                        <div class="col-sm-5">
                            <input type="text" id="numpedido" name="numpedido" class="form-control input-sm"  value="{$hidden_idpedidooperador}" readonly />
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
                            <input type="date" id="dataentrega" name="dataentrega" class="form-control input-sm" placeholder="{$plh_dataentrega}" value="{$dataentrega|date_format:"%Y-%m-%d"}" readonly />
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
                            <textarea rows="6" cols="100" id="motivo" name="motivo" class="form-control input-sm" required readonly>{$motivo}</textarea>
                        </div>
                    </div>

                </div>

            </div>


            <div class="row wrapper  white-bg {$flagCC}">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Centro de Custo:</label>
                        <div class="col-sm-5">
                            <select class="form-control input-sm centrodecusto" id="idcentrodecusto" name="idcentrodecusto" >
                                {html_options values=$centrodecustoids output=$centrodecustovals selected=$idcentrodecusto}
                            </select>
                        </div>


                    </div>

                </div>

            </div>
            <div class="row wrapper  white-bg {$flagCC}">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Conta Contábil:</label>
                        <div class="col-sm-5">
                            <select class="form-control input-sm contacontabil" id="idcontacontabil" name="idcontacontabil" >
                                {html_options values=$contacontabilids output=$contacontabilvals selected=$idcontacontabil}
                            </select>
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

            <div class="row wrapper  white-bg ">
                <hr />
            </div>

            <div class="row wrapper  white-bg">
                <div class="col-sm-12 b-l">
                    <div class="col-sm-1 b-l"></div>
                    <div class="col-sm-10 b-l">
                        <table id="itemList" class="table table-hover table-striped">
                            <thead>
                            <tr>
                                <th class="col-sm-5 text-center"><h4><strong>{$smarty.config.Item}</strong></h4></th>
                                <th class="col-sm-1 text-center"><h4><strong>&nbsp;</strong></h4></th>
                                <th class="col-sm-2 text-center"><h4><strong>{$smarty.config.availability}</strong></h4></th>
                                <th class="col-sm-1 text-center"><h4><strong>{$smarty.config.scm_Quantidade}</strong></h4></th>
                                <th class="col-sm-3 text-center"><h4><strong>{$smarty.config.scm_item_status}</strong></h4></th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach $arrItens as $key => $value}
                                {$i = 0}
                                <tr>
                                    <td>
                                        <input type="hidden" id="iditempedidos" name="iditempedidos[]" value="{$value.iditempedido}" readonly>
                                        {if $flgGC == 1}
                                        <select class="form-control input-sm produtos" name="produtos[{$value.iditempedido}]" id="produtos_{$key}"  onchange="checkAvailability(this)">
                                            {html_options options=$produtoopts disabled=$produtodisables strict=1 selected=$value.idproduto}
                                        </select>
                                        {else}
                                            <input type="hidden" class="form-control input-sm" name="produtos[{$value.iditempedido}]" id="produtos_{$key}" value="{$value.idproduto}">
                                            <span class="form-control-static">{$value.nome} - {$value.unidade}</span>
                                        {/if}
                                        <input type="hidden" id="numId" value="{$key}"/>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btnViewPicture" id="btnViewPicture" type="button" data-pedido="{{$key}}"><i class="fa fa-image" aria-hidden="true"></i></button>
                                    </td>
                                    <td class="text-center">
                                        <span id="availability_{$key}" class="form-control-static {$value.lblType}"><strong>{$value.disponibilidade}</strong></span>
                                    </td>
                                    <td>
                                        <input type="number" id="quantidades_{$key}" name="quantidades[{$value.iditempedido}]" class="form-control input-sm" placeholder="{$plh_quantidade}" value="{$value.quantidade}" step="0.25" min="0" onkeyup="checkAvailability(this)" onchange="checkAvailability(this)" {if $flgGC != 1}readonly{/if} />
                                    </td>
                                    <td>
                                        <select class="form-control input-sm status" id="idstatusitens" name="idstatusitens[{$value.iditempedido}]" >
                                            {html_options values=$statusitensids output=$statusitensvals selected=$value.idstatus}
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="5">
                                        <input type="hidden" id="_totalitens" name="" value="{$rsCotacao[$value.iditempedido]|@count}">
                                    {if $rsCotacao[$value.iditempedido]|@count gt 0}
                                    <div class="col-sm-12 cotacao-{$value.iditempedido} {$flagCC}" id="cotacao">
                                        {foreach from=$rsCotacao[$value.iditempedido] key="key1" item="itenscotacao"}
                                        {if $i == 0}
                                        <div class="col-sm-12 form-group itenscotacaolayout">
                                            {else}
                                            <div class="col-sm-12 form-group itenscotacaolayout" id="item_{$key1}">
                                                {/if}
                                                <input type="hidden" id="idcotacao" name="idcotacao[{$value.iditempedido}][{$itenscotacao.idcotacao}]" value="{$itenscotacao.idcotacao}">
                                                <div class="col-sm-11">
                                                    <div class="col-sm-5">
                                                        <label class="control-label">Fornecedor: </label>
                                                        <select class="form-control input-sm fornecedores"  name="fornecedores[{$value.iditempedido}][{$itenscotacao.idcotacao}]">
                                                            {html_options values=$fornecedorids output=$fornecedorvals selected=$itenscotacao.idperson}
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="control-label">Unitário: </label>
                                                        <input type="text" id="valoresunitarios" data-valorunitario="1" name="valoresunitarios[{$value.iditempedido}][{$itenscotacao.idcotacao}]" class="form-control input-sm valoresunitarios{$value.iditempedido}{$key1}" value="{$itenscotacao.valor_unitario}" >
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="control-label">Total:</label>
                                                        <input type="text" id="valorestotais"   data-valortotal="1" name="valorestotais[{$value.iditempedido}][{$itenscotacao.idcotacao}]" class="form-control input-sm valorestotais{$value.iditempedido}{$key1}"value="{$itenscotacao.valor_total}" >
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="control-label">Frete:</label>
                                                        <input type="text" id="valoresfrete"   data-valortotal="1" name="valoresfrete[{$value.iditempedido}][{$itenscotacao.idcotacao}]" class="form-control input-sm valoresfrete{$value.iditempedido}{$key1}" value="{$itenscotacao.valor_frete}" >
                                                    </div>
                                                </div>
                                                <div class="col-sm-11">
                                                    <div class="col-sm-4">
                                                        <label class="control-label">Pdf:</label>
                                                        {if $itenscotacao.arquivo}
                                                            <a href="{$caminho}{$itenscotacao.arquivo}" target="_blank">Ver Arquivo</a>
                                                        {/if}
                                                        <input type="file" id="arquivos" data-arquivo="{$value.iditempedido}{$key1}"  name="arquivos[{$value.iditempedido}][{$itenscotacao.idcotacao}]" placeholder="" accept="application/pdf" class="form-control input-sm arquivos" >
                                                    </div>
                                                    <div class="col-sm-1 text-center">
                                                        <label class="control-label">Transp.</label>
                                                        <div class="checkbox i-checks"><label> <input type="checkbox" data-flagcarrier="1" id="flagcarrier" name="flagcarrier[{$value.iditempedido}][{$itenscotacao.idcotacao}]" value="S" {if $itenscotacao.flg_carrier == 'S'}checked{/if}> <i></i> &nbsp;</label></div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-1 btnAddRemovelayout">
                                                    <br>
                                                    {if $i == 0}
                                                        <button class="btn btn-success btnAddCotacao" data-cotacao="{$value.iditempedido}" type="button"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                                    {else}
                                                        <button class="btn btn-danger btnRemoveCotacao" data-cotacao="{$key1}" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>
                                                    {/if}
                                                    {$i = $i + 1}
                                                </div>
                                            </div>
                                            {/foreach}
                                        </div>
                                        {else}
                                        <div class="col-sm-12 cotacao-{$value.iditempedido} {$flagCC}" id="cotacao">
                                            <div class="col-sm-12 form-group itenscotacaolayout" >
                                                <input type="hidden" id="idcotacao" name="idcotacao[{$value.iditempedido}][]" value="">
                                                <div class="col-sm-11">
                                                    <div class="col-sm-5">
                                                        <label class="control-label">Fornecedor: </label>
                                                        <select class="form-control input-sm fornecedores" name="fornecedores[{$value.iditempedido}][]">
                                                            {html_options values=$fornecedorids output=$fornecedorvals selected=$value.idfornecedor}
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="control-label">Unitário: </label>
                                                        <input type="text" id="valoresunitarios"  data-valorunitario="1" name="valoresunitarios[{$value.iditempedido}][]" class="form-control input-sm valoresunitarios{$value.iditempedido}" >
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="control-label">Total:</label>
                                                        <input type="text" id="valorestotais" data-valortotal="1" name="valorestotais[{$value.iditempedido}][]" class="form-control input-sm valorestotais{$value.iditempedido}" >
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="control-label">Frete:</label>
                                                        <input type="text" id="valoresfrete"   data-valortotal="1" name="valoresfrete[{$value.iditempedido}][]" class="form-control input-sm valoresfrete{$value.iditempedido}" >
                                                    </div>
                                                </div>
                                                <div class="col-sm-11">
                                                    <div class="col-sm-4">
                                                        <label class="control-label">Pdf:</label>
                                                        <input type="file" id="arquivos" data-arquivo="{$value.iditempedido}" name="arquivos[{$value.iditempedido}][]" placeholder="" accept="application/pdf" class="form-control input-sm arquivos" >
                                                    </div>
                                                    <div class="col-sm-1 text-center">
                                                        <label class="control-label">Transp.</label>
                                                        <div class="checkbox i-checks"><label> <input type="checkbox" data-flagcarrier="1" id="flagcarrier" name="flagcarrier[{$value.iditempedido}][]" value="S"> <i></i> &nbsp;</label></div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-1 btnAddRemovelayout">
                                                    <br>
                                                    <button class="btn btn-success btnAddCotacao " data-cotacao="{$value.iditempedido}" type="button"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                                </div>

                                            </div>
                                        </div>
                                        {/if}
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <div class="row wrapper  white-bg ">
                <div class="col-sm-12 b-l">
                    <div class="form-group">
                        <label  class="col-sm-2 control-label">TOTAL ITENS:</label>
                        <div class="col-sm-2">
                            <input type="text" id="totalitens" data-valoritens="1" name="totalitens" class="form-control input-sm" value="{$totalitens}" readonly>
                        </div>
                        <label  class="col-sm-2 control-label">TOTAL FRETE:</label>
                        <div class="col-sm-2">
                            <input type="text" id="totalfrete" data-valorfrete="1" name="totalfrete" class="form-control input-sm" value="{$totalfrete}" readonly>
                        </div>
                        <label  class="col-sm-2 control-label">TOTAL:</label>
                        <div class="col-sm-2">
                            <input type="text" id="totalpedido" data-valortotal="1" name="totalpedido" class="form-control input-sm" value="{$totalpedido}" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row wrapper  white-bg ">
                <div class="col-sm-1 b-l">
                </div>

                <div class="col-sm-11 b-l">
                    <div id="alert-update-pedidooperador"></div>
                </div>
            </div>

            <div class="row wrapper  border-bottom white-bg ">
                &nbsp;
            </div>

            <div class="col-xs-12 white-bg" style="height:10px;"></div>


            <div class="row wrapper  white-bg ">

                <div class="col-sm-12 b-l">
                    <div class="form-group">

                        <div class="col-sm-6">
                            <a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa fa-arrow-alt-circle-left" aria-hidden="true"></i> Volta </a>
                            <button type="button" class="btn btn-primary btn-md " id="btnUpdatePedidoOperador" >
                                <span class="fa fa-save"></span>  &nbsp;Salvar
                            </button>
                        </div>

                        <div class="col-sm-6 text-right">
                            <button type="button" class="btn btn-secondary btn-md btnPrint " id="btnPrint" >
                                <span class="fa fa-print"></span>  &nbsp;Imprimir
                            </button>
                            <!--<button class="btn btn-info btn-md btnEmail {$flagCC}" id="btnEmail" type="button" tabindex="-1">Cotação</button>-->
                            <button class="btn btn-warning btn-md btnNote" id="btnNote" type="button" tabindex="-1"> <i class="fa fa-comments" aria-hidden="true"></i>&nbsp; Apontamentos</button>

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


            {include file='modals/pedidooperador/modal-alert.tpl'}
            {include file='modals/pedidooperador/modal-email.tpl'}
            {include file='modals/pedidooperador/modal-note.tpl'}
            {include file='modals/pedidocompra/modal-produto-picture.tpl'}


</body>

</html>

