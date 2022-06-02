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
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/app/modules/emq/views/js/" files="emq-parent-create.js"}
    <!-- Font Awesome -->
    {*head_item type="css" src="$path/css/font-awesome/css/" files="font-awesome.css"*}
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



        <div class="wrapper wrapper-content  ">
            <div class="row wrapper white-bg ibox-title">
                <div class="col-sm-4">

                    <h4>{$smarty.config.cat_records} / {$smarty.config.pgr_emq_parents} / <strong>{$smarty.config.edit}</h4>

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
        <form method="get" class="form-horizontal" id="update-parent-form" enctype="multipart/form-data">

            <!-- Hidden -->
            <input type="hidden" name="_token" id= "_token" value="{$token}">
            <input type="hidden" name="idparent" id= "idparent" value="{$idparent}">
            <input type="hidden" name="idperson_profile" id= "idperson_profile" value="{$idperson_profile}">

            <div class="row wrapper  white-bg ">
                <div class="col-sm-1 b-l">
                </div>

                <div class="col-sm-11 b-l">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{$smarty.config.Name}:</label>
                        <div class="col-sm-5">
                            <input type="text" id="parentName" name="parentName" class="form-control input-sm"  value="{$parentName}" />
                        </div>
                    </div>
                </div>

            </div>

            <div class="row wrapper  white-bg ">
                <div class="col-sm-1 b-l">
                </div>

                <div class="col-sm-11 b-l">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{$smarty.config.Gender}:</label>
                        <div class="col-sm-5">
                            <select class="form-control input-sm"  id="parentGender" name="parentGender">
                                {html_options values=$genderids output=$gendervals selected=$idgender}
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
                        <label class="col-sm-2 control-label">{$smarty.config.cpf}:</label>
                        <div class="col-sm-5">
                            <input type="text" id="parentCpf" name="parentCpf" class="form-control input-sm"  value="{$parentCpf}" />
                        </div>
                    </div>
                </div>

            </div>

            <div class="row wrapper  white-bg ">

                <div class="col-sm-1 b-l">
                </div>

                <div class="col-sm-11 b-l">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">{$smarty.config.email}:</label>
                        <div class="col-sm-5">
                            <input type="text" id="parentEmail" name="parentEmail" class="form-control input-sm" value="{$parentEmail}" />
                        </div>

                    </div>

                </div>
            </div>

            <div class="col-xs-12 white-bg" style="height:30px;"></div>

            <div class="row wrapper  white-bg ">

                <div class="col-sm-12 b-l pedido" id="pedido">
                    <div class="col-sm-12">
                        <table id="studentList" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="col-sm-5 text-center"><h4><strong>{$smarty.config.itm_student}</strong></h4></th>
                                    <th class="col-sm-3 text-center"><h4><strong>{$smarty.config.emq_kinship}</strong></h4></th>
                                    <th class="col-sm-1 text-center"><h4><strong>{$smarty.config.emq_email_sms}</strong></h4></th>
                                    <th class="col-sm-1 text-center"><h4><strong>{$smarty.config.emq_bank_ticket}</strong></h4></th>
                                    <th class="col-sm-1 text-center"><h4><strong>{$smarty.config.emq_access_app}</strong></h4></th>
                                    <th class="col-sm-1 text-center"><h4><strong>{$smarty.config.TMS_Delete}</strong></h4></th>
                                </tr>
                            </thead>
                            <tbody>
                            {$i = 1}
                            {foreach $arrStudents as $key => $value}
                                <tr>
                                    <td>
                                        <select class="form-control input-sm updCmbStudent" id="idstudent{$i}" name="idstudent[]" >
                                            {html_options values=$studentids output=$studentvals selected=$value.idstudent}
                                        </select>
                                        <input type="hidden" id="numId" value="{$i}"/>
                                    </td>
                                    <td>
                                        <select class="form-control input-sm updCmbKinship" id="idkinship{$i}" name="idkinship[]" >
                                            {html_options values=$kinshipids output=$kinshipvals selected=$value.idkinship}
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" id="checkEmailSms{$i}" name="checkEmailSms[]" {$value.email_sms} value="1"/>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" id="checkBankTicket{$i}" name="checkBankTicket[]" {$value.bank_ticket} value="1"/>
                                    </td class="text-center">
                                    <td class="text-center">
                                        <input type="checkbox" id="checkAccessApp{$i}" name="checkAccessApp[]" {$value.access_app} value="1"/>
                                    </td class="text-center">
                                    <td class="text-center">
                                        <a href="javascript:;" onclick="removeRow(this,'studentList','upd')" class="btn btn-danger bt-xs"><i class="fa fa-times"></i></a>
                                    </td>
                                </tr>
                                {$i = $i + 1}
                            {/foreach}

                            </tbody>
                        </table>
                    </div>
                </div  class="col-sm-12">

                <div>
                    <button type="button" class="btn btn-primary btn-md " id="btnAddStudent" >
                        <span class="fa fa-plus"></span>  &nbsp;{$smarty.config.emq_add_student}
                    </button>
                </div>
            </div>

            <div class="col-xs-12 white-bg" style="height:10px;"></div>

            <div class="row wrapper  white-bg ">
                <div class="col-sm-12 b-l">
                    <div id="alert-update-parent"></div>
                </div>
            </div>

            <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>

            <div class="col-xs-12 white-bg" style="height:10px;"></div>

            <div class="row wrapper  white-bg text-center">
                <div class="col-sm-12 form-group">
                    <a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa-arrow-alt-circle-left" aria-hidden="true"></i> Volta </a>
                    <button type="button" class="btn btn-primary btn-md " id="btnUpdateParent" >
                        <span class="fa fa-save"></span>  &nbsp;{$smarty.config.Save}
                    </button>
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

            {include file='modals/main/modal-alert.tpl'}


</body>

</html>
