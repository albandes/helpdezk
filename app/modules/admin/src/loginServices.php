<?php

namespace App\modules\admin\src;

use App\modules\admin\dao\mysql\logoDAO;
use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\moduleDAO;
use App\modules\admin\dao\mysql\personDAO;
use App\src\appServices;

class loginServices
{
    public function __construct()
    {

    }

    /**
     * Returns login's logo data
	 * 
     * @return array login's logo data (path, width, height)
     */
	public function _getLoginLogoData(): array 
    {
        $aRet = [];
        $appSrc = new appServices();
		$logoDAO = new logoDao(); 
        $logo = $logoDAO->getLogoByName("login");
        
        $objLogo = $logo['data'];
        if ($_ENV['EXTERNAL_STORAGE']) {
            $pathLogoImage = $_ENV['EXTERNAL_STORAGE_PATH'] . '/logos/' . $objLogo->getFileName();
        } else {
            
            $pathLogoImage = $appSrc->_getHelpdezkPath() . '/storage/uploads/logos/' . $objLogo->getFileName();
        }
		
        if (empty($objLogo->getFileName()) or !file_exists($pathLogoImage)){
            $aRet['image'] 	= ($_ENV['EXTERNAL_STORAGE'] ? $_ENV['EXTERNAL_STORAGE_PATH'] . '/logos/' : $_ENV['HDK_URL'] . '/storage/uploads/logos/') . 'default/login.png';
			$aRet['width'] 	= "227";
			$aRet['height'] = "70";
        }else{
            $aRet['image'] 	= ($_ENV['EXTERNAL_STORAGE'] ? $_ENV['EXTERNAL_STORAGE_PATH'] . '/logos/' : $_ENV['HDK_URL'] . '/storage/uploads/logos/') . $objLogo->getFileName();
			$aRet['width'] 	= $objLogo->getWidth();
			$aRet['height'] = $objLogo->getHeight();
		}
        
		return $aRet;
    }

	

    // Since November 20
    // Used in user authentication methods. It comes here because it will be used in both admin and helpdezk.
    public function _startSession($idperson)
    {
        $loginDAO = new LoginDAO();

        session_start();
        $_SESSION['SES_COD_USUARIO'] = $idperson;
        $_SESSION['REFRESH']         = false;

        //SAVE THE CUSTOMER'S LICENSE
        $_SESSION['SES_LICENSE']    = $_ENV['LICENSE'];
        $_SESSION['SES_ENTERPRISE'] = $_ENV['ENTERPRISE'];

        $_SESSION['SES_ADM_MODULE_DEFAULT'] = $this->pathModuleDefault();

        if ($_SESSION['SES_COD_USUARIO'] != 1) {

            if ($this->isActiveHelpdezk()) {

                $typeuser = $loginDAO->selectDataSession($idperson);
                $_SESSION['SES_LOGIN_PERSON']       = $typeuser['data']['login'];
                $_SESSION['SES_NAME_PERSON']        = $typeuser['data']['name'];
                $_SESSION['SES_TYPE_PERSON']        = $typeuser['data']['idtypeperson'];
                $_SESSION['SES_IND_CODIGO_ANOMES']  = true;
                $_SESSION['SES_COD_EMPRESA']        = $typeuser['data']['idjuridical'];
                $_SESSION['SES_COD_TIPO']           = $typeuser['data']['idtypeperson'];
                
                $groups = $loginDAO->selectPersonGroups($idperson);
                $_SESSION['SES_PERSON_GROUPS'] = $groups['data'];

            } else {

                $personDAO = new PersonDAO();
                $rsPerson = $personDAO->selectPersonByID(" AND tbp.idperson = $idperson");
                $_SESSION['SES_LOGIN_PERSON']   = $rsPerson->fields['login'];
                $_SESSION['SES_NAME_PERSON']    = $rsPerson->fields['name'];
                $_SESSION['SES_TYPE_PERSON']    = $rsPerson->fields['idtypeperson'];

            }

        } else {

            if($this->isActiveHelpdezk()){

                $_SESSION['SES_NAME_PERSON']        = 'admin';
                $_SESSION['SES_TYPE_PERSON']        = 1;
                $_SESSION['SES_IND_CODIGO_ANOMES']  = true;
                $_SESSION['SES_COD_EMPRESA']        = 1;
                $_SESSION['SES_COD_TIPO']           = 1;

                $groups = $LoginDAO->selectAllGroups();
                $_SESSION['SES_PERSON_GROUPS'] = $groups['data'];

            } else {

                $_SESSION['SES_NAME_PERSON'] = 'admin';
                $_SESSION['SES_TYPE_PERSON'] = 1;
                $_SESSION['SES_COD_EMPRESA'] = 1;

            }
        }

    }

    // Since November 20
    // Used in user authentication methods. It comes here because it will be used in both admin and helpdezk.
    public function _getConfigSession()
    {
        $loginDAO = new LoginDAO();

        session_start();
        if (version_compare($this->getHelpdezkVersionNumber(), '1.0.1', '>' )) {

            $objModules = $this->getActiveModules();
            
            foreach($objModules as $k=>$v) {
                $prefix = $v['tableprefix'];
                if(!empty($prefix)) {
                    $data = $LoginDAO->getConfigDataByModule($prefix);
                    if (!$data['success']) {
                        //TODO: code for new log with monolog
                        /*if($this->log)
                            $this->logIt('Modules do not have config tables: ' . $prefix.'_tbconfig'. ' and ' . $prefix.'_tbconfigcategory - program: '. $this->program ,3,'general',__LINE__);*/
                    }else{
                        foreach($data['data'] as $key=>$val) {
                            $ses = $val['session_name'];
                            $val = $val['value'];
                            $_SESSION[$prefix][$ses] = $val;
                        }
                    }
                }
            }

        } else {
            $data = $this->dbIndex->getConfigData();
            if($data) {
                while (!$data->EOF) {
                    $ses = $data->fields['session_name'];
                    $val = $data->fields['value'];
                    $_SESSION[$ses] = $val;
                    $_SESSION[$prefix][$ses] = $val;
                    $data->MoveNext();
                }
            }
        }

        $idperson = $_SESSION['SES_COD_USUARIO'];

        // Global Config Data
        $rsConfig = $this->dbIndex->getConfigGlobalData();
        while (!$rsConfig->EOF) {
            $ses = $rsConfig->fields['session_name'];
            $val = $rsConfig->fields['value'];
            $_SESSION[$ses] = $val;
            $rsConfig->MoveNext();
        }

        // User config data
        $this->loadModel('admin/userconfig_model');
        $cf = new userconfig_model();
        $columns = $cf->getColumns(); //GET COLUMNS OF THE TABLE

        $database = $this->getConfig('db_connect');

        while (!$columns->EOF) {
            if($this->isMysql($database)) {
                $cols[] = strtolower($columns->fields['Field']);
            } elseif($database == 'oci8po') {
                $cols[] = strtolower($columns->fields['column_name']);
            }
            $columns->MoveNext();
        }


        $idconf = $cf->checkConf($idperson); //CHECK IF USER HAVE PERSONAL CONFIG, IF DNO'T HAVE IT'S CREATE

        $getUserConfig = $cf->getConf($cols,$idconf);
        foreach ($cols as $key => $value) {
            $_SESSION['SES_PERSONAL_USER_CONFIG'][$value] = $getUserConfig->fields[$value];
        }

    }

    public function pathModuleDefault()
    {
        $moduleDAO = new ModuleDAO();
        $rs = $moduleDAO->getModuleDefault();
        return $rs['data']['path'];
    }

    public function isActiveHelpdezk()
    {
        $LoginDAO = new LoginDAO();
        $ret = $LoginDAO->isActiveHelpdezk();
        return $ret['isactive'];
    }

    public function getHelpdezkVersionNumber()
    {
        $exp = explode('-', $this->getHelpdezkVersion());
        return $exp[2];
    }

    public function getActiveModules()
    {
        $LoginDAO = new LoginDAO();
        $ret = $LoginDAO->getActiveModules();
        return $ret['data'];

    }
}