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
    <!-- Helpdezk -->
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/app/modules/spm/views/js/" files="createatleta.js"}
    <!-- Font Awesome -->
    {head_item type="css" src="$path/css/font-awesome/css/" files="font-awesome.css"}
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
            <div class="col-xs-12 white-bg" style="height:10px;"></div>

            <div class="row wrapper    white-bg ">
                <div class="col-sm-4">

                    <h4>Cadastros / Atleta / <strong>Visualizar</strong></h4>

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
            <form method="get" class="form-horizontal" id="update-atleta-form">

                <!-- Hidden -->

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-2 b-l">
                        <div class="text-center" style="height:50px;">
                            <img alt="image" class="m-t-xs img-thumbnail" src="{$foto}">
                        </div>
                    </div>

                    <div class="col-sm-10 b-l">

                        <div class="form-group">
                            <label class="col-sm-1 control-label">Apelido:</label>
                            <div class="col-sm-3">
                                <input type="text"  class="form-control input-sm" value="{$apelido}" >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-1 control-label">Nome:</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control input-sm"  value="{$nome}" >
                            </div>
                        </div>

                        <div class="form-group">

                            <label class="col-sm-1 control-label">Posi&ccedil;&atildeo:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-sm"  value="{$posicao}" >
                            </div>

                            <label class="col-sm-1 control-label">Condi&ccedil;&atildeo:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-sm"  value="{$condicao}" >
                            </div>

                            <label class="col-sm-1 control-label">Departamento:</label>
                            <div class="col-sm-2 ">
                                <input type="text" class="form-control input-sm"  value="{$departamento}" >
                            </div>

                        </div>

                    </div>

                </div>

                <!-- row -->
                <div class="row wrapper  white-bg ">

                    <div class="col-sm-2 b-l">

                    </div>

                    <div class="col-sm-10 b-l">

                        <div class="form-group">
                            <label class="col-sm-1 control-label">Cpf:</label>
                            <div class="col-sm-2">
                                <input type="text"  class="form-control input-sm" value="{$cpf}" />
                            </div>
                            <label class="col-sm-1 control-label">Nascimento:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-sm" value="{$dtnasc}" />
                            </div>
                            <label class="col-sm-1 control-label">Email:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-sm" value="{$email}" />
                            </div>

                        </div>


                    </div>

                </div>
                <!--  -->
                <div class="row wrapper  white-bg ">

                    <div class="col-sm-2 b-l">

                    </div>

                    <div class="col-sm-10 b-l">
                        <div class="form-group">

                            <label class="col-sm-1 control-label">Telefone:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-sm"  value="{$telefone}" />
                            </div>

                            <label class="col-sm-1 control-label">Celular:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-sm" value="{$celular}"  required/>
                            </div>


                        </div>
                    </div>
                </div>

                <!-- row -->
                <div class="row wrapper  white-bg ">

                    <div class="col-sm-2 b-l">

                    </div>

                    <div class="col-sm-10 b-l">

                        <div class="form-group">

                            <label class="col-sm-1 control-label">Pa&iacute;s:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-sm"  value="{$pais}" />
                            </div>

                            <label class="col-sm-1 control-label">Estado:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-sm"  value="{$estado}" />
                            </div>

                            <label class="col-sm-1 control-label">Cidade:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-sm"  value="{$cidade}" />
                            </div>

                        </div>

                    </div>

                </div>

                <!-- row -->
                <div class="row wrapper  white-bg ">

                    <div class="col-sm-2 b-l">

                    </div>

                    <div class="col-sm-10 b-l">

                        <div class="form-group">
                            <label class="col-sm-1 control-label">Cep:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-sm"  value="{$cep}" />
                            </div>


                            <label class="col-sm-1 control-label">Logradouro:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-sm"  value="{$tipologradouro}" />
                            </div>
                            <label class="col-sm-1 control-label">Bairro:</label>
                            <div class="col-sm-2 ">
                                <input type="text" class="form-control input-sm"  value="{$bairro}" />
                            </div>
                        </div>

                    </div>

                </div>
                <!--  -->

                <!-- row -->
                <div class="row wrapper  white-bg ">

                    <div class="col-sm-2 b-l">

                    </div>

                    <div class="col-sm-10 b-l">

                        <div class="form-group">
                            <label class="col-sm-1 control-label">Endere&ccedil;o:</label>
                            <div class="col-sm-5">
                                <input type="text" id="endereco" class="form-control input-sm" placeholder="{$plh_logradouro}" value="{$logradouro}" />
                            </div>
                            <label class="col-sm-1 control-label">N&uacute;mero:</label>
                            <div class="col-sm-1">
                                <input type="text" id="numero" class="form-control input-sm"  value="{$numero}"  />
                            </div>

                        </div>

                    </div>

                </div>
                <!--  -->

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-2 b-l">

                    </div>

                    <div class="col-sm-10 b-l">
                        <div class="form-group">
                            <label class="col-sm-1 control-label">Complemento:</label>
                            <div class="col-sm-5">
                                <input type="text" id="complemento" class="form-control input-sm" placeholder="{$plh_complemento}" value="{$complemento}" />
                            </div>

                        </div>
                    </div>
                </div>

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-2 b-l">

                    </div>

                    <div class="col-sm-10 b-l">
                        <div id="alert-update-atleta"></div>
                    </div>
                </div>

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-2 b-l">

                    </div>
                    <div class="col-sm-10 b-l">
                        <div class="form-group">

                            <div class="col-sm-6">
                                <a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa fa-arrow-circle-o-left" aria-hidden="true"></i> Volta </a>

                            </div>

                            <div class="col-sm-3">

                            </div>
                        </div>
                    </div>
                </div>

            </form>
            <!-- End form area -->




            <div class="row border-bottom white-bg ">
            <div class="row border-bottom">


            <div class="footer">
            <div class="pull-right">
                10GB of <strong>250GB</strong> Free.
            </div>
            <div>
                <strong>Copyright</strong> Pipegrep &copy; 2014-2015
            </div>
        </div>
</div>


                {include file='modals/cadastroatleta/modalAlert.tpl'}
                {include file='modals/cadastroatleta/modal-alert.tpl'}
                {include file='modals/cadastroatleta/modal-bairro.tpl'}




</body>

</html>
