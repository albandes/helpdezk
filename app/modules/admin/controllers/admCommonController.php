<?php

require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/syslog.php');

/*
 *  Common methods - Admin Module
 */


class admCommon extends Controllers  {

    public static $_logStatus;

    public function __construct()
    {

        parent::__construct();

        // Log settings
        $objSyslog = new Syslog();
        $this->log  = $objSyslog->setLogStatus() ;
        self::$_logStatus = $objSyslog->setLogStatus() ;
        if ($this->log) {
            $this->_logLevel = $objSyslog->setLogLevel();
            $this->_logHost = $objSyslog->setLogHost();
            if($this->_logHost == 'remote')
                $this->_logRemoteServer = $objSyslog->setLogRemoteServer();
                $this->_logFacility = $objSyslog->setLogFacility();
        }

        //
        $this->modulename = 'admin' ;
        //


        $id = $this->getIdModule($this->modulename) ;
        if(!$id) {
            die('Module don\'t exists in tbmodule !!!') ;
        } else {
            $this->idmodule = $id ;
        }

        $this->loadModel('person_model');
        $dbPerson = new person_model();
        $this->dbPerson = $dbPerson;
        
        $this->loadModel('holidays_model');
        $dbHoliday = new holidays_model();
        $this->dbHoliday = $dbHoliday;

        $this->loadModel('modules_model');
        $dbModule = new modules_model();
        $this->dbModule = $dbModule;
        
        $this->loadModel('programs_model');
        $dbProgram = new programs_model();
        $this->dbProgram = $dbProgram;

        $this->loadModel('permissions_model');
        $dbPermissions = new permissions_model();
        $this->dbPermissions = $dbPermissions;

        $this->loadModel('helpdezk/groups_model');
        $dbGroups = new groups_model();
        $this->dbGroups = $dbGroups;
		
		// Tracker Settings
        if($_SESSION['TRACKER_STATUS'] == 1) {
            $this->modulename = 'admin' ;
            $this->idmodule = $this->getIdModule($this->modulename) ;
            $this->tracker = true;
        }  else {
            $this->tracker = false;
        }

    }

    public function _makeNavAdm($smarty)
    {
        $idPerson = $_SESSION['SES_COD_USUARIO'];
        $listRecords = $this->_makeMenuAdm($smarty);
        $moduleinfo = $this->getModuleInfo($this->idmodule);

        $smarty->assign('displayMenu_Adm',1);
        $smarty->assign('listMenu_Adm',$listRecords);
        $smarty->assign('moduleLogo',$moduleinfo->fields['headerlogo']);
        $smarty->assign('modulePath',$moduleinfo->fields['path']);
    }

    public function _comboCompany()
    {
        $rs = $this->dbPerson->getErpCompanies("WHERE idtypeperson IN (7,4,5)","ORDER BY name ASC");
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idcompany'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboLastYear($cond=null)
    {
        $rs = $this->dbHoliday->getYearsHolidays($cond);

        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['holiday_year'];
            $values[]   = $rs->fields['holiday_year'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboNextYear()
    {
        $date = date("Y");
        for($i = $date; $i <= $date+5; $i++){
            $fieldsID[] = $i;
            $values[]   = $i;                            			
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboModule()
    {
        $rs = $this->dbProgram->selectModules();
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idmodule'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboCategory($idmodule)
    {
        $rs = $this->dbProgram->selectCategory($idmodule);
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idprogramcategory'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    function _makeMenuAdm($smarty)
    {
        $rs = $this->getActiveModules();
        $list = '';
        while (!$rs->EOF) {
            $rsCat = $this->dbProgram->getModulesCategoryAtive($_SESSION['SES_COD_USUARIO'],$rs->fields['idmodule']);

            if($rsCat->RecordCount() > 0){
                $list .= "<li class='dropdown-submenu'>
                                <a tabindex='-1' href='#'>". $smarty->getConfigVars($rs->fields['smarty']) ."</a>
                                <ul class='dropdown-menu'>";


                while (!$rsCat->EOF) {
                    $list .= "<li class='dropdown-submenu'>
                                <a tabindex='-1' href='#'>". $smarty->getConfigVars($rsCat->fields['cat_smarty']) ."</a>
                                <ul class='dropdown-menu'>";

                    $andModule = " m.idmodule = " . $rs->fields['idmodule'] . " AND cat.idprogramcategory = " . $rsCat->fields['category_id'] ;
                    $groupperm = $this->dbProgram->getPermissionMenu($_SESSION['SES_COD_USUARIO'], $andModule);

                    if($groupperm){
                        while (!$groupperm->EOF) {
                            $allow = $groupperm->fields['allow'];
                            $path  = $groupperm->fields['path'];
                            $program = $groupperm->fields['program'];
                            $controller = $groupperm->fields['controller'];
                            $prsmarty = $groupperm->fields['pr_smarty'];

                            $checkbar = substr($groupperm->fields['controller'], -1);
                            if($checkbar != "/") $checkbar = "/";
                            else $checkbar = "";

                            $controllertmp = ($checkbar != "") ? $controller : substr($controller,0,-1);
                            $controller_path = 'app/modules/' . $path . '/controllers/' . $controllertmp . 'Controller.php';

                            if (!file_exists($controller_path)) {
                                $this->logIt("The controller does not exist: " . $controller_path. ' - program: '. $this->program ,3,'general',__LINE__);
                            }else{
                                if ($allow == 'Y') {

                                    $list .="<li><a href='" . $this->helpdezkUrl . "/".$path."/" . $controller . $checkbar."index' >" . $smarty->getConfigVars($prsmarty) . "</a></li>";
                                }
                            }

                            $groupperm->MoveNext();
                        }
                    }
                    $list .= "</ul>
                    </li>";
                    $rsCat->MoveNext();
                }

                $list .= "</ul>
                    </li>";
            }

            $rs->MoveNext();
        }
        //echo $list;
        return $list;
    }

    public function _comboTypeLogin()
    {
        $rs = $this->dbPerson->getLoginTypes();
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idtypelogin'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboCompanies()
    {
        $rs = $this->dbPerson->getErpCompanies("WHERE idtypeperson IN (4) AND status = 'A'","ORDER BY name ASC");
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idcompany'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboDepartment($idcompany)
    {
        $rs = $this->dbPerson->getDepartment("WHERE idperson = $idcompany AND status = 'A'","ORDER BY name ASC");
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['iddepartment'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboTypePerson($where=null,$group=null,$order=null,$limit=null)
    {
        $rs = $this->dbPerson->getTypePerson($where,$group,$order,$limit);
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idtypeperson'];
            $values[]   = $this->getLanguageWord('type_user_'.$rs->fields['name']);
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboLocation()
    {
        $rs = $this->dbPerson->getLocation();

        if($rs->RecordCount() > 0){
            while (!$rs->EOF) {
                $fieldsID[] = $rs->fields['idlocation'];
                $values[]   = $rs->fields['name'];
                $rs->MoveNext();
            }
        }else{
            $fieldsID[] = "";
            $values[]   = $this->getLanguageWord('No_result');
        }        

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboStreet($where=NULL,$group=NULL,$order=NULL,$limit=NULL)
    {
        $rs = $this->dbPerson->getStreet($where,$group,$order,$limit);
        while (!$rs->EOF) {
            $name = $rs->fields['idstreet'] != 1 ? $rs->fields['name'] : $this->getLanguageWord('Select_street');

            $fieldsID[] = $rs->fields['idstreet'];
            $values[]   = $name;
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboGroups($where=NULL,$order=NULL,$limit=NULL)
    {
        $rs = $this->dbGroups->selectGroup($where,$order,$limit);

        if($rs->RecordCount() > 0){
            $fieldsID[] = $rs->fields[''];
            $values[]   = $rs->fields[''];
            while (!$rs->EOF) {
                $fieldsID[] = $rs->fields['idgroup'];
                $values[]   = $rs->fields['name'];
                $rs->MoveNext();
            }
        }else{
            $fieldsID[] = "";
            $values[]   = $this->getLanguageWord('No_result');
        }


        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

}