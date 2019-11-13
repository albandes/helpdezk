<?php

require_once(HELPDEZK_PATH . '/app/modules/admin/controllers/admCommonController.php');


class Home extends admCommon {

    /**
     * Create an instance, check session time
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );

        //
        $this->modulename = 'admin' ;
        //

        $id = $this->getIdModule($this->modulename) ;
        if(!$id) {
            die('Module don\'t exists in tbmodule !!!') ;
        } else {
            $this->idmodule = $id ;
        }

        /*$this->loadModel('home_model');
        $dbHome = new home_model();
        $this->dbHome = $dbHome;

        $this->loadModel('ticket_model');
        $dbTicket = new ticket_model();
        $this-$dbTicket = $dbTicket;*/
    }

    public function index() {
        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('adm-main.tpl');
    }

	public function systemUpdate(){
		$smarty = $this->retornaSmarty();
        $smarty->assign('last_version', $this->get_last_version());
        $smarty->assign('instaled_version', $this->get_instaled_version());
		$smarty->display('modais/home/systemupdate.tpl.html');
	}

    /**
     *
     * Checks the available software versions, and proceeds to update.
     * Used by modal (systemUpdate).
     *
     * @access public
     * @param NULL
     * @return bollean
     */
    public function systemUpdateGoLive()
    {

        $logfile = 'logs/upgrade.log' ;
        $print_date = str_replace("%","", $this->getConfig('date_format')) . " " . str_replace("%","", $this->getConfig('hour_format'));
        $log_date = "[".date($print_date)."]" ;

        $str = file_get_contents('http://helpdezk.org/releases/getversions.php');
        $json = json_decode($str, true);
        $current = $this->get_instaled_version() ;

        $version = substr($current,0,strpos($current, 'rev')) ;
        $major = substr($version,strrpos($version, "-")+1);
        $major = str_replace('.','',$major);

        foreach ($json as $key => $value) {
            foreach ($value as $key => $patch) {
                $val = str_replace('.','',$patch);
                if((int)$val > (int)$major) {
                    //$ret = $this->systemUpdateFtp('helpdezk-community-patch-'.$patch.'.zip',$log_date,$logfile) ;
                    //if(!$ret) return false;
                    $ret = $this->systemUpdateUnpack('helpdezk-community-patch-'.$patch.'.zip',$log_date,$logfile) ;
                    if(!$ret) return false;
                    $ret = $this->systemUpdateDBScript($patch,$log_date,$logfile) ;
                    if(!$ret) return false;
                }
            }
        }

    }

    /*
     * Run the database script
     *
     * @access public
     * @param $version The version name
     * @param string $log_date Date to write in log file
     * @param string $logfile Log file name
     *
     * @return bollean
     */
    public function systemUpdateDBScript($version,$log_date,$logfile)
    {
        $DB = new home_model();
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $dbname="mysql";
        } elseif ($database == 'oci8po') {
            $dbname="oracle";
        }

        $sqlFileToExecute =  "upgrade/helpdezk-community-".$dbname."-".$version.".sql";

        if(!file_exists($sqlFileToExecute)) {
            $this->saveLog($log_date . " Don't exists data base upgrade file ",$logfile);
            return true ;
        }

        // Load and explode the sql file
        $f = fopen($sqlFileToExecute,"r+");
        $sqlFile = fread($f,filesize($sqlFileToExecute));
        $sqlArray = explode('-- [PIPE]',$sqlFile);
        $sqlArray=array_filter($sqlArray);

        $i=0;
        //Process the sql file by statements
        foreach ($sqlArray as $stmt) {
            //$stmt = 	base64_decode($stmt);
            if (strrpos($stmt	, "/*"))continue;
            if (strlen(trim($stmt))>3){
                $a_result = $DB->systemUpdateExecute($stmt);
                if (!$a_result['ret']){
                    $this->saveLog($log_date . " Error run db upgrade script: " . $a_result['msg'] . "\r\n" . $stmt, $logfile);
                    $i++;
                }
            }
        }

        if ($i==0)  {
            $this->saveLog($log_date . " Upgrade script successfully ran: " .  $sqlFileToExecute, $logfile);
            return true;
        } else {
            return false ;
        }
    }

    /*
     * Unpack the patch file
     *
     * @access public
     * @param string $local_file Patch version to unpack
     * @param string $log_date Date to write in log file
     * @param string $logfile Log file name
     *
     * @return bollean True if download the patch file
     */
    public function systemUpdateUnpack($local_file,$log_date,$logfile)
    {
        require_once('includes/classes/pclzip/pclzip.lib.php'); //include class
        $archive = new PclZip($local_file);
        if ($archive->extract() != 0) {
            $list = $archive->listContent();
            for ($i=0; $i<sizeof($list); $i++) {
                if($list[$i]['folder']) continue ;
                $this->saveLog($log_date . " Extract file: " . $list[$i]['filename'] , $logfile);
            }
            return true;
        } else {
            $this->saveLog($log_date . " Error extract files: " . $archive->errorInfo(true)  , $logfile);
            return false;
        }

    }
    /*
     * Download the helpdezk patch
     *
     * @access public
     * @param string $server_file Patch version to download
     * @param string $log_date Date to write in log file
     * @param string $logfile Log file name
     * @return bollean True if download the patch file
     */
    public function systemUpdateFtp($server_file,$log_date,$logfile)
    {
        $ftp_server     =   "ftp.helpdezk.org";
        $ftp_user_name  =   "anonymous@helpdezk.org";
        $ftp_user_pass  =   "";

        $local_file = $server_file;

        // set up a connection or die
        $conn_id = ftp_connect($ftp_server);
        if (!$conn_id)  {
            $this->saveLog($log_date . " Couldn't connect to $ftp_server" , $logfile);
            return false ;
        }

        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
        if (!$login_result) {
            $this->saveLog($log_date . " Couldn't connect with anonymous login" , $logfile) ;
            return false ;
        }

        if (!ftp_chdir($conn_id, "upgrades"))  {
            $this->saveLog($log_date . " Couldn't change to upgrades directory" , $logfile) ;
            return false;
        }

        ftp_pasv($conn_id, true);

        if (!ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
            $this->saveLog($log_date . " Couldn't download the file ". $server_file , $logfile) ;
            return false ;
        }

        ftp_close($conn_id);
        $this->saveLog($log_date . " Download the file ". $server_file , $logfile) ;
        return true;
    }

    public function logout() {
        $this->sessionDestroy();
        header('Location:' . path . '/admin/login');
    }

    public function check_release() {
        if (path == "/..") {
            $path_releases = DOCUMENT_ROOT;
        } else {
            $path_releases = DOCUMENT_ROOT . path;
        }
		if(substr($path_releases, -1)!= '/'){
			$path_releases = $path_releases.'/';
		} 	
        $arq_date = file_get_contents($path_releases . 'logs/releases.txt');
        $column = explode("|", $arq_date);

        if ($column[0] == date("Y-m-d")) {
            echo false;
        } else {
            //$fp = fopen($path_releases . "logs/releases.txt", "w");
            $date = date("Y-m-d");
            fwrite($fp, $date . '|');
            fclose($fp);
            echo true;
        }
    }

    public function current_release() {
        if (path == "/..") {
            $path_releases = DOCUMENT_ROOT;
        } else {
            $path_releases = DOCUMENT_ROOT . path;
        }
		if(substr($path_releases, -1)!='/'){
			$path_releases=$path_releases.'/';
		} 	
        //$fp = fopen($path_releases . "logs/releases.txt", "w");
        $date = date("Y-m-d");
        fwrite($fp, $date . '|' . $_POST['release']);
        fclose($fp);
    }


    public function get_last_release()
    {
        if (path == "/..") {
            $path_releases = DOCUMENT_ROOT;
        } else {
            $path_releases = DOCUMENT_ROOT . path;
        }
		if(substr($path_releases, -1)!='/'){
			$path_releases=$path_releases.'/';
		} 			
        $arq_date = file_get_contents($path_releases . 'logs/releases.txt');
        $column = explode("|", $arq_date);
        echo $column[1];
    }


    /*
     * Get the name of the installed version of helpdezk
     *
     * @access public
     * @param
     *
     * @return string Name of the installed version
     */
    function get_instaled_version()
    {
        $current = file_get_contents('version.txt');
        $current = preg_replace("/[\\n\\r]+/", "", $current);
        return $current ;
    }
    
    // Used by modal:  "update version"
    public function get_last_version()
    {
        if (path == "/..") {
            $path_releases = DOCUMENT_ROOT;
        } else {
            $path_releases = DOCUMENT_ROOT . path;
        }
        if(substr($path_releases, -1)!='/')
            $path_releases=$path_releases.'/';

        $arq_date = file_get_contents($path_releases . 'logs/releases.txt');
        $column = explode("|", $arq_date);
        return $column[1];
    }


/*
    public function sessionDestroy() {
        session_start();
        session_unset();
        session_destroy();
    }
    
  */
}

?>
