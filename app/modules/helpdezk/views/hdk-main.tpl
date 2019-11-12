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


    <!-- Custom and plugin javascript -->
    {head_item type="js"  src="$path/js/" files="inspinia.js"}
    {head_item type="js"  src="$path/js/plugins/pace/" files="pace.min.js"}

    {*head_item type="css" src="$path/css/font-awesome/css/" files="font-awesome.css"*}
    {head_item type="css" src="$path/css/font-awesome-5.9.0/css/" files="all.css"}
    {head_item type="css" src="$path/css/" files="animate.css"}

    {head_item type="css" src="$path/css/gn-menu/css/" files="component.css"}
    {head_item type="js"  src="$path/includes/js/plugins/gnmenu/" files="classie.js"}
    {head_item type="js"  src="$path/includes/js/plugins/gnmenu/" files="gnmenu.js"}

    {head_item type="js"  src="$path/includes/js/plugins/modernizr/" files="modernizr.custom.js"}

    <!-- Helpdezk -->
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/app/modules/helpdezk/views/js/" files="main.js"}

    <!-- Data Tables -->
    {head_item type="js" src="$path/includes/js/plugins/dataTables/" files="jquery.dataTables.js"}
    {head_item type="js" src="$path/includes/js/plugins/dataTables/" files="dataTables.bootstrap.js"}
    {head_item type="js" src="$path/includes/js/plugins/dataTables/" files="dataTables.responsive.js"}
    {head_item type="js" src="$path/includes/js/plugins/dataTables/" files="dataTables.tableTools.min.js"}
    {head_item type="css" src="$path/css/plugins/dataTables/" files="dataTables.bootstrap.css"}
    {head_item type="css" src="$path/css/plugins/dataTables/" files="dataTables.responsive.css"}
    {head_item type="css" src="$path/css/plugins/dataTables/" files="dataTables.tableTools.min.css"}

    <!-- Input Mask-->
    {head_item type="js" src="$path/includes/js/plugins/jquery-mask/" files="jquery.mask.min.js"}

    <!-- Autocomplete -->
    {head_item type="js" src="$path/includes/js/plugins/autocomplete/" files="jquery.autocomplete.js"}
    {head_item type="css" src="$path/includes/js/plugins/autocomplete/" files="jquery.autocomplete.css"}

    <!-- Combo Autocomplete -->
    {head_item type="js" src="$path/includes/js/plugins/chosen/" files="chosen.jquery.js"}
    {head_item type="css" src="$path/css/plugins/chosen/" files="chosen.css"}

    <!-- Jquery Validate -->
    {head_item type="js"  src="$path/includes/js/plugins/validate/" files="jquery.validate.min.js"}

    <!-- Personalized style -->
    {head_item type="css" src="$path/css/" files="$theme.css"}
    <!-- Last to be included therefore overwrite others css -->

    <!-- Dropzone  -->
    {head_item type="js"  src="$path/includes/js/plugins/dropzone/" files="dropzone.js"}
    {head_item type="css" src="$path/css/plugins/dropzone/" files="basic.css"}
    {head_item type="css" src="$path/css/plugins/dropzone/" files="pipe.dropzone.css"}

    {literal}

        <script type="text/javascript">
            var default_lang = "{/literal}{$lang}{literal}",
                path         = "{/literal}{$path}{literal}",
                langName     = '{/literal}{$smarty.config.Name}{literal}',
                theme        = '{/literal}{$theme}{literal}',
                timesession  = '{/literal}{$timesession}{literal}',
                id_mask      = '{/literal}{$id_mask}{literal}',
                ein_mask     = '{/literal}{$ein_mask}{literal}',
                zip_mask     = '{/literal}{$zip_mask}{literal}',
                phone_mask     = '{/literal}{$phone_mask}{literal}',
                cellphone_mask     = '{/literal}{$cellphone_mask}{literal}',
                typeuser = '{/literal}{$typeuser}{literal}';
        </script>

        <style>

            .panel-footer{
                background-color:#fff;
                border-color: #E7EAEC;
                color: #000;
            }


            body.DTTT_Print {
                background: #fff;

            }
            .DTTT_Print #page-wrapper {
                margin: 0;
                background:#fff;
            }

            button.DTTT_button, div.DTTT_button, a.DTTT_button {
                border: 1px solid #e7eaec;
                background: #fff;
                color: #676a6c;
                box-shadow: none;
                padding: 6px 8px;
            }
            button.DTTT_button:hover, div.DTTT_button:hover, a.DTTT_button:hover {
                border: 1px solid #d2d2d2;
                background: #fff;
                color: #676a6c;
                box-shadow: none;
                padding: 6px 8px;
            }

            .dataTables_filter label {
                margin-right: 5px;

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

            <div class="wrapper wrapper-content">
                <!--<div class="container">-->


                    {*
                    <div class="row">

                        <div class="col-md-2">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <span class="label label-success pull-right">Today</span>
                                    <h5>Total</h5>
                                </div>
                                <div class="ibox-content">
                                    <h1 class="no-margins">{$total_requests}</h1>
                                    *}{*<div class="stat-percent font-bold text-success">{$newrequests_percent}% <i class="fa fa-bolt"></i></div>*}{*
                                    <small>Tickets</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <span class="label label-primary pull-right">Today</span>
                                    <h5>Tickets ativos</h5>
                                </div>
                                <div class="ibox-content">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h1 class="no-margins">{$inprogress_requests}</h1>
                                            <div class="font-bold text-navy">{$inprogress_requests_percent}% <i class="fa "></i> <small>Em atendimento</small></div>
                                        </div>
                                        <div class="col-md-6">
                                            <h1 class="no-margins">{$waiting_service_requests}</h1>
                                            <div class="font-bold text-navy">{$waiting_service_requests_percent}% <i class="fa "></i> <small>Aguardando </small></div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>



                        <div class="col-md-2">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <span class="label label-success pull-right">Today</span>
                                    <h5>Fechados</h5>
                                </div>
                                <div class="ibox-content">
                                    <h1 class="no-margins">{$attended_requests}</h1>
                                    <div class="stat-percent font-bold text-success">{$attended_requests_percent}% <i class="fa fa-bolt"></i></div>
                                    <small>Tickets</small>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <span class="label label-primary pull-right">Today</span>
                                    <h5>Status dos fechados</h5>
                                </div>
                                <div class="ibox-content">

                                    <div class="row">
                                        <div class="col-md-7">
                                            <h1 class="no-margins">{$waiting_aprovall_requests}</h1>
                                            <div class="font-bold text-navy">{$waiting_aprovall_requests_percent}% <i class="fa "></i> <small>Aguardando aprovacao</small></div>
                                        </div>
                                        <div class="col-md-5">
                                            <h1 class="no-margins">{$approved_requests}</h1>
                                            <div class="font-bold text-navy">{$approved_requests_percent}% <i class="fa "></i> <small>Finalizadas</small></div>
                                        </div>
                                    </div>


                                </div>
                            </div>

                        </div>
                    </div>
                    *}


                    <!-- -->

                    <div class="row">

                        <div class="col-lg-12">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <h5>{$smarty.config.Tck_Open}</h5>
                                    <!--
                                    <div class="ibox-tools">
                                        <a class="collapse-link">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>

                                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                            <i class="fa fa-wrench"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-user">
                                            <li><a href="#">Config option 1</a>
                                            </li>
                                            <li><a href="#">Config option 2</a>
                                            </li>
                                        </ul>
                                        <a class="close-link">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </div>
                                    -->
                                </div>
                                <div class="ibox-content">

                                    <table id="dash_table" class="table table-striped table-bordered table-hover dataTables-example" >
                                        <thead>
                                        <tr>
                                            <th class="text-center">Assunto</th>
                                            <th class="text-center">Prazo Final</th>
                                            <th class="text-center">Termina em</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">No</th>

                                        </tr>
                                        </thead>
                                        <!-- body -->
                                        <tbody>
                                            {foreach from=$mylist key=k item=i}
                                                <tr class="gradeX">
                                                    <td>{$i.subject}</td>
                                                    <td data-order="{$i.ts_expire}" class="text-center">{$i.expire_date}</td>
                                                    <td data-order="{$i.ts_expire}">{$i.seconds}    </td>
                                                    <td class="text-center">{$i.status} </td>
                                                    <td data-order="{$i.code_request}" class="text-center">{$i.code_request_fmt}</td>
                                                </tr>
                                            {/foreach}
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th class="text-center">Assunto</th>
                                                <th class="text-center">Prazo Final</th>
                                                <th class="text-center">Termina em</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">No</th>

                                            </tr>
                                        </tfoot>
                                    </table>

                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- -->

                    <div class="row">
                        <!-- messages -->

                        <div class="col-lg-7">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <h5>{$smarty.config.Messages}</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                        <a class="close-link">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content ibox-heading">
                                    <h3><i class="fa fa-envelope-o"></i> {$smarty.config.New_messages}</h3>
                                    <small><i class="fa fa-tim"></i>{$num_messages}</small>
                                </div>
                                <div class="ibox-content">
                                    <div class="feed-activity-list">

                                        {foreach from=$messages key=k item=i}
                                            <div class="feed-element">
                                                <div>
                                                    <small class="pull-right text-navy">{$i.elapsed}</small>
                                                    <strong class="pull-left text-navy">{$i.code_request}</strong> &nbsp; - &nbsp;<strong>{$i.sender}</strong>
                                                    <div>&nbsp;</div>
                                                    <div>{$i.text}</div>
                                                    <small class="text-muted">{$i.datetime}</small>
                                                </div>
                                            </div>
                                        {/foreach}



                                    </div>
                                </div>
                            </div>
                        </div>


                        {*
                        <div class="col-lg-7">
                            <div class="ibox float-e-margins">
                                <div class="ibox-content">
                                    <div>
                                            <span class="pull-right text-right">
                                            <small>Average value of sales in the past month in: <strong>United states</strong></small>
                                                <br/>
                                                All sales: 162,862
                                            </span>
                                        <h3 class="font-bold no-margins">
                                            Half-year revenue margin
                                        </h3>
                                        <small>Sales marketing.</small>
                                    </div>

                                    <div class="m-t-sm">

                                        <div class="row">
                                            <div class="col-md-8">
                                                <div>
                                                    <canvas id="lineChart" height="114"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <ul class="stat-list m-t-lg">
                                                    <li>
                                                        <h2 class="no-margins">2,346</h2>
                                                        <small>Total orders in period</small>
                                                        <div class="progress progress-mini">
                                                            <div class="progress-bar" style="width: 48%;"></div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <h2 class="no-margins ">4,422</h2>
                                                        <small>Orders in last month</small>
                                                        <div class="progress progress-mini">
                                                            <div class="progress-bar" style="width: 60%;"></div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="m-t-md">
                                        <small class="pull-right">
                                            <i class="fa fa-clock-o"> </i>
                                            Update on 16.07.2015
                                        </small>
                                        <small>
                                            <strong>Analysis of sales:</strong> The value has been changed over time, and last month reached a level over $50,000.
                                        </small>
                                    </div>

                                </div>
                            </div>
                        </div>

                        *}
                        <!-- User data -->
                        <div class="col-lg-5">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <h5>{$smarty.config.UserData}</h5>
                                </div>

                                <div class="contact-box">
                                        <div class="col-sm-4">
                                            <div class="text-center">
                                                <img alt="image" class="img-circle m-t-xs img-responsive" src="{$person_photo}">
                                                <div class="m-t-xs font-bold">{$person_department}</div>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <h3><strong>{$user_name}</strong></h3>
                                            <p></p><strong>{$user_company}</strong><br></p>
                                            <i class="fa fa-map-marker"></i> {$user_typestreet} {$user_street} {$user_number}<br>
                                                {if !empty($user_complement)}
                                                    {$user_complement} <br>
                                                {/if}
                                                {$user_city}, {$user_state} {$user_zip}<br>
                                                {$smarty.config.Phone}: {$user_phone} <br>
                                                {$smarty.config.Mobile_phone}: {$user_cellphone}
                                            </address>
                                        </div>
                                        <div class="clearfix"></div>
                                    {*<div class="text-right">
                                        <button type="button" class="btn btn-primary" id="btnSend" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Send}</button>
                                    </div>*}
                                    <div class="panel-footer clearfix">
                                        <div class="pull-right">
                                            <button type="button" class="btn btn-primary" id="btnUpdateUserData">
                                                {$smarty.config.btn_update_userdata}
                                            </button>
                                        </div>
                                    </div>




                                </div>




                            </div>
                        </div>

                    </div>



                <!--</div>-->

            </div>


            <div class="footer">
                {include file=$footer}
            </div>
        </div>
    </div>

    {include file='modals/main/modalPersonData.tpl'}
    {include file='modals/main/modal-approve-require.tpl'}
</body>