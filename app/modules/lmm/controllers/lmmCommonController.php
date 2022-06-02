<?php
//Sistema irá informar o CommonController
require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/syslog.php');

if(class_exists('Controllers')) {
    class DynamicLmmCommon extends Controllers {}
} elseif(class_exists('cronController')) {
    class DynamicLmmCommon extends cronController {}
} elseif(class_exists('apiController')) {
    class DynamicLmmCommon extends apiController {}
}
class lmmCommon extends DynamicLmmCommon {

    public static $_logStatus;

    public function __construct(){

        parent::__construct();

        $this->loadModel('admin/tracker_model');
        $this->dbTracker = $dbTracker = new tracker_model();

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

         // Tracker Settings
        if($_SESSION['TRACKER_STATUS'] == 1) {
            $this->tracker = true;
        }  else {
            $this->tracker = false;
        }

        //
        $this->modulename = 'Biblioteca';
        //

        $id = $this->getIdModule($this->modulename) ;
        if(!$id) {
            die('Module don\'t exists in tbmodule !!!') ;
        } else {
            $this->idmodule = $id ;
        }
        $this->loadModel('materialtype_model');
        $this->dbMaterialtype= new materialtype_model();

        $this->loadModel('author_model');
        $this->dbAuthor= new author_model();

        $this->loadModel('collection_model');
        $this->dbCollection= new collection_model();

        $this->loadModel('publishing_model');
        $this->dbPublishing= new publishing_model();

        $this->loadModel('color_model');
        $this->dbColor= new color_model();

        $this->loadModel('classification_model');
        $this->dbClassification= new classification_model();

        $this->loadModel('origin_model');
        $this->dbOrigin= new origin_model();

        $this->loadModel('cdd_model');
        $this->dbCDD= new cdd_model();

        $this->loadModel('library_model');
        $this->dbLibrary= new library_model();
        
        


    }

    public function _makeNavlmm($smarty)
    {
        $idmaterialtype = $_SESSION['SES_COD_USUARIO'];
        $listRecords = $this->makeMenuByModule($idmaterialtype,$this->idmodule);
        $moduleinfo = $this->getModuleInfo($this->idmodule);

        $smarty->assign('listMenu_1',$listRecords);

        // Set Header Logo
        $smarty->assign('moduleLogo',$moduleinfo->fields['headerlogo']);

        $smarty->assign('modulePath',$moduleinfo->fields['path']);

    }

    public function _comboPublishing_company($where=null,$order=null)
    {
        $rs = $this->dbPublishing->getPublishing($where,$order);
        while (!$rs['data']->EOF) {
            $fieldsID[] = $rs['data']->fields['idpublishingcompany'];
            $values[]   = ($rs['data']->fields['name']);
            $rs['data']->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }


    public function _comboColor($where=null,$order=null)
    {
        $rs = $this->dbColor->getColor($where,$order);
        while (!$rs['data']->EOF) {
            $fieldsID[] = $rs['data']->fields['idcolor'];
            $values[]   = ($rs['data']->fields['name']);
            if($rs['data']->fields['default']=='Y') { $default[]=$rs['data']->fields['idcolor'];}
            $rs['data']->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;
        $arrRet['default'] = $default;

        return $arrRet;
    }

    public function _comboClassification($where=null,$order=null)
    {
        $rs = $this->dbClassification->getClassification($where,$order);
        while (!$rs['data']->EOF) {
            $fieldsID[] = $rs['data']->fields['idclassification'];
            $values[]   = ($rs['data']->fields['name']);
            if($rs['data']->fields['default']=='Y') { $default[]=$rs['data']->fields['idclassification'];}
            $rs['data']->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;
        $arrRet['default'] = $default;

        return $arrRet;
    }


    public function _comboMaterialtype($where=null,$order=null)
    {
        $rs = $this->dbMaterialtype->getMaterialtype($where,$order);
        while (!$rs['data']->EOF) {
            $fieldsID[] = $rs['data']->fields['idmaterialtype'];
            $values[]   = ($rs['data']->fields['name']);
            $rs['data']->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboCollection($where=null,$order=null)
    {
        $rs = $this->dbCollection->getCollection($where,$order);
        while (!$rs['data']->EOF) {
            $fieldsID[] = $rs['data']->fields['idcollection'];
            $values[]   = ($rs['data']->fields['name']);
            $rs['data']->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboCDD($where=null,$order=null)
    {
        $rs = $this->dbCDD->getCDD($where,$order);
        while (!$rs['data']->EOF) {
            $fieldsID[] = $rs['data']->fields['idcdd'];
            $values[]   = ($rs['data']->fields['code']);
            $rs['data']->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboOrigin($where=null,$order=null)
    {
        $rs = $this->dbOrigin->getOrigin($where,$order);
        while (!$rs['data']->EOF) {
            $fieldsID[] = $rs['data']->fields['idorigin'];
            $values[]   = ($rs['data']->fields['name']);
            if($rs['data']->fields['default']=='Y') { $default[]=$rs['data']->fields['idorigin'];}
            $rs['data']->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;
        $arrRet['default'] = $default;

        return $arrRet;
    }
    

    public function _comboAuthor($where=null,$order=null)
    {
        $rs = $this->dbAuthor->getAuthor($where,$order);
        while (!$rs['data']->EOF) {
            $fieldsID[] = $rs['data']->fields['idauthor'];
            $values[]   = ($rs['data']->fields['name']);
            $rs['data']->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboLibrary($where=null,$order=null)
    {
        $rs = $this->dbLibrary->getLibrary($where,$order);
        while (!$rs['data']->EOF) {
            $fieldsID[] = $rs['data']->fields['idlibrary'];
            $values[]   = ($rs['data']->fields['name']);
            if($rs['data']->fields['default']=='Y') { $default[]=$rs['data']->fields['idlibrary'];}
            $rs['data']->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;
        $arrRet['default'] = $default;

        return $arrRet;
    }
      
    

}