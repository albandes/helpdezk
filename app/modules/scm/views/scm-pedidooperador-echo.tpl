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
            padding-bottom: 10px;
            margin-top: 5px;
            margin-bottom: 2px;
        }
        .itenslayout{

            width: auto;
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



        <div class="wrapper wrapper-content  ">
            <div class="row wrapper white-bg ibox-title">
                <div class="col-sm-4">

                    <h4>Cadastros / Pedido / <strong>Visualizar</strong></h4>

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
            <input type="hidden" id="idpedidooperador" value="{$hidden_idpedidooperador}" />
            <input type="hidden" name="_token" id= "_token" value="{$token}">

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


            <div class="row wrapper  white-bg ">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Centro de Custo:</label>
                        <div class="col-sm-5">
                            <input type="text" id="nomecentrodecusto" name="nomecentrodecusto" class="form-control input-sm" value="{$codigonomecentrodecusto}" readonly />
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
                            <input type="text" id="nomecontacontabil" name="nomecontacontabil" class="form-control input-sm" value="{$codigonomecontacontabil}" readonly />
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
                            <input type="text" class="form-control input-sm" value="{$nomestatus}" readonly />
                        </div>
                    </div>

                </div>

            </div>
            {if $idstatus == 9 || $idstatus == 21}
                <div class="row wrapper  white-bg ">

                    <div class="col-sm-1 b-l">

                    </div>

                    <div class="col-sm-11 b-l">

                        <div class="form-group">
                            <label class="col-sm-2 text-danger control-label">Motivo Rejei&ccedil;&atilde;o / Cancelamento:</label>
                            <div class="col-sm-5">
                                <textarea class="form-control input-sm" readonly>{$motivocancelamento}</textarea>
                            </div>
                        </div>

                    </div>

                </div>
            {/if}

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
                                        <input type="hidden" class="form-control input-sm" name="produtos[{$value.iditempedido}]" id="produtos_{$key}" value="{$value.idproduto}">
                                        <span class="form-control-static">{$value.nome} - {$value.unidade}</span>
                                        <input type="hidden" id="numId" value="{$key}"/>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btnViewPicture" id="btnViewPicture" type="button" data-pedido="{{$key}}"><i class="fa fa-image" aria-hidden="true"></i></button>
                                    </td>
                                    <td class="text-center">
                                        <span id="availability_{$key}" class="form-control-static {$value.lblType}"><strong>{$value.disponibilidade}</strong></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="form-control-static">{$value.quantidade}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="form-control-static">{$value.nomestatusitem}</span>
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
                                                    <div class="col-sm-5">
                                                        <label class="control-label">Fornecedor: </label>
                                                        <input type="text" id="nomefornecedor" name="nomefornecedor[{$value.iditempedido}][{$itenscotacao.idcotacao}]" class="form-control input-sm nomefornecedor" value="{$itenscotacao.nomefornecedor}" readonly />
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="control-label">Valor Unitário: </label>
                                                        <input type="text" id="valoresunitarios" name="valoresunitarios[{$value.iditempedido}][{$itenscotacao.idcotacao}]" class="form-control input-sm valoresunitarios" value="{$itenscotacao.valor_unitario}" readonly />
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="control-label">Valor Total:</label>
                                                        <input type="text" id="valorestotais" name="valorestotais[{$value.iditempedido}][{$itenscotacao.idcotacao}]" class="form-control input-sm valorestotais"value="{$itenscotacao.valor_total}" readonly />
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="control-label">Frete:</label>
                                                        <input type="text" id="valoresfrete" name="valoresfrete[{$value.iditempedido}][{$itenscotacao.idcotacao}]" class="form-control input-sm valoresfrete"value="{$itenscotacao.valor_frete}" readonly />
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="control-label">Pdf:</label>
                                                        <br>
                                                        {if $itenscotacao.arquivo != ""}
                                                            <a href="{$caminho}{$itenscotacao.arquivo}" target="_blank">Download</a>
                                                        {else}
                                                            <p>Sem arquivo</p>
                                                        {/if}
                                                    </div>
                                                </div>
                                                {/foreach}
                                            </div>
                                            {/if}
                                        </div>
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!--<div class="row wrapper  white-bg ">

                <div class="col-sm-12 b-l pedido" id="pedido">
                    <hr />
                    {foreach $arrItens as $key => $value}
                        {$i = 0}
                        <div class="col-sm-1 b-l"></div>
                        <div class="col-sm-11 form-group" >

                            <div class="col-sm-12">
                                <input type="hidden" id="iditempedidos" name="iditempedidos" value="{$value.iditempedido}" readonly />
                                <div class="col-sm-6 layout">
                                    <div class="divitenslayout">
                                        <h3 class="itenslayout">Item / Quantidade:</h3>
                                        <h4 class="itenslayout">{$value.nome} - {$value.unidade}</h4> / <h4 class="itenslayout">{$value.quantidade}</h4>
                                    </div>
                                </div>
                                <div class="col-sm-1 layout">
                                    <div class="divitenslayout">
                                        <button class="btn btnViewPicture" id="btnViewPicture" type="button" data-pedido="{$value.idproduto}"><i class="fa fa-image" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                                <div class="col-sm-5 statusslectlayout text-right">
                                    <div class="col-sm-5 ">
                                        <h3 class="statuslayout">Status do item:</h3>
                                    </div>
                                    <div class="col-sm-7 ">
                                        <input type="text" id="nomestatusitem" name="nomestatusitem" class="form-control input-sm"  value="{$value.nomestatusitem}" readonly />
                                    </div>
                                </div>
                            </div>


                            <input type="hidden" id="_totalitens" name="" value="{$rsCotacao[$value.iditempedido]|@count}">

                            {if $rsCotacao[$value.iditempedido]|@count gt 0}

                                <div class="col-sm-12 cotacao-{$value.iditempedido}" id="cotacao">
                                    {foreach from=$rsCotacao[$value.iditempedido] key="key1" item="itenscotacao"}

                                        {if $i == 0}
                                            <div class="col-sm-12 form-group itenscotacaolayout">
                                        {else}
                                            <div class="col-sm-12 form-group itenscotacaolayout" id="item_{$key1}">
                                        {/if}

                                            <div class="col-sm-5">
                                                <label class="control-label">Fornecedor: </label>
                                                <input type="text" id="nomefornecedor" name="nomefornecedor[{$value.iditempedido}][{$itenscotacao.idcotacao}]" class="form-control input-sm nomefornecedor" value="{$itenscotacao.nomefornecedor}" readonly />
                                            </div>
                                            <div class="col-sm-2">
                                                <label class="control-label">Valor Unitário: </label>
                                                <input type="text" id="valoresunitarios" name="valoresunitarios[{$value.iditempedido}][{$itenscotacao.idcotacao}]" class="form-control input-sm valoresunitarios" value="{$itenscotacao.valor_unitario}" readonly />
                                            </div>
                                            <div class="col-sm-2">
                                                <label class="control-label">Valor Total:</label>
                                                <input type="text" id="valorestotais" name="valorestotais[{$value.iditempedido}][{$itenscotacao.idcotacao}]" class="form-control input-sm valorestotais"value="{$itenscotacao.valor_total}" readonly />
                                            </div>
                                            <div class="col-sm-2">
                                                <label class="control-label">Frete:</label>
                                                <input type="text" id="valoresfrete" name="valoresfrete[{$value.iditempedido}][{$itenscotacao.idcotacao}]" class="form-control input-sm valoresfrete"value="{$itenscotacao.valor_frete}" readonly />
                                            </div>
                                            <div class="col-sm-2">
                                                <label class="control-label">Pdf:</label>
                                                <br>
                                                {if $itenscotacao.arquivo != ""}
                                                    <a href="{$caminho}{$itenscotacao.arquivo}" target="_blank">Download</a>
                                                {else}
                                                    <p>Sem arquivo</p>
                                                {/if}
                                            </div>
                                        </div>
                                    {/foreach}
                                </div>
                            {else}
                                    <!-- <div class="col-sm-12 cotacao-{$value.iditempedido}" id="cotacao">
                                    <div class="col-sm-12 form-group itenscotacaolayout" >
                                        <div class="col-sm-6">
                                            <label class="control-label">Fornecedor: </label>
                                            <select class="form-control input-sm fornecedores" name="fornecedores[{$value.iditempedido}][]">
                                                {html_options values=$fornecedorids output=$fornecedorvals selected=$value.idfornecedor}
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="control-label">Valor Unitário: </label>
                                            <input type="text" id="valoresunitarios" name="valoresunitarios[{$value.iditempedido}][]" class="form-control input-sm valoresunitarios" readonly />
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="control-label">Valor Total:</label>
                                            <input type="text" id="valorestotais" name="valorestotais[{$value.iditempedido}][]" class="form-control input-sm valorestotais" readonly />
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="control-label">Pdf:</label>
                                            <br>
                                            {if $value.iditempedido.arquivo != ""}
                                                <a href="./app/uploads/cotacoes/{$value.iditempedido.arquivo}" target="_blank">Download</a>
                                            {else}
                                                <p>Sem arquivo</p>
                                            {/if}
                                        </div>

                                        <div class="col-sm-1 ">


                                        </div>

                                    </div>
                                </div>
                            {/if}
                            </div>
                        {/foreach}
                    </div>
                </div>-->

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
                            <a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa-arrow-alt-circle-left" aria-hidden="true"></i> Volta </a>
                        </div>

                        <div class="col-sm-6">
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
            {include file='modals/pedidocompra/modal-produto-picture.tpl'}


</body>

</html>