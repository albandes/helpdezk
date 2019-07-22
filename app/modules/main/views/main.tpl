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
    <!-- Flot -->

    <link href="/parracho/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="/parracho/css/animate.css" rel="stylesheet">


    {head_item type="css" src="$path/css/" files="$theme.css"}


    <!-- Helpdezk -->
    {head_item type="js" src="$path/includes/js/" files="default.js"}
    {head_item type="js" src="$path/includes/js/" files="flex_lang.js"}

    <!-- aqui -->

    {literal}
    <script type="text/javascript">
        var default_lang = "{/literal}{$lang}{literal}",
            path = "{/literal}{$path}{literal}",
            langName = '{/literal}{$smarty.config.Name}{literal}',
            theme = '{/literal}{$theme}{literal}',
            timesession = '{/literal}{$timesession}{literal}';
    </script>
    {/literal}

</head>

<body class="top-navigation">

<div id="wrapper">
    <div id="page-wrapper" class="gray-bg">

        <div class="row border-bottom white-bg">
            {include file=$navBar}
        </div>

        <div class="wrapper wrapper-content">
            <div class="container">
                <div class="row">

                    <div class="col-md-4">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-primary pull-right">Today</span>
                                <h5>Em atendimento</h5>
                            </div>
                            <div class="ibox-content">

                                <div class="row">
                                    <div class="col-md-6">
                                        <h1 class="no-margins">$ 406,420</h1>
                                        <div class="font-bold text-navy">44% <i class="fa fa-level-up"></i> <small>Rapid pace</small></div>
                                    </div>
                                    <div class="col-md-6">
                                        <h1 class="no-margins">206,120</h1>
                                        <div class="font-bold text-navy">22% <i class="fa fa-level-up"></i> <small>Slow pace</small></div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-success pull-right">This year</span>
                                <h5>Novas</h5>
                            </div>
                            <div class="ibox-content">
                                <h1 class="no-margins">12</h1>
                                <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div>
                                <small>Tickets</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-info pull-right">Annual</span>
                                <h5>Encerradas</h5>
                            </div>
                            <div class="ibox-content">
                                <h1 class="no-margins">80,800</h1>
                                <div class="stat-percent font-bold text-info">20% <i class="fa fa-level-up"></i></div>
                                <small>New orders</small>
                            </div>
                        </div>
                    </div>



                    <div class="col-md-4">
                        {*
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Monthly income</h5>
                                <div class="ibox-tools">
                                    <span class="label label-primary">Updated 12.2015</span>
                                </div>
                            </div>
                            <div class="ibox-content no-padding">
                                <div class="flot-chart m-t-lg" style="height: 55px;">
                                    <div class="flot-chart-content" id="flot-chart1"></div>
                                </div>
                            </div>

                        </div>*}
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-primary pull-right">Today</span>
                                <h5>Encerradas</h5>
                            </div>
                            <div class="ibox-content">

                                <div class="row">
                                    <div class="col-md-7">
                                        <h1 class="no-margins">123</h1>
                                        <div class="font-bold text-navy">44% <i class="fa fa-level-up"></i> <small>Aguardando aprovacao</small></div>
                                    </div>
                                    <div class="col-md-5">
                                        <h1 class="no-margins">324</h1>
                                        <div class="font-bold text-navy">22% <i class="fa fa-level-up"></i> <small>Aprovadas</small></div>
                                    </div>
                                </div>


                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
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
                    <div class="col-lg-4">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-warning pull-right">Data has changed</span>
                                <h5>User activity</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <small class="stats-label">Pages / Visit</small>
                                        <h4>236 321.80</h4>
                                    </div>

                                    <div class="col-xs-4">
                                        <small class="stats-label">% New Visits</small>
                                        <h4>46.11%</h4>
                                    </div>
                                    <div class="col-xs-4">
                                        <small class="stats-label">Last week</small>
                                        <h4>432.021</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <small class="stats-label">Pages / Visit</small>
                                        <h4>643 321.10</h4>
                                    </div>

                                    <div class="col-xs-4">
                                        <small class="stats-label">% New Visits</small>
                                        <h4>92.43%</h4>
                                    </div>
                                    <div class="col-xs-4">
                                        <small class="stats-label">Last week</small>
                                        <h4>564.554</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <small class="stats-label">Pages / Visit</small>
                                        <h4>436 547.20</h4>
                                    </div>

                                    <div class="col-xs-4">
                                        <small class="stats-label">% New Visits</small>
                                        <h4>150.23%</h4>
                                    </div>
                                    <div class="col-xs-4">
                                        <small class="stats-label">Last week</small>
                                        <h4>124.990</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Custom responsive table </h5>
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
                            </div>
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-sm-9 m-b-xs">
                                        <div data-toggle="buttons" class="btn-group">
                                            <label class="btn btn-sm btn-white"> <input type="radio" id="option1" name="options"> Day </label>
                                            <label class="btn btn-sm btn-white active"> <input type="radio" id="option2" name="options"> Week </label>
                                            <label class="btn btn-sm btn-white"> <input type="radio" id="option3" name="options"> Month </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="input-group"><input type="text" placeholder="Search" class="input-sm form-control"> <span class="input-group-btn">
                                        <button type="button" class="btn btn-sm btn-primary"> Go!</button> </span></div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>

                                            <th>#</th>
                                            <th>Project </th>
                                            <th>Name </th>
                                            <th>Phone </th>
                                            <th>Company </th>
                                            <th>Completed </th>
                                            <th>Task</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Project <small>This is example of project</small></td>
                                            <td>Patrick Smith</td>
                                            <td>0800 051213</td>
                                            <td>Inceptos Hymenaeos Ltd</td>
                                            <td><span class="pie">0.52/1.561</span></td>
                                            <td>20%</td>
                                            <td>Jul 14, 2013</td>
                                            <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Alpha project</td>
                                            <td>Alice Jackson</td>
                                            <td>0500 780909</td>
                                            <td>Nec Euismod In Company</td>
                                            <td><span class="pie">6,9</span></td>
                                            <td>40%</td>
                                            <td>Jul 16, 2013</td>
                                            <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Betha project</td>
                                            <td>John Smith</td>
                                            <td>0800 1111</td>
                                            <td>Erat Volutpat</td>
                                            <td><span class="pie">3,1</span></td>
                                            <td>75%</td>
                                            <td>Jul 18, 2013</td>
                                            <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Gamma project</td>
                                            <td>Anna Jordan</td>
                                            <td>(016977) 0648</td>
                                            <td>Tellus Ltd</td>
                                            <td><span class="pie">4,9</span></td>
                                            <td>18%</td>
                                            <td>Jul 22, 2013</td>
                                            <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Alpha project</td>
                                            <td>Alice Jackson</td>
                                            <td>0500 780909</td>
                                            <td>Nec Euismod In Company</td>
                                            <td><span class="pie">6,9</span></td>
                                            <td>40%</td>
                                            <td>Jul 16, 2013</td>
                                            <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>Project <small>This is example of project</small></td>
                                            <td>Patrick Smith</td>
                                            <td>0800 051213</td>
                                            <td>Inceptos Hymenaeos Ltd</td>
                                            <td><span class="pie">0.52/1.561</span></td>
                                            <td>20%</td>
                                            <td>Jul 14, 2013</td>
                                            <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Gamma project</td>
                                            <td>Anna Jordan</td>
                                            <td>(016977) 0648</td>
                                            <td>Tellus Ltd</td>
                                            <td><span class="pie">4,9</span></td>
                                            <td>18%</td>
                                            <td>Jul 22, 2013</td>
                                            <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>Project <small>This is example of project</small></td>
                                            <td>Patrick Smith</td>
                                            <td>0800 051213</td>
                                            <td>Inceptos Hymenaeos Ltd</td>
                                            <td><span class="pie">0.52/1.561</span></td>
                                            <td>20%</td>
                                            <td>Jul 14, 2013</td>
                                            <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Alpha project</td>
                                            <td>Alice Jackson</td>
                                            <td>0500 780909</td>
                                            <td>Nec Euismod In Company</td>
                                            <td><span class="pie">6,9</span></td>
                                            <td>40%</td>
                                            <td>Jul 16, 2013</td>
                                            <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Betha project</td>
                                            <td>John Smith</td>
                                            <td>0800 1111</td>
                                            <td>Erat Volutpat</td>
                                            <td><span class="pie">3,1</span></td>
                                            <td>75%</td>
                                            <td>Jul 18, 2013</td>
                                            <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Gamma project</td>
                                            <td>Anna Jordan</td>
                                            <td>(016977) 0648</td>
                                            <td>Tellus Ltd</td>
                                            <td><span class="pie">4,9</span></td>
                                            <td>18%</td>
                                            <td>Jul 22, 2013</td>
                                            <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Alpha project</td>
                                            <td>Alice Jackson</td>
                                            <td>0500 780909</td>
                                            <td>Nec Euismod In Company</td>
                                            <td><span class="pie">6,9</span></td>
                                            <td>40%</td>
                                            <td>Jul 16, 2013</td>
                                            <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>Project <small>This is example of project</small></td>
                                            <td>Patrick Smith</td>
                                            <td>0800 051213</td>
                                            <td>Inceptos Hymenaeos Ltd</td>
                                            <td><span class="pie">0.52/1.561</span></td>
                                            <td>20%</td>
                                            <td>Jul 14, 2013</td>
                                            <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Gamma project</td>
                                            <td>Anna Jordan</td>
                                            <td>(016977) 0648</td>
                                            <td>Tellus Ltd</td>
                                            <td><span class="pie">4,9</span></td>
                                            <td>18%</td>
                                            <td>Jul 22, 2013</td>
                                            <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
        <div class="footer">
            <div class="pull-right">
                10GB of <strong>250GB</strong> Free.
            </div>
            <div>
                <strong>Copyright</strong> Example Company &copy; 2014-2015
            </div>
        </div>

    </div>
</div>

