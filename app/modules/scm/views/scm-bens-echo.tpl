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
    {head_item type="js" src="$path/app/modules/scm/views/js/" files="createbens.js"}
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
                    <h4>Cadastros / Bens / <strong>Visualizar</strong></h4>
                </div>
                <div class="col-sm-8 text-right"">&nbsp;
            </div>

            <div class="row wrapper  border-bottom white-bg ">
                &nbsp;
            </div>

            <!-- First Line -->


            <div class="col-xs-12 white-bg" style="height:10px;"></div>

            <!-- Form area -->
            <form method="get" class="form-horizontal" id="update-bens-form">

                <!-- Hidden -->

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-1 b-l">

                    </div>

                    <div class="col-sm-11 b-l">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Descricao:</label>
                            <div class="col-sm-5">
                                <input type="text" id="descricao" name="descricao" class="form-control input-sm" required placeholder="{$plh_descricao}" value="{$descricao}" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Número do Patrimônio:</label>
                            <div class="col-sm-5">
                                <input type="text" id="numeropatrimonio" name="numeropatrimonio" class="form-control input-sm" required placeholder="{$plh_numeropatrimonio}" value="{$numeropatrimonio}" readonly />
                            </div>
                        </div>

                        <div class="form-group ">
                            <label class="col-sm-2 control-label">Marca:</label>
                            <div class="col-sm-5">
                                <input type="text" name="nomemarca" class="form-control input-sm" value="{$nomemarca}" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Estado:</label>
                            <div class="col-sm-5">
                                <input type="text" name="nomeestado" class="form-control input-sm" value="{$nomeestado}" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Local:</label>
                            <div class="col-sm-5">
                                <input type="text" name="nomelocal" class="form-control input-sm" value="{$nomelocal}" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Grupo de Bens:</label>
                            <div class="col-sm-5">
                                <input type="text" name="nomegrupodebens" class="form-control input-sm" value="{$nomegrupodebens}" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Data de Aquisição:</label>
                            <div class="col-sm-3">
                                {if empty($dataaquisicao)}
                                    <input type="text" id="dataaquisicao" name="dataaquisicao" class="form-control input-sm" value="{$dataaquisicao}" readonly />
                                {else}
                                    <input type="date" id="dataaquisicao" name="dataaquisicao" class="form-control input-sm" value="{$dataaquisicao}" readonly />
                                {/if}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Fornecedor:</label>
                            <div class="col-sm-5">
                                <input type="text" name="nomefornecedor" class="form-control input-sm" value="{$nomefornecedor}" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Doação:</label>
                            <div class="col-sm-3">
                                {if $doacao == 'S'}
                                    <input type="radio" name="doacao" value="N" disabled="disabled" class="control-label" ><label class="control-label">Não</label>
                                    <input type="radio" name="doacao" value="S" disabled="disabled" checked class="control-label" ><label class="control-label">Sim</label>
                                {else}
                                    <input type="radio" name="doacao" value="N" disabled="disabled" checked class="control-label" ><label class="control-label">Não</label>
                                    <input type="radio" name="doacao" value="S" disabled="disabled" class="control-label" ><label class="control-label">Sim</label>
                                {/if}

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">NF Entrada:</label>
                            <div class="col-sm-3">
                                <input type="text" id="nfentrada" name="nfentrada" class="form-control input-sm" value="{$nfentrada}" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Valor:</label>
                            <div class="col-sm-3">
                                <input type="text" id="valor" name="valor" class="form-control input-sm" value="{$valor}" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Número de Série:</label>
                            <div class="col-sm-5">
                                <input type="text" id="numeroserie" name="numeroserie" class="form-control input-sm" value="{$numeroserie}" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Data de Garantia:</label>
                            <div class="col-sm-3">
                                {if empty($datagarantia)}
                                    <input type="text" id="datagarantia" name="datagarantia" class="form-control input-sm" value="{$datagarantia}" readonly />
                                {else}
                                    <input type="date" id="datagarantia" name="datagarantia" class="form-control input-sm" value="{$datagarantia}" readonly />
                                {/if}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Quantidade:</label>
                            <div class="col-sm-5">
                                <input type="text" id="quantidade" name="quantidade" class="form-control input-sm" value="{$quantidade}" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Baixa:</label>
                            <div class="col-sm-3">
                                {if $baixa == 'S'}
                                    <input type="radio" name="baixa" value="N" disabled="disabled" class="control-label" ><label class="control-label">Não</label>
                                    <input type="radio" name="baixa" value="S" disabled="disabled" checked class="control-label" ><label class="control-label">Sim</label>
                                {else}
                                    <input type="radio" name="baixa" value="N" disabled="disabled" checked class="control-label" ><label class="control-label">Não</label>
                                    <input type="radio" name="baixa" value="S" disabled="disabled" class="control-label" ><label class="control-label">Sim</label>
                                {/if}
                            </div>
                        </div>

                    </div>

                </div>

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-1 b-l">

                    </div>

                    <div class="col-sm-11 b-l">
                        <div id="alert-update-bens"></div>
                    </div>
                </div>

                <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>

                <div class="col-xs-12 white-bg" style="height:10px;"></div>

                <div class="row wrapper  white-bg text-center">
                    <div class="col-sm-12 b-l">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa fa-arrow-circle-o-left" aria-hidden="true"></i> Volta </a>
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


                {include file='modals/bens/modal-alert.tpl'}


</body>

</html>
