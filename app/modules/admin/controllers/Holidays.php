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

        return $params;
    }

    public function jsonGrid()
    {
        $appSrc = new appServices();
        $translator = new localeServices();
        $holidayDao = new holidayDAO(); 

        $where = "";
        $group = "";
        $filterValue ="";
        if(isset($_POST["filterIndx"]) && isset($_POST["filterValue"]) )
        {
            $filterIndx = $_POST["filterIndx"];
            $filterValue = $_POST["filterValue"];
            $filterOp = $_POST["filterOperation"];
            
            $where .= (empty($where) ? "WHERE " : " AND ") . $appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);
        } 

        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $where .= (empty($where) ? "WHERE " : " AND ") . "(tbh.holiday_date LIKE '".$appSrc->_formatSaveDate($_POST['quickValue'])."' OR tbh.holiday_description LIKE '{$_POST['quickValue']}')";
        }
        
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = $pq_sort[0]->dataIndx;
        if(!$this->isValidColumn($sortIndx)){
            throw("invalid sort column");
        }
        
        $sortDir = (isset($pq_sort[0]->dir) && $pq_sort[0]->dir =="up") ? "ASC" : "DESC";
        $order = "ORDER BY {$sortIndx} {$sortDir}";
        
        $pq_curPage = !isset($_POST["pq_curpage"]) ? 1 : $_POST["pq_curpage"];    
    
        $pq_rPP = $_POST["pq_rpp"];
        
        $countHolidays = $holidayDao->queryHolidays($where,$group); 
        if(!is_null($countHolidays) && !empty($countHolidays)){
            $total_Records = count($countHolidays);
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->pageHelper($pq_curPage, $pq_rPP, $total_Records);
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
                    'idholiday'                  => $v['idholiday'],
                    'holiday_description' => $v['holiday_description'],
                    'holiday_date'        => $v['holiday_date'],
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

    //check every column name
    public function isValidColumn($dataIndx){
        
        if (preg_match('/^[a-z,A-Z]*$/', $dataIndx))
        {
            return true;
        }
        else
        {
            return false;
        }    
    }

    public function pageHelper(&$pq_curPage, $pq_rPP, $total_Records){
        $skip = ($pq_rPP * ($pq_curPage - 1));

        if ($skip >= $total_Records)
        {        
            $pq_curPage = ceil($total_Records / $pq_rPP);
            $skip = ($pq_rPP * ($pq_curPage - 1));
        }    
        return $skip;
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



}