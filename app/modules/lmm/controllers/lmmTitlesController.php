<?php
require_once(HELPDEZK_PATH . '/app/modules/lmm/controllers/lmmCommonController.php');
   
class lmmTitles extends lmmCommon {
    
    public function __construct()
    {

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );
        $this->idprogram =  $this->getIdProgramByController('lmmTitles');
        
        $this->loadModel('titles_model');
        $this->dbTitles = new titles_model();

        $this->loadModel('author_model');
        $this->dbAuthor = new author_model();

    }

    public function index()
    {
        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('lmm-titles-grid.tpl');
    }


    public function jsonGrid()
    {
        $this->validasessao();
        $this->protectFormInput();
        $where = '';

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='name';
        if(!$sord)
            $sord ='ASC';      
            
        if ($_POST['_search'] == 'true'){
            if($_POST['searchField']=='materialtype')
                $_POST['searchField']='b.name';
            if($_POST['searchField']=='collection')
                $_POST['searchField']='c.name';
            if($_POST['searchField']=='name')
                $_POST['searchField']='a.name';
            if($_POST['searchField']=='cdd')
                $_POST['searchField']='d.code';
            if($_POST['searchField']=='publishingcompany')
                $_POST['searchField']='e.name';
            if($_POST['searchField']=='color')
                $_POST['searchField']='f.name';
            if($_POST['searchField']=='classification')
                $_POST['searchField']='g.name';

            $where .= ($where == '' ? 'WHERE ' : ' AND ') . $this->getJqGridOperation($_POST['searchOper'],$_POST['searchField'],$_POST['searchString']);
        }

        $rsCount = $this->dbTitles->getTitles($where);

        if (!$rsCount['success']) {
            if($this->log)
                $this->logIt("Can't get Cor. {$rsCount['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $count = $rsCount['data']->RecordCount();

        if( $count > 0 && $rows > 0) {
            $total_pages = ceil($count/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
            $start = $rows*$page - $rows;
        if($start <0) $start = 0;

        $order = "ORDER BY $sidx $sord";
        $limit = "LIMIT $start , $rows";

        $rsBrand =  $this->dbTitles->getTitles($where,$order,$limit);

        while (!$rsBrand['data']->EOF) {
           
            $aColumns[] = array(
                'id'                      => $rsBrand['data']->fields['idtitle'],
                'materialtype'            => $rsBrand['data']->fields['materialtype'],                
                'collection'              => $rsBrand['data']->fields['collection'], 
                'title'                   => $rsBrand['data']->fields['name'], 
                'cutter'                  => $rsBrand['data']->fields['cutter'], 
                'isbn'                    => $rsBrand['data']->fields['isbn'], 
                'issn'                    => $rsBrand['data']->fields['issn'], 
                'cdd'                     => $rsBrand['data']->fields['cdd'], 
                'cdu'                     => $rsBrand['data']->fields['cdu'], 
                'publishing'              => $rsBrand['data']->fields['publishingcompany'],
                'color'                   => $rsBrand['data']->fields['color'],
                'classification'          => $rsBrand['data']->fields['classification'],  

            );
            $rsBrand['data']->MoveNext();   
        }




        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    //FUNCIONALIDADES ESPECÍFICAS DESSE PROGRAMA
    public function formCreate()
    {
        $smarty = $this->retornaSmarty();

       // $this->makeScreenTitles($smarty,'','create');
        $this->makeScreenTitles($smarty,'$rsBrand'['data'],'$rsexemplar'['data'],'$rsauthor'['data'],'create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmtitles-create.tpl');
    }

    //Ver Scm update
    public function formUpdate()    
    {
        $token = $this->_makeToken();

        $smarty = $this->retornaSmarty();

        $lmmID = $this->getParam('lmmID');
        $rsBrand = $this->dbTitles->getTitles("WHERE idtitle= $lmmID");
        if (!$rsBrand['success']) {
            if($this->log)
                $this->logIt("Can't get Titulo. {$rsBrand['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);           
            return false;
        }

        $rsexemplar = $this->dbTitles->getExemplar("AND a.idtitle= $lmmID");
        if (!$rsexemplar['success']) {
            if($this->log)
                $this->logIt("Can't get Exemplar. {$rsexemplar['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);           
            return false;
        }

        $rsauthor = $this->dbTitles->getAuthor("AND a.idtitle= $lmmID");
        if (!$rsauthor['success']) {
            if($this->log)
                $this->logIt("Can't get Author. {$rsauthor['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);         
            return false;
        }        


        $this->makeScreenTitles($smarty,$rsBrand['data'],$rsexemplar['data'],$rsauthor['data'],'update');


        $smarty->assign('token', $token) ;

        $smarty->assign('idtitle', $lmmID);    

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavlmm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);
        $smarty->display('lmmtitles-update.tpl');

    }    
    

    function makeScreenTitles($objSmarty,$rs,$rsexemplar,$rsauthor,$oper)
    {       

        $materialtype=$this->_comboMaterialtype(); 
        if ($oper == 'update') {      
            $idModuleEnable = $rs->fields['idmaterialtype'];  
        }elseif ($oper == 'create') {
            $idModuleEnable = $materialtype['ids'][0];        
        }            
        $objSmarty->assign('materialtypeids',  $materialtype['ids']);
        $objSmarty->assign('materialtypevals',$materialtype['values']);
        $objSmarty->assign('idmaterialtype', $idModuleEnable );


        $Collection=$this->_comboCollection();  
        if ($oper == 'update') {      
            $idModuleEnable = $rs->fields['idcollection'];  
        }elseif ($oper == 'create') {
            $idModuleEnable = $Collection['ids'][0];         
        }      
        $objSmarty->assign('Collectionids',  $Collection['ids']);
        $objSmarty->assign('Collectionvals',$Collection['values']);
        $objSmarty->assign('idCollection', $idModuleEnable );

        
        $CDD=$this->_comboCDD(); 
        if ($oper == 'update') {      
            $idModuleEnable = $rs->fields['idcdd'];  
        }elseif ($oper == 'create') {
            $idModuleEnable = $CDD['ids'][0];          
        }       
        $objSmarty->assign('CDDids',  $CDD['ids']);
        $objSmarty->assign('CDDvals',$CDD['values']);
        $objSmarty->assign('idCDD', $idModuleEnable ); 

      
        $edit=$this->_comboPublishing_company();
        if ($oper == 'update') {      
            $idModuleEnable = $rs->fields['idpublishingcompany'];  
        }elseif ($oper == 'create') {
            $idModuleEnable = $edit['ids'][0];        
        }           
        $objSmarty->assign('editids',  $edit['ids']);
        $objSmarty->assign('editvals',$edit['values']);
        $objSmarty->assign('idedit', $idModuleEnable );
        

        $color=$this->_comboColor();
        if ($oper == 'update') {      
            $idModuleEnable = $rs->fields['idcolor'];  
        }elseif ($oper == 'create') {
            $idModuleEnable = $color['default'];         
        } 
        $objSmarty->assign('colorids',  $color['ids']);
        $objSmarty->assign('colorvals',$color['values']);
        $objSmarty->assign('idcolor', $idModuleEnable );


        $classif=$this->_comboClassification();  
        if ($oper == 'update') {      
            $idModuleEnable = $rs->fields['idclassification'];  
        }elseif ($oper == 'create') {            
            $idModuleEnable = $classif['default'];
        }         
        $objSmarty->assign('classifids',  $classif['ids']);
        $objSmarty->assign('classifvals',$classif['values']);
        $objSmarty->assign('idclassif', $idModuleEnable );    
        
        
        $origin=$this->_comboOrigin(); 
        if ($oper == 'update') {      
            $idModuleEnable = $rs->fields['idorigin'];  
        }elseif ($oper == 'create') {           
            $idModuleEnable = $origin['default'];
        }          
        $objSmarty->assign('originids',  $origin['ids']);
        $objSmarty->assign('originvals',$origin['values']);
        $objSmarty->assign('idorigin', $idModuleEnable );


        $Author=$this->_comboAuthor(); 
        if ($oper == 'update') {      
            $idModuleEnable = $rs->fields['idauthor'];  
        }elseif ($oper == 'create') {           
            $idModuleEnable = $Author['ids'][0]; 
        }        
        $objSmarty->assign('Authorids',  $Author['ids']);
        $objSmarty->assign('Authorvals',$Author['values']);
        $objSmarty->assign('idAuthor', $idModuleEnable );         

        
   
        $objSmarty->assign('radiosim',$rs->fields['flagcollection']=='Y'?'checked=checked':'');
        $objSmarty->assign('radionao', $rs->fields['flagcollection']=='N'?'checked=checked':'' );
        $objSmarty->assign('mostracoll', $rs->fields['flagcollection']=='N'?'hide':'' );  

         
        // --- update Titulo ---
        
        if ($oper == 'update') {
            if (empty($rs->fields['name']))
                $objSmarty->assign('plh_titles','Informe o Titulo.');
            else
                $objSmarty->assign('titles',$rs->fields['name']);
        }elseif ($oper == 'create') {
            $objSmarty->assign('plh_titles','Informe o Titulo.');
        }elseif ($oper == 'echo') {
             $objSmarty->assign('titles',$rs->fields['name']);
        }    
        
        
        // --- update ISBN ---
        
        if ($oper == 'update') {
            if (empty($rs->fields['isbn']))
                $objSmarty->assign('plh_ISBN',' ISBN.');
            else
                $objSmarty->assign('ISBN',$rs->fields['isbn']);
        }elseif ($oper == 'create') {
            $objSmarty->assign('plh_ISBN',' ISBN.');
        }elseif ($oper == 'echo') {
             $objSmarty->assign('ISBN',$rs->fields['isbn']);
        } 

        // --- update ISSN ---
        
        if ($oper == 'update') {
            if (empty($rs->fields['issn']))
                $objSmarty->assign('plh_ISSN',' ISSN.');
            else
                $objSmarty->assign('ISSN',$rs->fields['issn']);
        }elseif ($oper == 'create') {
            $objSmarty->assign('plh_ISSN',' ISSN.');
        }elseif ($oper == 'echo') {
             $objSmarty->assign('ISSN',$rs->fields['issn']);
        } 

         // --- update CDU ---
        
        if ($oper == 'update') {
            if (empty($rs->fields['cdu']))
                $objSmarty->assign('plh_CDU',' CDU.');
            else
                $objSmarty->assign('CDU',$rs->fields['cdu']);
        }elseif ($oper == 'create') {
            $objSmarty->assign('plh_CDU',' CDU.');
        }elseif ($oper == 'echo') {
             $objSmarty->assign('CDU',$rs->fields['cdu']);
        } 
         

        // --- update Cutter Titulo ---

        if ($oper == 'update') {
            if (empty($rs->fields['cutter']))
                $objSmarty->assign('plh_Cutter','Cód.Cutter.');
            else
                $objSmarty->assign('Cutter',$rs->fields['cutter']);
        }elseif ($oper == 'create') {
            $objSmarty->assign('plh_Cutter','Cód.Cutter');
        }elseif ($oper == 'echo') {
             $objSmarty->assign('Cutter',$rs->fields['cutter']);
        }



        // --- update Volume ---

        if ($oper == 'update') {
            if (empty($rs->fields['volume']))
                $objSmarty->assign('plh_Volume',' Volume.');
            else
                $objSmarty->assign('Volume',$rs->fields['volume']);
        }elseif ($oper == 'create') {
            $objSmarty->assign('plh_Volume','Volume');
        }elseif ($oper == 'echo') {
             $objSmarty->assign('Volume',$rs->fields['volume']);
        }

         // --- update Edition ---

         if ($oper == 'update') {
            if (empty($rs->fields['edition']))
                $objSmarty->assign('plh_Edition ',' Edition .');
            else
                $objSmarty->assign('Edition ',$rs->fields['edition']);
        }elseif ($oper == 'create') {
            $objSmarty->assign('plh_Edition ','Edition ');
        }elseif ($oper == 'echo') {
             $objSmarty->assign('Edition ',$rs->fields['edition']);
        }

         // --- update Year ---

         if ($oper == 'update') {
            if (empty($rs->fields['bookyear']))
                $objSmarty->assign('plh_Year ',' Ano ');
            else
                $objSmarty->assign('Year',$rs->fields['bookyear']);
        }elseif ($oper == 'create') {
            $objSmarty->assign('plh_Year ','Ano ');
        }elseif ($oper == 'echo') {
             $objSmarty->assign('Year ',$rs->fields['bookyear']);
        }

        // --- update Cutter Author ---

        if ($oper == 'update') {
            if (empty($rs->fields['cutter']))
                $objSmarty->assign('plh_tabcutter','Cód.Cutter.');
            else
                $objSmarty->assign('tabcutter',$rs->fields['cutter']);
        }elseif ($oper == 'create') {
            $objSmarty->assign('plh_tabcutter','Cód.Cutter');
        }elseif ($oper == 'echo') {
             $objSmarty->assign('tabcutter',$rs->fields['cutter']);
        }



        
        // --- Exemplar ---
        if ($oper == 'update') {
            $idbookcopy = $rs->fields['Exemplar'];
        }elseif ($oper == 'create') {
            $idbookcopy = 1;
        }

        // --- Author ---

        if ($oper == 'update') {
            $idtitle = $rs->fields['Author'];
        }elseif ($oper == 'create') {
            $idtitle = 1;
        }
        

         // --- Exemplar---
         $Library=$this->_comboLibrary();
         $objSmarty->assign('Libraryids',  $Library['ids']);
         $objSmarty->assign('Libraryvals',$Library['values']);
 
        if ($oper == 'update') {
 
           
             while (!$rsexemplar->EOF) {
 
                 $arrItens[] = array(
                     'idbookcopy'   => $rsexemplar->fields['idbookcopy'],
                     'Library'      => $rsexemplar->fields['idlibrary'],
                     'aquis'        => $rsexemplar->fields['dtacquisition'],
                     'origin'       => $rsexemplar->fields['idorigin'],
                     'Volume'       => $rsexemplar->fields['volume'],
                     'Edition'      => $rsexemplar->fields['edition'],
                     'Year'         => $rsexemplar->fields['bookyear'],
                     'cd'           => $rsexemplar->fields['hascd'],
 
                 );
                 $rsexemplar->MoveNext();
            }
            $objSmarty->assign('arrItens', $arrItens);

        }elseif($oper == 'create'){
             $objSmarty->assign('idLibrary',$Library['default']);
        }
 
 
        if ($oper == 'update') {
                       
            while (!$rsauthor->EOF) {
 
                $arrAut[] = array(
                    'idtitle'   => $rsauthor->fields['idtitle'],
                    'author'    => $rsauthor->fields['idauthor'],
                    'cutter'    => $rsauthor->fields['cutter'],                      
 
                );
                $rsauthor->MoveNext();
            }
            $objSmarty->assign('arrAut', $arrAut);
        }               

    }



    public function ajaxcutter()
    {
        $arrModule = $this->dbAuthor->getAuthor("WHERE idauthor= {$_POST['idauthor']}");
        if (!$arrModule['success']) {
            if($this->log)
                $this->logIt("Can't get Author. {$arrModule['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);           
            return false;
        }            
       
        echo $arrModule['data']->fields['cutter'];

        
    }
    


    function createTitles()
    {
         $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
      
        //variaveis referente a tabela tbtitle

        $idmaterialtype= trim($_POST['materialtype']);
        $flagcollection= trim($_POST['col']);
        $idcollection= $flagcollection=='Y'?trim($_POST['Collection']):0;
        $title= trim($_POST['titles']);
        $cutter= trim($_POST['Cutter']);
        $isbn= trim($_POST['ISBN']);
        $issn= trim($_POST['ISSN']);
        $idcdd= trim($_POST['CDD']);
        $cdu= trim($_POST['CDU']);
        $idpublishingcompany= trim($_POST['edit']);
        $idcolor= trim($_POST['Color']);
        $idclassification= trim($_POST['classif']);

        //variaveis referente a tabela tbbookcopy
    
                 
        $idlibrary= ($_POST['Library']);
        $dtacquisition= ($_POST['aquis']);
        $origin= ($_POST['origin']);
        $volume= ($_POST['Volume']);
        $edition= ($_POST['Edition']);        
        $bookyear= ($_POST['Year']);
        $hascd= ($_POST['hascd']);  
        
        
      

        //variaveis referente a tabela tbtitle_has_author
       
        $idauthor= ($_POST['tabAuthor']);       

                   

        $this->dbTitles->BeginTrans();

        $ret = $this->dbTitles->insertTitles($idmaterialtype,$flagcollection,$idcollection,$title,$cutter,$isbn,$issn,$idcdd,$cdu,$idpublishingcompany,$idcolor,$idclassification);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't insert Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbTitles->RollbackTrans();
            return false;
        }

        $idtitle=$ret['id']; 

        foreach($idlibrary as $key=>$value){           
            $retexemplar = $this->dbTitles->insertExemplar($idtitle,$value,$dtacquisition[$key],$origin[$key],$volume[$key],$edition[$key],$bookyear[$key],$hascd[($key+1)]);

            if (!$retexemplar['success']) {
                if($this->log)
                    $this->logIt("Can't insert Brand data. {$retexemplar['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    $this->dbTitles->RollbackTrans();
                return false;
            }

           
        }

        foreach($idauthor as $key=>$value){
            $retauthor = $this->dbTitles->insertHasAuthor($idtitle,$value);

            if (!$retauthor['success']) {
                if($this->log)
                    $this->logIt("Can't insert Brand data. {$retauthor['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    $this->dbTitles->RollbackTrans();
                return false;
            }
        }
        

        $aRet = array(
            "success" => true,
            "brandID" => $ret['id']
        );

        $this->dbTitles->CommitTrans();

        echo json_encode($aRet);

    }

    function updateTitles()
    {
        
        $this->protectFormInput();
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        //variaveis referente a tabela tbtitle

        $idtitle= ($_POST['idtitle']);
        $idmaterialtype= ($_POST['materialtype']);
        $flagcollection= trim($_POST['col']);
        $idcollection= $flagcollection=='Y'?trim($_POST['Collection']):0;
        $title= trim($_POST['titles']);
        $cutter= trim($_POST['Cutter']);
        $isbn= trim($_POST['ISBN']);
        $issn= trim($_POST['ISSN']);
        $idcdd= trim($_POST['CDD']);
        $cdu= trim($_POST['CDU']);
        $idpublishingcompany= trim($_POST['edit']);
        $idcolor= trim($_POST['Color']);
        $idclassification= trim($_POST['classif']);


        //variaveis referente a tabela tbbookcopy
    
        $idbookcopy= ($_POST['idbookcopy']);        
        $idlibrary= ($_POST['Library']);
        $dtacquisition= ($_POST['aquis']);
        $origin= ($_POST['origin']);
        $volume= ($_POST['Volume']);
        $edition= ($_POST['Edition']);        
        $bookyear= ($_POST['Year']);
        $hascd= ($_POST['hascd']); 
        
      

        //variaveis referente a tabela tbtitle_has_author

        $idauthor= ($_POST['tabAuthor']);               

       
                 
        $this->dbTitles->BeginTrans();

        $ret = $this->dbTitles->updateTitles($idtitle,$idmaterialtype,$flagcollection,$idcollection,$title,$cutter,$isbn,$issn,$idcdd,$cdu,$idpublishingcompany,$idcolor,$idclassification);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbTitles->RollbackTrans();
            return false;
        }

        
        
        $retexemplar = $this->dbTitles->DeleteExemplar($idtitle);

        if (!$retexemplar['success']) {
            if($this->log)
                $this->logIt("Can't Delete Brand data. {$retexemplar['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbTitles->RollbackTrans();
            return false;
        }          
        
           
        
        foreach($idlibrary as $key=>$value){           
            $retexemplar = $this->dbTitles->InsertExemplar($idtitle,$value,$dtacquisition[$key],$origin[$key],$volume[$key],$edition[$key],$bookyear[$key],$hascd[($key+1)]);

            if (!$retexemplar['success']) {
                if($this->log)
                    $this->logIt("Can't update Brand data. {$retexemplar['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    $this->dbTitles->RollbackTrans();
                return false;
            }

           
        }

        $retauthor = $this->dbTitles->DeleteHasAuthor($idtitle);

        if (!$retauthor['success']) {
            if($this->log)
                $this->logIt("Can't Delete Brand data. {$retauthor['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbTitles->RollbackTrans();
            return false;
        }

        foreach($idauthor as $key=>$value){
            $retauthor = $this->dbTitles->InsertHasAuthor($idtitle,$value);

            if (!$retauthor['success']) {
                if($this->log)
                    $this->logIt("Can't Insert Brand data. {$retauthor['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                    $this->dbTitles->RollbackTrans();
                return false;
            }
        }
        

        $aRet = array(
            "success" => true,
            "brandID" => $ret['id']
        );

        $this->dbTitles->CommitTrans();

        echo json_encode($aRet);

    }

    function deleteTitles()
    {
        $this->protectFormInput();
        
        $idtitle = $_POST['idtitles_modal'];
     

        $this->dbTitles->BeginTrans();

        $retauthor = $this->dbTitles->DeleteHasAuthor($idtitle);

        if (!$retauthor['success']) {
            if($this->log)
                $this->logIt("Can't Delete Brand data. {$retauthor['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbTitles->RollbackTrans();
            return false;
        }

        $retexemplar = $this->dbTitles->DeleteExemplar($idtitle);

        if (!$retexemplar['success']) {
            if($this->log)
                $this->logIt("Can't Delete Brand data. {$retexemplar['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbTitles->RollbackTrans();
            return false;
        }          
        

        $ret = $this->dbTitles->deleteTitles($idtitle);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("Can't update Brand data. {$ret['message']}. User: {$_SESSION['SES_LOGIN_PERSON']}. Program: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
                $this->dbTitles->RollbackTrans();
            return false;
        }

        $aRet = array(
            "success"   => true,
            "brandID" => $idtitle
        );

        $this->dbTitles->CommitTrans();

        echo json_encode($aRet);

    }
    

    public function ajaxAtualCollection()
    {
        echo $this->comboCollectionHtml();
    }


    public function comboCollectionHtml()
    {
        $arrCollection = $this->_comboCollection();
        $select = '';
        
        foreach ( $arrCollection['ids'] as $indexKey => $indexValue ) {
            if ($arrCollection['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrCollection['values'][$indexKey]."</option>";
        }

        return $select;
    }


    public function ajaxAtualPublishing()
    {
        echo $this->comboPublishingHtml();
    }



    public function comboPublishingHtml()
    {
        $arrPublishing = $this->_comboPublishing_company();
        $select = '';
        
        foreach ( $arrPublishing['ids'] as $indexKey => $indexValue ) {
            if ($arrPublishing['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrPublishing['values'][$indexKey]."</option>";
        }

        return $select;
    }


    public function ajaxAtualCDD()
    {
        echo $this->comboCDDHtml();
    }


    public function comboCDDHtml()
    {
        $arrCDD = $this->_comboCDD();
        $select = '';
        
        foreach ( $arrCDD['ids'] as $indexKey => $indexValue ) {
            if ($arrCDD['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            }else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrCDD['values'][$indexKey]."</option>";
        }

        return $select;
    }


    public function ajaxAtualColor()
    {
        echo $this->comboColorHtml();
    }


    public function comboColorHtml()
    {
        $arrColor = $this->_comboColor();
        $select = '';
        
        foreach ( $arrColor['ids'] as $indexKey => $indexValue ) {
            if ($arrColor['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            }else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrColor['values'][$indexKey]."</option>";
        }

        return $select;
    }


    public function ajaxAtualClassification()
    {
        echo $this->comboClassificationHtml();
    }


    public function comboClassificationHtml()
    {
        $arrClassification = $this->_comboClassification();
        $select = '';
        
        foreach ( $arrClassification['ids'] as $indexKey => $indexValue ) {
            if ($arrClassification['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            }else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrClassification['values'][$indexKey]."</option>";
        }

        return $select;
    }
    

    public function ajaxAtualAuthor()
    {
        echo $this->comboAuthorHtml();
    }


    public function comboAuthorHtml()
    {
        $arrAuthor = $this->_comboAuthor();
        $select = '';
        
        foreach ( $arrAuthor['ids'] as $indexKey => $indexValue ) {
            if ($arrAuthor['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            }else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrAuthor['values'][$indexKey]."</option>";
        }
       
        return $select;
    }
   
  
}