<?php

use App\core\Controller;


use App\modules\exp\dao\mysql\cityDAO;

use App\modules\admin\src\adminServices;
use App\modules\exp\src\expServices;
use App\src\appServices;
use App\src\localeServices;
use App\src\awsServices;


class ExpCity extends Controller
{    
    /**
     * @var string
     */
    protected $saveMode;
    
    /**
     * @var string
     */
    protected $imgDir;

    /**
     * @var string
     */
    protected $imgBucket;
    
    public function __construct()
    {
        parent::__construct();

        $appSrc = new appServices();

        //
        $this->saveMode = $_ENV['S3BUCKET_STORAGE'] ? "aws-s3" : 'disk';

        if($this->saveMode == "aws-s3"){
            $bucket = $_ENV['S3BUCKET_NAME'];
            $this->imgBucket = "https://{$bucket}.s3.amazonaws.com/exp/city/";
            $this->imgDir = "exp/city/";
        }else{
            if($_ENV['EXTERNAL_STORAGE']) {
                $modDir = $appSrc->_setFolder($_ENV['EXTERNAL_STORAGE_PATH'].'/exp/');
                $this->imgDir = $appSrc->_setFolder($modDir.'city/');
                $this->imgBucket = $_ENV['EXTERNAL_STORAGE_URL'].'exp/city/';
            } else {
                $storageDir = $appSrc->_setFolder($appSrc->_getHelpdezkPath().'/storage/');
                $upDir = $appSrc->_setFolder($storageDir.'uploads/');
                $modDir = $appSrc->_setFolder($upDir.'exp/');
                $this->imgDir = $appSrc->_setFolder($modDir.'city/');
                $this->imgBucket = $_ENV['HDK_URL']."/storage/uploads/exp/city/";
            }
        }
        
    }

    /**
     * en_us Renders the holidays home screen template
     *
     * pt_br Renderiza o template da tela de home de feriados
     */
    public function index()
    {
        $params = $this->makeScreenCity();
		
		$this->view('exp','city',$params);
    }

    /**
     * en_us Configure program screens
	 * 
	 * pt_br Configura as telas do programa
     *
     * @param  string $option Indicates the type of screen (idx = index, add = new, upd = update)
     * @param  mixed $obj
     * @return void
     */
    public function makeScreenCity($option='idx',$obj=null)
    {
        $appSrc = new appServices();
        $adminSrc = new adminServices();
        $expSrc = new expServices();
        $translator = new localeServices();
        $params = $appSrc->_getDefaultParams();
        $params = $expSrc->_makeNavExp($params);

        // -- States --
        $params['cmbStates'] = $adminSrc->_comboStates();
       
        // -- Datepicker settings -- 
        $retDtpicker = $appSrc->_datepickerSettings();
        $params['dtpFormat'] = $retDtpicker['dtpFormat'];
        $params['dtpLanguage'] = $retDtpicker['dtpLanguage'];
        $params['dtpAutoclose'] = $retDtpicker['dtpAutoclose'];
        $params['dtpOrientation'] = $retDtpicker['dtpOrientation'];
        $params['dtpickerLocale'] = $retDtpicker['dtpickerLocale'];
        $params['dtSearchFmt'] = $retDtpicker['dtSearchFmt'];
        
        // -- Search action --
        if($option=='idx'){
            $params['cmbFilterOpts'] = $appSrc->_comboFilterOpts();
            $params['cmbFilters'] = $this->comboCityFilters();
            $params['modalFilters'] = $appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-search-filters.latte';
        }
        
        // -- Others modals --
        $params['modalAlert'] = $appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-alert.latte';
        $params['modalNextStep'] = $appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-next-step.latte';
        
        // -- Token: to prevent attacks --
        $params['token'] = $appSrc->_makeToken();

        if($option=='upd'){
            $params['cityID'] = $obj->getIdcity();
            $params['stateID'] = $obj->getIdstate();
            $params['cityName'] = $obj->getName();
            $params['foundationDate'] = $appSrc->_formatDate($obj->getDtfoundation());
            $params['isDefault'] = $obj->getIsdefault();
        }
        
        return $params;
    }

    public function jsonGrid()
    {
        $appSrc = new appServices();
        $translator = new localeServices();
        $cityDao = new cityDAO(); 

        $where = "";

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
        $countCities = $cityDao->queryCities($where); 
        if(!is_null($countCities) && !empty($countCities)){
            $total_Records = count($countCities);
        }else{
            $total_Records = 0;
        }
        
        $skip = $appSrc->_pageHelper($pq_curPage, $pq_rPP, $total_Records);
        $limit = "LIMIT {$skip},$pq_rPP";

        $cities = $cityDao->queryCities($where,$group,$order,$limit);
        
        if(!is_null($cities) && !empty($cities)){     
            
            foreach($cities as $k=>$v) {
                $status_fmt = ($v['status'] == 'A' ) ? '<span class="label label-info">A</span>' : '<span class="label label-danger">I</span>';
                $default_fmt = ($v['default'] == 1 ) ? '<span class="label label-info">&check;</span>' : '';

                $data[] = array(
                    'idcity'        => $v['idcity'],
                    'city'          => $v['city'],//utf8_decode($v['holiday_description']),
                    'uf'            => $v['uf'],
                    'dtfoundation'  => $appSrc->_formatDate($v['dtfoundation']),
                    'status'        => $status_fmt,
                    'status_val'    => $v['status'],
                    'default'       => $default_fmt,
                    'default_val'   => $v['default']
    
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
    public function comboCityFilters(): array
    {
        $translator = new localeServices();

        $aRet = array(
            array("id" => 'holiday_description',"text"=>$translator->translate('Name')), // equal
            array("id" => 'holiday_date',"text"=>$translator->translate('Date'))
        );
        
        return $aRet;
    }

    /**
     * en_us Renders the city add screen
     *
     * pt_br Renderiza o template da tela de novo cadastro
     */
    public function formCreate()
    {
        $params = $this->makeScreenCity();
        
        $this->view('exp','city-create',$params);
    }

    /**
     * en_us Renders the city update screen
     *
     * pt_br Renderiza o template da tela de atualização do cadastro
     */
    public function formUpdate($idcity=null)
    {
        $cityDao = new cityDAO(); 
        $cityUpd = $cityDao->getcity($idcity);

        $params = $this->makeScreenCity('upd',$cityUpd);
        $params['cityID'] = $idcity;
      
        $this->view('exp','city-update',$params);
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
     * en_us Write the city information to the DB
     *
     * pt_br Grava no BD as informações da cidade
     */
    public function createCity()
    {
        $appSrc = new appServices();
        $translator = new localeServices();
        
        if (!$appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }        
        
        $cityDao = new cityDAO();
        
        $uf = $_POST['cmbUF'];
        $name = trim($_POST['cityName']);
        $dtFoundation = $appSrc->_formatSaveDate($_POST['foundationDate']);        
        $flgDefault = $_POST['cityDefault'];
        $aAttachs 	= $_POST["attachments"]; // Attachments
        $aSize = count($aAttachs); // count attachs files
        
        $ins = $cityDao->insertCity($uf,$name,$dtFoundation,$flgDefault);
        if(is_null($ins) || empty($ins)){
            return false;
        }        
        
        $cityID = $ins->getIdcity();
        
        // link attachments to the city
        if($aSize > 0){
            $insAttachs = $this->linkCityAttachments($cityID,$aAttachs);
            
            if(!$insAttachs['success']){
                $this->logger->error("{$insAttachs['message']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                return false;
            }
        }

        $aRet = array(
            "success"       => true,
            "idcity"        => $cityID,
            "description"   => $name,
            "dtfoundation"  => $_POST['foundationDate']
        );        

        echo json_encode($aRet);
    }

    /**
     * en_us Update the city information to the DB
     *
     * pt_br Atualiza no BD as informações da cidade
     */
    public function updateCity()
    { 
        $appSrc = new appServices();
        $translator = new localeServices();
        
        if (!$appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $cityDao = new cityDAO();
        
        $cityID = $_POST['cityID'];
        $uf = $_POST['cmbUF'];
        $name = trim($_POST['cityName']);
        $dtFoundation = $appSrc->_formatSaveDate($_POST['foundationDate']);        
        $flgDefault = $_POST['cityDefault'];
        $aAttachs 	= $_POST["attachments"]; // Attachments
        $aSize = count($aAttachs); // count attachs files
        
        $upd = $cityDao->updateCity($cityID,$uf,$name,$dtFoundation,$flgDefault);
        if(is_null($upd) || empty($upd)){
            return false;
        }        
        
        // link attachments to the city
        if($aSize > 0){
            /*$insAttachs = $this->linkProductAttachments($idProduto,$aAttachs,$dbProduto);
            
            if(!$retAttachs['success']){
                if($this->log)
                    $this->logIt("{$retAttachs['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                $dbProduto->RollbackTrans();
                return false;
            }*/
        }        
        
        $aRet = array(
            "success" => true,
            "idcity" => $cityID
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

    /**
     * en_us Writes the holidays of the previous year and selected company in the DB
     *
     * pt_br Grava no BD os feriados do ano anterior e empresa selecionados
     */
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

    /**
     * en_us Remove the holiday from the DB
     *
     * pt_br Remove o feriado do BD
     */
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

    function checkExist(){
        
        $appSrc = new appServices();
        $translator = new localeServices();
        
        if (!$appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $cityDao = new cityDAO();

        $stateID = $_POST['uf'];
        $name = strip_tags($_POST['cityName']);

        $where = "AND a.name = '$name' AND a.idstate = $stateID";
        $where .= (isset($_POST['idcity'])) ? "AND idcity != {$_POST['idcity']}" : "";

        $check =  $cityDao->queryCities($where);
        if(is_null($check)){
            return false;
        }
        
        if(count($check) > 0){
            echo json_encode($translator->translate('city_already_registered'));
        }else{
            echo json_encode(true);
        }

    }

    function saveImage()
    {
        $translator = new localeServices();
        
        if (!empty($_FILES) && ($_FILES['file']['error'] == 0)) {

            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");

            if($this->saveMode == 'disk') {
                $targetFile =  $this->imgDir.$fileName;
    
                if (move_uploaded_file($tempFile,$targetFile)){
                    $this->logger->info("City's image saved", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>true,"message"=>""));
                } else {
                    $this->logger->error("Error trying save city image: {$fileName}.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
                }
                    
            }elseif($this->saveMode == "aws-s3"){
                
                $aws = new awsServices();
                
                $arrayRet = $aws->_copyToBucket($tempFile,$this->imgDir.$fileName);
                
                if($arrayRet['success']) {
                    $this->logger->info("Save temp attachment file {$fileName}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

                    echo json_encode(array("success"=>true,"message"=>""));     
                } else {
                    $this->logger->error("I could not save the temp file: {$fileName} in S3 bucket !!", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    echo json_encode(array("success"=>false,"message"=>"{$translator->translate('Alert_failure')}"));
                }             

            }

        }else{
            echo json_encode(array("success"=>false,"message"=>"{$translator->translate('Alert_failure')}"));
        }

        exit;
    }

    function loadImage()
    {
        $appSrc = new appServices();
        $translator = new localeServices();        
        $cityDao = new cityDAO();

        $cityID = $_POST['cityID'];
        $imgList = $cityDao->fetchCityImage($cityID);

        if (is_null($imgList) || empty($imgList)) {
            return false;
        }

        $resultimagens = [];
        foreach ($imgList as $key => $value){
            if($this->saveMode == "aws-s3"){
                $size = strlen(file_get_contents($this->imgBucket.$value['name']));
            }else{
                $size = filesize($this->imgDir.$value['name']);
            }            
            
            $resultimagens[] = array(
                'idimage'   => $value['idimage'],
                'idcity'    => $value['idcity'], 
                'filename'      => $value['filename'],
                'fmtname'   => $value['fileuploaded'],
                'size'      => $size,
                'url'       => $this->imgBucket
            );
        }
        echo json_encode($resultimagens);
    }

    function removeImage()
    {
        echo "",print_r($_POST,true),"\n";
        /*$idimage = $_POST['idimage'];
        $filename = $_POST['filename'];

        $this->loadModel('produto_model');
        $dbUnidade = new produto_model();
        $ret = $dbUnidade->deleteImagem($idimage);

        if (!$ret) {
            if($this->log)
                $this->logIt('Remove Image - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($this->saveMode == 'disk') {
            unlink($this->imgDir.$filename);
            $msg = 'OK';
        }else if ($this->saveMode == 'aws-s3'){           
            $aws = $this->getAwsS3Client();
            $arrayRet = $aws->removeFile("scm/produtos/{$filename}");
            if($arrayRet['success']) {
                $msg = 'OK';
            } else {
                if($this->log)
                    $this->logIt('I could not remove the product image file: '.$filename.' from S3 bucket !! - program: '.$this->program ,3,'general',__LINE__);
                $msg = 'error';
            }
        }

        $aRet = array(
            "status" => $msg,
        );
        echo json_encode($aRet);*/
    }
    
    /**
     * Link City to uploaded files
     *
     * @param  int $cityID
     * @param  array $aAttachs
     * @return array
     */
    public function linkCityAttachments(int $cityID,array $aAttachs): array
    {
        $appSrc = new appServices();
        $translator = new localeServices();
        
        $cityDao = new cityDAO();
        //echo __METHOD__." ".__LINE__."\n";
        foreach($aAttachs as $key=>$fileName){
            $ins = $cityDao->insertCityImage($cityID,$fileName);

            if (is_null($ins) || empty($ins)) {
                return array("success"=>false,"message"=>"Can't link file {$fileName} to city # {$cityID}");
            }

            $extension = strrchr($fileName, ".");
            $imageID = $ins->getIdimage();
            $newFile = $imageID.$extension;

            if($this->saveMode == 'disk') {
                $targetOld = $this->imgDir.$fileName;
                $targetNew =  $this->imgDir.$newFile;
                if(!rename($targetOld,$targetNew)){
                    $del = $cityDao->deleteCityImage($imageID);
                    if(is_null($del)) {
                        return array("success"=>false,"message"=>"Can't link file {$fileName} to city # {$cityID}");
                    }
                    return array("success"=>false,"message"=>"Can't link file {$fileName} to city #{$productID}");
                }
                
            }elseif($this->saveMode == 'aws-s3') {
                $aws = new awsServices();
                $arrayRet = $aws->_renameFile("{$this->imgDir}{$fileName}","{$this->imgDir}{$newFile}");
                
                if($arrayRet['success']) {
                    $this->logger->info("Rename city image file {$fileName} to {$newFile}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                } else {
                    $this->logger->error("I could not rename city image file {$fileName} to {$newFile} in S3 bucket !!", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    return json_encode(array("success"=>false,"message"=>"{$translator->translate('Alert_failure')}"));
                }
            
            }

            $upd = $cityDao->updateCityImageName($imageID,$newFile);
            if (is_null($upd)) {
                return array("success"=>false,"message"=>"Can't update link file {$fileName} to city {$cityID}");
            }

        }

        return array("success"=>true,"message"=>"");

    }


}