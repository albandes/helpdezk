<?php

use App\core\Controller;


use App\modules\admin\dao\mysql\personDAO;
use App\modules\admin\models\mysql\personModel;

use App\modules\admin\src\adminServices;

class Person extends Controller
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
        $params = $this->appSrc->_datepickerSettings($params);
        
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
            $params['idholiday'] = $obj->getIdHoliday();
            $params['holidayDesc'] = $obj->getDescription();
            $params['holidayDate'] = $this->appSrc->_formatDate($obj->getDate());           
        }elseif($option=='add'){
            $params['companyID'] = $obj->getIdCompany();
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
            $quickValue = str_replace(" ","%",$quickValue);
            $where .= (empty($where) ? "WHERE " : " AND ") . "(tbh.holiday_description LIKE '%{$quickValue}%' OR tbh.holiday_date LIKE '".$this->appSrc->_formatSaveDate($quickValue)."')";
        }
        
        //sort options
        $pq_sort = json_decode($_POST['pq_sort']);
        $sortIndx = $pq_sort[0]->dataIndx;
        
        $sortDir = (isset($pq_sort[0]->dir) && $pq_sort[0]->dir =="up") ? "ASC" : "DESC";
        $order = "ORDER BY {$sortIndx} {$sortDir}";
        
        $pq_curPage = !isset($_POST["pq_curpage"]) ? 1 : $_POST["pq_curpage"];    
    
        $pq_rPP = $_POST["pq_rpp"];
        
        //Count records
        $countHolidays = $holidayDao->countHolidays($where,$group); 
        if($countHolidays['status']){
            $total_Records = $countHolidays['push']['object']->getTotalRows();
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
    public function comboPersonFilters(): array
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
        $holidayMod->setIdHoliday($idholiday);

        $holidayUpd = $holidayDao->getHoliday($holidayMod);
        
        $params = $this->makeScreenHolidays('upd',$holidayUpd['push']['object']);
        $params['holidayID'] = $idholiday;
      
        $this->view('admin','holidays-update',$params);
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
            $holidayID = $ins['push']['object']->getIdHoliday();
            $holidayDescription = $ins['push']['object']->getDescription();
            
            //Link holiday with the company
            if($companyID != 0){
                $ins['push']['object']->setIdCompany($companyID);
               
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
        
        $holidayMod->setIdHoliday($_POST['holidayID'])
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
            "idholiday" => (!is_null($upd['push']['object']) && !empty($upd['push']['object'])) ? $holidayMod->getIdHoliday() : ""
        );        

        echo json_encode($aRet);
    }

    
    function ajaxStates()
    {
        $adminSrc = new adminServices();
        echo $adminSrc->_comboStatesHtml($_POST['countryId']);

    }

    function ajaxCities()
    {
        $adminSrc = new adminServices();
        echo $adminSrc->_comboCitiesHtml($_POST['stateId']);

    }

    function ajaxNeighborhood()
    {
        $adminSrc = new adminServices();
        echo $adminSrc->_comboNeighborhoodHtml($_POST['cityId']);

    }


}