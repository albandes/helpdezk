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
    {head_item type="js" src="$path/app/modules/acd/views/js/" files="createstudent.js"}
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

                    <h4>Cadastros / Aluno / <strong>Editar</strong></h4>

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
            <form method="get" class="form-horizontal" id="update-student-form">

                <!-- Hidden -->
                <input type="hidden" id="idperson" value="{$hidden_idperson}" />

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-2 b-l">
                        <div class="text-center" style="height:50px;">

                            <div id="myDropzone" class="dropzone dz-default dz-message" ><img alt="image" class="m-t-xs img-thumbnail" src="{$foto}"></div>
                        </div>
                    </div>

                    <div class="col-sm-10 b-l">

                        <div class="form-group">
                            <label class="col-sm-1 control-label">Nome:</label>
                            <div class="col-sm-5">
                                <input type="text" id="nome" name="nome" class="form-control input-sm" required placeholder="{$plh_nome}" value="{$nome}" >
                            </div>
                        </div>

                        <div class="col-sm-10 b-l">

                            <div class="form-group">
                                <label class="col-sm-1 control-label">ID Legado:</label>
                                <div class="col-sm-5">
                                    <input type="text" id="idlegacy" name="idlegacy" class="form-control input-sm" required placeholder="{$plh_idlegacy}" value="{$idlegacy}">
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-2 b-l">

                    </div>

                    <div class="col-sm-10 b-l">

                        <div class="form-group">
                            <label class="col-sm-1 control-label">Matr&iacute;cula:</label>
                            <div class="col-sm-5">
                                <input type="text" id="matricula" name="matricula" class="form-control input-sm" required placeholder="{$plh_matricula}" value="{$matricula}" >
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-2 b-l">

                    </div>

                    <div class="col-sm-10 b-l">
                        <div id="alert-update-student"></div>
                    </div>
                </div>

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-2 b-l">

                    </div>
                    <div class="col-sm-10 b-l">
                        <div class="form-group">

                            <div class="col-sm-6">
                                <a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa-times" aria-hidden="true"></i> Cancela </a>
                                <button type="button" class="btn btn-primary btn-md " id="btnUpdateStudent" >
                                    <span class="fa fa-check"></span>  &nbsp;Envia
                                </button>
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


                {*include file='modals/acdstudent/modalAlert.tpl'*}
                {include file='modals/acdstudent/modal-alert.tpl'}




</body>

</html>
