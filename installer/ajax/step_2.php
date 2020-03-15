<?php
/**
 * Date: 01/07/2019
 * Time: 16:47
 */
error_reporting(E_ERROR | E_PARSE);

$install_dir	    = "installer/";
$upload_dir 		= "app/uploads/";
$smartycache_dir 	= "system/templates_c/";
$log_dir			= "logs/";
$tmp_dir            = "app/tmp";
$config_file		= "includes/config/config.php" ;

if ( !isset($_POST['i18n']) )
    die('Language not set.');
else
    $lang = $_POST['i18n'];


include ("../lang/". $_POST['i18n'] . ".php" ."");

if (DIRECTORY_SEPARATOR=='/')
    $absolute_path = dirname(__FILE__).'/';
else
    $absolute_path = str_replace('\\', '/', dirname(__FILE__)).'/';


if (strnatcmp(phpversion(),'7.0.0') >= 0)
{
    $statPHP = phpversion() . '&nbsp; - ' . OK ;
    $linePHP = '<span class="label label-primary">1</span> PHP Version' ;
}
else
{
    $statPHP = phpversion() . '&nbsp; - ' . OK ;
    $linePHP = '<span class="label label-primary">1</span> PHP Version' ;
}

if (ini_get('file_uploads')) {
    $statUpl =  OK ;
    $lineUpl = '<span class="label label-primary">2</span> file_upload (php.ini)' ;
} else {
    $statUpl = NOT_OK ;
    $lineUpl = '<span class="label label-danger">2</span> file_upload (php.ini)' ;
}

if (ini_get('upload_max_filesize')) {
    $statMax =  ini_get('upload_max_filesize') . '&nbsp;- ' .OK ;
    $lineMax = '<span class="label label-primary">3</span> upload_max_filesize (php.ini)' ;
} else {
    $statMax = NOT_OK ;
    $lineMax = '<span class="label label-danger">3</span> upload_max_filesize (php.ini)' ;
}

if (function_exists(mysqli_connect)) {
    $statMysql =   "version: " . mysqli_get_client_version() . '&nbsp;- ' . OK ;
    $lineMysql = '<span class="label label-primary">1</span> Mysqli '  ;
} else {
    $statMysql = NOT_OK ;
    $lineMysql = '<span class="label label-danger">1</span> Mysqli)' ;
}

if (!empty(PDO::getAvailableDrivers())) {
    $CountDrivers = 0;
    foreach(PDO::getAvailableDrivers() AS $DRIVERS) :
        $CountDrivers++;
        $ARR_DRIVERS[$CountDrivers] = $DRIVERS;
    endforeach;
    $driversList = implode(',', $ARR_DRIVERS);
    $statPdo =   "drivers: " . $driversList  . '&nbsp;- ' . OK ;
    $linePdo = '<span class="label label-primary">2</span> Pdo '  ;
} else {
    $statPdo = NOT_OK ;
    $linePdo = '<span class="label label-danger">2</span> Pdo' ;

}
if (DIRECTORY_SEPARATOR=='/')
    $absolute_path = dirname(__FILE__).'/';
else
    $absolute_path = str_replace('\\', '/', dirname(__FILE__)).'/';


$path = substr($absolute_path, 0, strpos($absolute_path, $install_dir)   );
$name  = $path.$config_file;
session_start() ;
$_SESSION['config_file'] = $name;

clearstatcache();
$aReturn = testeDir($path.$upload_dir,1);
$statUploadDir = $aReturn['stat'];
$lineUploadDir = $aReturn['line'];

clearstatcache();
$aReturn = testeDir($path.$smartycache_dir,2);
$statSmarty = $aReturn['stat'];
$lineSmarty = $aReturn['line'];

clearstatcache();
$aReturn = testeDir($path.$log_dir,3);
$statLog = $aReturn['stat'];
$lineLog = $aReturn['line'];

clearstatcache();
/*
 * Creation of the tmp folder because it was not in git. As soon as it is placed in git we can remove it.
 */
if (!file_exists($path.$tmp_dir)){
    mkdir ($path.$tmp_dir, 0755);
}

$aReturn = testeDir($path.$tmp_dir,3);
$statLog = $aReturn['stat'];
$lineLog = $aReturn['line'];

function testeDir($fileName,$seq)
{
    if (!file_exists($fileName)){
        $stat = NOT_OK;
        $line = '<span class="label label-danger">'.$seq.'</span> ' . $fileName ;
    } else {
        if (is_writable($fileName)) {
            $stat = utf8_encode(WRITABLE) . " - " . OK;
            $line = '<span class="label label-primary">'.$seq.'</span> ' . $fileName  ;
        } else {
            $stat = utf8_encode(NOT_WRITABLE) . " - " . NOT_OK;
            $line = '<span class="label label-danger">'.$seq.'</span> ' . $fileName  ;
        }
    }

    return array('stat' => $stat, 'line' => $line);
}

?>


<div id=content class="col-md-8">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5><?php echo utf8_encode(PROGRESS_STEP_2) ?></h5>
        </div>
        <div class="ibox-content">
            <div>
                <div class="feed-activity-list">
                    <div class="feed-element">
                        <div class="media-body ">
                            <form method="get" class="form-horizontal">
                                <div class="col-sm-12">
                                    <h5><?php echo INFO_STEP_2 ?></h5>
                                </div>

                                <div class="form-group">
                                    <!-- -->
                                    <div class="row  border-bottom white-bg dashboard-header">
                                        <div class="col-sm-12">

                                            <?php echo PHP_SETTINGS ?>
                                            <ul class="list-group clear-list m-t">
                                                <li class="list-group-item fist-item">
                                                        <span class="pull-right">
                                                            <?php echo $statPHP ?>
                                                        </span>
                                                    <?php echo $linePHP ?>
                                                </li>
                                                <li class="list-group-item">
                                                        <span class="pull-right">
                                                            <?php echo $statUpl ?>
                                                        </span>
                                                    <?php echo $lineUpl ?>
                                                </li>
                                                <li class="list-group-item ">
                                                            <span class="pull-right">
                                                                <?php echo $statMax ?>
                                                            </span>
                                                    <?php echo $lineMax ?>
                                                </li>
                                            </ul>

                                            <?php echo PHP_MODULES ?>
                                            <ul class="list-group clear-list m-t">
                                                <li class="list-group-item fist-item">
                                                            <span class="pull-right">
                                                                <?php echo $statMysql ?>
                                                            </span>
                                                    <?php echo $lineMysql ?>
                                                </li>
                                                <li class="list-group-item ">
                                                            <span class="pull-right">
                                                                <?php echo $statPdo ?>
                                                            </span>
                                                    <?php echo $linePdo ?>
                                                </li>

                                            </ul>


                                            <?php echo FOLDERS_FILES ?>
                                            <ul class="list-group clear-list m-t">
                                                <li class="list-group-item fist-item">
                                                            <span class="pull-right">
                                                                <?php echo $statUploadDir ?>
                                                            </span>
                                                    <?php echo $lineUploadDir ?>
                                                </li>
                                                <li class="list-group-item">
                                                            <span class="pull-right">
                                                                <?php echo $statSmarty ?>
                                                            </span>
                                                    <?php echo $lineSmarty ?>
                                                </li>

                                                <li class="list-group-item">
                                                            <span class="pull-right">
                                                                <?php echo $statLog ?>
                                                            </span>
                                                    <?php echo $lineLog ?>


                                            </ul>

                                        </div>

                                    </div>
                                    <!-- -->




                                </div>

                            </form>

                            <div class="row">

                                <div class="col-xs-1"></div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="step_1('<?php echo $_POST['i18n'] ?>')">
                                        <i class="fa fa-arrow-left"></i>&nbsp;<?php echo BACK ?></button>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="step_3('<?php echo $_POST['i18n'] ?>')">
                                        <i class="fa fa-arrow-right"></i>&nbsp;<?php echo NEXT ?></button>
                                </div>
                            </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


