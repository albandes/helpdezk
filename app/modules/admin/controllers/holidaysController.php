<?php

require_once(HELPDEZK_PATH . '/app/modules/admin/controllers/admCommonController.php');

class Holidays extends admCommon
{
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

        $this->idPerson = $_SESSION['SES_COD_USUARIO'];

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('holidays');

        $this->loadModel('holidays_model');
        $dbHoliday = new holidays_model();
        $this->dbHoliday = $dbHoliday;

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language

        $smarty->display('holidays.tpl');

    }

    public function jsonGrid()
    {
        $this->validasessao();
        $smarty = $this->retornaSmarty();

        $this->protectFormInput();

        $where = '';

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='holiday_date';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            if ( $_POST['searchField'] == 'holiday_description') $searchField = 'tbh.holiday_description';
            if ( $_POST['searchField'] == 'holiday_date'){
                $searchField = "tbh.holiday_date";
                $_POST['searchString'] = str_replace("'", "",$this->formatSaveDate($_POST['searchString']));
            }
            if ( $_POST['searchField'] == 'tbp.name') $searchField = 'tbp.name';

            if (empty($where))
                $oper = ' WHERE ';
            else
                $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->dbHoliday->CountHoliday($where);

        if( $count->fields['total'] > 0 && $rows > 0) {
            $total_pages = ceil($count->fields['total']/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $rows*$page - $rows;
        if($start <0) $start = 0;

        $order = "ORDER BY $sidx $sord";
        $limit = "LIMIT $start , $rows";
        //

        $rsHolidays = $this->dbHoliday->selectHoliday($where,$order,null,$limit);
        
        while (!$rsHolidays->EOF) {
            
            if(isset($rsHolidays->fields['idperson'])){
				$type_holiday = $rsHolidays->fields['name'];
			}else{
                $type_holiday = $this->getLanguageWord('National_holiday');
            }
            
            $aColumns[] = array(
                'id'                  => $rsHolidays->fields['idholiday'],
                'holiday_description' => $rsHolidays->fields['holiday_description'],
                'holiday_date'        => $rsHolidays->fields['holiday_date'],
                'company'             => $type_holiday

            );
            $rsHolidays->MoveNext();
        }


        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count->fields['total'],
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreateHolidays()
    {
        $smarty = $this->retornaSmarty();

        $this->makeScreenHolidays($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $this->datepickerSettings($smarty);//set up datepicker options by language
        $smarty->display('holidays-create.tpl');
    }

    public function formUpdateHolidays()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $idHolidays = $this->getParam('idholiday');
        
        $rsHoliday = $this->dbHoliday->getHoliday('WHERE a.idholiday = ' . $idHolidays);

        $this->makeScreenHolidays($smarty,$rsHoliday,'update');

        $smarty->assign('token', $token) ;

        $smarty->assign('hidden_idholidays', $idHolidays);

        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language

        $smarty->display('holidays-update.tpl');

    }

    function formImportHolidays()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $smarty = $this->retornaSmarty();

        $this->makeScreenHolidays($smarty,'','import');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language

        $smarty->display('holidays-import.tpl');

    }

    function makeScreenHolidays($objSmarty,$rs,$oper)
    {
        // --- Holiday description ---
        if ($oper == 'update') {
            if (empty($rs->fields['holiday_description']))
                $objSmarty->assign('plh_holiday_description', $this->getLanguageWord('plh_holiday_description'));
            else
                $objSmarty->assign('holiday_description',$rs->fields['holiday_description']);
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_holiday_description', $this->getLanguageWord('plh_holiday_description'));
        } elseif ($oper == 'echo') {
            $objSmarty->assign('holiday_description',$rs->fields['holiday_description']);
        }

        // --- Holiday date ---
        if ($oper == 'update') {
            if (empty($rs->fields['holiday_date']))
                $objSmarty->assign('plh_holiday_date', $this->getLanguageWord('plh_holiday_date'));
            else{
                if ($this->database == 'oci8po') {
                    $dataformatada = $rs->fields['holiday_date'];
                }else{
                    $dataformatada = $this->formatDate($rs->fields['holiday_date']);
                }
                $objSmarty->assign('holiday_date',$dataformatada);
            }
        } elseif ($oper == 'create') {
            $objSmarty->assign('plh_holiday_date', $this->getLanguageWord('plh_holiday_date'));
        } elseif ($oper == 'echo') {
            if ($this->database == 'oci8po') {
                $dataformatada = $rs->fields['holiday_date'];
            }else{
                $dataformatada = $this->formatDate($rs->fields['holiday_date']);
            }
            $objSmarty->assign('holiday_date',$dataformatada);
        }

        // --- Company ---
        if ($oper == 'update') {
            $idCompanyEnable = $rs->fields['idperson'];
        }elseif ($oper == 'echo') {
            if($rs->fields['idperson'] == 0){$nameCompany = $this->getLanguageWord('National_holiday');}
            else{$nameCompany = $rs->fields['name'];}
            $objSmarty->assign('company_name',$nameCompany);
        }elseif($oper == 'create') {
            $idCompanyEnable = 0;
        } 
        
        $arrCompany = $this->_comboCompany();

        array_push($arrCompany['ids'],0);
        array_push($arrCompany['values'],$this->getLanguageWord('National_holiday'));
        
        $objSmarty->assign('companyids',  $arrCompany['ids']);
        $objSmarty->assign('companyvals',$arrCompany['values']);
        $objSmarty->assign('idcompany', $idCompanyEnable );

        // --- Previous year ---
        $arrPreviousYear = $this->_comboLastYear();
        if ($oper == 'update') {
            $idPreviousYearEnable = $rs->fields['idperson'];
        }elseif ($oper == 'echo') {
            if($rs->fields['idperson'] == 0){$nameCompany = $this->getLanguageWord('National_holiday');}
            else{$nameCompany = $rs->fields['name'];}
            $objSmarty->assign('company_name',$nameCompany);
        }elseif($oper == 'create') {
            $idPreviousYearEnable = $arrPreviousYear['ids'][0];
        } 

        $objSmarty->assign('lastyearids',  $arrPreviousYear['ids']);
        $objSmarty->assign('lastyearvals',$arrPreviousYear['values']);
        $objSmarty->assign('idlastyear', $idPreviousYearEnable );

        // --- Next year ---
        $arrNextYear = $this->_comboNextYear();
        if ($oper == 'update') {
            $idNextYearEnable = $rs->fields['idperson'];
        }elseif ($oper == 'echo') {
            if($rs->fields['idperson'] == 0){$nameCompany = $this->getLanguageWord('National_holiday');}
            else{$nameCompany = $rs->fields['name'];}
            $objSmarty->assign('company_name',$nameCompany);
        } else {
            $idNextYearEnable = $arrNextYear['ids'][0];
        } 

        $objSmarty->assign('nextyearids',  $arrNextYear['ids']);
        $objSmarty->assign('nextyearvals',$arrNextYear['values']);
        $objSmarty->assign('idnextyear', $idNextYearEnable );

    }

    function createHoliday()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->protectFormInput();

        $this->dbHoliday->BeginTrans();

        $data = array(
            'holiday_date' => $this->formatSaveDate($_POST['holiday_date']),
            'holiday_description' => "'{$_POST['holiday_description']}'"
        );

        $ins = $this->dbHoliday->insertHoliday($data);
		if(!$ins){
			$this->dbHoliday->RollbackTrans();
			return false;
        }
        
        $id_holiday = $this->dbHoliday->TableMaxID('tbholiday','idholiday');
		
		if($_POST['company'] != 0){			
			$data = array(
                'idholiday' => $id_holiday,
                'idperson' => $_POST['company']
            );
			
			$ins = $this->dbHoliday->insertHolidayHasCompany($data);
			if(!$ins){
				$this->dbHoliday->RollbackTrans();
				return false;
			}
		}

        $aRet = array(
            "idholiday" => $id_holiday,
            "description" => $_POST['holiday_description']
        );

        $this->dbHoliday->CommitTrans();

        echo json_encode($aRet);

    }

    function updateHoliday()
    {
        $this->protectFormInput();

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idHoliday = $_POST['idholiday'];

        $this->dbHoliday->BeginTrans();

        $desc = $_POST['holiday_description'];
		$holiday_date = $_POST['holiday_date'];

        $ret = $this->dbHoliday->updateHoliday($idHoliday, $desc, $this->formatSaveDate($holiday_date));

        if (!$ret) {
            $this->dbHoliday->RollbackTrans();
            if($this->log)
                $this->logIt('Update Holiday - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "idholiday" => $idHoliday,
            "status"   => 'OK'
        );

        $this->dbHoliday->CommitTrans();

        echo json_encode($aRet);


    }

    public function modalDeleteHoliday()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);

        $this->protectFormInput();

        $idholiday = $_POST['idholiday'];

        $aRet = array(
            "idholiday" => $idholiday,
            "token" => $token
        );

        echo json_encode($aRet);

    }

    function deleteHoliday()
    {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->protectFormInput();

        $id = $_POST['idholiday'];

        $this->dbHoliday->BeginTrans();
		
		$retCompany = $this->dbHoliday->holidayDeleteHasCompany($id);
		if(!$retCompany){
            $this->dbHoliday->RollbackTrans();
            if($this->log)
                $this->logIt('Delete Holidays Company - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
			return false;
		}
		
        $ret = $this->dbHoliday->holidayDelete($id);
		if(!$ret){
            $this->dbHoliday->RollbackTrans();
            if($this->log)
                $this->logIt('Delete Holiday - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
			return false;
		}

        $this->dbHoliday->CommitTrans();

        $aRet = array(
            "status" => "OK"
        );

        echo json_encode($aRet);

    }

    public function ajaxYearByCompany()
    {
        echo $this->_comboYearByCompanyHtml($_POST['companyId']);
    }

    public function _comboYearByCompanyHtml($companyId)
    {
        if($companyId != 0 && $companyId != "X"){$cond = 'and b.idperson = ' . $companyId;}
        else{$cond = '';}

        $arrYear = $this->_comboLastYear($cond);
        $select = '';

        if(sizeof($arrYear['ids']) == 0){
            $select .= "<option value=''>- {$this->getLanguageWord('no_holidays_for_company')} -</option>";
        }else{
            $select .= "<option value='X'>&nbsp;</option>";
            foreach ( $arrYear['ids'] as $indexKey => $indexValue ) {
                if ($arrYear['default'][$indexKey] == 1) {
                    $default = 'selected="selected"';
                } else {
                    $default = '';
                }
                $select .= "<option value='$indexValue' $default>".$arrYear['values'][$indexKey]."</option>";
            }
        }

        
        return $select;
    }

    public function _comboNextYearHtml()
    {
        $arrYear = $this->_comboNextYear();
        foreach ( $arrYear['ids'] as $indexKey => $indexValue ) {
            if ($arrNextYear['ids'][0]) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrYear['values'][$indexKey]."</option>";
        }

        
        return $select;
    }

    public function load()
    {
		$smarty = $this->retornaSmarty();

        $this->protectFormInput();

        $year = $_POST['prevyear'];
        $idcompany = $_POST['companyId'];

        if($idcompany == 0){
            $where = "
                        WHERE YEAR(a.holiday_date) = $year
                        AND b.idperson IS NULL
                        ORDER BY holiday_date
                    ";
        }else{
            $where = "
                        WHERE YEAR(a.holiday_date) = $year
                        AND c.idperson=$idcompany
                        ORDER BY holiday_date
                    ";
        }        

        $ret = $this->dbHoliday->getHoliday($where) ;

		$count = $ret->RecordCount();

        $date = date('Y');
		$i = 0;		
        
        $list = '';
		while (!$ret->EOF) {
			
			if($ret->fields['idperson'] != 0){
				$type_holiday = $ret->fields['name'];
			}else{
				$type_holiday =  $this->getLanguageWord('National_holiday');
			}
			
            $dataformatada = $this->formatDate($ret->fields['holiday_date']);
            
            $list .= "<tr>
                        <td>".$dataformatada."</td>
                        <td>".$ret->fields['holiday_description']."</td>
                        <td>".$type_holiday."</td>
                    </tr>";
			$i++;
			$ret->MoveNext();
        }

        $resultado = array(
            'year' => $year,
            'count' => $ret->RecordCount(),
            'result' => $list,
            'yearto' => $this->_comboNextYearHtml()
        );

		echo json_encode($resultado);
    }

    public function import() {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->protectFormInput();

        $year = $_POST['lastyear'];
		$nextyear = $_POST['nextyear'];
        $idcompany = $_POST['company'];
        
        if(!$nextyear) return false;

        $this->dbHoliday->BeginTrans();
        
        $count = $this->dbHoliday->countAllHolidays($year);
        
        if($idcompany == 0){
            $where = "WHERE YEAR(a.holiday_date) = $year
                        AND b.idperson IS NULL
                        ORDER BY holiday_date
                    ";
        }else{
            $where = "WHERE YEAR(a.holiday_date) = $year
                        AND c.idperson=$idcompany
                        ORDER BY holiday_date
                    ";
        }

        $sel = $this->dbHoliday->getHoliday($where) ;

		while (!$sel->EOF) {
            $desc = $sel->fields['holiday_description'];
			$newdate = $sel->fields['holiday_date'];

            $newdate = substr($newdate, 4);
			$newdate = $nextyear . $newdate;
			
			$database = $this->getConfig('db_connect');
			if ($database == 'oci8po') {
                $newdate = $sel->fields['holiday_date'];
                $newdate = substr($newdate, 0, 6);
                $newdate = $newdate . $nextyear;
				$dataformatada = $newdate ;
				$newdate = "to_date('".$dataformatada."','DD/MM/YYYY')" ;
                $ins = $this->dbHoliday->insertHoliday(array('holiday_date' => $newdate,'holiday_description' => "'".addslashes($desc)."'"));
                if (!$ins) {
                    $this->dbHoliday->RollbackTrans();
                    if($this->log)
                        $this->logIt('Import Holidays - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
			}elseif($database == "mysqli"){
				$ins = $this->dbHoliday->insertHoliday(array('holiday_date' => "'".$newdate."'",'holiday_description' => "'".addslashes($desc)."'"));
                if (!$ins) {
                    $this->dbHoliday->RollbackTrans();
                    if($this->log)
                        $this->logIt('Import Holidays - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
                $id_holiday = $this->dbHoliday->TableMaxID('tbholiday','idholiday');

                if($sel->fields['idperson'] != 0){
                    $data = array( 'idholiday' => $id_holiday, 'idperson' => $sel->fields['idperson'] );
                    $ret = $this->dbHoliday->insertHolidayHasCompany($data);
                    if (!$ret) {
                        $this->dbHoliday->RollbackTrans();
                        if($this->log)
                            $this->logIt('Import Holidays - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                        return false;
                    }
                }
                
			}

			$sel->MoveNext();
        }

        $aRet = array(
            "status"   => 'OK'
        );
        
        $this->dbHoliday->CommitTrans();

        echo json_encode($aRet);

	}

}