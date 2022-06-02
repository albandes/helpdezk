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
    {head_item type="js" src="$path/app/modules/scm/views/js/" files="createrelatoriopedido.js"}
    {head_item type="js" src="$path/app/modules/scm/views/js/" files="relatoriopedido.js"}
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
            <div class="col-xs-12 white-bg" style="height:10px;"></div>

            <div class="row wrapper    white-bg ">
                <div class="col-sm-4">

                    <h4>Relatório de Pedidos <strong></strong></h4>


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
        <form method="get" class="form-horizontal" id="relatoriopedido-form">

            <!-- Hidden -->
            <input type="hidden" name="_token" id= "_token" value="{$token}">


            <div class="row wrapper  white-bg ">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tipo:</label>
                        <div class="col-sm-3">
                            <select class="form-control input-sm " name="tipo" id="tipo" >
                                <option value="S">Solicitante</option>
                                <option value="C">Centro de Custo</option>
                                <option value="T">Compra pelo Transportador</option>
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
                        <label class="col-sm-2 control-label">Tipo Período:</label>
                        <div class="col-sm-5">
                            <label class="radio-line i-checks"> <input type="radio" value="E" name="tipoperiodo" checked> <i></i> Data Pedido </label>&nbsp;&nbsp;
                            <label class="radio-line i-checks"> <input type="radio" value="D" name="tipoperiodo"> <i></i> Data Entrega </label>
                        </div>


                    </div>

                </div>

            </div>


            <div class="row wrapper  white-bg ">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Período de:</label>
                        <div class="col-sm-3">
                            <div class="input-group date">
                                <input type="text" class="input-sm form-control" id="datainicial" name="datainicial" value="{$startdate}"/>
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            </div>
                        </div>
                        <label class="col-sm-1 control-label">até</label>
                        <div class="col-sm-3">
                            <div class="input-group date">
                                <input type="text" class="input-sm form-control" id="datafinal" name="datafinal" value="{$enddate}"/>
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            </div>
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
                            <select class="form-control input-sm solicitante"  id="solicitante" name="solicitante" >
                                {html_options values=$personids output=$personvals selected=$idpersonsel}
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
                        <label class="col-sm-2 control-label">Centro de Custo:</label>
                        <div class="col-sm-5">
                            <select class="form-control input-sm centrodecusto"  id="idcentrocusto" name="idcentrocusto" >
                                {html_options values=$centrocustoids output=$centrocustovals selected=$idcentrocusto}
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
                        <label class="col-sm-2 control-label">Produto:</label>
                        <div class="col-sm-5">
                            <select class="form-control input-sm produto" id="idproduto" name="idproduto"  >
                                {html_options values=$produtoids output=$produtovals selected=$idproduto}
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
                        <label class="col-sm-2 control-label">N° Pedido:</label>
                        <div class="col-sm-5">
                            <input type="text" id="idpedido" name="idpedido" class="form-control input-sm" placeholder="" >
                        </div>


                    </div>

                </div>

            </div>

            <div class="row wrapper  white-bg ">

                 <div class="col-sm-1 b-l">

                  </div>

                  <div class="col-sm-11 b-l">

                      <div class="form-group">
                           <label class="col-sm-2 control-label">Status:</label>
                           <div class="col-sm-5">
                               <select class="form-control input-sm status" id="idstatus" name="idstatus"  >
                                   {html_options values=$statusids output=$statusvals selected=$idstatus}
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
                        <label class="col-sm-2 control-label">Tipo Impressão:</label>
                        <div class="col-sm-5">
                            <label class="radio-line i-checks"> <input type="radio" value="1" name="tipoimpressao" checked> <i></i> Sem Cotação </label>&nbsp;&nbsp;
                            <label class="radio-line i-checks"> <input type="radio" value="2" name="tipoimpressao"> <i></i> Com Cotação </label>
                        </div>


                    </div>

                </div>

            </div>

            <div class="row wrapper  white-bg ">

                <div class="col-sm-1 b-l">

                </div>

                <div class="col-sm-11 b-l">
                    <div id="alert-create-relatoriopedido"></div>
                </div>
            </div>

            <div class="row wrapper  border-bottom white-bg ">
                &nbsp;
            </div>

            <div class="col-xs-12 white-bg" style="height:10px;"></div>


            <div class="row wrapper  white-bg ">

                <div class="col-sm-12 b-l">
                    <div class="form-group text-center">
                        <!--<a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa-times" aria-hidden="true"></i> Cancela </a>-->
                        <button type="button" class="btn btn-primary btn-md btnPrint " id="btnPrint" >
                            <span class="fa fa-print"></span>  &nbsp;Imprimir
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





</body>

</html>
</html>

