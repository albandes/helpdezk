<?php

use App\core\Controller;


use App\modules\admin\dao\mysql\holidayDAO;

use App\modules\admin\src\adminServices;
use App\src\appServices;
use App\src\localeServices;


class Holidays extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
    }

    /**
     * en_us Renders the holidays home screen template
     *
     * pt_br Renderiza o template da tela de home de feriados
     */
    public function index()
    {
        $params = $this->makeScreenHolidays();
		
		$this->view('admin','holidays',$params);
    }

    /**
	 *  en_us Configure program screens
	 * 
	 *  pt_br Configura as telas do programa
	 */
    public function makeScreenHolidays($option='idx',$obj=null)
    {
        $appSrc = new appServices();
        $adminSrc = new adminServices();
        $translator = new localeServices();
        $params = $appSrc->_getDefaultParams();
        $params = $adminSrc->_makeNavAdm($params);
        
        // -- Datepicker settings -- 
        $retDtpicker = $appSrc->_datepickerSettings();
        $params['dtpFormat'] = $retDtpicker['dtpFormat'];
        $params['dtpLanguage'] = $retDtpicker['dtpLanguage'];
        $params['dtpAutoclose'] = $retDtpicker['dtpAutoclose'];
        $params['dtpOrientation'] = $retDtpicker['dtpOrientation'];
        $params['dtpickerLocale'] = $retDtpicker['dtpickerLocale'];
        $params['dtSearchFmt'] = $retDtpicker['dtSearchFmt'];
        
        // -- Companies --
        $params['cmbCompanies'] = $adminSrc->_comboCompany();
        array_push($params['cmbCompanies'],array("id"=>0,"text"=>$translator->translate('National_holiday')));

        // -- Search action --
        if($option=='idx'){
            $params['cmbFilterOpts'] = $appSrc->_comboFilterOpts();
            $params['cmbFilters'] = $this->comboHolidayFilters();
        }

        if($option=='upd'){
            $params['idholiday'] = $obj->getIdholiday();
            $params['holidayDesc'] = $obj->getDescription();
            $params['holidayDate'] = $appSrc->_formatDate($obj->getDate());           
        }elseif($option=='add'){
            $params['companyID'] = $obj->getIdcompany();
        }

        // -- Last year --
        $params['cmbLastYear'] = $adminSrc->_comboLastYear();
      
        return $params;
    }

    public function jsonGrid()
    {
        $appSrc = new appServices();
        $translator = new localeServices();
        $holidayDao = new holidayDAO(); 

        $where = "";
        $group = "";

        //Search with params sended from filter's modal
        $filterValue ="";
        if(isset($_POST["filterIndx"]) && isset($_POST["filterValue"]) )
        {
            $filterIndx = $_POST["filterIndx"];
            $filterValue = $_POST["filterValue"];
            $filterOp = $_POST["filterOperation"];
            
            $where .= (empty($where) ? "WHERE " : " AND ") . $appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);
        } 
        
        //Search with params sended from quick search input
        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $quickValue = trim($_POST['quickValue']);
            if(strtotime($quickValue)){
                $where .= (empty($where) ? "WHERE " : " AND ") . "tbh.holiday_date LIKE '".$appSrc->_formatSaveDate($quickValue)."'";// it's in date format
            }else{
                $quickValue = str_replace(" ","%",$quickValue);
                $where .= (empty($where) ? "WHERE " : " AND ") . "tbh.holiday_description LIKE '%{$quickValue}%'";
            }
        }
        
        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = $pq_sort[0]->dataIndx;
        
        $sortDir = (isset($pq_sort[0]->dir) && $pq_sort[0]->dir =="up") ? "ASC" : "DESC";
        $order = "ORDER BY {$sortIndx} {$sortDir}";
        
        $pq_curPage = !isset($_POST["pq_curpage"]) ? 1 : $_POST["pq_curpage"];    
    
        $pq_rPP = $_POST["pq_rpp"];
        
        //Count records
        $countHolidays = $holidayDao->queryHolidays($where,$group); 
        if(!is_null($countHolidays) && !empty($countHolidays)){
            $total_Records = count($countHolidays);
        }else{
            $total_Records = 0;
        }
        
        $skip = $appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $holidays = $holidayDao->queryHolidays($where,$group,$order,$limit);
        
        if(!is_null($holidays) && !empty($holidays)){     
            
            foreach($holidays as $k=>$v) {
                if(isset($v['idperson'])){
                    $type_holiday = $v['name'];
                }else{
                    $type_holiday = $translator->translate('National_holiday');
                }

                $data[] = array(
                    'idholiday'           => $v['idholiday'],
                    'holiday_description' => $v['holiday_description'],//utf8_decode($v['holiday_description']),
                    'holiday_date'        => $appSrc->_formatDate($v['holiday_date']),
                    'company'             => $type_holiday
    
                );
            }
            $aRet = array(
                "totalRecords" => $total_Records,
                "curPage" => $pq_curPage,
                "data" => $data
            );

            echo json_encode($aRet);
            
        }else{
            echo json_encode(array());            
        }
    }

    /**
     * Returns an array with ID and name of filters
     *
     * @return array
     */
    public function comboHolidayFilters(): array
    {
        $translator = new localeServices();

        $aRet = array(
            array("id" => 'holiday_description',"text"=>$translator->translate('Name')), // equal
            array("id" => 'holiday_date',"text"=>$translator->translate('Date'))
        );
        
        return $aRet;
    }

    /**
     * en_us Renders the holidays add screen
     *
     * pt_br Renderiza o template da tela de novo cadastro
     */
    public function formCreate()
    {
        $params = $this->makeScreenHolidays();
        
        $this->view('admin','holidays-create',$params);
    }

    /**
     * en_us Renders the holidays update screen
     *
     * pt_br Renderiza o template da tela de atualização do cadastro
     */
    public function formUpdate($idholiday=null)
    {
        $holidayDao = new holidayDAO(); 
        $holidayUpd = $holidayDao->getHoliday($idholiday);

        $params = $this->makeScreenHolidays('upd',$holidayUpd);
        $params['holidayID'] = $idholiday;
      
        $this->view('admin','holidays-update',$params);
    }

    /**
     * en_us Renders the holidays add screen
     *
     * pt_br Renderiza o template da tela de novo cadastro
     */
    public function formImport()
    {
        $params = $this->makeScreenHolidays();
        
        $this->view('admin','holidays-import',$params);
    }

    /**
     * en_us Write the holiday information to the DB
     *
     * pt_br Grava no BD as informações do feriado
     */
    public function createHoliday()
    {
        /*if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }*/
        
        $appSrc = new appServices();
        $holidayDao = new holidayDAO();
        
        $dtholiday = $appSrc->_formatSaveDate($_POST['holiday_date']);
        $description = trim($_POST['holiday_description']);
        $companyID = $_POST['company'];
        
        $ins = $holidayDao->insertHoliday($dtholiday,$description);
		    if(is_null($ins) || empty($ins)){
			    return false;
        }        
        
        $holidayID = $ins->getIdholiday();
        
        //Link holiday with the company
        if($companyID != 0){
            $insCompany = $holidayDao->insertHolidayHasCompany($holidayID,$companyID);
            if(is_null($insCompany) || empty($insCompany)){
                return false;
            }
        }

        $aRet = array(
            "idholiday" => $holidayID,
            "description" => $description
        );        

        echo json_encode($aRet);
    }

    /**
     * en_us Update the holiday information to the DB
     *
     * pt_br Atualiza no BD as informações do feriado
     */
    public function updateHoliday()
    {
        /*if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }*/
        
        $appSrc = new appServices();
        $holidayDao = new holidayDAO();
        
        $holidayID = $_POST['holidayID'];
        $dtholiday = $appSrc->_formatSaveDate($_POST['holiday_date']);
        $description = trim($_POST['holiday_description']);
        
        $upd = $holidayDao->updateHoliday($holidayID,$dtholiday,$description);
        if(is_null($upd) || empty($upd)){
            return false;
        }        
        
        $aRet = array(
            "success" => true,
            "idholiday" => $holidayID
        );        

        echo json_encode($aRet);
    }

    public function ajaxYearByCompany()
    {
        echo $this->comboYearByCompanyHtml($_POST['companyID']);
    }

    public function comboYearByCompanyHtml($companyID)
    {
        $holidayDao = new holidayDAO();
        $translator = new localeServices();
        
        $companyYear = $holidayDao->fetchHolidayYearsByCompany($companyID);
        $select = '';
        
        if(is_null($companyYear) || empty($companyYear) || sizeof($companyYear) == 0){
            $select .= "<option value='X'> - {$translator->translate('no_holidays_for_company')} - </option>";
        }else{
            $select .= "<option></option>";
            foreach ($companyYear as $key=>$value) {
                $select .= "<option value='{$value['holiday_year']}'>{$value['holiday_year']}</option>";
            }
        }
        
        return $select;
    }

    public function comboNextYearHtml()
    {
        $adminSrc = new adminServices();
        $arrYear = $adminSrc->_comboNextYear();
        $select = "<option></option>";

        foreach ($arrYear as $key=>$value) {
            $select .= "<option value='{$value['id']}'>".$value['text']."</option>";
        }        
        return $select;
    }

    public function load()
    {
        $holidayDao = new holidayDAO();
        $translator = new localeServices();
        $appSrc = new appServices();

        $year = $_POST['prevyear'];
        $companyID = $_POST['companyID'];       

        $loadHoliday = $holidayDao->fetchHolidays($companyID,$year);
        $count = 0;
        $list = '';

        if(!is_null($loadHoliday) && !empty($loadHoliday)){
            $count = count($loadHoliday);            
            
            foreach($loadHoliday as $key=>$val) {
                $type_holiday = ($val['idperson'] != 0) ? $val['name'] : $translator->translate('National_holiday');
                
                $dataformatada = $appSrc->_formatDate($val['holiday_date']);
                
                $list .= "<tr>
                            <td>".$dataformatada."</td>
                            <td>".$val['holiday_description']."</td>
                            <td>".$type_holiday."</td>
                        </tr>";
            }
        }

        $resultado = array(
            'year' => $year,
            'count' => $count,
            'result' => $list,
            'yearto' => $this->comboNextYearHtml()
        );
        
        echo json_encode($resultado);
    }

    public function import() {

        /*if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }*/

        $holidayDao = new holidayDAO();
        $translator = new localeServices();
        $appSrc = new appServices();

        $year = $_POST['lastyear'];
        $nextyear = $_POST['nextyear'];
        $companyID = $_POST['company'];

        $loadHoliday = $holidayDao->fetchHolidays($companyID,$year);
        $count = 0;
        $list = '';

        if(!is_null($loadHoliday) && !empty($loadHoliday)){
            $count = count($loadHoliday);            
            
            foreach($loadHoliday as $key=>$val) {
                $desc = $val['holiday_description'];
                $newdate = $val['holiday_date'];

                $newdate = substr($newdate,4);
                $newdate = $nextyear . $newdate;
                
                $ins = $holidayDao->insertHoliday($newdate,$desc);
                if(is_null($ins) || empty($ins)){
                    return false;
                }        
                
                $holidayID = $ins->getIdholiday();
                
                //Link holiday with the company
                if($val['idperson'] != 0){			
                    $insCompany = $holidayDao->insertHolidayHasCompany($holidayID,$val['idperson']);
                    if(is_null($insCompany) || empty($insCompany)){
                        return false;
                    }
                }
            }
        }else{
            return false;
        }

        $aRet = array(
            "success"   => true
        );

        echo json_encode($aRet);
    
    }

    function deleteHoliday()
    {

        /*if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }*/

        $holidayDao = new holidayDAO();

        $id = $_POST['holidayID'];
        
        $delCompany = $holidayDao->deleteHolidayCompany($id);
        if(is_null($delCompany)){
            return false;
        }
        
        $del = $holidayDao->deleteHoliday($id);
		if(is_null($del) || empty($del)){
            return false;
        }

        $aRet = array(
            "success" => true
        );

        echo json_encode($aRet);

    }


}