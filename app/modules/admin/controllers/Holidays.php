<?php

use App\core\Controller;


use App\modules\admin\dao\mysql\holidayDAO;
use App\modules\admin\models\mysql\holidayModel;

use App\modules\admin\src\adminServices;
use App\src\appServices;
use App\src\localeServices;


class Holidays extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->appSrc->_sessionValidate();
        
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
        $adminSrc = new adminServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $adminSrc->_makeNavAdm($params);

        // -- Token: to prevent attacks --
        $params['token'] = $this->appSrc->_makeToken();
        
        // -- Datepicker settings -- 
        $retDtpicker = $this->appSrc->_datepickerSettings();
        $params['dtpFormat'] = $retDtpicker['dtpFormat'];
        $params['dtpLanguage'] = $retDtpicker['dtpLanguage'];
        $params['dtpAutoclose'] = $retDtpicker['dtpAutoclose'];
        $params['dtpOrientation'] = $retDtpicker['dtpOrientation'];
        $params['dtpickerLocale'] = $retDtpicker['dtpickerLocale'];
        $params['dtSearchFmt'] = $retDtpicker['dtSearchFmt'];
        
        // -- Companies --
        $params['cmbCompanies'] = $adminSrc->_comboCompany();
        array_push($params['cmbCompanies'],array("id"=>0,"text"=>$this->translator->translate('National_holiday')));

        // -- Search action --
        if($option=='idx'){
            $params['cmbFilterOpts'] = $this->appSrc->_comboFilterOpts();
            $params['cmbFilters'] = $this->comboHolidayFilters();
            $params['modalFilters'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-search-filters.latte';
        }

        // -- Others modals --
        $params['modalAlert'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-alert.latte';
        $params['modalError'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-error.latte';

        if($option=='upd'){
            $params['idholiday'] = $obj->getIdholiday();
            $params['holidayDesc'] = $obj->getDescription();
            $params['holidayDate'] = $this->appSrc->_formatDate($obj->getDate());           
        }elseif($option=='add'){
            $params['companyID'] = $obj->getIdcompany();
        }

        // -- Last year --
        $params['cmbLastYear'] = $adminSrc->_comboLastYear();
      
        return $params;
    }

    public function jsonGrid()
    {
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
            
            $where .= (empty($where) ? "WHERE " : " AND ") . $this->appSrc->_formatGridOperation($filterOp,$filterIndx,$filterValue);
        } 
        
        //Search with params sended from quick search input
        if(isset($_POST["quickSearch"]) && $_POST["quickSearch"])
        {
            $quickValue = trim($_POST['quickValue']);
            if(strtotime($quickValue)){
                $where .= (empty($where) ? "WHERE " : " AND ") . "tbh.holiday_date LIKE '".$this->appSrc->_formatSaveDate($quickValue)."'";// it's in date format
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
        if($countHolidays['status']){
            $countObj = $countHolidays['push']['object']->getGridList();
            $total_Records = count($countObj);
        }else{
            $total_Records = 0;
        }
        
        $skip = $this->appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $holidays = $holidayDao->queryHolidays($where,$group,$order,$limit);
        
        if($holidays['status']){     
            $holidaysObj = $holidays['push']['object']->getGridList();

            foreach($holidaysObj as $k=>$v) {
                if(isset($v['idperson'])){
                    $type_holiday = $v['name'];
                }else{
                    $type_holiday = $this->translator->translate('National_holiday');
                }

                $data[] = array(
                    'idholiday'           => $v['idholiday'],
                    'holiday_description' => $v['holiday_description'],//utf8_decode($v['holiday_description']),
                    'holiday_date'        => $this->appSrc->_formatDate($v['holiday_date']),
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
        $aRet = array(
            array("id" => 'holiday_description',"text"=>$this->translator->translate('Name')), // equal
            array("id" => 'holiday_date',"text"=>$this->translator->translate('Date'))
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
        $holidayMod = new holidayModel();


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
        $holidayDao = new holidayDAO();
        $holidayMod = new holidayModel();

        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $holidayMod->setDate($this->appSrc->_formatSaveDate($_POST['holiday_date']))
                   ->setDescription(trim($_POST['holiday_description']));        
        
        $ins = $holidayDao->insertHoliday($holidayMod);
        if($ins['status']){
            $companyID = $_POST['company'];

            $st = true;
            $msg = "";
            $holidayID = $ins['push']['object']->getIdholiday();
            $holidayDescription = $ins['push']['object']->getDescription();
            
            //Link holiday with the company
            if($companyID != 0){
                $ins['push']['object']->setIdcompany($companyID);
               
                $insCompany = $holidayDao->insertHolidayHasCompany($ins['push']['object']);
                if(!$insCompany['status']){
                    $st = false;
                    $msg = $insCompany['push']['message'];
                    $holidayID = "";
                    $holidayDescription = "";
                }
            }
        }else{
            $st = false;
            $msg = $ins['push']['message'];
            $holidayID = "";
            $holidayDescription = "";
        }       
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "idholiday" => $holidayID,
            "description" => $holidayDescription
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
        $holidayDao = new holidayDAO();
        $holidayMod = new holidayModel();

        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $holidayMod->setIdholiday($_POST['holidayID'])
                   ->setDate($this->appSrc->_formatSaveDate($_POST['holiday_date']))
                   ->setDescription(trim($_POST['holiday_description']));
        
        $upd = $holidayDao->updateHoliday($holidayMod);
        
        if(!$upd['status']){
            $st = false;
        }else{
            $st = true;
        }        
        
        $aRet = array(
            "success" => $st,
            "message" => $upd['push']['message'],
            "idholiday" => (!is_null($upd['push']['object']) && !empty($upd['push']['object'])) ? $holidayMod->getIdholiday() : ""
        );        

        echo json_encode($aRet);
    }

    /**
     * en_us Returns on screen the list of holidays years of the selected company
     *
     * pt_br Retorna em tela a lista de anos de feriados da empresa selecionada
     */
    public function ajaxYearByCompany()
    {
        echo $this->comboYearByCompanyHtml($_POST['companyID']);
    }
    
    /**
     * en_us Gets the list of holidays of the selected company from the DB and formats it in options of the select tag
     *
     * pt_br Obtém a lista de feriados da empresa selecionada do BD e formata em options da tag select
     * 
     * @param  int $companyID
     * @return string
     */
    public function comboYearByCompanyHtml(int $companyID): string
    {
        $holidayDao = new holidayDAO();
        $holidayModel = new holidayModel();
        $holidayModel->setIdcompany($companyID);
        
        $retCompanyYear = $holidayDao->fetchHolidayYearsByCompany($holidayModel);
        $select = '';
        
        if(!$retCompanyYear['status'] || sizeof($retCompanyYear['push']['object']->getYearList()) == 0){
            $select .= "<option value='X'> - {$this->translator->translate('no_holidays_for_company')} - </option>";
        }else{
            $companyYear = $retCompanyYear['push']['object']->getYearList();
            $select .= "<option></option>";
            foreach ($companyYear as $key=>$value) {
                $select .= "<option value='{$value['holiday_year']}'>{$value['holiday_year']}</option>";
            }
        }
        
        return $select;
    }

    /**
     * en_us Formats the list of years in the select tag's options
     *
     * pt_br Formata em opções selecionadas de HTML
     * 
     * @param  int $companyID
     * @return string
     */
    public function comboNextYearHtml(): string
    {
        $adminSrc = new adminServices();
        $arrYear = $adminSrc->_comboNextYear();
        $select = "<option></option>";

        foreach ($arrYear as $key=>$value) {
            $select .= "<option value='{$value['id']}'>".$value['text']."</option>";
        }        
        return $select;
    }

    /**
     * en_us Returns the list of selected company and year holidays to the screen
     *
     * pt_br Retorna em tela a lista de feriados da empresa e ano selecionados
     */
    public function load()
    {
        $holidayDao = new holidayDAO();
        $holidayModel = new holidayModel();

        $holidayModel->setYear($_POST['prevyear'])
                     ->setIdcompany($_POST['companyID']);       

        $retLoad = $holidayDao->fetchHolidays($holidayModel);
        $count = 0;
        $list = '';

        if($retLoad['status']){
            $loadHoliday = $retLoad['push']['object']->getGridList();
            $count = count($loadHoliday);            
            
            foreach($loadHoliday as $key=>$val) {
                $type_holiday = ($val['idperson'] != 0) ? $val['name'] : $this->translator->translate('National_holiday');
                
                $dataformatada = $this->appSrc->_formatDate($val['holiday_date']);
                
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

    /**
     * en_us Writes the holidays of the previous year and selected company in the DB
     *
     * pt_br Grava no BD os feriados do ano anterior e empresa selecionados
     */
    public function import() {

        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $holidayDao = new holidayDAO();
        $holidayModel = new holidayModel();

        $nextyear = $_POST['nextyear'];
        $holidayModel->setYear($_POST['lastyear'])
                     ->setNextYear($nextyear)
                     ->setIdcompany($_POST['company']);       

        $retLoad = $holidayDao->fetchHolidays($holidayModel);
        $count = 0;
        $list = '';
        
        if($retLoad['status']){
            $loadHoliday = $retLoad['push']['object']->getGridList();
            $count = count($loadHoliday);            
            
            foreach($loadHoliday as $key=>$val) {
                $desc = $val['holiday_description'];
                $newdate = $val['holiday_date'];

                $newdate = substr($newdate,4);
                $newdate = $nextyear . $newdate;
                $holidayModel->setDate($newdate)
                             ->setDescription($desc);
                
                $ins = $holidayDao->insertHoliday($holidayModel);
                if(!$ins['status']){
                    return false;
                }        
                
                //Link holiday with the company
                if($val['idperson'] != 0){			
                    $insCompany = $holidayDao->insertHolidayHasCompany($ins['push']['object']);
                    if(!$insCompany['status']){
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

    /**
     * en_us Remove the holiday from the DB
     *
     * pt_br Remove o feriado do BD
     */
    function deleteHoliday()
    {

        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $holidayDao = new holidayDAO();
        $holidayModel = new holidayModel();
        
        $holidayModel->setIdholiday($_POST['holidayID']);
        
        $retDelCompany = $holidayDao->deleteHolidayCompany($holidayModel);
        if(!$retDelCompany['status']){
            return false;
        }
        
        $retDel = $holidayDao->deleteHoliday($holidayModel);
		if(!$retDel['status']){
            return false;
        }

        $aRet = array(
            "success" => true
        );

        echo json_encode($aRet);

    }


}