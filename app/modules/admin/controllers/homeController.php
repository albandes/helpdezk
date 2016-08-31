<?php

session_start();

class Home extends Controllers {

    public function index() {
        $this->validasessao();
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();

        if (isset($_SESSION['SES_COD_USUARIO'])) {
            $cod_usu = $_SESSION['SES_COD_USUARIO'];
            $bd = new home_model();
            $typeperson = $bd->selectTypePerson($cod_usu);
            $usu_name   = $bd->selectUserLogin($cod_usu);
            $rsmenu     = $bd->selectMenu($cod_usu, $typeperson);
            $programcount   = $bd->countPrograms();
            //$groupperm      = $bd->selectGroupPermissionMenu($cod_usu, $typeperson,);
            $groupperm      = $bd->getPermissionMenu($cod_usu, $typeperson,'m.idmodule <= 3');


			if($groupperm){
	            while (!$groupperm->EOF) {
					$allow = $groupperm->fields['allow'];
					$program = $groupperm->fields['program'];
					$idmodule_pai = $groupperm->fields['idmodule_pai'];
					$module = $groupperm->fields['module'];
					$idmodule_origem = $groupperm->fields['idmodule_origem'];
					$category = $groupperm->fields['category'];
					$category_pai = $groupperm->fields['category_pai'];
					$idcategory_origem = $groupperm->fields['idcategory_origem'];
					$controller = $groupperm->fields['controller'];
					$idprogram = $groupperm->fields['idprogram'];
					$prsmarty = $groupperm->fields['pr_smarty'];
					$ctsmarty = $groupperm->fields['cat_smarty'];
	                $perm[$idprogram] = array('program' => $program, 'smartypr' => $prsmarty, 'smartyct' => $ctsmarty, 'idmodule_pai' => $idmodule_pai, 'module' => $module, 'idmodule_origem' => $idmodule_origem, 'category' => $category, 'category_pai' => $category_pai, 'idcategory_origem' => $idcategory_origem, 'controller' => $controller, 'idprogram' => $idprogram, 'allow' => $allow);
	                $groupperm->MoveNext();
	            }
			}

            for ($j = 1; $j <= $programcount; $j++) {
                if (in_array($perm[$j]['idmodule_pai'], $modules) || $perm[$j]['allow'] != 'Y') {
                    
                } else {
                    $modules[$perm[$j]['idmodule_pai']] = array('idmodule' => $perm[$j]['idmodule_pai'], 'module' => $perm[$j]['module']);
                }

                //agrupa as categorias tirando as duplicadas
                if (in_array($perm[$j]['category_pai'], $categories) || $perm[$j]['allow'] != 'Y') {
                    
                } else {
                    $categories[$perm[$j]['category_pai']] = array('idmodule_origem' => $perm[$j]['idmodule_origem'], 'category' => $perm[$j]['category'], 'idcategory' => $perm[$j]['category_pai'], 'smarty' => $perm[$j]['smartyct']);
                }

                //agrupa os programas separando os duplicados
                if (in_array($perm[$j]['idprogram'], $programs) || $perm[$j]['allow'] != 'Y') {
                    
                } else {
                    $programs[$perm[$j]['idprogram']] = array('idprogram' => $perm[$j]['idprogram'],'idcategory_origem' => $perm[$j]['idcategory_origem'], 'program' => $perm[$j]['program'], 'controller' => $perm[$j]['controller'], 'smarty' => $perm[$j]['smartypr']);
                }
            }
        
            $countmodules    = $bd->countModules();
            $countcategories = $bd->countCategories();

            $lista = "<ul id='menu' class='filetree'>";
            for ($i = 0; $i < $countmodules; $i++) {
				if($modules[$i + 1]['module']){
					$lista.="<li><span>" . $modules[$i + 1]['module'] . "</span>";
                	$lista.="<ul>";
	                for ($j = 0; $j <= $countcategories; $j++) {
	                    if ($modules[$i + 1]['idmodule'] == ($categories[$j + 1]['idmodule_origem'])) {
	                        $cat = $categories[$j + 1]['smarty'];
	                        $lista.="<li><span>" . $langVars[$cat] . "</span>";
	                        $lista.="<ul>";
	                        for ($k = 0; $k <= $programcount; $k++) {
	                            if ($categories[$j + 1]['idcategory'] == ($programs[$k + 1]['idcategory_origem'])) {
	                                $pr = $programs[$k + 1]['smarty'];
									$checkbar = substr($programs[$k + 1]['controller'], -1);
									if($checkbar != "/") $checkbar = "/";
									else $checkbar = "";
                                    // echo $programs[$k + 1]['controller'] ;
	                                $lista.="<li><span><a href='#/" . $programs[$k + 1]['controller'] . "' class='loadMenu' rel='" . $programs[$k + 1]['controller'] . $checkbar."' >" . $langVars[$pr] . "</a></span></li>";
	                            }
	                        }
	                        $lista.="</ul></li>";
	                    }
	                }                
                $lista.="</ul></li>";
                }
            }
        	$lista.="</ul>";                        
//;
            $smarty = $this->retornaSmarty();
            $smarty->assign('lang', $this->getConfig('lang'));
            $smarty->assign('path', path);
            $smarty->assign('lista', $lista);
            $smarty->assign('nom_usuario', $usu_name);
            $smarty->assign('userid', $cod_usu);
            $smarty->assign('SES_COD_USUARIO', $cod_usu);
            $db = new logos_model();
            $headerlogo = $db->getHeaderLogo();
            $smarty->assign('headerlogo', $headerlogo->fields['file_name']);
            $smarty->assign('headerheight', $headerlogo->fields['height']);
            $smarty->assign('headerwidth', $headerlogo->fields['width']);
            $smarty->assign('typeperson', $typeperson);
            $smarty->assign('SES_COD_JURIDICAL', $_SESSION['SES_COD_EMPRESA']);
			$smarty->assign('enterprise', $this->getConfig('enterprise'));
			
			if(!$_SESSION['SES_TIME_SESSION'])
				$smarty->assign('timesession', 600);
			else
				$smarty->assign('timesession', $_SESSION['SES_TIME_SESSION']);

            //
            if($this->getConfig("license") == '200701006')
            {
                $ckEditor = path.'/app/reports/classes/ckeditor/ckeditor.js';
            } else {
                $ckEditor = path.'/includes/classes/ckeditor/ckeditor.js';
            }
            $smarty->assign('ckEditor', $ckEditor);
            //
            $smarty->display('admin.tpl.html');
        } else {            
            $this->sessionDestroy();
            header('Location:' . path . '/admin/login');
        }
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
            $fp = fopen($path_releases . "logs/releases.txt", "w");
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
        $fp = fopen($path_releases . "logs/releases.txt", "w");
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



    public function sessionDestroy() {
        session_start();
        session_unset();
        session_destroy();
    }
    
    
}

?>
