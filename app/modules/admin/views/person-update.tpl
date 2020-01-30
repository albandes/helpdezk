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
    {head_item type="js"  src="$path/includes/js/plugins/jqGrid/i18n/" files="$jqgrid_i18nFile"}
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

    {head_item type="css" src="$path/css/" files="admmenu.css"}
    <!-- Helpdezk -->
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/app/modules/admin/views/js/" files="createperson.js"}
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
            demoVersion = '{/literal}{$demoversion}{literal}';


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

        #btnCancel{
            margin-left: 150px;
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
                    <h4>Cadastros / {$smarty.config.people} / <strong>{$smarty.config.edit}</strong></h4>
                </div>
            </div>

            <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>

            <!-- First Line -->


            <div class="col-xs-12 white-bg" style="height:10px;"></div>


            <!-- Form area -->
            <form method="get" class="form-horizontal" id="update-person-form">

                <!-- Hidden -->
                <input type="hidden" name="_token" id= "_token" value="{$token}">
                <input type="hidden" name="idperson" id= "idperson" value="{$hidden_idperson}">
                <input type="hidden" name="category" id= "category" value="{$idnatureperson}">
                <input type="hidden" name="logindemo" id= "logindemo" value="{$hidden_login}"> <!-- Use in demo version -->

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-1 b-l">
                    </div>

                    <div class="col-sm-10 b-l">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.Category}:</label>
                            <div class="col-sm-5 form-control-static">
                                {$txtCategory}
                            </div>
                        </div>

                        <div class="form-group {$displayNatural}">
                            <label class="col-sm-2 control-label">{$smarty.config.Login}:</label>
                            <div class="col-sm-2 form-control-static">
                                {$txtLogin}
                            </div>
                            <label class="col-sm-2 control-label">{$smarty.config.Login_type}:</label>
                            <div class="col-sm-4">
                                <select class="form-control input-sm"  id="logintype" name="logintype"" >
                                    {html_options values=$logintypeids output=$logintypevals selected=$idlogintype}
                                </select>
                            </div>
                            <div class="col-sm-2 ">
                                <button class="btn btn-default tooltip-buttons" id="btnChangePass" type="button" data-toggle="tooltip" data-placement="top" title="{$smarty.config.Change_password}" tabindex="-1"><i class="fa fa-key" aria-hidden="true"></i> {$smarty.config.Change_password}</button>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label class="col-sm-2 control-label">{$smarty.config.Name}:</label>
                            <div class="col-sm-7">
                                <input type="text" id="personName" name="personName" class="form-control input-sm" placeholder="{$plh_controller_description}" value="{$personName}" >
                            </div>
                        </div>

                        <div class="form-group {$displayNatural}">
                            <label class="col-sm-2 control-label">{$smarty.config.cpf}:</label>
                            <div class="col-sm-4">
                                <input type="text" id="cpf" name="cpf" class="form-control input-sm" placeholder="{$plh_program_description}" value="{$cpfVal}" >
                            </div>
                            <label class="col-sm-2 control-label">{$smarty.config.Birth_date}:</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <input type="text" id="dtbirth" name="dtbirth" class="form-control input-sm" value="{$dtbirthVal}" readonly />
                                    <span class="input-group-addon"><i class="fa fa-calendar-alt"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group {$displayJuridical}">
                            <label class="col-sm-2 control-label">{$smarty.config.EIN_CNPJ}:</label>
                            <div class="col-sm-4">
                                <input type="text" id="cnpj" name="cnpj" class="form-control input-sm" placeholder="{$plh_program_description}" value="{$cnpjVal}" >
                            </div>
                            <label class="col-sm-2 control-label">{$smarty.config.Type}:</label>
                            <div class="col-sm-4">
                                <select class="form-control input-sm "  id="type_company" name="type_company" data-placeholder="{$plh_module_select}" >
                                    {html_options values=$levelcompanyids output=$levelcompanyvals selected=$idlevelcompany}
                                </select>
                            </div>
                        </div>

                        <div class="form-group {$displayNatural}">
                            <label class="col-sm-2 control-label">{$smarty.config.Gender}:</label>
                            <div class="col-sm-5">
                                <label class="radio-inline i-checks"> <input type="radio" name="gender" id="male" value="M" {$checkM}>&nbsp;&nbsp;{$smarty.config.Male}</label>
                                <label class="radio-inline i-checks"> <input type="radio" name="gender" id="female" value="F" {$checkF}>&nbsp;&nbsp;{$smarty.config.Female}</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.email}:</label>
                            <div class="col-sm-7">
                                <input type="text" id="email" name="email" class="form-control input-sm" required placeholder="{$plh_module_smartyvar}" value="{$emailVal}" >
                            </div>
                        </div>

                        <div class="form-group {$displayNatural}">
                            <label class="col-sm-2 control-label">{$smarty.config.Company}:</label>
                            <div class="col-sm-4">
                                <select class="form-control input-sm"  id="company" name="company" data-placeholder="{$plh_module_select}" >
                                    <option value="">{$smarty.config.Select_company}</option>
                                    {html_options values=$juridicalids output=$juridicalvals selected=$idjuridical}
                                </select>
                            </div>
                            <label class="col-sm-2 control-label">{$smarty.config.Department}:</label>
                            <div class="col-sm-4">
                                <select class="form-control input-sm"  id="department" name="department" data-placeholder="{$smarty.config.Select_department}" >
                                    <option value="">{$smarty.config.Select_department}</option>
                                    {html_options values=$departmentids output=$departmentvals selected=$iddepartment}
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.Phone}:</label>
                            <div class="col-sm-3">
                                <input type="text" id="phone" name="phone" class="form-control input-sm" placeholder="{$plh_program_description}" value="{$phoneVal}" >
                            </div>
                            <label class="col-sm-1 control-label">{$smarty.config.Branch}:</label>
                            <div class="col-sm-2">
                                <input type="text" id="branch" name="branch" class="form-control input-sm" placeholder="{$plh_program_description}" value="{$branchVal}" >
                            </div>
                            <label class="col-sm-1 control-label {$displayNatural}">{$smarty.config.Mobile_phone}:</label>
                            <div class="col-sm-3 {$displayNatural}">
                                <input type="text" id="mobile" name="mobile" class="form-control input-sm" placeholder="{$plh_program_description}" value="{$mobileVal}" >
                            </div>
                            <label class="col-sm-1 control-label {$displayJuridical}">Fax:</label>
                            <div class="col-sm-3 {$displayJuridical}">
                                <input type="text" id="fax" name="fax" class="form-control input-sm" placeholder="{$plh_program_description}" value="{$faxVal}" >
                            </div>
                        </div>

                        <div class="form-group {$displayNatural}">
                            <label class="col-sm-2 control-label">{$smarty.config.VIP_user}:</label>
                            <div class="col-sm-4 checkbox i-checks"><label> <input type="checkbox" name="vip" id="vip" value="1" {$checkVip}> <i></i> &nbsp;{$smarty.config.Yes}</label></div>
                            <label class="col-sm-2 control-label">{$smarty.config.Acess_level}:</label>
                            <div class="col-sm-4">
                                <select class="form-control input-sm"  id="type_user" name="type_user" data-placeholder="{$plh_module_select}" >
                                    {html_options values=$levelids output=$levelvals selected=$idlevel}
                                </select>
                            </div>
                        </div>

                        <div class="form-group {$displayNatural}">
                            <label class="col-sm-2 control-label">{$smarty.config.Permission_Groups}:</label>
                            <div class="col-sm-4">
                                <select class="form-control input-sm" multiple data-placeholder="{$smarty.config.Permission_Groups_Select}" id="permgroups" name="permgroups[]">
                                    <option value></option>
                                    {html_options values=$permgroupsids output=$permgroupsvals selected=$idpermgroups}
                                </select>
                            </div>
                            <label class="col-sm-2 control-label operatorView {$displayOperator}">{$smarty.config.Groups}:</label>
                            <div class="col-sm-4 operatorView {$displayOperator}">
                                <select class="form-control input-sm" multiple data-placeholder="{$smarty.config.Select_group}" id="persongroups" name="persongroups[]">
                                    <option value></option>
                                    {html_options values=$persongroupsids output=$persongroupsvals selected=$idpersongroups}
                                </select>
                            </div>
                        </div>

                        <div class="form-group operatorView {$displayOperator}">
                            <label class="col-sm-2 control-label">{$smarty.config.Time_value}:</label>
                            <div class="col-sm-4">
                                <input type="text" id="time_value" name="time_value" class="form-control input-sm" placeholder="{$plh_program_description}" value="" >
                            </div>
                            <label class="col-sm-2 control-label">{$smarty.config.Overtime}:</label>
                            <div class="col-sm-4">
                                <input type="text" id="overtime" name="overtime" class="form-control input-sm" placeholder="{$plh_program_description}" value="" >
                            </div>
                        </div>

                        <div class="form-group userView {$displayUser}">
                            <label class="col-sm-2 control-label">{$smarty.config.Location}:</label>
                            <div class="col-sm-4">
                                <select class="form-control input-sm"  id="location" name="location" data-placeholder="{$plh_location_select}" >
                                    {html_options values=$locationids output=$locationvals selected=$idlocation}
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <button class="btn btn-default" id="btnAddLocation" type="button" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            </div>
                        </div>

                        <div class="form-group {$displayJuridical}">
                            <label class="col-sm-2 control-label">{$smarty.config.Contact_person}:</label>
                            <div class="col-sm-7">
                                <input type="text" id="cperson" name="cperson" class="form-control input-sm" placeholder="{$plh_controller_description}" value="{$cpersonVal}" >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.Country}:</label>
                            <div class="col-sm-2">
                                <select class="form-control input-sm" id="country" name="country" data-placeholder="{$smarty.config.Select_country}">
                                    <option value="1"></option>
                                    {html_options values=$pcountryids output=$pcountryvals selected=$pidcountry}
                                </select>
                            </div>

                            <label class="col-sm-1 control-label">{$smarty.config.State}:</label>
                            <div class="col-sm-2">
                                <select class="form-control input-sm" id="state" name="state">
                                    {html_options values=$pstateids output=$pstatevals selected=$pidstate}
                                </select>
                            </div>
                            <div class="col-sm-1 ">
                                <button class="btn btn-default tooltip-buttons" id="btnAddState" type="button" data-toggle="tooltip" data-placement="top" title="{$smarty.config.tooltip_state}" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            </div>

                            <label class="col-sm-1 control-label">{$smarty.config.City}:</label>
                            <div class="col-sm-2">
                                <select class="form-control input-sm" id="city" name="city">
                                    {html_options values=$pcityids output=$pcityvals selected=$pidcity}
                                </select>
                            </div>
                            <div class="col-sm-1 ">
                                <button class="btn btn-default tooltip-buttons" id="btnAddCity" type="button" data-toggle="tooltip" data-placement="top" title="{$smarty.config.tooltip_city}" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.Neighborhood}:</label>
                            <div class="col-sm-2 ">
                                <select class="form-control input-sm" id="neighborhood" name="neighborhood" {$person_neighborhood_disabled}>
                                    {html_options values=$pneighborhoodids output=$pneighborhoodvals selected=$pidneighborhood}
                                </select>
                            </div>
                            <div class="col-sm-1 ">
                                <button class="btn btn-default tooltip-buttons" id="btnAddNeighborhood" type="button" data-toggle="tooltip" data-placement="top" title="{$smarty.config.tooltip_neighborhood}" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            </div>
                            <label class="col-sm-1 control-label">{$smarty.config.Zipcode}:</label>
                            <div class="col-sm-2">
                                <input type="text" name="zipcode" id="zipcode" class="form-control input-sm" data-mask="99999-999"  placeholder="{$plh_zipcode}" value="{$zipcodeVal}" />
                            </div>
                            <label class="col-sm-1 control-label">{$smarty.config.Type_adress}:</label>
                            <div class="col-sm-3">
                                <select class="form-control input-sm" name="type_street" id="type_street" {$person_typestreet_disabled}>
                                    {html_options values=$ptypestreetids output=$ptypestreetvals selected=$pidtypestreet}
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.Adress}:</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" id="address" name="address" data-placeholder=" ">
                                    <option value></option>
                                    {html_options values=$pstreetids output=$pstreetvals selected=$pidstreet}
                                </select>
                            </div>
                            <div class="col-sm-1 ">
                                <button class="btn btn-default tooltip-buttons" id="btnAddStreet" type="button" data-toggle="tooltip" data-placement="top" title="{$smarty.config.tooltip_street}" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            </div>
                            <label class="col-sm-1 control-label">{$smarty.config.Number}:</label>
                            <div class="col-sm-2">
                                <input type="text" id="number" name="number" class="form-control input-sm" placeholder="" value="{$numberVal}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">{$smarty.config.Complement}:</label>
                            <div class="col-sm-6">
                                <input type="text" id="complement" name="complement" class="form-control input-sm" placeholder="{$plh_logradouro}" value="{$complementVal}"/>
                            </div>
                        </div>

                        <div class="form-group {$displayJuridical}">
                            <label class="col-sm-2 control-label">{$smarty.config.Observation}:</label>
                            <div class="col-sm-9">
                                <textarea rows="6" id="observation" name="observation" class="form-control input-sm" placeholder="{$plh_motivo}">{$obsVal}</textarea>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="row wrapper  white-bg ">

                    <div class="col-sm-1 b-l">

                    </div>

                    <div class="col-sm-11 b-l">
                        <div id="alert-update-person"></div>
                    </div>
                </div>

                <div class="row wrapper  border-bottom white-bg ">
                    &nbsp;
                </div>

                <div class="col-xs-12 white-bg" style="height:10px;"></div>


                <div class="row wrapper  white-bg text-center">

                    <div class="col-sm-12 b-l">
                        <div class="form-group ">
                            <div class="col-sm-12">
                                <a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa-arrow-alt-circle-left" aria-hidden="true"></i> {$smarty.config.Back_btn} </a>
                                <button type="button" class="btn btn-primary btn-md" id="btnUpdatePerson" >
                                    <span class="fa fa-save"></span>  &nbsp;{$smarty.config.Save}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
            <!-- End form area -->
            <div class="row border-bottom white-bg ">
                <div class="footer">
                    {include file=$footer}
                </div>
            </div>
        </div>

            {include file='modals/programs/modal-alert.tpl'}
            {include file='modals/person/modal-change-password.tpl'}
            {include file='modals/person/modal-location.tpl'}
            {include file='modals/person/modal-state.tpl'}
            {include file='modals/person/modal-city.tpl'}
            {include file='modals/person/modal-neighborhood.tpl'}
            {include file='modals/person/modal-street.tpl'}
    </div>
</div>


</body>

</html>

