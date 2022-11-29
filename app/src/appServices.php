<?php

namespace App\src;

use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\admin\dao\mysql\logoDAO;
use App\modules\admin\dao\mysql\vocabularyDAO;
use App\modules\admin\dao\mysql\emailServerDAO;
use App\modules\admin\dao\mysql\featureDAO;
use App\modules\helpdezk\dao\mysql\ticketDAO;
use App\modules\admin\dao\mysql\trackerDAO;
use App\modules\admin\dao\mysql\holidayDAO;
use App\modules\helpdezk\dao\mysql\expireDateDAO;

use App\modules\admin\models\mysql\logoModel;
use App\modules\admin\models\mysql\moduleModel;
use App\modules\admin\models\mysql\vocabularyModel;
use App\modules\admin\models\mysql\emailServerModel;
use App\modules\admin\models\mysql\emailSettingsModel;
use App\modules\helpdezk\models\mysql\ticketModel;
use App\modules\admin\models\mysql\trackerModel;
use App\modules\admin\models\mysql\holidayModel;
use App\modules\helpdezk\models\mysql\expireDateModel;

use App\modules\admin\src\loginServices;
use App\src\localeServices;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception; 

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class appServices
{
    /**
     * @var object
     */
    protected $applogger;
    
    /**
     * @var object
     */
    protected $appEmailLogger;

    /**
     * @var string
     */
    protected $saveMode;

    /**
     * @var string
     */
    protected $imgDir;

    /**
     * @var string
     */
    protected $imgBucket;

    public function __construct()
    {
        // create a log channel
        $formatter = new LineFormatter(null, $_ENV['LOG_DATE_FORMAT']);
        
        $stream = $this->_getStreamHandler();
        $stream->setFormatter($formatter);


        $this->applogger  = new Logger('helpdezk');
        $this->applogger->pushHandler($stream);
        
        // Clone the first one to only change the channel
        $this->appEmailLogger = $this->applogger->withName('email');

        // Setting up the save mode of files
        $this->saveMode = $_ENV['S3BUCKET_STORAGE'] ? "aws-s3" : 'disk';
        if($this->saveMode == "aws-s3"){
            $bucket = $_ENV['S3BUCKET_NAME'];
            $this->imgDir = "logos/";
            $this->imgBucket = "https://{$bucket}.s3.amazonaws.com/logos/";
        }else{
            if($_ENV['EXTERNAL_STORAGE']) {
                $this->imgDir = $this->_setFolder($_ENV['EXTERNAL_STORAGE_PATH'].'/logos/');
                $this->imgBucket = $_ENV['EXTERNAL_STORAGE_URL'].'logos/';
            } else {
                $storageDir = $this->_setFolder($this->_getHelpdezkPath().'/storage/');
                $upDir = $this->_setFolder($storageDir.'uploads/');
                $this->imgDir = $this->_setFolder($upDir.'logos/');
                $this->imgBucket = $_ENV['HDK_URL']."/storage/uploads/logos/";
            }
        }

    }
    
    /**
     * en_us Returns system's version to display on the login screen and footer
     * 
     * pt_br Retorna a versão do sistema para exibição na tela de login e no rodapé
     *
     * @return string
     */
    public function _getHelpdezkVersion(): string
    {
        // Read the version.txt file
        $versionFile = $this->_getHelpdezkPath() . "/version.txt";

        if (is_readable($versionFile)) {
            $info = file_get_contents($versionFile, FALSE, NULL, 0, 50);
            if ($info) {
                return trim($info);
            } else {
                return '2.0';
            }
        } else {
            return '2.0';
        }

    }
    
    /**
     * en_us Returns directory path
     * 
     * pt_br Retorna o caminho do diretório
     *
     * @return string
     */
    public function _getHelpdezkPath()
    {
        $pathInfo = pathinfo(dirname(__DIR__));
        return $pathInfo['dirname'];
    }
        
    /**
     * _getPath
     *
     * @return string
     */
    public function _getPath()
    {
        $docRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
        $dirName = str_replace("\\","/",dirname(__DIR__,PATHINFO_BASENAME));
        //The following code snippet is used to resolve the default path in virtual host
        $path_default = ($docRoot == $dirName) ? "" : str_replace($docRoot,'',$dirName);
        
        if (!empty($path_default) && substr($path_default, 0, 1) != '/') {
            $path_default = '/' . $path_default;
        }

        if ($path_default == "/..") {
            $path = "";
        } else {
            $path = $path_default;
        }
        
        return $path;
    }
        
    /**
     * _getLayoutTemplate
     *
     * @return void
     */
    public function _getLayoutTemplate()
    {
        return $this->_getHelpdezkPath().'/app/modules/main/views/layout.latte';
    }
        
    /**
     * _getNavbarTemplate
     *
     * @return void
     */
    public function _getNavbarTemplate()
    {
        return $this->_getHelpdezkPath().'/app/modules/main/views/nav-main.latte';
    }
        
    /**
     * _getFooterTemplate
     *
     * @return void
     */
    public function _getFooterTemplate()
    {
        return $this->_getHelpdezkPath().'/app/modules/main/views/footer.latte';
    }
        
    /**
     * en_us Returns common parameters for all programs
     *
     * @return array
     */
    public function _getDefaultParams(): array
    {
        $loginSrc = new loginServices();
        $aHeader = $this->_getHeaderData();

        if($this->saveMode == "aws-s3"){
            $awsSrc = new awsServices();
            
            $retAdmUrl = $awsSrc->_getFile($this->imgDir.'adm_header.png');
            $admImgSrc = $retAdmUrl['fileUrl'];
        }else{
            $admImgSrc = $this->imgBucket.'adm_header.png';
        }

        
        return array(
            "path"			    => $this->_getPath(),
            "lang_default"	    => $_ENV["DEFAULT_LANG"],
            "layout"		    => $this->_getLayoutTemplate(),
            "version" 		    => $this->_getHelpdezkVersion(),
            "navBar"		    => $this->_getNavbarTemplate(),
            "footer"		    => $this->_getFooterTemplate(),
            "demoVersion" 	    => empty($_ENV['DEMO']) ? 0 : $_ENV['DEMO'], // Demo version - Since January 29, 2020
            "isroot"            => ($_SESSION['SES_COD_USUARIO'] == 1) ? true : false,
            "hasadmin"          => ($_SESSION['SES_TYPE_PERSON'] == 1 && $_SESSION['SES_COD_USUARIO'] != 1) ? true : false,
            "navlogin"          => ($_SESSION['SES_COD_USUARIO'] == 1) ? $_SESSION['SES_NAME_PERSON'] : $_SESSION['SES_LOGIN_PERSON'],
            "adminhome"         => $_ENV['HDK_URL'].'/admin/home/index',
            "adminlogo"         => $admImgSrc,
            "hashelpdezk"       => $loginSrc->_isActiveHelpdezk(),
            "helpdezkhome"      => $_ENV['HDK_URL'].'/helpdezk/home/index',
            "hdklogo"           => $aHeader['image'],
            "logout"            => $_ENV['HDK_URL'].'/main/home/logout',
            "id_mask"           => $_ENV['ID_MASK'],
            "ein_mask"          => $_ENV['EIN_MASK'],
            "zip_mask"          => $_ENV['ZIP_MASK'],
            "phone_mask"        => $_ENV['PHONE_MASK'],
            "cellphone_mask"    => $_ENV['CELLPHONE_MASK'],
            "mascdatetime"      => str_replace('%', '', "{$_ENV['DATE_FORMAT']} {$_ENV['HOUR_FORMAT']}"),
            "mascdate"          => str_replace('%', '', $_ENV['DATE_FORMAT']),
            "timesession"       => (!$_SESSION['SES_TIME_SESSION']) ? 600 : $_SESSION['SES_TIME_SESSION'],
            "modules"           => (!isset($_SESSION['SES_COD_USUARIO'])) ? array() :$this->_getModulesByUser($_SESSION['SES_COD_USUARIO']),
            "modalUserSettings" => $this->_getUserSettingsTemplate(),
            "vocabulary"        => $this->_loadVocabulary(),
            "lang"              => $this->_formatLanguageParam($_ENV["DEFAULT_LANG"])
        );
    }
    
    /**
     * _getHelpdezkVersionNumber
     *
     * @return void
     */
    public function _getHelpdezkVersionNumber()
    {
        $exp = explode('-', $this->_getHelpdezkVersion());
        return $exp[2];
    }
    
    /**
     * _getActiveModules
     *
     * @return void
     */
    public function _getActiveModules()
    {
        $moduleDAO = new moduleDAO();
        $moduleModel = new moduleModel();

        $activeModules = $moduleDAO->fetchActiveModules($moduleModel);
        return ($activeModules['status']) ? $activeModules['push']['object']->getActiveList() : false;

    }

    /**
     * Returns header's logo data
	 * 
     * @return array header's logo data (path, width, height)
     */
	public function _getHeaderData(): array 
    {
        $logoDAO = new logoDao(); 
        $logoModel = new logoModel();
        $awsSrc = new awsServices();

        $logoModel->setName("header");
        $logo = $logoDAO->getLogoByName($logoModel);
		
        if(!$logo['status']){ //(empty($objLogo->getFileName()) or !){
            if($this->saveMode == 'disk'){
                $image 	= $this->imgBucket . 'default/header.png';
            }elseif($this->saveMode == "aws-s3"){
                $retDefaultLogoUrl = $awsSrc->_getFile($this->imgDir . 'default/header.png');
                $image = $retDefaultLogoUrl['fileUrl'];
            }
            
			$width 	= "227";
			$height = "70";
        }else{
            $objLogo = $logo['push']['object'];
            
            if(empty($objLogo->getFileName())){
                $st = false;
            }else{
                if($this->saveMode == 'disk'){
                    $pathLogoImage = $this->imgDir . $objLogo->getFileName();                
                    $st = file_exists($pathLogoImage) ? true : false;
                }elseif($this->saveMode == "aws-s3"){            
                    $retLogoUrl = $awsSrc->_getFile($this->imgDir.$objLogo->getFileName());
                    $pathLogoImage = $retLogoUrl['fileUrl'];
                    $st = (@fopen($pathLogoImage, 'r')) ? true : false; 
                }
            }

            if(!$st){
                if($this->saveMode == 'disk'){
                    $image 	= $this->imgBucket . 'default/header.png';
                }elseif($this->saveMode == "aws-s3"){
                    $retDefaultLogoUrl = $awsSrc->_getFile($this->imgDir . 'default/header.png');
                    $image = $retDefaultLogoUrl['fileUrl'];
                }
                $width 	= "227";
                $height = "70";
            }else{
                if($this->saveMode == 'disk'){
                    $image 	=$this->imgBucket . $objLogo->getFileName();
                }elseif($this->saveMode == "aws-s3"){
                    $image = $pathLogoImage;
                }
			    $width 	= $objLogo->getWidth();
			    $height = $objLogo->getHeight();
            }
		}
        
        $aRet = array(
            'image'  => $image,
            'width'  => $width,
            'height' => $height
        );
        
		return $aRet;
    }
	
	/**
	 * en_us Returns an array with module data for the side menu
     *
     * pt_br Retorna um array com os dados dos módulos para o menu lateral
	 *
	 * @param  int $userID
	 * @return array
	 */
	public function _getModulesByUser(int $userID): array 
    {
        $aRet = [];
		$moduleDAO = new moduleDao();
        $moduleModel = new moduleModel();
        $moduleModel->setUserID($userID);
        
        $retModule = $moduleDAO->fetchExtraModulesPerson($moduleModel);
        if($retModule['status']){
            $aModule = $retModule['push']['object']->getActiveList();
            foreach($aModule as $k=>$v) {
                $prefix = $v['tableprefix'];
                if(!empty($prefix)) {
                    $moduleModel->setTablePrefix($prefix);
                    $retSettings = $moduleDAO->fetchConfigDataByModule($moduleModel);
                    if ($retSettings['status']){
                        $modSettings = $retSettings['push']['object']->getSettingsList();

                        if($this->saveMode == "aws-s3"){
                            $awsSrc = new awsServices();
                            
                            $retUrl = $awsSrc->_getFile($this->imgDir.$v['headerlogo']);
                            $imageSrc = $retUrl['fileUrl'];
                        }else{
                            $imageSrc = $this->imgBucket.$v['headerlogo'];
                        }

                        $aRet[] = array(
                            'idmodule' => $v['idmodule'],
                            'path' => $v['path'],
                            'class' => $v['class'],
                            'headerlogo' => $imageSrc,
                            'reportslogo' => $v['reportslogo'],
                            'varsmarty' => $v['smarty']
                        );
                    }
                }
            }
        }else{
            return array();
        }
        
        return $aRet;
    }

    /**
     * en_us Check if the user is logged in
     *
     * pt_br Verifica se o usuário está logado
     *
     * @param  mixed $mob
     * @return void
     * 
     * @since November 03, 2017
     */
    public function _sessionValidate($mob=null) {
        if (!isset($_SESSION['SES_COD_USUARIO'])) {
            if($mob){
                echo 1;
            }else{
                $this->_sessionDestroy();
                header('Location:' . $_ENV['HDK_URL'] . '/admin/login');
            }
        }
    }
        
    /**
     * en_us Clear the session variable
     *
     * pt_br Limpa a variável de sessão
     * 
     * @return void
     * 
     * @since November 03, 2017
     */
    public function _sessionDestroy()
    {
        session_start();
        session_unset();
        session_destroy();
    }
    
    /**
     * en_us Return calendar settings
     *
     * pt_br Retorna as configurações do calendário
     *
     * @param array $params Array with others default parameters
     * @return array
     */
    public function _datepickerSettings($params): array
    {
        
        switch ($_ENV['DEFAULT_LANG']) {
            case 'pt_br':
                $params['dtpFormat'] = "dd/mm/yyyy";
                $params['dtpLanguage'] = "pt-BR";
                $params['dtpAutoclose'] = true;
                $params['dtpOrientation'] = "bottom auto";
                $params['dtpickerLocale'] = "bootstrap-datepicker.pt-BR.min.js";
                $params['dtSearchFmt'] = 'd/m/Y';
                break;
            case 'es_es':
                $params['dtpFormat'] = "dd/mm/yyyy";
                $params['dtpLanguage'] = "es";
                $params['dtpAutoclose'] = true;
                $params['dtpOrientation'] = "bottom auto";
                $params['dtpickerLocale'] = "bootstrap-datepicker.es.min.js";
                $params['dtSearchFmt'] = 'd/m/Y';
                break;
            default:
                $params['dtpFormat'] = "mm/dd/yyyy";
                $params['dtpAutoclose'] = true;
                $params['dtpOrientation'] = "bottom auto";
                $params['dtpickerLocale'] = "";
                $params['dtSearchFmt'] = 'm/d/Y';
                break;

        }

        return $params;
    }
    
    /**
     * en_us Create the token to prevent sql injection and xss attacks
     * 
     * pt_br Cria o token para prevenir ataques sql injection e xss
     *
     * @return string
     */
    public function _makeToken(): string
    {
        $token =  hash('sha512',rand(100,1000));
        $_SESSION['TOKEN'] =  $token;
        return $token;
    }

    /**
     * en_us Get the token written to the session variable
     * 
     * pt_br Obtem o token gravado na variável de sessão
     *
     * @return string
     */
    public function _getToken(): string
    {
        session_start();
        return $_SESSION['TOKEN'];

    }

    /**
     * en_us Compares the token sent by the form with the one in the session variable
     * 
     * pt_br Compara o token enviado pelo formulário com o existente na variável de sessão
     *
     * @return bool
     */
    public function _checkToken(): bool
    {

        if (empty($_POST) || empty($_GET) ) {
            return false;
        } else {
            if($_POST['_token'] == $this->_getToken() || $_GET['_token'] == $this->_getToken()) {
                return true;
            }
        }

        return false;
    }

    /**
     * en_us Format a date to write to BD
     * 
     * pt_br Formata uma data para gravar no BD
     *
     * @return string
     */
    public function _formatSaveDate($date): string
    {
        $date = str_replace("/","-",$date);
        
        return date("Y-m-d",strtotime($date));
    }

    /**
     * en_us Format a date to write to BD
     * 
     * pt_br Formata uma data para gravar no BD
     *
     * @return string
     */
    public function _formatSaveDateTime($date): string
    {
        $date = str_replace("/","-",$date);
        
        return date("Y-m-d H:i:s",strtotime($date));
    }
    
    /**
     * en_us Format a date to view on screen
     * 
     * pt_br Formata uma data para visualizar em tela
     *
     * @param  mixed $date
     * @return string
     */
    public function _formatDate(string $date): string
    {
        $date = str_replace("/","-",$date);
        if(!isset($_ENV["SCREEN_DATE_FORMAT"]) || empty($_ENV["SCREEN_DATE_FORMAT"])){
            $this->applogger->error("Environment variable SCREEN_DATE_FORMAT doesn't exist",['Class' => __CLASS__, 'Method' => __METHOD__]);
        }

        return date($_ENV["SCREEN_DATE_FORMAT"],strtotime($date));
    }

    /**
     * en_us Format a time to view on screen
     * 
     * pt_br Formata uma hora para visualizar em tela
     *
     * @param  mixed $date
     * @return string
     */
    public function _formatHour(string $date): string
    {
        $date = str_replace("/","-",$date);
        if(!isset($_ENV["SCREEN_HOUR_FORMAT"]) || empty($_ENV["SCREEN_HOUR_FORMAT"])){
            $this->applogger->error("Environment variable SCREEN_HOUR_FORMAT doesn't exist",['Class' => __CLASS__, 'Method' => __METHOD__]);
        }

        return date($_ENV["SCREEN_HOUR_FORMAT"],strtotime($date));
    }

    /**
     * en_us Format a date and hour to view on screen
     * 
     * pt_br Formata uma data e hora para visualizar em tela
     *
     * @param  mixed $date
     * @return string
     */
    public function _formatDateHour(string $date): string
    {
        $date = str_replace("/","-",$date);
        if((!isset($_ENV["SCREEN_DATE_FORMAT"]) || empty($_ENV["SCREEN_DATE_FORMAT"])) && (!isset($_ENV["SCREEN_HOUR_FORMAT"]) || empty($_ENV["SCREEN_HOUR_FORMAT"]))){
            $this->applogger->error("Environment variable SCREEN_DATE_FORMAT doesn't exist",['Class' => __CLASS__, 'Method' => __METHOD__]);
        }
        
        $format = "{$_ENV["SCREEN_DATE_FORMAT"]} {$_ENV["SCREEN_HOUR_FORMAT"]}";
        return date($format,strtotime($date));
    }

    /**
     * Returns an array with ID and name of search options
     *
     * @return array
     */
    public function _comboFilterOpts(): array
    {
        $translator = new localeServices();

        $aRet = array(
            array("id" => 'eq',"text"=>$translator->translate('equal')), // equal
            array("id" => 'ne',"text"=>$translator->translate('not_equal')), // not equal
            array("id" => 'lt',"text"=>$translator->translate('less')), // less
            array("id" => 'le',"text"=>$translator->translate('less_equal')), // less or equal
            array("id" => 'gt',"text"=>$translator->translate('greater')), // greater
            array("id" => 'ge',"text"=>$translator->translate('greater_equal')), // greater or equal
            array("id" => 'bw',"text"=>$translator->translate('begin_with')), // begins with
            array("id" => 'bn',"text"=>$translator->translate('not_begin_with')), //does not begin with
            array("id" => 'in',"text"=>$translator->translate('in')), // is in
            array("id" => 'ni',"text"=>$translator->translate('not_in')), // is not in
            array("id" => 'ew',"text"=>$translator->translate('end_with')), // ends with
            array("id" => 'en',"text"=>$translator->translate('not_end_with')), // does not end with
            array("id" => 'cn',"text"=>$translator->translate('contain')), // contains
            array("id" => 'nc',"text"=>$translator->translate('not_contain')), // does not contain
            array("id" => 'nu',"text"=>$translator->translate('is_null')), //is null
            array("id" => 'nn',"text"=>$translator->translate('is_not_null'))  // is not null
        );
        
        return $aRet;
    }

    /**
     * Check column name of search
     *
     * @param  mixed $dataIndx
     * @return void
     */
    public function _isValidColumn($dataIndx){
        
        if (preg_match('/^[a-z,A-Z]*$/', $dataIndx))
        {
            return true;
        }
        else
        {
            return false;
        }    
    }
    
    /**
     * Returns rows offset for pagination
     *
     * @param  mixed $pq_curPage
     * @param  mixed $pq_rPP
     * @param  mixed $total_Records
     * @return void
     */
    public function _pageHelper(&$pq_curPage, $pq_rPP, $total_Records){
        $skip = ($pq_curPage > 0) ? ($pq_rPP * ($pq_curPage - 1)) : 0;

        if ($skip >= $total_Records)
        {        
            $pq_curPage = ceil($total_Records / $pq_rPP);
            $skip = ($pq_curPage > 0) ? ($pq_rPP * ($pq_curPage - 1)) : 0;
        }    
        return $skip;
    }

    /**
     * Returns the sql sintax, according filter sended by grid
     *
     * @param string $oper Name of the PqGrid operation
     * @param string $column Field to search
     * @param string $search Column to search
     * @return bool|string    False is not exists operation
     *
     */
    public function _formatGridOperation($oper, $column, $search)
    {
        switch ($oper) {
            case 'eq' : // equal
                $ret = "pipeLatinToUtf8(" . $column . ")" . ' = ' . "pipeLatinToUtf8('" . $search . "')";
                break;
            case 'ne': // not equal
                $ret = "pipeLatinToUtf8(" . $column . ")" . ' != ' . "pipeLatinToUtf8('" . $search . "')";
                break;
            case 'lt': // less
                $ret = $column . ' < ' . $search;
                break;
            case 'le': // less or equal
                $ret = $column . ' <= ' . $search;
                break;
            case 'gt': // greater
                $ret = $column . ' > ' . $search;
                break;
            case 'ge': // greater or equal
                $ret = $column . ' >= ' . $search;
                break;
            case 'bw': // begins with
                $search = str_replace("_", "\_", $search);
                $ret = "pipeLatinToUtf8(" . $column . ")" . ' LIKE ' . "pipeLatinToUtf8('" . $search . '%' . "')";
                break;
            case 'bn': //does not begin with
                $ret = "pipeLatinToUtf8(" . $column . ")" . ' NOT LIKE ' . "pipeLatinToUtf8('" . $search . '%' . "')";
            case 'in': // is in
                $ret = "pipeLatinToUtf8(" . $column . ")" . ' IN (' . "pipeLatinToUtf8('" . $search . "')" . ')';
                break;
            case 'ni': // is not in
                $ret = "pipeLatinToUtf8(" . $column . ")" . ' NOT IN (' . "pipeLatinToUtf8('" . $search . "')" . ')';
                break;
            case 'ew': // ends with
                $search = str_replace("_", "\_", $search);
                $ret = "pipeLatinToUtf8(" . $column . ")" . ' LIKE ' . "pipeLatinToUtf8('" . '%' . rtrim($search) . "')";
                break;
            case 'en': // does not end with
                $search = str_replace("_", "\_", $search);
                $ret = "pipeLatinToUtf8(" . $column . ")" . ' NOT LIKE ' . "pipeLatinToUtf8('" . '%' . rtrim($search) . "')";
                break;
            case 'cn': // contains
                $search = str_replace("_", "\_", $search);
                $ret = "pipeLatinToUtf8(" . $column . ")" . ' LIKE ' . "pipeLatinToUtf8('" . '%' . $search . '%' . "')";
                break;
            case 'nc': // does not contain
                $search = str_replace("_", "\_", $search);
                $ret = "pipeLatinToUtf8(" . $column . ")" . ' NOT LIKE ' . "pipeLatinToUtf8('" . '%' . $search . '%' . "')";
                break;
            case 'nu': //is null
                $ret = $column . ' IS NULL';
                break;
            case 'nn': // is not null
                $ret = $column . ' IS NOT NULL';
                break;
            default:
                die('Operator invalid in grid search !!!' . " File: " . __FILE__ . " Line: " . __LINE__);
                break;
        }

        return $ret;
    }

    /**
     * en_us Format a date to write to BD
     * 
     * pt_br Formata uma data para gravar no BD
     *
     * @return object
     */
    public function _getStreamHandler()
    { 
        $logFile = (!isset($_ENV['LOG_REMOTE']) || !$_ENV['LOG_REMOTE']) ? $this->_getHelpdezkPath() ."/". $_ENV['LOG_FILE'] : $_ENV['LOG_FILE'];
        
        switch($_ENV['LOG_LEVEL']){
            case 'INFO':
                $stream = new StreamHandler($logFile, Logger::INFO);
                break;
            case 'NOTICE':
                $stream = new StreamHandler($logFile, Logger::NOTICE);
                break;
            case 'WARNING':
                $stream = new StreamHandler($logFile, Logger::WARNING);
                break;
            case 'ERROR':
                $stream = new StreamHandler($logFile, Logger::ERROR);
                break;
            case 'CRITICAL':
                $stream = new StreamHandler($logFile, Logger::CRITICAL);
                break;
            case 'ALERT':
                $stream = new StreamHandler($logFile, Logger::ALERT);
                break;
            case 'EMERGENCY':
                $stream = new StreamHandler($logFile, Logger::EMERGENCY);
                break;
            default:
                $stream = new StreamHandler($logFile, Logger::DEBUG);
                break;
        }
        
        return $stream;
    }
    
    /**
     * en_us Checks if the directory exists, if not, it will be created. 
     *       It also checks if you have write permissions, if not, grant the corresponding permissions.
     * 
     * pt_br Verifica se o diretório existe, caso não exista, será criado. 
     *       Também verifica se tem permissões de escrita, caso não possua, concede as permissões correspondentes
     *
     * @param  mixed $path
     * @return string
     */
    public function _setFolder(string $path): string
    {
        if(!is_dir($path)) {
            $this->applogger->info("Directory: $path does not exists, I will try to create it.",['Class' => __CLASS__, 'Method' => __METHOD__]);
            if (!mkdir ($path, 0777 )) {
                $this->applogger->error("I could not create the directory: $path",['Class' => __CLASS__, 'Method' => __METHOD__]);
                return false;
            }else{
                $this->applogger->info("The directory $path, was created successfully.",['Class' => __CLASS__, 'Method' => __METHOD__]);
            }
        }

        if (!is_writable($path)) {
            $this->applogger->info('Directory: '. $path.' is not writable, I will try to make it writable',['Class' => __CLASS__, 'Method' => __METHOD__]);
            if (!chmod($path,0777)){
                $this->applogger->error("Directory: $path is not writable!!",['Class' => __CLASS__, 'Method' => __METHOD__]);
                return false;
            }else{
                $this->applogger->info("The directory $path is writable!!",['Class' => __CLASS__, 'Method' => __METHOD__]);
            }
        }

        return $path;
    }

    public function _makeMenuByModule($moduleModel)
    {
        $moduleDAO = new moduleDAO();
        $retCategories = $moduleDAO->fetchModuleActiveCategories($moduleModel);
        $aCategories = array();
        
        if($retCategories['status']){
            $categoriesObj = $retCategories['push']['object'];
            $categories = $categoriesObj->getCategoriesList();
            
            foreach($categories as $ck=>$cv) {
                $categoriesObj->setCategoryID($cv['category_id']);
                
                $retPermissions = $moduleDAO->fetchPermissionMenu($categoriesObj);
                
                if($retPermissions['status']){
                    $permissionsObj = $retPermissions['push']['object'];
                    $permissionsMod = $permissionsObj->getPermissionsList();
                    
                    foreach($permissionsMod as $permidx=>$permval) {
                        $allow = $permval['allow'];
                        $path  = $permval['path'];
                        $program = $permval['program'];
                        $controller = $permval['controller'];
                        $prsmarty = $permval['pr_smarty'];

                        $checkbar = substr($permval['controller'], -1);
                        if($checkbar != "/") $checkbar = "/";
                        else $checkbar = "";

                        $controllertmp = ($checkbar != "") ? $controller : substr($controller,0,-1);
                        $controller_path = 'app/modules/'. $path  .'/controllers/' . ucfirst($controllertmp)  . '.php';
                        
                        if (!file_exists($controller_path)) {
                            $this->applogger->error("The controller does not exist: {$controller_path}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                        }else{
                            if ($allow == 'Y') {
                                $aCategories[$cv['cat_smarty']][$prsmarty] = array("url"=>$_ENV['HDK_URL'] . "/".$path."/" . $controller . $checkbar."index", "program_name"=>$prsmarty);
                            }
                        }
                    }
                }
            }
        }
        
        return $aCategories;

    }
    
    /**
     * Returns module's ID
     *
     * @param  string $moduleName
     * @return int
     */
    public function _getModuleID(string $moduleName): int
    {
        $moduleDAO = new moduleDAO();
        $moduleModel = new moduleModel();

        $moduleModel->setName($moduleName);
        $ret = $moduleDAO->getModuleInfoByName($moduleModel);
        return ($ret['status']) ? $ret['push']['object']->getIdModule() : 0;
    }

    /**
     * Returns the image file format( Only allowed formats: GIF, PNG, JPEG ans BMP)
     *
     * Used for some cases where you can upload various formats and at the time of showing,
     * we do not know what format it is in. The method tests if the file exists and verifies
     * that the format is compatible
     *
     * @param string $target Image file
     * @return boolean|string    False is not exists ou file extention
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */
    public function _getImageFileFormat($target)
    {
        $target = $target . '.*';
        
        $arrImages = glob($target);

        if (empty($arrImages))
            return false;
       
        foreach ($arrImages as &$imgFile) {
            if (in_array(exif_imagetype($imgFile), array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP))) {
                switch (exif_imagetype($imgFile)) {
                    case 1:
                        $ext = 'gif';
                        break;
                    case 2:
                        $ext = 'jpg';
                        break;
                    case 3:
                        $ext = 'png';
                        break;
                    case 6:
                        $ext = 'bmp';
                }
                return $ext;
            }
        }
        return false;
    }

    public function _getUserSettingsTemplate()
    {
        return $this->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-user-settings.latte';
    }

    /**
     * Setup vocabulary
     *
     * @return array
     */
    public function _loadVocabulary(): array
    {
        $vocabDAO = new vocabularyDAO();
        $vocabModel = new vocabularyModel();
        $aRet = array();

        $ret = $vocabDAO->queryVocabularies("AND UPPER(b.name) = UPPER('{$_ENV['DEFAULT_LANG']}')",null,"ORDER BY key_name");

        if($ret['status']){
            $vocabularies = $ret['push']['object']->getGridList();

            foreach($vocabularies as $k=>$v){
                $aRet[$v['key_name']] = $v['key_value'];
            }
            
        }
        
        return $aRet;
    }

    /**
     * en_us Format a date to write to BD
     * 
     * pt_br Formata uma data para gravar no BD
     *
     * @return string
     */
    public function _formatSaveDateHour($dateHour): string
    {
        $dateHour = str_replace("/","-",$dateHour);
        
        return date("Y-m-d H:i:s",strtotime($dateHour));
    }
    
    /**
     * en_us Send email
     * 
     * pt_br Envia e-mail 
     *
     * @param  string $type         text here
     * @param  string $serverName   text here
     * @param  array  $params       text here
     * @return array
     */
    public function _sendEmail(array $params,string $type=null,string $serverName=null): array
    {
        $emailSrvDAO = new emailServerDAO();
        $emailSrvModel = new emailServerModel();

        $where = (!$type && !$serverName) ? "WHERE a.default = 'Y'" : "WHERE b.name = '{$type}' AND a.name = '{$serverName}'";
        $ret = $emailSrvDAO->queryEmailServers($where);

        if(!$ret['status']){
            $this->applogger->error("No result returned",['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return array('status'=>false,"message"=>$ret['push']['message']);
        }

        $aEmailSrv = $ret['push']['object']->getGridList();
        $params = array_merge($aEmailSrv[0],$params);

        switch($aEmailSrv[0]['servertype']){
            case "SMTP"://STMP
                $retSend = $this->_sendSMTP($params);
                if(!$retSend['status']){
                    $this->applogger->error("Error trying send email. Error: {$retSend['message']}",['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    return array('status'=>false,"message"=>"{$retSend['message']}","data"=>"");
                }
                $data = "";
                break;
            case "API"://API
                $retSend = $this->_sendMandrill($params);
                if(!$retSend['status']){
                    $this->applogger->error("Error trying send email. Error: {$retSend['message']}",['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    return array('status'=>false,"message"=>"{$retSend['data']['message']}","data"=>"");
                }
                $data = $retSend['data'];
                break;
            default:
                echo "Case default\n";
                break;

        }

        return array('status'=>true,"message"=>"","data"=>$data);
    }

    /**
     * en_us Send email via SMTP
     * 
     * pt_br Envia e-mail por SMTP
     *
     * @param  array  $params       text here
     * @return array                text here
     */
    public function _sendSMTP(array $params)
    {
        $featureDAO = new featureDAO();
        $emailSettingModel = new emailSettingsModel();

        $ret = $featureDAO->getEmailSettings($emailSettingModel);

        if(!$ret['status']){
            return array("success"=>false,"message"=>"");
        }
        
        $aEmailSrvObj = $ret['push']['object'];

        $mailTitle     = '=?UTF-8?B?'.base64_encode($aEmailSrvObj->getTitle()).'?=';
        $mailMethod    = 'smtp';
        $mailHost      = $params['apiendpoint'];
        $mailDomain    = $aEmailSrvObj->getDomain();
        $mailAuth      = $aEmailSrvObj->getAuth();
        $mailUsername  = $params['user'];
        $mailPassword  = $params['password'];
        $mailSender    = $aEmailSrvObj->getSender();
        $mailHeader    = $aEmailSrvObj->getHeader();
        $mailFooter    = $aEmailSrvObj->getFooter();
        $mailPort      = $params['port'];
        
        $mail = new PHPMailer(true);

        $mail->CharSet = 'utf-8';

        if($params['customHeader'] && $params['customHeader'] != ''){
            $mail->addCustomHeader($params['customHeader']);
        }

        if($_ENV['DEMO']){
            $mail->addCustomHeader('X-hdkLicence:' . 'demo');
        }else{
            $mail->addCustomHeader('X-hdkLicence:' . $_ENV['LICENSE']);
        }

        if($params['sender'] && $params['sender'] != ''){
            $mailSender = $params['sender'];
        }

        if($params['sender_name'] && $params['sender_name'] != ''){
            $mailTitle = '=?UTF-8?B?'.base64_encode($params['sender_name']).'?=';
        }

        $mail->setFrom($mailSender, $mailTitle);

        if($mailHost)
            $mail->Host = $mailHost;

        if(isset($mailPort) AND !empty($mailPort)) {
            $mail->Port = $mailPort;
        }

        $mail->Mailer = $mailMethod;
        $mail->SMTPAuth = $mailAuth;

        if($aEmailSrvObj->getTls())
            $mail->SMTPSecure = 'tls';

        $mail->Username = $mailUsername;
        $mail->Password = $mailPassword;

        $mail->AltBody 	= "HTML";
        $mail->Subject 	= '=?UTF-8?B?'.base64_encode($params['subject']).'?=';

        //$mail->SetLanguage('br', $this->helpdezkPath . "/includes/classes/phpMailer/language/");

        $paramsDone = array("msg" => $params['msg'],
                            "msg2" => $params['msg2'],
                            "mailHost" => $mailHost,
                            "mailDomain" => $mailDomain,
                            "mailAuth" => $mailAuth,
                            "mailPort" => $mailPort,
                            "mailUsername" => $mailUsername,
                            "mailPassword" => $mailPassword,
                            "mailSender" => $mailSender
                            );

        if(sizeof($params['attachment']) > 0){
            foreach($params['attachment'] as $key=>$value){
                $mail->AddAttachment($value['filepath'], $value['filename']);  // optional name
            }
        }

        $normalProcedure = true;

        if((isset($params['tracker']) && $params['tracker']) || (isset($params['tokenOperatorLink']) && $params['tokenOperatorLink'])) {
            
            $aEmail = $this->_makeArrayTracker($params['address']);
            $body = $mailHeader . $params['contents'] . $mailFooter;

            foreach ($aEmail as $key => $sendEmailTo) {
                $mail->AddAddress($sendEmailTo);

                if($params['tokenOperatorLink']) {
                    $linkOperatorToken = $this->_makeLinkOperatorToken($sendEmailTo, $params['code_request']);
                    if(!$linkOperatorToken){
                        $this->appEmailLogger->error("Error make link operator with token, ticket #{$params['code_request']}",['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    } else {
                        $newContent = $this->_replaceBetweenTags($params['contents'], $linkOperatorToken, 'pipegrep');
                        $body = $mailHeader . $newContent . $mailFooter;
                    }
                }

                if($params['tracker']) {
                    $retTracker = $this->_saveTracker($params['idmodule'],$mailSender,$sendEmailTo,addslashes($params['subject']),addslashes($params['contents']));
                    if(!$retTracker['status']) {
                        $this->appEmailLogger->error("Error insert in tbtracker, {$retTracker['message']}",['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    } else {
                        $idEmail = $retTracker['idEmail'];
                        $trackerID = "<img src='{$_ENV['HDK_URL']}/tracker/{$params['moduleName']}/{$idEmail}.png' height='1' width='1' />";
                        $body = $body . $trackerID;
                    }
                }

                $mail->Body = $body;
                
                //sent email
                $retSend = $this->_isEmailDone($mail,$paramsDone);
                if(!$retSend['status'])
                    $this->appEmailLogger->error("Can't send email to {$sendEmailTo} Error: {$retSend['message']}",['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                else
                    $this->appEmailLogger->info("Email sent to {$sendEmailTo}",['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);


                $mail->ClearAddresses();
            }

            $normalProcedure = false;
        }

        if ($normalProcedure){
            //Checks for more than 1 email address at recipient
            $this->_makeSentTo($mail,$params['address']);
            $mail->Body = $mailHeader . $params['contents'] . $mailFooter;
            // sent email
            $retSend = $this->_isEmailDone($mail,$paramsDone);
        }

        $mail->ClearAttachments();

        if(!$retSend['status'])
            return array("status"=>false,"message"=>"{$retSend['message']}");
        else
            return array("status"=>true,"message"=>"");       
    }
    
    /**
     * en_us Convert a list of email addresses to an array
     * 
     * pt_br Converte em array uma lista de endereços de e-mail
     *
     * @param  string $sentTo
     * @return array
     */
    public function _makeArrayTracker(string $sentTo): array
    {
        $aExist = array();
        $aRet = array();

        if(preg_match("/;/", $sentTo)){
            $aRecipient = explode(";", $sentTo);
            if (is_array($aRecipient)) {
                for ($i = 0; $i < count($aRecipient); $i++) {
                    if (empty($aRecipient[$i]))
                        continue;
                    if (!in_array($aRecipient[$i], $aExist)) {
                        $aExist[] = $aRecipient[$i];
                        array_push($aRet,$aRecipient[$i]);
                    }
                }
            } else {
                array_push($aRet,$aRecipient);
            }
        }else{
            array_push($aRet,$sentTo);
        }

        return $aRet;
    }
    
    /**
     * en_us Create link to view from email sent
     * 
     * pt_br Cria link para visualização a partir do e-mail enviado
     *
     * @param  mixed $recipient     Recipient's email address
     * @param  mixed $ticketCode    Ticket's code
     * @return void
     */
    public function _makeLinkOperatorToken($recipient,$ticketCode)
    {
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();

        $ticketModel->setRecipientEmail($recipient)
                    ->setTicketCode($ticketCode);

        $ret = $ticketDAO->getUrlTokenByEmail($ticketModel);
        if(!$ret['status']){
            return false;
        }

        $token = $ret['push']['object']->getLinkToken();
        if($token && !empty($token))
            return "<a href='".$_ENV['HDK_URL']."/helpdezk/hdkTicket/viewTicket/{$ticketCode}/{$token}' target='_blank'>{$ticketCode}</a>";
        else
            return false ;
    }

    /**
     * en_us Replace text between tags and delete the tags
     * 
     * pt_br Substitui o texto entre as tags e deleta as tags
     *
     * @author Rogerio Albandes <rogerio.albandes@pipegrep.com.br>
     *
     * @param string $text     Original text
     * @param string $replace  New text
     * @param string $tag      Tag's string
     *
     * @return string           New text without tags
     */
    public function _replaceBetweenTags($text, $newText, $tag)
    {
        return  preg_replace("#(<{$tag}.*?>).*?(</{$tag}>)#", $newText , $text);
    }
    
    /**
     * en_us Replace text between tags and delete the tags
     * 
     * pt_br Substitui o texto entre as tags e deleta as tags
     *
     * @param  mixed $idModule
     * @param  mixed $mailSender
     * @param  mixed $sentTo
     * @param  mixed $subject
     * @param  mixed $body
     * @return array
     */
    function _saveTracker($idModule,$mailSender,$sentTo,$subject,$body): array
    {
        $trackerDAO = new trackerDAO();
        $trackerModel = new trackerModel();
        $trackerModel->setIdModule($idmodule)
                     ->setSender($mailSender)
                     ->setRecipient($senTo)
                     ->setSubject($subject)
                     ->setContent($body);

        $ret = $trackerDAO->insertTracker($trackerModel);
        if(!$ret['status']) {
            return array('status'=>false,'message'=>$ret['push']['message'],'idEmail'=>'');
        } else {
            return array('status'=>false,'message'=>'','idEmail'=>$ret['push']['object']->getIdEmail());
        }

    }
    
    /**
     * en_us Process email sending
     * 
     * pt_br Processa o envio de e-mail
     *
     * @param  object $mail
     * @param  array $params
     * @return array
     */
    public function _isEmailDone($mail,$params){
        try{
            $mail->send();
            $this->appEmailLogger->info("Email Succesfully Sent, {$params['msg']}",['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            $aRet = array("status"=>true,"message"=>"");
        }catch(Exception $e){
            $this->appEmailLogger->error("Error send email, {$params['msg']}",['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            $this->appEmailLogger->error("Error send email, {$params['msg2']}. Erro: {$mail->ErrorInfo}",['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            $this->appEmailLogger->info("Error send email, request # {$params['request']}. HOST: {$params['mailHost']} DOMAIN: {$params['mailDomain']} AUTH: {$params['mailAuth']} PORT: {$params['mailPort']} USER: {$params['mailUserName']} PASS: {$params['mailPassword']} SENDER: {$params['mailSender']}",['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            $aRet = array("status"=>false,"message"=>"{$mail->ErrorInfo}");
        }

        return $aRet;
    }
    
    /**
     * en_us Add the e-mail addresses for shipping
     * 
     * pt_br Adiciona os endereços de e-mail para envio
     *
     * @param  object $mail     Object phpmailer
     * @param  string $sentTo   Email's list
     * @return void
     */
    public function _makeSentTo($mail,$sentTo)
    {
        $aExist = array();
        if (preg_match("/;/", $sentTo)) {
            //$this->logIt('Entrou',7,'email');
            $aRecipient = explode(";", $sentTo);
            if (is_array($aRecipient)) {
                for ($i = 0; $i < count($aRecipient); $i++) {
                    // If the e-mail address is NOT in the array, it sends e-mail and puts it in the array
                    // If the email already has the array, do not send again, avoiding duplicate emails
                    if (!in_array($aRecipient[$i], $aExist)) {
                        $mail->AddAddress($aRecipient[$i]);
                        $aExist[] = $aRecipient[$i];
                    }
                }
            } else {
                $mail->AddAddress($aRecipient);
            }
        } else {
            $mail->AddAddress($sentTo);
        }
    }
    
    /**
     * en_us Sends an email using Mandrill API
     * pt_br Envia um e-mail usando a API do Mandrill
     *
     * @param  mixed $params
     * @return array
     */
    public function _sendMandrill(array $params)
    {
        $endPoint = $params['apiendpoint'];
        $token = $params['apikey'];

        $message = $this->_formatMandrillMessage($params);
        
        $params = array(
            "key" => $token,
            "message" => $message
        );
        
        $headers = [
            "Content-Type: application/json"
        ];
        $ch = curl_init();
        $ch_options = [
            CURLOPT_URL => $endPoint,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST    => 1,
            CURLOPT_HEADER  => 0,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($params)
        ];
        curl_setopt_array($ch,$ch_options);
        $callback = curl_exec($ch); 
        $result   = (($callback) ? json_decode($callback,true) : curl_error($ch));
        $aRet = ($result['status'] != 'error') ? array("status"=>true,"data"=>$result) : array("status"=>false,"data"=>$result);
        
        return $aRet;            
    }
        
    /**
     * en_us Returns the expiration date
     * 
     * pt_br Retorna a data de expiração
     *
     * @param  mixed $startDate
     * @param  mixed $days
     * @param  mixed $fullday
     * @param  mixed $noWeekend     Include weekends
     * @param  mixed $noHolidays    Include holidays
     * @return void
     */
    public function _getExpireDate($startDate=null,$days=null,$time=null,$timeType=null,$fullday=true,$noWeekend=false,$noHolidays=false,$companyID=null)
    {
        if(!isset($startDate)){$startDate = date("Y-m-d H:i:s");}
        
        if(!$days){
            $daysSum = "+0 day";
        }elseif($days > 0 or $days == 1){
            $daysSum = "+".$days." day";
        }else{
            $daysSum = "+".$days." days";
        }

        if(($time && $time > 0) && $timeType){
            if($timeType == "H"){
                $daysSum .= ($time > 0 && $time == 1) ? " {$time} hour" : " {$time} hours";
            }elseif($timeType == "M"){
                $daysSum .= ($time > 0 && $time == 1) ? " {$time} minute" : " {$time} minutes";
            }
        }
        
        $dataSum = date("Y-m-d H:i:s",strtotime($startDate." ".$daysSum));

        $dateHolyStart = date("Y-m-d",strtotime($startDate)); // Separate only the inicial date to check for holidays in the period
        $dateHolyEnd = date("Y-m-d",strtotime($dataSum)); //Separate only the final date to check for holidays in the period
        
        // Add holidays
        if(!$noHolidays){
            $sumDaysHolidays = $this->_getTotalHolidays($dateHolyStart,$dateHolyEnd);
            $sumDaysHolidays = ($sumDaysHolidays) ? $sumDaysHolidays : "0";
            
            $dataSum = ($sumDaysHolidays && $sumDaysHolidays > 1) 
            ? date("Y-m-d H:i:s",strtotime($dataSum." +".$sumDaysHolidays." days")) 
            : date("Y-m-d H:i:s",strtotime($dataSum." +".$sumDaysHolidays." day"));
        }
                
        // Working days
        $businessDays = $this->_getBusinessDays();
        if(!$businessDays)
            return false;
        
        $dateCheckStart = date("Y-m-d",strtotime($startDate));
        $dateCheckEnd = date("Y-m-d",strtotime($dataSum));
        $addNotBussinesDay = 0;
        
        // Non-working days
        if(!$noWeekend){
            while(strtotime($dateCheckStart) <= strtotime($dateCheckEnd)) {
                $numWeek = date('w',strtotime($dateCheckStart));
                if (!array_key_exists($numWeek,$businessDays)) {
                    $addNotBussinesDay++;
                }
                $dateCheckStart = date ("Y-m-d", strtotime("+1 day", strtotime($dateCheckStart)));
            }
        }
        
        $dataSum = date("Y-m-d H:i:s",strtotime($dataSum." +".$addNotBussinesDay." days")); // Add non-working days
        $dataCheckBD = $this->_checkValidBusinessDay($dataSum,$businessDays,$companyID);
        if(!$fullday){
            $dataSum = $this->_checkValidBusinessHour($dataCheckBD,$businessDays); // Verify if the time is the interval of service
        }
        
        // If you change the day, check to see if it is a working day
        if(strtotime(date("Y-m-d",strtotime($dataCheckBD))) != strtotime(date("Y-m-d",strtotime($dataSum)))){
            $dataCheckBD = $this->_checkValidBusinessDay($dataSum,$businessDays,$companyID);
            return $dataCheckBD;
        }else{
            return $dataSum;
        }

    }

    public function _checkValidBusinessDay($date,$businessDay,$companyID=null)
    {
        $numWeek = date('w',strtotime($date));

        $i = 0;
        while($i == 0){
            
            while (!array_key_exists($numWeek, $businessDay)) {
                $date = date ("Y-m-d H:i:s", strtotime("+1 day", strtotime($date)));
                $numWeek = date('w',strtotime($date));
            }
            $dateHoly = date("Y-m-d",strtotime($date));

            $daysHoly = $this->_getTotalHolidays($dateHoly,$dateHoly,$companyID);
            if(!$daysHoly)
                $i = 1;
                
            
            if($daysHoly > 0){
                $date = date("Y-m-d H:i:s",strtotime($date." +".$daysHoly." days"));
                $numWeek = date('w',strtotime($date));
            }
        }
        
        return $date;
    }

    public function _checkValidBusinessHour($date,$businessDay){
        $i = 0;
        while($i == 0){
            $numWeek = date('w',strtotime($date));
            $hour = strtotime(date('H:i:s',strtotime($date)));
            $begin_morning = strtotime($businessDay[$numWeek]['begin_morning']);
            $end_morning = strtotime($businessDay[$numWeek]['end_morning']);
            $begin_afternoon = strtotime($businessDay[$numWeek]['begin_afternoon']);
            $end_afternoon = strtotime($businessDay[$numWeek]['end_afternoon']);
            if($hour >= $begin_morning && $hour <= $end_morning){
                $i = 1;
            }
            else if($hour >= $begin_afternoon && $hour <= $end_afternoon){
                $i = 1;
            }
            else{
                $date = date ("Y-m-d H:i:s", strtotime("+1 hour", strtotime($date)));
                $i = 0;
            }
        }
        return $date;
    }

    public function _getTotalHolidays($startDate,$endDate,$companyID=null)
    {
        $holidayDAO = new holidayDAO();
        $holidayModel = new holidayModel();
        $holidayModel->setStartDate($startDate)
                     ->setEndDate($endDate);
        
        $rsNationalDaysHoliday = $holidayDAO->getNationalHolidaysTotal($holidayModel); // Verifies the quantity of holidays in the period
        
        if(!$rsNationalDaysHoliday['status'])
            return false;

        if($companyID){
            $rsNationalDaysHoliday['push']['object']->setIdCompany($companyID);

            $rsCompanyDaysHoliday = $holidayDAO->getCompanyDaysHoliday($rsNationalDaysHoliday['push']['object']); // Verifies the quantity of company�s holidays in the period
            if(!$rsCompanyDaysHoliday['status'])
                return false;

            $sumDaysHolidays = $rsCompanyDaysHoliday['push']['object']->getTotalNational() + $rsCompanyDaysHoliday['push']['object']->getTotalCompany();
        }else{
            $sumDaysHolidays = $rsNationalDaysHoliday['push']['object']->getTotalNational();
        }
        
        return $sumDaysHolidays;
    }

    public function _getBusinessDays()
    {
        $expireDateDAO = new expireDateDAO();
        $expireDateModel = new expireDateModel();

        $ret = $expireDateDAO->fetchBusinessDays($expireDateModel); // Verifies the quantity of holidays in the period
        
        if(!$ret['status'])
            return false;

        foreach($ret['push']['object']->getBusinessDays() as $k=>$v){
            $businessDay[$v['num_day_week']] = array(
                "begin_morning" 	=> $v['begin_morning'],
                "end_morning" 		=> $v['end_morning'],
                "begin_afternoon" 	=> $v['begin_afternoon'],
                "end_afternoon" 	=> $v['end_afternoon']
            );
        }
        
        return $businessDay;
    }
    
    /**
     * Reduce a string
     *
     * @param  mixed $string The text to reduce
     * @param  mixed $lenght Lenght of new string
     * @return string
     */
    public function _reduceText(string $string, int $lenght): string
    {
        $string = strip_tags($string);
        $string = substr($string, 0, $lenght) . "...";
        return $string;
    }

    public function _checkFile($file)
    {
        if(!is_file($file)){
            $this->applogger->info("File: {$file} does not exists, I will try to create it.",['Class' => __CLASS__, 'Method' => __METHOD__]);
            
            file_put_contents($file, ""); 
            if(is_file($file)) {
                return true;
            } else {
                $this->applogger->error("I could not create the file: {$file}",['Class' => __CLASS__, 'Method' => __METHOD__]);
                return false;
            }
        } else {
            return true;
        }
    }

    public function _formatLanguageParam($langName)
    {
        $aLang = explode("_",$langName);
        $aLang[1] = strtoupper($aLang[1]);

        return implode("-",$aLang);
    }
    
    /**
     * _request
     *
     * @param  mixed $type
     * @param  mixed $request
     * @param  mixed $args
     * @return void
     */
    public function _request($type, $request, $args = false) {
        if (!$args) {
            $args = array();
        } elseif (!is_array($args)) {
            $args = array($args);
        }

        $url = $request;
        
        $c = curl_init();
        curl_setopt($c, CURLOPT_HEADER, 0);
        curl_setopt($c, CURLOPT_VERBOSE, 0);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);

        if (count($args)) curl_setopt($c, CURLOPT_POSTFIELDS , http_build_query($args));

        switch ($type) {
            case 'POST':
                curl_setopt($c, CURLOPT_POST, 1);
                break;
            case 'GET':
                curl_setopt($c, CURLOPT_HTTPGET, 1);
                break;
            default:
                curl_setopt($c, CURLOPT_CUSTOMREQUEST, $type);
        }
        
        $data = curl_exec($c);
        
        if(!curl_errno($c)) {
            $info = curl_getinfo($c);
            if ($info['http_code'] == 401) {
                $message = 'Got error, http code: ' . $info['http_code'] . ' - ' . $this->_getHttpErrorCode($info['http_code']) ;
                $arrayRet = array('success' => false, 'message' => $message, 'return' => '');
            } else {
                $res = json_decode($data,true);
                $st = $res['status'] ? true : false;
                $aDat = $res['status'] ? $res['result'] : '';
                $msg = $res['status'] ? '' : $res['message'];
                
                $arrayRet = array('success' => $st, 'message' => $msg, 'return' => $aDat);
            }

        } else {
            $message = 'Error making API request, curl error: ' . $this->_getCurlErrorCode(curl_error($c));
            $arrayRet = array('success' => false, 'message' => $message, 'return' => '');
        }

        curl_close($c);

        return $arrayRet;
    }

    public function _getHttpErrorCode($code)
    {
        $http_codes = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            103 => 'Checkpoint',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Unordered Collection',
            426 => 'Upgrade Required',
            449 => 'Retry With',
            450 => 'Blocked by Windows Parental Controls',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended'
        );
        return $http_codes[$code];
    }

    public function _getCurlErrorCode($code)
    {
        $curl_error_codes = array (
            0 => 'CURLE_OK',
            1 => 'CURLE_UNSUPPORTED_PROTOCOL',
            2 => 'CURLE_FAILED_INIT',
            3 => 'CURLE_URL_MALFORMAT',
            4 => 'CURLE_NOT_BUILT_IN',
            5 => 'CURLE_COULDNT_RESOLVE_PROXY',
            6 => 'CURLE_COULDNT_RESOLVE_HOST',
            7 => 'CURLE_COULDNT_CONNECT',
            8 => 'CURLE_FTP_WEIRD_SERVER_REPLY',
            9 => 'CURLE_REMOTE_ACCESS_DENIED',
            10 => 'CURLE_FTP_ACCEPT_FAILED',
            11 => 'CURLE_FTP_WEIRD_PASS_REPLY',
            12 => 'CURLE_FTP_ACCEPT_TIMEOUT',
            13 => 'CURLE_FTP_WEIRD_PASV_REPLY',
            14 => 'CURLE_FTP_WEIRD_227_FORMAT',
            15 => 'CURLE_FTP_CANT_GET_HOST',
            17 => 'CURLE_FTP_COULDNT_SET_TYPE',
            18 => 'CURLE_PARTIAL_FILE',
            19 => 'CURLE_FTP_COULDNT_RETR_FILE',
            21 => 'CURLE_QUOTE_ERROR',
            22 => 'CURLE_HTTP_RETURNED_ERROR',
            23 => 'CURLE_WRITE_ERROR',
            25 => 'CURLE_UPLOAD_FAILED',
            26 => 'CURLE_READ_ERROR',
            27 => 'CURLE_OUT_OF_MEMORY',
            28 => 'CURLE_OPERATION_TIMEDOUT',
            30 => 'CURLE_FTP_PORT_FAILED',
            31 => 'CURLE_FTP_COULDNT_USE_REST',
            33 => 'CURLE_RANGE_ERROR',
            34 => 'CURLE_HTTP_POST_ERROR',
            35 => 'CURLE_SSL_CONNECT_ERROR',
            36 => 'CURLE_BAD_DOWNLOAD_RESUME',
            37 => 'CURLE_FILE_COULDNT_READ_FILE',
            38 => 'CURLE_LDAP_CANNOT_BIND',
            39 => 'CURLE_LDAP_SEARCH_FAILED',
            41 => 'CURLE_FUNCTION_NOT_FOUND',
            42 => 'CURLE_ABORTED_BY_CALLBACK',
            43 => 'CURLE_BAD_FUNCTION_ARGUMENT',
            45 => 'CURLE_INTERFACE_FAILED',
            47 => 'CURLE_TOO_MANY_REDIRECTS',
            48 => 'CURLE_UNKNOWN_OPTION',
            49 => 'CURLE_TELNET_OPTION_SYNTAX',
            51 => 'CURLE_PEER_FAILED_VERIFICATION',
            52 => 'CURLE_GOT_NOTHING',
            53 => 'CURLE_SSL_ENGINE_NOTFOUND',
            54 => 'CURLE_SSL_ENGINE_SETFAILED',
            55 => 'CURLE_SEND_ERROR',
            56 => 'CURLE_RECV_ERROR',
            58 => 'CURLE_SSL_CERTPROBLEM',
            59 => 'CURLE_SSL_CIPHER',
            60 => 'CURLE_SSL_CACERT',
            61 => 'CURLE_BAD_CONTENT_ENCODING',
            62 => 'CURLE_LDAP_INVALID_URL',
            63 => 'CURLE_FILESIZE_EXCEEDED',
            64 => 'CURLE_USE_SSL_FAILED',
            65 => 'CURLE_SEND_FAIL_REWIND',
            66 => 'CURLE_SSL_ENGINE_INITFAILED',
            67 => 'CURLE_LOGIN_DENIED',
            68 => 'CURLE_TFTP_NOTFOUND',
            69 => 'CURLE_TFTP_PERM',
            70 => 'CURLE_REMOTE_DISK_FULL',
            71 => 'CURLE_TFTP_ILLEGAL',
            72 => 'CURLE_TFTP_UNKNOWNID',
            73 => 'CURLE_REMOTE_FILE_EXISTS',
            74 => 'CURLE_TFTP_NOSUCHUSER',
            75 => 'CURLE_CONV_FAILED',
            76 => 'CURLE_CONV_REQD',
            77 => 'CURLE_SSL_CACERT_BADFILE',
            78 => 'CURLE_REMOTE_FILE_NOT_FOUND',
            79 => 'CURLE_SSH',
            80 => 'CURLE_SSL_SHUTDOWN_FAILED',
            81 => 'CURLE_AGAIN',
            82 => 'CURLE_SSL_CRL_BADFILE',
            83 => 'CURLE_SSL_ISSUER_ERROR',
            84 => 'CURLE_FTP_PRET_FAILED',
            85 => 'CURLE_RTSP_CSEQ_ERROR',
            86 => 'CURLE_RTSP_SESSION_ERROR',
            87 => 'CURLE_FTP_BAD_FILE_LIST',
            88 => 'CURLE_CHUNK_FAILED',
            89 => 'CURLE_NO_CONNECTION_AVAILABLE'
        );
        return $curl_error_codes[$code];
    }

    public function _formatTextFill($value, $size, $fill=' ')
    {
        if(strlen($value) <= $size){
            $value = "{$fill}{$value}";
            return $this->_formatTextFill($value, $size, $fill);
        }else{
            return $value;
        }
    }
    
    /**
     * Make JWT for api connections
     *
     * @param  array $payload
     * @param  string $expirationTime
     * @return void
     */
    public function _makeJWT(array $payload)
    {
        if(!isset($_ENV['JWT_SECRET_KEY']) || empty($_ENV['JWT_SECRET_KEY'])){
            $this->applogger->error("JWT secret key not found",['Class' => __CLASS__, 'Method' => __METHOD__]);
            return false;
        }

        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        $jwt = JWT::encode($payload,$_ENV['JWT_SECRET_KEY'],'HS256');

        return $jwt; //token to be written to the database
    }
    
    /**
     * Converts URI request string in array
     *
     * @param  string $request URI Request
     * @return array
     */
    public function _makeRequestParams($request){
        $requestData = explode("?",$request);
        $requestData = explode("&",$requestData[1]);

        $aRet = array();
        foreach($requestData as $k=>$v){
            $bus = explode("=",$v);
            $aRet[$bus[0]] = $bus[1];
        }

        return $aRet;
    }
    
    /**
     * Returns the maximum amount of files allowed to upload for notes
     *
     * @return void
     */
    public function _getNoteAttMaxFiles()
    {
        if (version_compare($this->_getHelpdezkVersionNumber(), '1.0.1', '>' )) {
            return 5;
        } else {
            return 1;
        }
    }
    
    /**
     * Returns a list of allowed files to upload
     *
     * @return void
     */
    public function _getAcceptedFiles()
    {
        // Images
        $images = '.jpg, .jpeg, .png, .gif';
        // Documents
        $documents = '.pdf, .doc, .docx, .ppt, .pptx, .pps, .ppsx, .odt, .xls, .xlsx, .zip';
        // Audio
        $audio = '.mp3, .m4a, .ogg, .wav';
        // Video
        $video = '.mp4, .m4v, .mov, .wmv, .avi, .mpg, .ogv, .3gp, .3g2';

        return $images .','.$documents.','.$audio.','.$video ;
    }

    /**
     * Returns the maximum amount of files allowed to upload for new tickets
     *
     * @return void
     */
    public function _getTicketAttMaxFiles()
    {
        
        if (version_compare($this->_getHelpdezkVersionNumber(), '1.0.1', '>' )) {
            return 10;
        } else {
            return 1;
        }
    }
    
    /**
     * Returns the maximum file size to upload
     *
     * @return void
     */
    public function _getTicketAttMaxFileSize()
    {
        return ini_get('upload_max_filesize');
    }
    
    /**
     * _createRequestCode
     *
     * @param  mixed $prefix
     * @return void
     */
    public function _createRequestCode($prefix="hdk")
    {
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $ticketModel->setTablePrefix($prefix);
    
        $ret = $ticketDAO->getLastTicketCode($ticketModel);

        if(!$ret['status'])
           return $false;
          
        $lastCode = $ret['push']['object']->getLastTicketCode();

        if ($lastCode > 0)        
        {
            $up = $ticketDAO->increaseTicketCode($ticketModel);
            if(!$up['status'])
                return false;
        } else {
            $ins = $ticketDAO->createTicketCode($ticketModel);
            if (!$ins['status'])
                return false;
        }

        $retLast = $ticketDAO->getLastTicketCode($ticketModel);
        if(!$retLast['status'])
           return false;
        
        $lastCode = $retLast['push']['object']->getLastTicketCode();
        
        $ticketCode = date("Ym") . str_pad($lastCode, 6, '0', STR_PAD_LEFT);

        return $ticketCode;
    }

    /**
     * en_us Destroys the session and sends it to the login page, used for unauthorized access.
     *
     * pt_br Destrói a sessão e a envia para a página de login, usada para acesso não autorizado.
     * 
     * @return void
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     *
     */
    public function _accessDenied()
    {
        $this->_sessionDestroy();
        header('Location:' . $_ENV['HDK_URL'] . '/admin/login');
    }

    /**
     * en_us Writes the data for sending the e-mail to the tbemailcron table.
     *
     * pt_br Grava na tabela tbemailcron os dados para envio de e-mail.
     * 
     * @return void
     *
     */
    public function _saveEmailCron($idModule,$ticketCode,$tag)
    {
        $emailSrvDAO = new emailServerDAO();
        $emailSrvModel = new emailServerModel();

        $where = "WHERE a.default = 'Y'";
        $ret = $emailSrvDAO->queryEmailServers($where);

        if(!$ret['status']){
            $this->applogger->error("No result returned",['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        }

        $aEmailSrv = $ret['push']['object']->getGridList();
        $idServer = !empty($aEmailSrv[0]['idemailserver']) ? $aEmailSrv[0]['idemailserver'] : 1;

        $emailSrvModel->setIdEmailServer($idServer)
                      ->setIdModule($idModule)
                      ->setCode($ticketCode)
                      ->setTag($tag);
        
        $insCron = $emailSrvDAO->insertEmailCron($emailSrvModel);
        if(!$insCron['status']){
            return array("status"=>false,"message"=>$insCron['message']);
        }else{
            return array("status"=>true,"message"=>"");
        }
    }
    
    /**
     * Uppercase the first character of each word in a string with a Roman numeral
     *
     * @param  mixed $string
     * @return void
     */
    public function _formatStringWithRomanNumeral($string){
        $split = explode(' ',$string);
        $pattern = '(\b(?:M{1,4})?(?:CM|CD|D?C{1,3})?(?:XC|XL|L?X{1,3})?(?:IX|IV|V?I{1,3})?\b)';
        preg_match_all($pattern,$string,$matches);
        $matches = $matches[0];
        $newString = "";
        
        foreach($split as $k=>$v){            
            if(!in_array($v,$matches)){
                $newString .= ucwords(strtolower($v))." ";
            }else{
                $newString .= $v." ";
            }
        }

        return trim($newString);
    }

    public function _monthInLetterBrPortuguese($month){
        switch($month){
            case '01':
                $month = 'Janeiro';
                break;
            case '02':
                $month = 'Fevereiro';
                break;
            case '03':
                $month = 'Março';
                break;
            case '04':
                $month = 'Abril';
                break;
            case '05':
                $month = 'Maio';
                break;
            case '06':
                $month = 'Junho';
                break;
            case '07':
                $month = 'Julho';
                break;
            case '08':
                $month = 'Agosto';
                break;
            case '09':
                $month = 'Setembro';
                break;
            case '10':
                $month = 'Outubro';
                break;
            case '11':
                $month = 'Novembro';
                break;
            case '12':
                $month = 'Dezembro';
                break;
        }

        return $month;
    }
    
    /**
     * Checks if the extension was loaded
     *
     * @param  mixed $extension
     * @return void
     */
    public function _checkExtensionLoaded(string $extension){
        if (extension_loaded($extension)) {
            $this->applogger->info(strtoupper($extension)." support is loaded ",['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return true;
        }else{
            $this->applogger->error(strtoupper($extension)." support is NOT loaded ",['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
    }

    /**
     * Checks if the function exists
     *
     * @param  mixed $function
     * @return void
     */
    public function _checkFunctionExists(string $function){
        if (function_exists($function)) {
            $this->applogger->info("{$function} function support is available ",['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return true;
        }else{
            $this->applogger->error("{$function} support is NOT available ",['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
    }
    
    /**
     * en_us Formats the message to send by Mandrill API
     * pt_br Formata a mensagem a ser enviada pela API do Mandrill
     *
     * @param  array $params
     * @return array
     */
    public function _formatMandrillMessage(array $params): array
    {
        $aAttachments = array();
        foreach($params['attachment'] as $key=>$value){
            $bus = array(
                'type' => mime_content_type($value['filepath']),
                'name' => $value['filename'],
                'content' => base64_encode(file_get_contents($value['filepath']))
            );
    
            array_push($aAttachments,$bus);
        }

        $params['to'] = array();    
        foreach ($params['address'] as $key => $sendEmailTo) {
            $busAddress = array(
                'email' => $sendEmailTo['to_address'],
                'name' => $sendEmailTo['to_name'],
                'type' => 'to'
            );
            array_push($params['to'],$busAddress);
        }
    
        $message = array(
            'html' => $params['contents'],
            'subject' => $params['subject'],
            'from_email' => $params['sender'],
            'from_name' => $params['sender_name'],
            'to' => $params['to'],
            'headers' => $params['extra_headers'],
            'important' => false,
            'track_opens' => null,
            'track_clicks' => null,
            'auto_text' => null,
            'auto_html' => null,
            'inline_css' => null,
            'url_strip_qs' => null,
            'preserve_recipients' => null,
            'view_content_link' => null,
            'tracking_domain' => null,
            'signing_domain' => null,
            'return_path_domain' => null,
            'merge' => true,
            'merge_language' => 'mailchimp',
            'global_merge_vars' => $params['global_merge_vars'],
            'merge_vars' => $params['merge_vars'],
            'tags' => $params['tags'],
            'google_analytics_domains' => $params['analytics_domains'],
            'google_analytics_campaign' => 'teste',
            'metadata' => $params['metadata'],
            'recipient_metadata' => $params['recipient_metadata'],
            'attachments' => $aAttachments,
            'images' => $params['images']
        );

        return $message;
    }
    
    /**
     * en_us Removes special characters, accents, whitespaces
     * pt_br Remove caracteres especiais, acentos, espaços em branco
     *
     * @param  mixed $string
     * @return string
     */
    public function _clearAccent($string)
    {
        $forbidden = Array(",",".","'","\"","&","|","!","#","$","¨","*","(",")","`","´","<",">",";","=","+","§","{","}","[","]","^","~","?","%","°","º");
        $special =  Array('Á','È','ô','Ç','á','è','Ò','ç','Â','Ë','ò','â','ë','Ø','Ñ','À','Ð','ø','ñ','à','ð','Õ','Å','õ','Ý','å','Í','Ö','ý','Ã','í','ö','ã','Î','Ä','î','Ú','ä','Ì','ú','Æ','ì','Û','æ','Ï','û','ï','Ù','®','É','ù','©','é','Ó','Ü','Þ','Ê','ó','ü','þ','ê','Ô','ß','‘','’','‚','“','”','„');
        $clearspc = Array('A','E','o','C','a','e','o','c','A','E','o','a','e','o','N','A','D','o','n','a','o','O','A','o','y','a','I','O','y','A','i','o','a','I','A','i','U','a','I','u','A','i','U','a','I','u','i','U','','E','u','c','e','O','U','p','E','o','u','b','e','O','b','','','','','','');
        
        $newString = str_replace($special, $clearspc, $string);
        $newString = str_replace($forbidden, "", trim($newString));
        $newString = str_replace(" ", "_", $newString);
        
        return strtolower($newString);
    }
    
    /**
     * en_us Get user IP
     * pt_br Obtem o IP do usuário
     *
     * @return void
     */
    public function _getUserIpAddress(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * en_us Formats IP string to his numeric representation to save into DB
     * pt_br Formata a string IP para sua representação numérica para salvar no banco de dados
     *
     * @param  mixed $ip    IP dotted-quad representation
     * @return string
     */
    public function _formatIpToLong($ip)
    {
        if(strpos($ip, '/') !== false){
            list($ipTmp, $netMask) = explode('/', $ip, 2);
            $aTmp = explode(".",$ipTmp);
            $size = sizeof($aTmp);
            $aTmp[($size - 1)] = 1;
            $aTmp2 = $aTmp;
            $aTmp2[($size - 1)] = 255;

            $newIp = ip2long(implode(".",$aTmp)) . "-" . ip2long(implode(".",$aTmp2));
        }elseif(strpos($ip, '-') !== false){
            list($aTmp, $aTmp2) = explode('-', $ip);
            
            $newIp = ip2long($aTmp) . "-" . ip2long($aTmp2);
        }else{
            $newIp = ip2long($ip);
        }
        
        return $newIp;
    }
}