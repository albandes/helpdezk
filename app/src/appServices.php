<?php

namespace App\src;

use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\admin\dao\mysql\logoDAO;
use App\modules\admin\dao\mysql\vocabularyDAO;

use App\modules\admin\models\mysql\logoModel;
use App\modules\admin\models\mysql\moduleModel;
use App\modules\admin\models\mysql\vocabularyModel;

use App\modules\admin\src\loginServices;
use App\src\localeServices;


use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

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

    public function _getHelpdezkVersion(): string
    {
        // Read the version.txt file
        $versionFile = $this->_getHelpdezkPath() . "/version.txt";

        if (is_readable($versionFile)) {
            $info = file_get_contents($versionFile, FALSE, NULL, 0, 50);
            if ($info) {
                return trim($info);
            } else {
                return '1.0';
            }
        } else {
            return '1.0';
        }

    }

    public function _getHelpdezkPath()
    {
        $pathInfo = pathinfo(dirname(__DIR__));
        return $pathInfo['dirname'];
    }
    
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
    
    public function _getLayoutTemplate()
    {
        return $this->_getHelpdezkPath().'/app/modules/main/views/layout.latte';
    }
    
    public function _getNavbarTemplate()
    {
        return $this->_getHelpdezkPath().'/app/modules/main/views/nav-main.latte';
    }
    
    public function _getFooterTemplate()
    {
        return $this->_getHelpdezkPath().'/app/modules/main/views/footer.latte';
    }
    
    public function _getDefaultParams(): array
    {
        $loginSrc = new loginServices();
        $aHeader = $this->_getHeaderData();

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
            "adminlogo"         => $this->imgBucket.'adm_header.png',
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
            "vocabulary"     => $this->_loadVocabulary()
        );
    }

    public function _getHelpdezkVersionNumber()
    {
        $exp = explode('-', $this->_getHelpdezkVersion());
        return $exp[2];
    }

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

        $logoModel->setName("header");
        $logo = $logoDAO->getLogoByName($logoModel);
		
        if(!$logo['status']){ //(empty($objLogo->getFileName()) or !){
            $image 	= $this->imgBucket . 'default/header.png';
			$width 	= "227";
			$height = "70";
        }else{
            $objLogo = $logo['push']['object'];            
            
            if($this->saveMode == 'disk'){
                $pathLogoImage = $this->imgDir . $objLogo->getFileName();
                $st = file_exists($pathLogoImage) ? true : false;
            }elseif($this->saveMode == "aws-s3"){
                $pathLogoImage = $this->imgBucket . $objLogo->getFileName();
                $st = (@fopen($pathLogoImage, 'r')) ? true : false; 
            }

            if(!$st){
                $image 	= $this->imgBucket . 'default/header.png';
                $width 	= "227";
                $height = "70";
            }else{
                $image 	= $this->imgBucket . $objLogo->getFileName();
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
                        $aRet[] = array(
                            'idmodule' => $v['idmodule'],
                            'path' => $v['path'],
                            'class' => $v['class'],
                            'headerlogo' => $this->imgBucket.$v['headerlogo'],
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
        
        return date($_ENV["SCREEN_DATE_FORMAT"],strtotime($date));
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
        switch($_ENV['LOG_LEVEL']){
            case 'INFO':
                $stream = new StreamHandler($_ENV['LOG_FILE'], Logger::INFO);
                break;
            case 'NOTICE':
                $stream = new StreamHandler($_ENV['LOG_FILE'], Logger::NOTICE);
                break;
            case 'WARNING':
                $stream = new StreamHandler($_ENV['LOG_FILE'], Logger::WARNING);
                break;
            case 'ERROR':
                $stream = new StreamHandler($_ENV['LOG_FILE'], Logger::ERROR);
                break;
            case 'CRITICAL':
                $stream = new StreamHandler($_ENV['LOG_FILE'], Logger::CRITICAL);
                break;
            case 'ALERT':
                $stream = new StreamHandler($_ENV['LOG_FILE'], Logger::ALERT);
                break;
            case 'EMERGENCY':
                $stream = new StreamHandler($_ENV['LOG_FILE'], Logger::EMERGENCY);
                break;
            default:
                $stream = new StreamHandler($_ENV['LOG_FILE'], Logger::DEBUG);
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
            }
        }

        if (!is_writable($path)) {
            $this->applogger->info('Directory: '. $path.' is not writable, I will try to make it writable',['Class' => __CLASS__, 'Method' => __METHOD__]);
            if (!chmod($path,0777)){
                $this->applogger->error("Directory: $path is not writable!!",['Class' => __CLASS__, 'Method' => __METHOD__]);
                return false;
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
        $moduleID = $moduleDAO->getModuleInfoByName($moduleName);

        return (!is_null($moduleID) && !empty($moduleID)) ? $moduleID->getIdModule() : 0;
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

}