<?php

require_once(HELPDEZK_PATH . '/app/modules/hur/controllers/hurCommonController.php');

class hurCandidate extends hurCommon
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

        $this->modulename = 'RecursosHumanos' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('hurCandidate');

        $this->saveMode = $this->_s3bucketStorage ? "aws-s3" : 'disk';

        if($this->saveMode == "aws-s3"){
            $bucket = $this->getConfig('s3bucket_name');
            $this->hurFilePath = "https://{$bucket}.s3.amazonaws.com/hur/cv/";
            $this->hurFileTmp = $this->helpdezkPath.'/app/uploads/tmp/';
            $this->hurImgPath =  "https://{$bucket}.s3.amazonaws.com/hur/cv/";
        }else{
            if($this->_externalStorage) {
                $this->hurFilePath = $this->_externalStoragePath.'/hur/cv/';
                $this->hurFileTmp = $this->_externalStoragePath.'/tmp/';
                $this->hurImgPath =  $this->_externalStoragePath.'/hur/cv/';
            } else {
                $this->hurFilePath = $this->helpdezkPath.'/app/uploads/hur/cv/';
                $this->hurFileTmp = $this->helpdezkPath.'/app/uploads/tmp/';
                $this->hurImgPath =  $this->helpdezkUrl.'/app/uploads/hur/cv/';
            }
        }        
        
        $this->facebookUrl = 'https://www.facebook.com/';
        $this->linkedinUrl = 'https://www.linkedin.com/in/';
        $this->twitterUrl = 'https://www.twitter.com/';


    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));

        $this->makeNavVariables($smarty,'RecursosHumanos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavHur($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->assign('mascdate', str_replace('%','',$this->dateFormat));

        // --- Area ---
        $arrArea = $this->_comboArea();
        $smarty->assign('areaids',  $arrArea['ids']);
        $smarty->assign('areavals', $arrArea['values']);
        $smarty->assign('idarea', $arrArea['ids'][0] );

        // --- Role ---
        if($arrArea['ids'][0]){
            $arrRole = $this->_comboRole($arrArea['ids'][0]);
            $smarty->assign('roleids',  $arrRole['ids']);
            $smarty->assign('rolevals', $arrRole['values']);
            $smarty->assign('idrole', $arrRole['ids'][0] );
        }

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->_displayButtons($smarty,$permissions);
        if($permissions[0] == "Y"){
            $smarty->display('hur-candidate-grid.tpl');
        }else{
            $smarty->assign('href', $this->helpdezkUrl.'/bmm/home');
            $smarty->display($this->helpdezkPath.'/app/modules/main/views/access_denied.tpl');
        }

    }

    public function jsonGrid()
    {


        $this->validasessao();
        $smarty = $this->retornaSmarty();

        $where = '';

        $idCondicao = $_POST['idcondicao'];
        if ($idCondicao) {
            if ($idCondicao == 'ALL')
                $where = '';
            else
                $where .= " WHERE idrole = $idCondicao ";
        }

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='name';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            $arrSearch = array('.','-','/','_');
            $arrReplace = array('','','','');

            switch ($_POST['searchField']){
                case 'name':
                    $searchField = 'name';
                    break;
                case 'dtentry':
                    $searchField = "DATE_FORMAT(dtentry,'%d/%m/%Y')";
                    break;
                default:
                    $searchField = 'role';
                    break;
            }

            if (empty($where))
                $oper = ' WHERE ';
            else
                $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        if((!$idCondicao || $idCondicao == "ALL") && $_POST['_search'] == 'false'){
            $rsCount = $this->_getInitialGrid($where);
            $count = $rsCount->RecordCount();
        }else{
            $count = $this->_getNumCandidates($where);
        }
        
        //echo $count;
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
        //

        if((!$idCondicao || $idCondicao == "ALL") && $_POST['_search'] == 'false'){
            $rsCandidate = $this->_getInitialGrid($where,$order,'name',$limit);
        }else{
            $rsCandidate = $this->_getCandidate($where,$order,null,$limit);
        }
        

        while (!$rsCandidate->EOF) {
            $exptime = $rsCandidate->fields['exp_role_year'].' '.$rsCandidate->fields['exp_role_month'];
            if(is_numeric($rsCandidate->fields['role'])){
                $lblname = $rsCandidate->fields['name']." <span class='label label-primary'>".$rsCandidate->fields['role']."</span>";
                $lblrole = "";
            }else {
                $lblname = $rsCandidate->fields['name'];
                $lblrole = $rsCandidate->fields['role'];
            }

            $aColumns[] = array(
                'id'            => $rsCandidate->fields['idcurriculum'],
                'nome'          => $lblname,
                'cargo'         => $lblrole,
                'dtbirth'       => $rsCandidate->fields['dtbirth'],
                'exptime'       => $exptime,
                'dtentry'       => $rsCandidate->fields['dtentry'],
                'rolenum'       => $rsCandidate->fields['role'],
                'candidatename'       => $rsCandidate->fields['name']
            );
            $rsCandidate->MoveNext();
        }
        //

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    function ajaxRole()
    {
        echo $this->comboRolesHtml($_POST['areaId']);
    }

    public function comboRolesHtml($idArea)
    {

        $arrType = $this->_comboRole($idArea);
        $select = '';

        foreach ( $arrType['ids'] as $indexKey => $indexValue ) {
            if ($arrType['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrType['values'][$indexKey]."</option>";
        }
        if($idArea == 0){$select = "<option value='' selected='selected'></option>";}

        return $select;
    }

    public function viewCandidate()
    {
        $smarty = $this->retornaSmarty();

        $idCurriculum = $this->getParam('id');
        $rs = $this->_getCandidate("WHERE idcurriculum = $idCurriculum");
        $rsPhoto = $this->_getCandidateFile($idCurriculum,'I');
        $rsFile = $this->_getCandidateFile($idCurriculum,'P');

        $smarty->assign('arrData',$rs->fields);
        $smarty->assign('hdkUrl',$this->hurImgPath);
        $smarty->assign('facebookUrl',$this->makeProfileUrl($this->facebookUrl,$rs->fields['facebook_profile']));
        $smarty->assign('linkedinUrl',$this->makeProfileUrl($this->linkedinUrl,$rs->fields['linkedin_profile']));
        $smarty->assign('twitterUrl',$this->makeProfileUrl($this->twitterUrl,$rs->fields['twitter_profile']));
        $smarty->assign('photoName',$rsPhoto->fields['filename']);
        $smarty->assign('fileName',$rsFile->fields['filename']);
        $smarty->assign('curyear', date('Y') );

        $smarty->assign('summernote_version', $this->summernote);
        $smarty->assign('hidden_idcurriculum', $idCurriculum);
        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'RecursosHumanos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavHur($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']);

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('hur-candidate-echo.tpl');

    }

    public function sendEmail()
    {
        /*if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }*/

        $idcandidate = $_POST['idcurriculumemail'];
        $toaddress = implode(";",$_POST['toAddress']);
        $body = $_POST['msg'];
        $attchList = $_POST['curriculumItem'];
        $subject = $_POST['emailtitle'];

        $attachment = array();

        foreach ($attchList as $type){
            if($type == 'D'){
                $file = $this->makeFile($idcandidate,"email"); 
                if(file_exists($file)){
                    $fileName = substr(strrchr($file, "/"),1);
                    array_push($attachment,array("filename"=>$fileName,"filepath"=>$file));
                    //echo $file;
                }
            }else{
                $rsFile = $this->_getCandidateFile($idcandidate,'P');
                $file = $this->hurFilePath.$rsFile->fields['filename'];
                if($this->saveMode == "aws-s3"){
                    if(!file_put_contents($this->helpdezkPath . "/app/uploads/tmp/{$rsFile->fields['filename']}",file_get_contents($file))) {
                        if($this->log)
                            $this->logIt("Can\'t save S3 temp file {$rsFile->fields['filename']} - program: ".$this->program ,3,'general',__LINE__);
                    }else{
                        array_push($attachment,array("filename"=>$rsFile->fields['filename'],"filepath"=>$this->helpdezkPath . "/app/uploads/tmp/{$rsFile->fields['filename']}"));
                    }
                    
                }else{
                    if($this->_externalStorage) {
                        array_push($attachment,array("filename"=>$rsFile->fields['filename'],"filepath"=>$file));
                    } else {
                        if(file_exists($file)){
                            array_push($attachment,array("filename"=>$rsFile->fields['filename'],"filepath"=>$file));
                            //echo $file;
                        }
                    }
                }
            }
        }
        //echo "<pre>",print_r($attachment,true),"</pre>";//
        $ret = $this->_sendEmailDefault($subject,$body,$toaddress,$attachment);

        if($ret){
            $msg = "OK";
        }else{
            $msg = "Error";
        }

        $aRet = array(
            "status" => $msg
        );

        echo json_encode($aRet);


    }

    public function makeFile($idcandidate,$type)
    {
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $rs = $this->_getCandidate("WHERE idcurriculum = $idcandidate");
        $rsPhoto = $this->_getCandidateFile($idcandidate,'I');

        // class FPDF with extension to parsehtml
        // Cria o objeto da biblioteca FPDF
        $pdf = $this->returnHtml2pdf();

        /*
         *  Variables
         */
        //Parâmetros para o cabeçalho
        $this->SetPdfLogo($this->helpdezkPath . '/app/uploads/logos/' .  $this->getReportsLogoImage() ); //Logo
        $leftMargin   = 10; //variável para margem à esquerda
        $this->SetPdfTitle(html_entity_decode(utf8_decode('Dados do Candidato'),ENT_QUOTES, "ISO8859-1")); //Titulo //Titulo
        $this->SetPdfPage(utf8_decode($langVars['PDF_Page'])) ; //numeração página
        $this->SetPdfleftMargin($leftMargin);
        //Parâmetros para a Fonte a ser utilizado no relatório
        $this->pdfFontFamily = 'Arial';
        $this->pdfFontStyle  = '';
        $this->pdfFontSyze   = 8;

        $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);

        $pdf->AliasNbPages();

        $pdf->AddPage(); //Cria a página no arquivo pdf

        $pdf = $this->ReportPdfHeader($pdf); //Insere o cabeçalho  relatório
        $CelHeight = 6;

        $pdf->SetFont($this->pdfFontFamily, 'B', $this->pdfFontSyze);
        $this->makePdfLineBlur($pdf, array(array('title'=>'Dados Pessoais','cellWidth'=>190,'cellHeight'=>$CelHeight,'titleAlign'=>'L')));
        $pdf->SetFont($this->pdfFontFamily, $this->pdfFontStyle, $this->pdfFontSyze);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode('Nome Completo', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(124, $CelHeight, utf8_decode($rs->fields['name']), 0, 1, 'L', 0);
        $pdf->Image($this->hurFilePath.$rsPhoto->fields['filename'],170,35,30,40);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode('CPF', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(50, $CelHeight, utf8_decode($rs->fields['ssn_cpf']), 0, 0, 'L', 0);

        $pdf->Cell(15, $CelHeight, html_entity_decode('RG',ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(25, $CelHeight, utf8_decode($rs->fields['rg']), 0, 1, 'L', 0);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode('Data Nascimento', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(50, $CelHeight, utf8_decode($rs->fields['dtbirth_fmt']), 0, 0, 'L', 0);

        $pdf->Cell(15, $CelHeight, html_entity_decode('Idade', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(25, $CelHeight, utf8_decode($rs->fields['age'].' anos'), 0, 1, 'L', 0);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode('E-mail', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(124, $CelHeight, utf8_decode($rs->fields['email']), 0, 1, 'L', 0);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Gênero'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(30, $CelHeight, utf8_decode($rs->fields['gender']), 0, 0, 'L', 0);

        $pdf->Cell(15, $CelHeight, html_entity_decode('Estado Civil',ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(30, $CelHeight, utf8_decode($rs->fields['maritalstatus']), 0, 0, 'L', 0);

        $pdf->Cell(15, $CelHeight, html_entity_decode('Filhos', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(15, $CelHeight, utf8_decode($rs->fields['qtchilds']), 0, 1, 'L', 0);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Nome Mãe'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(60, $CelHeight, utf8_decode($rs->fields['mother']), 0, 1, 'L', 0);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode('Nacionalidade',ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(40, $CelHeight, utf8_decode($rs->fields['nationality']), 0, 0, 'L', 0);

        $pdf->Cell(15, $CelHeight, html_entity_decode('Naturalidade', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(40, $CelHeight, utf8_decode($rs->fields['birth_place']), 0, 1, 'L', 0);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Residencial'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(30, $CelHeight, utf8_decode($rs->fields['phone']), 0, 0, 'L', 0);

        $pdf->Cell(15, $CelHeight, html_entity_decode('Celular',ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(30, $CelHeight, utf8_decode($rs->fields['mobile_phone']), 0, 0, 'L', 0);

        $pdf->Cell(15, $CelHeight, html_entity_decode('Recado', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(15, $CelHeight, utf8_decode($rs->fields['scrap_phone']), 0, 1, 'L', 0);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode('Perfil Facebook',ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        if($rs->fields['facebook_profile'] != ''){
            $pdf->Cell(60, $CelHeight, utf8_decode($this->makeProfileUrl($this->facebookUrl,$rs->fields['facebook_profile'])), 0, 1, 'L', 0);
        }else{
            $pdf->Cell(60, $CelHeight, '', 0, 1, 'L', 0); 
        }

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode('Perfil LinkedIn',ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        if($rs->fields['linkedin_profile'] != ''){
            $pdf->Cell(60, $CelHeight, utf8_decode($this->makeProfileUrl($this->linkedinUrl,$rs->fields['linkedin_profile'])), 0, 1, 'L', 0);
        }else{
            $pdf->Cell(60, $CelHeight, '', 0, 1, 'L', 0); 
        }        

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode('Perfil Twitter',ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        if($rs->fields['twitter_profile'] != ''){
            $pdf->Cell(60, $CelHeight, utf8_decode($this->makeProfileUrl($this->twitterUrl,$rs->fields['_twitterprofile'])), 0, 1, 'L', 0);
        }else{
            $pdf->Cell(60, $CelHeight, '', 0, 1, 'L', 0); 
        }        

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode('User Skype',ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(60, $CelHeight, utf8_decode($rs->fields['skype_profile']), 0, 1, 'L', 0);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Currículo Lattes'),ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(60, $CelHeight, utf8_decode($rs->fields['lattes_link']), 0, 1, 'L', 0);

        $pdf->Ln(10);

        $pdf->SetFont($this->pdfFontFamily, 'B', $this->pdfFontSyze);
        $this->makePdfLineBlur($pdf, array(array('title'=>utf8_decode('Endereço'),'cellWidth'=>190,'cellHeight'=>$CelHeight,'titleAlign'=>'L')));
        $pdf->SetFont($this->pdfFontFamily, $this->pdfFontStyle, $this->pdfFontSyze);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Endereço'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(80, $CelHeight, utf8_decode($rs->fields['address']), 0, 0, 'L', 0);

        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Número'),ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(25, $CelHeight, utf8_decode($rs->fields['number']), 0, 1, 'L', 0);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Complemento'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(80, $CelHeight, utf8_decode($rs->fields['complement']), 0, 0, 'L', 0);

        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('CEP'),ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(25, $CelHeight, utf8_decode($rs->fields['zipcode']), 0, 1, 'L', 0);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Bairro'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(30, $CelHeight, utf8_decode($rs->fields['neighborhood']), 0, 0, 'L', 0);

        $pdf->Cell(15, $CelHeight, html_entity_decode('Cidade',ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(30, $CelHeight, utf8_decode($rs->fields['city']), 0, 0, 'L', 0);

        $pdf->Cell(15, $CelHeight, html_entity_decode('UF', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(15, $CelHeight, utf8_decode($rs->fields['uf']), 0, 1, 'L', 0);

        $pdf->Ln(10);

        $pdf->SetFont($this->pdfFontFamily, 'B', $this->pdfFontSyze);
        $this->makePdfLineBlur($pdf, array(array('title'=>utf8_decode('Cargo Desejado'),'cellWidth'=>190,'cellHeight'=>$CelHeight,'titleAlign'=>'L')));
        $pdf->SetFont($this->pdfFontFamily, $this->pdfFontStyle, $this->pdfFontSyze);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Área'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(40, $CelHeight, utf8_decode($rs->fields['area']), 0, 0, 'L', 0);

        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Cargo'),ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(40, $CelHeight, utf8_decode($rs->fields['role']), 0, 1, 'L', 0);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Experiência no cargo'),ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(40, $CelHeight, utf8_decode($rs->fields['exp_role_year'].' '.$rs->fields['exp_role_month']), 0, 1, 'L', 0);

        $pdf->Ln(10);

        $pdf->SetFont($this->pdfFontFamily, 'B', $this->pdfFontSyze);
        $this->makePdfLineBlur($pdf, array(array('title'=>utf8_decode('Dados do Currículo'),'cellWidth'=>190,'cellHeight'=>$CelHeight,'titleAlign'=>'L')));
        $pdf->SetFont($this->pdfFontFamily, $this->pdfFontStyle, $this->pdfFontSyze);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Turno de Preferência'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(40, $CelHeight, utf8_decode($rs->fields['shift']), 0, 0, 'L', 0);

        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Contratação'),ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(40, $CelHeight, utf8_decode($rs->fields['typehiring']), 0, 0, 'L', 0);

        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Deficiência'),ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->Cell(40, $CelHeight, utf8_decode($rs->fields['deficiency']), 0, 1, 'L', 0);

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Observação'),ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->MultiCell(165, $CelHeight, utf8_decode($rs->fields['note']), 0, 'L', 0);

        $pdf->Ln();

        $pdf->Cell($leftMargin);
        $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Resumo'),ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
        $pdf->MultiCell(165, $CelHeight, utf8_decode($rs->fields['summary']), 0, 'L', 0);        

        //Parâmetros para salvar o arquivo
        $filename = "candidate_$idcandidate" . "_report_".time().".pdf"; //nome do arquivo

        if(!$this->_externalStorage) {                
            if(!is_writable($this->hurFileTmp)) {//validação    
                if( !chmod($this->hurFileTmp, 0777) )
                    $this->logIt("Make report candidate # ". $idcandidate. ' - Directory ' . $this->hurFileTmp . ' is not writable ' ,3,'general',__LINE__);
            }
        }            

        $fileNameWrite = $this->hurFileTmp . $filename ; //caminho onde é salvo o arquivo
        $fileNameUrl   = $this->helpdezkUrl . '/app/uploads/tmp/'. $filename ; //link para visualização em nova aba/janela        
        
        $pdf->Output($fileNameWrite,'F'); //a biblioteca cria o arquivo        
        
        if($type == 'email'){
            return $fileNameWrite;
        }else{
            return $fileNameUrl;
        }    

    }

    public function printData()
    {
        $idcandidate = $_POST['id']; 
        
        echo $this->makeFile($idcandidate,"view");

    }

    public function makeProfileUrl($socialLink,$urlprofile)
    {
        $tmpUrl = parse_url($urlprofile);
        
        if($tmpUrl['scheme'] && $tmpUrl['host']){$newUrl = $urlprofile;}
        else{$newUrl = $socialLink.$urlprofile;}
        
        return $newUrl;

    }


}