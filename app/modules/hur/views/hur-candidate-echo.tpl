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
    {head_item type="js" src="$path/app/modules/hur/views/js/" files="candidate-form.js"}
    <!-- Font Awesome -->
    {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
    <!-- animate -->
    {head_item type="css" src="$path/css/" files="animate.css"}
    <!-- Helpdezk CSS -->
    {head_item type="css" src="$path/css/" files="$theme.css"}
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
   {* <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>*}
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
    <!-- Summernote Editor -->
    {head_item type="css" src="$path/css/plugins/summernote/$summernote_version/" files="summernote.css"}
    {head_item type="css" src="$path/css/plugins/summernote/" files="summernote-bs3.css"}
    {head_item type="js"  src="$path/includes/js/plugins/summernote/$summernote_version/" files="summernote.js"}
    

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

            <div class="row border-bottom"></div>

            <div class="wrapper wrapper-content">
                <div class="row wrapper white-bg ibox-title">
                    <div class="col-sm-4">
                        <h4>Cadastros / Currículos / <strong>Visualização</strong></h4>
                    </div>
                    <div class="col-sm-8 text-right">
                    </div>
                </div>

                <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>

                <div class="col-xs-12 white-bg" style="height:10px;"></div>

                <!-- Form area -->
                <form method="get" class="form-horizontal" id="insert-form" name="insert-form">
                    <!-- Hidden -->
                    <input type="hidden" name="_token" id= "_token" value="{$token}">
                    <input type="hidden" name="idcurriculum" id= "idcurriculum" value="{$hidden_idcurriculum}">

                    <div class="row wrapper  white-bg "></div>
                    <div class="row wrapper  white-bg ">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-user" aria-hidden="true"></i>&nbsp;{$smarty.config.personal_details}
                                <div class="text-right"><a href="" id="btnCancel_1" class="btn btn-white btn-md btnCancel" role="button"><i class="fa fa-arrow-circle-o-left" aria-hidden="true"></i> {$smarty.config.Back_btn} </a></div>
                            </div>
                            <div class="panel-body">
                                <div class="row col-sm-10 b-l">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.name_full}:</label>
                                        <div class="col-sm-5 control-text">
                                            <p class="form-control-static">{$arrData.name}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.cpf}:</label>
                                        <div class="col-sm-2">
                                            <p id="cpfinfo" class="form-control-static">{$arrData.ssn_cpf}</p>
                                        </div>
                                        <label class="col-sm-2 control-label">{$smarty.config.rg}:</label>
                                        <div class="col-sm-2">
                                            <p class="form-control-static">{$arrData.rg}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.Birth_date}:</label>
                                        <div class="col-sm-2">
                                            <p id="birthdate" class="form-control-static">{$arrData.dtbirth_fmt}</p>
                                        </div>
                                        <label class="col-sm-2 control-label">Idade:</label>
                                        <div class="col-sm-2">
                                            <p class="form-control-static">{$arrData.age} anos</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.email}:</label>
                                        <div class="col-sm-5">
                                            <p class="form-control-static">{$arrData.email}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.gender}:</label>
                                        <div class="col-sm-2">
                                            <p class="form-control-static">{$arrData.gender}</p>
                                        </div>
                                        <label class="col-sm-2 control-label">{$smarty.config.marital_status}:</label>
                                        <div class="col-sm-2">
                                            <p class="form-control-static">{$arrData.maritalstatus}</p>
                                        </div>
                                        <label class="col-sm-2 control-label">{$smarty.config.childs_number}:</label>
                                        <div class="col-sm-2">
                                            <p class="form-control-static">{$arrData.qtchilds}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.mother}:</label>
                                        <div class="col-sm-5">
                                            <p class="form-control-static">{$arrData.mother}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.nationality}:</label>
                                        <div class="col-sm-3">
                                            <p class="form-control-static">{$arrData.nationality}</p>
                                        </div>
                                        <label class="col-sm-2 control-label">{$smarty.config.birth_place}:</label>
                                        <div class="col-sm-3">
                                            <p class="form-control-static">{$arrData.birth_place}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.home_phone}:</label>
                                        <div class="col-sm-2">
                                            <p id="home_phone" class="form-control-static">{$arrData.phone}</p>
                                        </div>
                                        <label class="col-sm-2 control-label">{$smarty.config.Mobile_phone}:</label>
                                        <div class="col-sm-2">
                                            <p id="mobile_phone" class="form-control-static">{$arrData.mobile_phone}</p>
                                        </div>
                                        <label class="col-sm-2 control-label">{$smarty.config.scrap_phone}:</label>
                                        <div class="col-sm-2">
                                            <p id="mobile_phone" class="form-control-static">{$arrData.scrap_phone}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">&nbsp;</label>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary" type="button"> <i class="fab fa-facebook-square" aria-hidden="true"></i></button>
                                            </span>
                                                <p class="form-control"><a href="{$facebookUrl}" target="_blank">{$arrData.facebook_profile}</a></p>
                                            </div>
                                        </div>

                                        <div class="col-sm-5">
                                            <div class="input-group">
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary" type="button"> <i class="fab fa-linkedin" aria-hidden="true"></i></button>
                                            </span>
                                                <p class="form-control"><a href="{$linkedinUrl}" target="_blank">{$arrData.linkedin_profile}</a></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">&nbsp;</label>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary" type="button"> <i class="fab fa-twitter-square" aria-hidden="true"></i></button>
                                            </span>
                                                <p class="form-control"><a href="{$twitterUrl}" target="_blank">{$arrData.twitter_profile}</a></p>
                                            </div>
                                        </div>

                                        <div class="col-sm-5">
                                            <div class="input-group">
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary" type="button"> <i class="fab fa-skype" aria-hidden="true"></i></button>
                                            </span>
                                                <p class="form-control">{$arrData.skype_profile}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.lattes}:</label>
                                        <div class="col-sm-5">
                                            <p class="form-control-static">{$arrData.lattes_link}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-2 b-l">
                                    <div class="text-center">
                                       <img src="{$hdkUrl}{$photoName}" style="height: auto; width: 200px">
                                    </div>
                                </div>


                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp;{$smarty.config.address_details}
                                <div class="text-right"><a href="" id="btnCancel_2" class="btn btn-white btn-md btnCancel" role="button"><i class="fa fa-arrow-circle-o-left" aria-hidden="true"></i> {$smarty.config.Back_btn} </a></div>
                            </div>
                            <div class="panel-body">
                                <div class="row col-sm-10 b-l">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.address}:</label>
                                        <div class="col-sm-5">
                                            <p class="form-control-static">{$arrData.address}</p>
                                        </div>
                                        <label class="col-sm-2 control-label">{$smarty.config.Number}:</label>
                                        <div class="col-sm-2">
                                            <p class="form-control-static">{$arrData.number}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.complement}:</label>
                                        <div class="col-sm-5">
                                            <p class="form-control-static">{$arrData.complement}</p>
                                        </div>
                                        <label class="col-sm-2 control-label">{$smarty.config.Zipcode}:</label>
                                        <div class="col-sm-2">
                                            <p id="cep" class="form-control-static">{$arrData.zipcode}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.Neighborhood}:</label>
                                        <div class="col-sm-2">
                                            <p class="form-control-static">{$arrData.neighborhood}</p>
                                        </div>
                                        <label class="col-sm-1 control-label">{$smarty.config.City}:</label>
                                        <div class="col-sm-2">
                                            <p class="form-control-static">{$arrData.city}</p>
                                        </div>
                                        <label class="col-sm-2 control-label">{$smarty.config.uf}:</label>
                                        <div class="col-sm-2">
                                            <p class="form-control-static">{$arrData.uf}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row col-sm-1 b-l">&nbsp;</div>

                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-briefcase" aria-hidden="true"></i>&nbsp;{$smarty.config.role_details}
                                <div class="text-right"><a href="" id="btnCancel_3" class="btn btn-white btn-md btnCancel" role="button"><i class="fa fa-arrow-circle-o-left" aria-hidden="true"></i> {$smarty.config.Back_btn} </a></div>
                            </div>
                            <div class="panel-body">
                                <div class="row col-sm-10 b-l">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.Area}:</label>
                                        <div class="col-sm-3">
                                            <p class="form-control-static">{$arrData.area}</p>
                                        </div>
                                        <label class="col-sm-2 control-label">{$smarty.config.role}:</label>
                                        <div class="col-sm-3">
                                            <p class="form-control-static">{$arrData.role}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.role_experience_time}:</label>
                                        <div class="col-sm-4">
                                            <p class="form-control-static">{$arrData.exp_role_year} {$arrData.exp_role_month}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row col-sm-1 b-l">&nbsp;</div>

                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-file-alt" aria-hidden="true"></i>&nbsp;{$smarty.config.curriculum_details}
                                <div class="text-right"><a href="" id="btnCancel_4" class="btn btn-white btn-md btnCancel" role="button"><i class="fa fa-arrow-circle-o-left" aria-hidden="true"></i> {$smarty.config.Back_btn} </a></div>
                            </div>
                            <div class="panel-body">
                                <div class="row col-sm-10 b-l">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.preference_shift}:</label>
                                        <div class="col-sm-2">
                                            <p class="form-control-static">{$arrData.shift}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.hiring}:</label>
                                        <div class="col-sm-2">
                                            <p class="form-control-static">{$arrData.typehiring}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.deficiency}:</label>
                                        <div class="col-sm-2">
                                            <p class="form-control-static">{$arrData.deficiency}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.Observation}:</label>
                                        <div class="col-sm-7">
                                            <textarea rows="6" cols="100" id="motivo" name="motivo" class="form-control input-sm" readonly>{$arrData.note}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{$smarty.config.summary}:</label>
                                        <div class="col-sm-7">
                                            <textarea rows="6" cols="100" id="motivo" name="motivo" class="form-control input-sm" readonly>{$arrData.summary}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row col-sm-1 b-l">&nbsp;</div>
                                {if $fileName}
                                <div class="form-group">
                                    <div class="col-sm-3">&nbsp;</div>
                                    <div class="col-sm-6" style="text-align: center">
                                        <i id="btnPDF" class="far fa-file-pdf fa-5x" aria-hidden="true"></i><br>
                                        <small class="text-navy">Clique no &iacute;cone acima para visualizar o curr&iacute;culo enviado</small>
                                        <input type="hidden" id="filename" name="filename" value="{$hdkUrl}{$fileName}">
                                    </div>
                                </div>
                                {/if}
                            </div>
                        </div>


                    </div>                    
                </form>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-12 b-l">
                        <div id="alert-send-curriculum"></div>
                    </div>
                </div>

                <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>

                <div class="col-xs-12 white-bg" style="height:10px;"></div>

                <div class="row wrapper  white-bg ">
                    <div class="col-sm-12 b-l">
                        <div class="form-group">
                            <div class="col-sm-12" style="text-align: center">
                                <a href="" id="btnCancel" class="btn btn-white btn-md btnCancel" role="button"><i class="fa fa-arrow-circle-o-left" aria-hidden="true"></i> {$smarty.config.Back_btn} </a>
                                <button type="button" class="btn btn-primary btn-md " id="btnEmail" >
                                    <span class="fa fa-envelope"></span>  &nbsp;{$smarty.config.email}
                                </button>
                                <button type="button" class="btn btn-secondary btn-md " id="btnPrint" >
                                    <span class="fa fa-print"></span>  &nbsp;{$smarty.config.Print}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row wrapper  border-bottom white-bg ">
                    &nbsp;
                </div>

                <div class="row border-bottom white-bg ">
                    <div class="row border-bottom">
                        <div class="footer">
                            {include file=$footer}
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <!--<div class="row border-bottom white-bg "></div>
        <div class="row border-bottom"></div>


        <div class="footer">
            {*include file=$footer*}
        </div>-->
    </div>


    {include file='modals/candidate/modal-email.tpl'}




</body>

</html>