<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
if ($url == "http://demo.helpdezk.org/installer/"){
    die("Installer don't work in $url") ;
}

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

    <title>Helpdezk | Installer</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">


    <script src="js/jquery-3.4.1.min.js"  type="text/javascript"></script>
    <script src="js/bootstrap.js"  type="text/javascript"></script>

    <script src="js/installer.js" type="text/javascript"></script>
    <script src="js/jquery.validate.min.js" type="text/javascript"></script>

    <script   type="text/javascript">


    </script>


</head>




<body>

<div  >

    <div  class="gray-bg">



        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10 ">
                <img alt="image" class="img-responsive" src="images/header.png" width="15%" height="15%">
            </div>
            <div class="col-lg-10">
                <h2>Helpdezk Installer</h2>
                <ol class="breadcrumb">
                    <li>
                        This wizard will guide you through the installation process.
                    </li>
                </ol>
            </div>
            <div class="col-lg-2">
            </div>
        </div>


        <div class="wrapper wrapper-content">
            <div class="row ">
                <div class="col-md-4">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Steps</h5>
                        </div>
                        <div>
                            <div class="ibox-content no-padding border-left-right">

                            </div>

                            <div class="ibox-content no-padding border-left-right">

                            </div>
                            <div class="ibox-content profile-content">


                                <h4><div id="etapa1" class="fa fa-square"></div>&nbsp; Select your language</h4></p>
                                <h4><div id="etapa2" class="fa fa-square"></div>&nbsp; Server requirements</h4></p>
                                <h4><div id="etapa3" class="fa fa-square"></div>&nbsp; Website URL</h4></p>
                                <h4><div id="etapa4" class="fa fa-square"></div>&nbsp; Database settings</h4></p>
                                <h4><div id="etapa5" class="fa fa-square"></div>&nbsp; Administrator account</h4></p>
                                <h4><div id="etapa6" class="fa fa-square"></div>&nbsp; Ready to install</h4></p>
                                <h4><div id="etapa7" class="fa fa-square"></div>&nbsp; Installing</h4></p>

                            </div>
                        </div>
                    </div>
                </div>


                <div id=content class="col-md-8">



                </div>

            </div>
        </div>

        <div class="footer">
            <div class="pull-right">
                <strong>Community version</strong>.
            </div>
            <div>
                <strong>Copyright</strong> Example Company &copy; 2014-2015
            </div>
        </div>


    </div>
</div>








</body>

</html>
