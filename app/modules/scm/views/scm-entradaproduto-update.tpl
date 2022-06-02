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
    {head_item type="js" src="$path/app/modules/scm/views/js/" files="createentradaproduto.js"}
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
        .itenspedidolayout{
            border: 1px solid black;
            border-radius: 10px;
            padding-bottom: 10px;
            margin-top: 5px;
            margin-bottom: 10px;
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
        }

        .layout{
            padding: 5px 0px 0px 0px;
        }

        .statusslectlayout{
            padding: 0px;
        }

        .btnAddRemovelayout{
            margin-top: 20px;
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
            <div class="row wrapper white-bg ibox-title">
                <div class="col-sm-4">
                    <h4>Cadastros / Entrada Produto / <strong>Editar</strong></h4>
                </div>
                <div class="col-sm-8 text-right">&nbsp;
            </div>
        </div>

        <div class="row wrapper  border-bottom white-bg ">
            &nbsp;
        </div>

        <!-- First Line -->


        <div class="col-xs-12 white-bg" style="height:10px;"></div>

        <!-- Form area -->
        <form method="get" class="form-horizontal" id="update-entradaproduto-form">

            <!-- Hidden -->
            <input type="hidden" name="_token" id= "_token" value="{$token}">
            <input type="hidden" name="identradaproduto" id= "identradaproduto" value="{$identradaproduto}">

            <div class="row wrapper  white-bg ">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tipo:</label>
                        <div class="col-sm-5">
                            {if $tipo== "C"}
                                <label class="radio-inline i-checks"><input type="radio" name="tipo" value="C" class="control-label" checked disabled>&nbsp;&nbsp;Compra</label>
                                <label class="radio-inline i-checks"><input type="radio" name="tipo" value="L" class="control-label" disabled>&nbsp;&nbsp;Lista de Materiais</label>
                                
                            {else}
                                <label class="radio-inline i-checks"><input type="radio" name="tipo" value="C" class="control-label" disabled>&nbsp;&nbsp;Compra</label>
                                <label class="radio-inline i-checks"><input type="radio" name="tipo" value="L" class="control-label" checked disabled>&nbsp;&nbsp;Lista de Materiais</label>
                            {/if}
                            
                        </div>
                        <div class="col-sm-3">
                            


                        </div>

                    </div>

                </div>

            </div>

            <div class="row wrapper  white-bg numeropedidodiv {$displayLine}">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">N° do Pedido:</label>
                        <div class="col-sm-5">
                            <input type="text" id="numeropedido" name="numeropedido" class="form-control input-sm"  placeholder="{$plh_numeropedido}" value="{$numeropedido}">
                        </div>

                    </div>

                </div>

            </div>

            <div class="row wrapper  white-bg notafiscaldiv {$displayLine}">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">N° da Nota Fiscal:</label>
                        <div class="col-sm-5">
                            <input type="text" id="numeronotafiscal" name="numeronotafiscal" class="form-control input-sm"  placeholder="{$plh_numeronotafiscal}" value="{$numeronotafiscal}">
                        </div>

                    </div>

                </div>

            </div>

            <div class="row wrapper  white-bg notafiscaldiv {$displayLine}">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Data Nota Fiscal:</label>
                        <div class="col-sm-3">
                            <div class="input-group date">
                                <input type="text" id="dtnotafiscal" name="dtnotafiscal" class="form-control input-sm" placeholder="{$plh_dtnotafiscal}" value="{$dtnotafiscal}" readonly />
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="row wrapper  white-bg fornecedordiv {$displayLine}">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Fornecedor:</label>
                        <div class="col-sm-5">
                            <select class="form-control input-sm fornecedores"  id="idfornecedor" name="idfornecedor" >
                                {html_options values=$personids output=$personvals selected=$idperson}
                            </select>
                        </div>

                    </div>

                </div>

            </div>

            <div class="row wrapper  white-bg ">

                <hr />
                <div class="col-sm-1"></div>

                <input type="hidden" id="_totalitens" name="" value="{$arrItens->_numOfRows}">
                <div class="col-sm-10 b-l pedido" id="pedido">
                    {foreach $arrItens as $key => $value}
                        {if $i == 0}
                            <div class="form-group  itenspedidolayout" >
                        {else}
                            <div class="form-group  itenspedidolayout" id="item_{$key}">
                            {*<div class="form-group" id="item_{$key}">*}
                        {/if}
                            <div class="col-sm-7">
                                <label class="control-label">Produto: </label>
                                <select class="form-control input-sm produtos" name="produtos[]"  >
                                    {html_options values=$produtoids output=$produtovals selected=$value.idproduto}
                                </select>
                            </div>
                                <input type="hidden" id="iditementradaproduto" name="iditementradaproduto[]" value="{$value.iditementradaproduto}">

                            <div class="col-sm-2">
                                <label class="control-label">Quantidade: </label>
                                <input type="number" id="quantidades" name="quantidades[]" class="form-control input-sm quantidades" placeholder="{$plh_quantidade}" value="{$value.quantidade}" step="0.25" min="0" />
                            </div>
                            <div class="col-sm-2">
                                <label class="control-label">Valor:</label>
                                <input type="text" id="valores" name="valores[]" class="form-control input-sm valores" value="{$value.valor}" >
                            </div>

                            <div class="col-sm-1">
                                <div class="btn-group btnAddRemovelayout">
                                    {if $i == 0}
                                        <button class="btn btn-success" id="btnAddPedido" type="button"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                    {else}
                                        <button class="btn btn-danger btnRemovePedido" data-pedido="{$key}" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>
                                    {/if}
                                    {$i = $i + 1}
                                </div>
                            </div>

                        </div>
                    {/foreach}
                </div>
                <div class="col-sm-1"></div>
            </div>
            <div class="row wrapper  white-bg ">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Valor Total dos Itens:</label>
                        <div class="col-sm-2">
                            <input type="text" id="valorestotais" name="valorestotais" class="form-control input-sm"  placeholder="{$plh_valorestotais}" value="{$valorestotais}">
                        </div>

                    </div>

                </div>

            </div>

            <div class="row wrapper white-bg ">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Valor Total da Nota Fiscal:</label>
                        <div class="col-sm-2">
                            <input type="text" id="valorestotaisnotafiscal" name="valorestotaisnotafiscal" class="form-control input-sm"  placeholder="{$plh_valornota}"value="{$valornota}" >
                        </div>

                    </div>

                </div>

            </div>


            <div class="row wrapper  white-bg ">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">
                    <div id="alert-update-entradaproduto"></div>
                </div>
            </div>

            <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>

            <div class="col-xs-12 white-bg" style="height:10px;"></div>

            <div class="row wrapper  white-bg text-center">
                <div class="col-sm-12 b-l">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa-arrow-alt-circle-left" aria-hidden="true"></i> {$smarty.config.Back_btn} </a>
                            <button type="button" class="btn btn-primary btn-md " id="btnUpdateEntradaProduto" >
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


            {include file='modals/entradaproduto/modal-alert.tpl'}

</body>

</html>

