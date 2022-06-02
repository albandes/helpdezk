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
    {head_item type="js" src="$path/app/modules/lmm/views/js/" files="lmmtitles-create.js"}
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

            <div class="wrapper wrapper-content ">
                <div class="row wrapper white-bg ibox-title">
                    <div class="col-sm-8">
                        <h4>{$smarty.config.cat_records} / {$smarty.config.pgr_titles}  / <strong>{$smarty.config.Add}</strong></h4>
                    </div>
                    <div class="col-sm-4 text-right"></div>
                </div>

                <div class="row wrapper  border-bottom white-bg "></div>

                <!-- First Line -->

                <div class="col-xs-12 white-bg" style="height:10px;"></div>

                <!-- Form area -->
                <form method="get" class="form-horizontal" id="create-titles-form">

                    <!-- Hidden -->
                    <input type="hidden" name="_token" id= "_token" value="{$token}">        
                
                
                    <div class="row wrapper white-bg ">
                        <div class="col-sm-1 b-l"></div>
                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Material_Type}:</label>
                                <div class="col-sm-3">
                                    <select class="form-control input-sm"  id="materialtype" name="materialtype" data-placeholder="{$plh_materialtype}" >
                                        <option value="">{$smarty.config.selected} </option>
                                        {html_options values=$materialtypeids output=$materialtypevals selected=$idmaterialtype}
                                    </select>
                                </div>
                                <div class="col-sm-1 ">
                                    <button class="btn btn-default" id="btnAddMaterialtype" type="button" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>                                     

                    </div>

                    <div class="row wrapper white-bg ">
                        <div class="col-sm-1 b-l"></div>
                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Collection}:</label>
                                <div class="col-sm-1">                                       
                                    <p>
                                        <input type="radio" id="sim" name="col"  value="Y"/>Sim  
                                    </p>
                                </div> 
                                <div class="col-sm-1">                                       
                                    <p>                                                                                     
                                        <input type="radio" id="nao" name="col" checked=checked value="N"/>Não
                                    </p>
                                </div>                                     
                            </div>
                        </div>                                     

                    </div>

                    <div id="Collectionline" class="row wrapper white-bg hide">

                        <div class="col-sm-1 b-l"></div>
                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Collection}:</label>
                                <div class="col-sm-3">
                                    <select class="form-control input-sm"  id="Collection" name="Collection" data-placeholder="{$plh_Collection}" >
                                        <option value="0">{$smarty.config.selected} </option>
                                        {html_options values=$Collectionids output=$Collectionvals selected=$idCollection}
                                    </select>
                                </div>
                                <div class="col-sm-1 ">
                                    <button class="btn btn-default" id="btnAddCollection" type="button" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>                                     

                    </div>

                    
                    <div class="row wrapper white-bg ">

                        <div class="col-sm-1 b-l"></div>
                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Title}:</label>
                                <div class="col-sm-3">
                                    <input type="text" id="titles" name="titles" class="form-control input-sm" placeholder="{$plh_titles}" >
                                </div>
                            </div>
                        </div>
                        

                    </div>

                    <div class="row wrapper white-bg ">
                        <div class="col-sm-1 b-l"></div>
                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Cutter}:</label>
                                <div class="col-sm-2">
                                    <input type="text" id="Cutter" name="Cutter" class="form-control input-sm" placeholder="{$plh_Cutter}" >
                                </div>
                                <label class="col-sm-1 control-label">{$smarty.config.ISBN}:</label>
                                <div class="col-sm-2">
                                    <input type="text" id="ISBN" name="ISBN" class="form-control input-sm" placeholder="{$plh_ISBN}" >
                                </div>                                    
                            </div>
                        </div>                                     

                    </div>

                    <div class="row wrapper white-bg ">
                        <div class="col-sm-1 b-l"></div>

                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.ISSN}:</label>
                                <div class="col-sm-2">
                                    <input type="text" id="ISSN" name="ISSN" class="form-control input-sm" placeholder="{$plh_ISSN}" >
                                </div> 
                                <label class="col-sm-1 control-label">{$smarty.config.CDU}:</label>
                                <div class="col-sm-2">
                                    <input type="text" id="CDU" name="CDU" class="form-control input-sm" placeholder="{$plh_CDU}" >
                                </div> 
                            </div>                                   
                        </div>                                                         

                    </div>

                    <div class="row wrapper white-bg ">
                        <div class="col-sm-1 b-l"></div>
                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.CDD}:</label>
                                <div class="col-sm-3">
                                    <select class="form-control input-sm"  id="CDD" name="CDD" data-placeholder="{$plh_CDD}" >
                                        <option value="">{$smarty.config.selected} </option>
                                        {html_options values=$CDDids output=$CDDvals selected=$idCDD}
                                    </select>
                                </div>
                                <div class="col-sm-1 ">
                                    <button class="btn btn-default" id="btnAddCDD" type="button" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>                                     

                    </div>           
                        
                    <div class="row wrapper white-bg ">
                        <div class="col-sm-1 b-l"></div>
                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Publishing_company}:</label>
                                <div class="col-sm-3">
                                    <select class="form-control input-sm"  id="edit" name="edit" data-placeholder="{$plh_edit}" >
                                        <option value="">{$smarty.config.selected} </option>
                                        {html_options values=$editids output=$editvals selected=$idedit}
                                    </select>
                                </div>
                                <div class="col-sm-1 ">
                                    <button class="btn btn-default" id="btnAddPublishing_company" type="button" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>                                     

                    </div>                 

                   
                    <div class="row wrapper white-bg ">
                        <div class="col-sm-1 b-l"></div>
                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Color}:</label>
                                <div class="col-sm-3">
                                    <select class="form-control input-sm"  id="Color" name="Color" data-placeholder="{$plh_color}" >
                                        <option value="">{$smarty.config.selected} </option>
                                        {html_options values=$colorids output=$colorvals selected=$idcolor}
                                    </select>
                                </div>
                                <div class="col-sm-1 ">
                                    <button class="btn btn-default" id="btnAddColor" type="button" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>                                     

                    </div>

                    <div class="row wrapper white-bg ">
                        <div class="col-sm-1 b-l"></div>
                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Classification}:</label>
                                <div class="col-sm-3">
                                    <select class="form-control input-sm"  id="classif" name="classif" data-placeholder="{$plh_classif}" >
                                        <option value="">{$smarty.config.selected} </option>
                                        {html_options values=$classifids output=$classifvals selected=$idclassif}
                                    </select>
                                </div>
                                <div class="col-sm-1 ">
                                    <button class="btn btn-default" id="btnAddClassification" type="button" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>                                     
                    </div>


                    <div class="row wrapper white-bg">
                        <div class="col-sm-1 b-l"></div>
                        <div class="col-sm-11 b-l">
                            <div class="panel blank-panel">

                                <div class="panel-heading">
                                    <div class="panel-title m-b-md"><h4></h4></div>
                                    <div class="panel-options">
                                        <ul class="nav nav-tabs">
                                            <li class="active"><a data-toggle="tab" href="#tab-1">Exemplar</a></li>
                                            <li class=""><a data-toggle="tab" href="#tab-2">Autores</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="panel-body">

                                    <div class="tab-content">
                                        <div id="tab-1" class="tab-pane active">                                        

                                            <div class="wrapper-content">

                                                <div class="exemplaryform" id="detailsTr1">
                                                    <input type="hidden" id="numId" value="1"/>
                                                    <div class="row wrapper white-bg ">

                                                        <div class="col-sm-1 b-l"></div>
                                                        <div class="col-sm-11 b-l">
                                                            <div class="form-group">
                                                                <label class="col-sm-2 text-right control-label">{$smarty.config.Exemplary}:</label>
                                                                <div class="col-sm-3">
                                                                    <input type="text" id="Exemplary1" name="Exemplary[]" class="form-control input-sm" placeholder="{$plh_Exemplary}" value="1" readonly>
                                                                </div>
                                                                </td>
                                                                    <td class="text-center"><a href="javascript:;" id="btndelbook1" onclick="removeRow(this,'tab-1','add')" class="btn btn-danger bt-xs lbltooltip" data-toggle="tooltip" data-placement="right" title="{$smarty.config.Delete_exemplary}"><i class="fa fa-times"></i></a></td>
                                                                </tr>                                                              
                                                                
                                                            </div>
                                                        </div>                  
                                                    </div>

                                                    <div class="row wrapper white-bg ">

                                                        <div class="col-sm-1 b-l"></div>

                                                        <div class="col-sm-11 b-l">
                                                            <div class="form-group">
                                                                <label class="col-sm-2 text-right control-label">{$smarty.config.Library}:</label>
                                                                    <div class="col-sm-3">
                                                                        <select class="form-control input-sm"  id="Library1" name="Library[]" data-placeholder="{$plh_Library}" >
                                                                            <option value="">{$smarty.config.selected} </option>
                                                                            {html_options values=$Libraryids output=$Libraryvals selected=$idLibrary}
                                                                        </select>
                                                                    </div>                                                           
                                                            </div>
                                                        </div>                                     
                                                    </div>

                                                    <div class="row wrapper white-bg ">
                                                        <div class="col-sm-1 b-l"></div>
                                                        <div class="col-sm-11 b-l">
                                                            <div class="form-group">
                                                                <label class="col-sm-2 text-right control-label">{$smarty.config.Acquisition_date}:</label>
                                                                <div class="col-sm-3">
                                                                    <input type="date" id="aquis1" name="aquis[]" class="form-control input-sm" placeholder="{$plh_aquis}" >
                                                                </div>
                                                            </div>
                                                        </div>                   

                                                    </div>

                                                    <div class="row wrapper white-bg ">
                                                        <div class="col-sm-1 b-l"></div>
                                                        <div class="col-sm-11 b-l">
                                                            <div class="form-group">
                                                                <label class="col-sm-2 text-right control-label">{$smarty.config.Origin}:</label>
                                                                    <div class="col-sm-3">
                                                                        <select class="form-control input-sm"  id="origin1" name="origin[]" data-placeholder="{$plh_origin}" >
                                                                            <option value="">{$smarty.config.selected} </option>
                                                                            {html_options values=$originids output=$originvals selected=$idorigin}
                                                                        </select>
                                                                    </div>                                                           
                                                            </div>
                                                        </div>                                     
                                                    </div>

                                                    <div class="row wrapper white-bg ">
                                                        <div class="col-sm-1 b-l"></div>
                                                        <div class="col-sm-11 b-l">
                                                            <div class="form-group">
                                                                <label class="col-sm-2 text-right control-label">{$smarty.config.Volume}:</label>
                                                                <div class="col-sm-3">
                                                                    <input type="text" id="Volume1" name="Volume[]" class="form-control input-sm" placeholder="{$plh_Volume}" >
                                                                </div>
                                                            </div>
                                                        </div>                 
                                                    </div>

                                                    <div class="row wrapper white-bg ">
                                                        <div class="col-sm-1 b-l"></div>
                                                        <div class="col-sm-11 b-l">
                                                            <div class="form-group">
                                                                <label class="col-sm-2 text-right control-label">{$smarty.config.Edition}:</label>
                                                                <div class="col-sm-3">
                                                                    <input type="text" id="Edition1" name="Edition[]" class="form-control input-sm" placeholder="{$plh_Edition}" >
                                                                </div>
                                                            </div>
                                                        </div>                   

                                                    </div>

                                                    <div class="row wrapper white-bg ">
                                                        <div class="col-sm-1 b-l"></div>
                                                        <div class="col-sm-11 b-l">
                                                            <div class="form-group">
                                                                <label class="col-sm-2 text-right control-label">{$smarty.config.Year}:</label>
                                                                <div class="col-sm-3">
                                                                    <input type="text" id="Year1" name="Year[]" class="form-control input-sm ExemplaryYear" placeholder="{$plh_Year}" >
                                                                </div>
                                                            </div>
                                                        </div>                   

                                                    </div>

                                                    <div class="row wrapper white-bg ">
                                                        <div class="col-sm-1 b-l"></div>
                                                        <div class="col-sm-11 b-l">
                                                            <div class="form-group">
                                                                <label class="col-sm-2 text-right control-label">{$smarty.config.Comes_with_CD}:</label>
                                                                <div class="col-sm-1">                                       
                                                                    <p>
                                                                        <input type="radio" id="sim1" name="hascd[1]" value="Y"/>Sim                                        
                                                                
                                                                    </p>
                                                                </div> 
                                                                <div class="col-sm-1">                                       
                                                                    <p>                                                                                     
                                                                        <input type="radio" id="nao1" name="hascd[1]" checked=checked  value="N"/>Não
                                                                    </p>
                                                                </div>                                     
                                                            </div>
                                                        </div>                                     

                                                    </div>

                                                    <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>
                                                </div>

                                                <div class="col-xs-12 white-bg" style="height:10px;"></div>

                                                <div class="row wrapper  white-bg text-left">
                                                    <div class="col-sm-11 b-l">
                                                        <div class="form-group">
                                                            <div class="col-sm-12">  
                                                                <button id="btnAdd_exemplary" type="button" class="btn btn-primary btn-md"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;{$smarty.config.Add_exemplary}</button>                                                 
                                                                
                                                            </div>                                                           
                                                        </div>
                                                    </div>
                                                </div>                                               
                                                
                                               
                                            </div>
                                                                                   
                                        </div>

                                
                                        <div id="tab-2" class="tab-pane">

                                            <div class="authorform" id="detailsAuthor1">
                                                <input type="hidden" id="nummId" value="1"/>                                                

                                                <div class="row wrapper white-bg ">
                                                    <div class="col-sm-1 b-l"></div>
                                                    <div class="col-sm-11 b-l">
                                                        <div class="form-group">
                                                            <label class="col-sm-2 text-right control-label">{$smarty.config.Author}:</label>
                                                            <div class="col-sm-3">
                                                                <input type="text" id="Author1" name="Author[]" class="form-control input-sm" placeholder="{$plh_Author}" value="1" readonly>
                                                            </div>
                                                            </td>
                                                                <td class="text-center"><a href="javascript:;" id="btndelauthor1" onclick="delRow(this,'tab-2','add')" class="btn btn-danger bt-xs lbltooltip" data-toggle="tooltip" data-placement="right" title="{$smarty.config.Delete_author}"><i class="fa fa-times"></i></a></td>
                                                            </tr>                                                            
                                                        </div>
                                                    </div>                  
                                                </div>

                                                <div class="row wrapper white-bg ">
                                                    <div class="col-sm-1 b-l"></div>                                                
                                                    <div class="col-sm-11 b-l">
                                                        <div class="form-group">                                                                                                               
                                                            <label class="col-sm-1 control-label">{$smarty.config.Author}:</label>
                                                            <div class="col-sm-4">
                                                                <select class="form-control input-sm"  id="tabAuthor1" name="tabAuthor[]" data-placeholder="{$plh_Author}"onchange="loadcutter(this)">
                                                                    <option value="">{$smarty.config.selected} </option>
                                                                    {html_options values=$Authorids output=$Authorvals selected=$idAuthor}
                                                                </select>
                                                            </div>
                                                            <div class="form-group">                                                         
                                                                <label class="col-sm-1 control-label">{$smarty.config.Cutter}:</label>
                                                                <div class="col-sm-3">
                                                                    <input type="text" id="tabcutter1" name="tabcutter[]" class="form-control input-sm" placeholder="{$plh_tabcutter}" readonly>    
                                                                </div>                                                                 
                                                                <div class="col-sm-1 ">
                                                                    <button class="btn btn-default" id="btnAddAuthor" type="button" onclick="addauthor(this)" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                                                </div>                                                                                                                 
                                                            </div>
                                                        </div>                                     
                                                    </div> 
                                                </div>                                         

                                                <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>
                                            </div>
                                            <div class="col-xs-12 white-bg" style="height:10px;"></div>

                                            <div class="row wrapper  white-bg text-left">
                                                <div class="col-sm-11 b-l">
                                                    <div class="form-group">
                                                        <div class="col-sm-12">  
                                                            <button id="btnAdd_author" type="button" class="btn btn-primary btn-md"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;{$smarty.config.Add_author}</button>                                                 
                                                            
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>                                    
                                    
                                            
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="col-xs-12 white-bg" style="height:10px;"></div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-11 b-l">
                            <div id="alert-titles-create"></div>
                        </div>
                    </div>

                    <div class="row wrapper  border-bottom white-bg ">&nbsp;</div>

                    <div class="col-xs-12 white-bg" style="height:10px;"></div>

                    <div class="row wrapper  white-bg text-center">
                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <a href="" id="btnCancel" class="btn btn-white btn-md" role="button"><i class="fa fa-arrow-alt-circle-left" aria-hidden="true"></i> {$smarty.config.Back_btn} </a>
                                    <button type="button" class="btn btn-primary btn-md " id="btnSave" >
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
    </div>       

   
    {include file='modals/main/modal-alert.tpl'}
    {include file='modals/titles/modal-publishing.tpl'}
    {include file='modals/titles/modal-color.tpl'}
    {include file='modals/titles/modal-classification.tpl'}
    {include file='modals/titles/modal-materialtype.tpl'}
    {include file='modals/titles/modal-collection.tpl'}
    {include file='modals/titles/modal-author.tpl'}
    {include file='modals/titles/modal-cdd.tpl'}

</body>


</html>


