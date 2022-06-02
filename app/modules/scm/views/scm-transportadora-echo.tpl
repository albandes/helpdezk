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
    {head_item type="js" src="$path/app/modules/scm/views/js/" files="createtransportadora.js"}
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
                    <h4>Cadastros / Transportadora / <strong>Visualizar</strong></h4>
                </div>
                <div class="col-sm-8 text-right"">&nbsp;
            </div>
        </div>

        <div class="row wrapper  border-bottom white-bg ">
            &nbsp;
        </div>

        <!-- First Line -->


        <div class="col-xs-12 white-bg" style="height:10px;"></div>

        <!-- Form area -->
        <form method="get" class="form-horizontal" id="update-transportadora-form">

            <!-- Hidden -->
            <input type="hidden" id="idperson" value="{$hidden_idperson}" />
            <input type="hidden" name="_token" id= "_token" value="{$token}">

            <div class="row wrapper  white-bg ">

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-1 b-l">

                    </div>

                    <div class="col-sm-11 b-l">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Pessoa:</label>
                            <div class="col-sm-3">
                                <input type="radio" name="tipo" value="2" disabled="disabled" class="control-label" {$tipojuridico}><label class="control-label">Jurídica</label>
                                <input type="radio" name="tipo" value="1"  disabled="disabled" class="control-label" {$tipofisico}><label class="control-label">Física</label>
                            </div>
                        </div>

                        <div class="form-group" id="juridica" >

                            <label class="col-sm-2 control-label">Razão Social:</label>
                            <div class="col-sm-5">
                                <input type="text" id="razaosocial" name="razaosocial" class="form-control input-sm"  placeholder="{$plh_razaosocial}" value="{$razaosocial}" readonly />
                            </div>

                        </div>

                        <div class="form-group" id="fisica">

                            <label class="col-sm-2 control-label">Nome:</label>
                            <div class="col-sm-5">
                                <input type="text" id="nomefisico" name="nomefisico" class="form-control input-sm"  placeholder="{$plh_nome}" value="{$nomefisico}" readonly />
                            </div>

                        </div>

                    </div>

                </div>

                <!-- row -->
                <div class="row wrapper  white-bg ">

                    <div class="col-sm-1 b-l">

                    </div>

                    <div class="col-sm-11 b-l">

                        <div class="form-group" id="fisica1">
                            <label class="col-sm-2 control-label">RG:</label>
                            <div class="col-sm-2">
                                <input type="text" id="rg" class="form-control input-sm" placeholder="{$plh_rg}"  value="{$rg}" readonly />
                            </div>
                            <label class="col-sm-2 control-label">Cpf:</label>
                            <div class="col-sm-2">
                                <input type="text"  id="cpf" class="form-control input-sm" placeholder="{$plh_cpf}"  value="{$cpf}" readonly />
                            </div>
                            <label class="col-sm-1 control-label">Email:</label>
                            <div class="col-sm-2">
                                <input type="text" id="email" name="email" class="form-control input-sm" placeholder="{$plh_email}" value="{$rg}" readonly />
                            </div>

                        </div>

                        <div class="form-group" id="juridica1">
                            <label class="col-sm-2 control-label">CNPJ:</label>
                            <div class="col-sm-2">
                                <input type="text" id="ein_cnpj" name="ein_cnpj" class="form-control input-sm" placeholder="{$plh_ein_cnpj}" value="{$ein_cnpj}" readonly />
                            </div>

                            <label class="col-sm-2 control-label">Inscrição Estadual:</label>
                            <div class="col-sm-2 ">
                                <input type="text" id="iestadual" name="iestadual" class="form-control input-sm" placeholder="{$plh_iestadual}"  value="{$iestadual}" readonly />
                            </div>
                            <label class="col-sm-1 control-label">Email:</label>
                            <div class="col-sm-2">
                                <input type="text" id="emailjuridica" name="emailjuridica" class="form-control input-sm" placeholder="{$plh_email}"  value="{$emailjuridica}" readonly />
                            </div>

                        </div>


                    </div>

                </div>
                <!--  -->
                <div class="row wrapper  white-bg ">

                    <div class="col-sm-1 b-l">

                    </div>

                    <div class="col-sm-11 b-l">
                        <div class="form-group">

                            <label class="col-sm-2 control-label">Telefone:</label>
                            <div class="col-sm-2">
                                <input type="text" id="phone_number" class="form-control input-sm" data-mask="(99) 999999999" placeholder="{$plh_phone_number}"  value="{$phone_number}" readonly />
                            </div>

                            <label class="col-sm-2 control-label">Celular:</label>
                            <div class="col-sm-2">
                                <input type="text" id="cel_phone" name="cel_phone" class="form-control input-sm" placeholder="{$plh_celular}"  value="{$cel_phone}" readonly />
                            </div>


                        </div>
                    </div>
                </div>

                <!-- row -->
                <div class="row wrapper  white-bg ">

                    <div class="col-sm-1 b-l">

                    </div>

                    <div class="col-sm-11 b-l">

                        <div class="form-group">

                            <label class="col-sm-2 control-label">Pa&iacute;s:</label>
                            <div class="col-sm-2">
                                <input type="text"  name="pais" class="form-control input-sm"  value="{$pais}" readonly />
                            </div>
                            <label class="col-sm-2 control-label">Estado:</label>
                            <div class="col-sm-2">
                                <input type="text"  name="estado" class="form-control input-sm"  value="{$estado}" readonly />
                            </div>
                            <label class="col-sm-1 control-label">Cidade:</label>
                            <div class="col-sm-2">
                                <input type="text"  name="cidade" class="form-control input-sm"  value="{$cidade}" readonly />
                            </div>

                        </div>

                    </div>

                </div>

                <!-- row -->
                <div class="row wrapper  white-bg ">

                    <div class="col-sm-1 b-l">

                    </div>

                    <div class="col-sm-11 b-l">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Cep:</label>
                            <div class="col-sm-2">
                                <input type="text" id="cep" class="form-control input-sm" data-mask="99999-999"  placeholder="{$plh_cep}" value="{$cep}" readonly />
                            </div>


                            <label class="col-sm-2 control-label">Logradouro:</label>
                            <div class="col-sm-2">
                                <input type="text" id="logradouro" name="logradouro" class="form-control input-sm"  value="{$logradouro}" readonly />
                            </div>



                            <label class="col-sm-1 control-label">Bairro:</label>
                            <div class="col-sm-2 ">
                                <input type="text"  name="bairro" class="form-control input-sm"  value="{$bairro}" readonly />
                            </div>



                        </div>

                    </div>

                </div>
                <!--  -->

                <!-- row -->
                <div class="row wrapper  white-bg ">

                    <div class="col-sm-1 b-l">

                    </div>

                    <div class="col-sm-11 b-l">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Endere&ccedil;o:</label>
                            <div class="col-sm-4">
                                <input type="text" id="endereco" class="form-control input-sm" placeholder="{$plh_logradouro}"  value="{$logradouro}" readonly />
                            </div>
                            <label class="col-sm-2 control-label">N&uacute;mero:</label>
                            <div class="col-sm-1">
                                <input type="text" id="numero" class="form-control input-sm" placeholder="" value="{$numero}" readonly />
                            </div>

                        </div>

                    </div>

                </div>
                <!--  -->

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-1 b-l">

                    </div>

                    <div class="col-sm-11 b-l">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Complemento:</label>
                            <div class="col-sm-4">
                                <input type="text" id="complemento" class="form-control input-sm" placeholder="{$plh_complemento}" value="{$complemento}" readonly/>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-1 b-l">

                    </div>

                    <div class="col-sm-11 b-l">
                        <div id="alert-update-transportadora"></div>
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



            {include file='modals/transportadora/modal-alert.tpl'}
            {include file='modals/transportadora/modal-bairro.tpl'}


</body>

</html>
