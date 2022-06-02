<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class scmRelatorioBens extends scmCommon
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

        $this->idPerson = $this->_companyDefault;

        $this->modulename = 'suprimentos' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('scmRelatorioBens');

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbTicket = $dbPerson;

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));

        $this->makeScreenRelatorioBens($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->_displayButtons($smarty,$permissions);
        if($permissions[0] == "Y"){
            $smarty->display('scm-relatoriobens-create.tpl');
        }else{
            $smarty->assign('href', $this->helpdezkUrl.'/scm/home');
            $smarty->display($this->helpdezkPath.'/app/modules/main/views/access_denied.tpl');
        }

    }

    function makeScreenRelatorioBens($objSmarty,$rs,$oper)
    {
        // --- Bens ---
        $fieldsID[0] = 0;
        $values[0]   = 'TODOS';
        $fieldsID[1] = 'S';
        $values[1]   = 'Sim';
        $fieldsID[2] = 'N';
        $values[2]   = 'Não';

        $objSmarty->assign('baixaids',  $fieldsID);
        $objSmarty->assign('baixavals', $values);

        // --- Estado ---
        $arrEstado = $this->_comboEstadoRelatorio($relatorio = '0');
        $result = [];
        foreach ($arrEstado['values'] as $key => $value){
            $result[] = utf8_encode($value);
        }
        $objSmarty->assign('estadoids',  $arrEstado['ids']);
        $objSmarty->assign('estadovals', $result);

        // --- Local ---
        $arrLocal = $this->_comboLocalRelatorio($relatorio = '0');
        $objSmarty->assign('localids',  $arrLocal['ids']);
        $objSmarty->assign('localvals', $arrLocal['values']);

    }

    public function makeReport()
    {
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $baixa = $_POST['idbaixa'];

        if($baixa != '0') {
            $where = "WHERE scm_tbbens.baixa = '$baixa'";
        }

        $estado = $_POST['idestado'];

        if($estado  != 0)  {
            $where .= strlen($where) == 0 ? 'WHERE ' : ' AND ';
            $where .= " scm_tbbens.idestado = $estado";
        }

        $local = $_POST['idlocal'];

        if($local  != 0)  {
            $where .= strlen($where) == 0 ? 'WHERE ' : ' AND ';
            $where .= " scm_tbbens.idlocal = $local";
        }

        $descricao = $_POST['descricao'];

        if(!empty($descricao))  {
            $where .= strlen($where) == 0 ? 'WHERE ' : ' AND ';
            $where .= " scm_tbbens.descricao LIKE '%".$descricao."%'";
        }

        $valor = $_POST['valor'];

        $rsBens = $this->dbBens->getRequestDataImprimir($where);

        //permissão para download do relatório
        $idperson = $_SESSION['SES_COD_USUARIO'];

//        $idowner  = $rsTicket->fields['idperson_owner'];
//
//        if($idperson != $idowner) die ($langVars['Access_denied']);

        // class FPDF with extension to parsehtml
        // Cria o objeto da biblioteca FPDF
        $pdf = $this->returnHtml2pdf();

        /*
         *  Variables
         */
        //Parâmetros para o cabeçalho
        $this->SetPdfLogo($this->helpdezkPath . '/app/uploads/logos/' .  $this->getReportsLogoImage() ); //Logo
        $leftMargin   = 10; //variável para margem à esquerda
        $this->SetPdfTitle(html_entity_decode(utf8_decode('Relatório de Bens'),ENT_QUOTES, "ISO8859-1")); //Titulo //Titulo
        $this->SetPdfPage(utf8_decode($langVars['PDF_Page'])) ; //numeração página
        $this->SetPdfleftMargin($leftMargin);
        //Parâmetros para a Fonte a ser utilizado no relatório
        $this->pdfFontFamily = 'Arial';
        $this->pdfFontStyle  = '';
        $this->pdfFontSyze   = 8;

        $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);

        $pdf->AliasNbPages();
        //$this->SetPdfHeaderData($a_HeaderData);

        $pdf->AddPage(); //Cria a página no arquivo pdf

        $pdf = $this->ReportPdfHeader($pdf); //Insere o cabeçalho no arquivo

        foreach ($rsBens as $key => $value) {

            $CelHeight = 4;

            $numeropatrimonio = $value['numeropatrimonio'];

            $pdf->Cell($leftMargin);
            $pdf->SetFont($this->pdfFontFamily, 'B', $this->pdfFontSyze);
            $this->makePdfLineBlur($pdf, html_entity_decode(utf8_decode("N° Patrimônio $numeropatrimonio"), ENT_QUOTES, "ISO8859-1"), $CelHeight, 179);
            $pdf->SetFont($this->pdfFontFamily, $this->pdfFontStyle, $this->pdfFontSyze);
            $pdf->SetFont('Arial', '', 8);

            $pdf->Cell($leftMargin);
            $pdf->Cell(16, $CelHeight, html_entity_decode(utf8_decode('Descrição'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
            $pdf->Cell(16, $CelHeight, utf8_decode($value['descricao']), 0, 1, 'L', 0);

            $pdf->Cell($leftMargin);
            $pdf->Cell(11, $CelHeight, html_entity_decode('Marca', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
            $pdf->Cell(11, $CelHeight, utf8_decode($value['nomemarca']), 0, 1, 'L', 0);

            $pdf->Cell($leftMargin);
            $pdf->Cell(12, $CelHeight, html_entity_decode(utf8_decode('Estado'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
            $pdf->Cell(12, $CelHeight, utf8_decode($value['nomeestado']), 0, 1, 'L', 0);

            $pdf->Cell($leftMargin);
            $pdf->Cell(10, $CelHeight, html_entity_decode('Local', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
            $pdf->Cell(10, $CelHeight, utf8_decode($value['nomelocal']), 0, 1, 'L', 0);

            $pdf->Cell($leftMargin);
            $pdf->Cell(22, $CelHeight, html_entity_decode('Grupo de Bens', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
            $pdf->Cell(22, $CelHeight, utf8_decode($value['nomegrupodebens']), 0, 1, 'L', 0);

            $pdf->Cell($leftMargin);
            $pdf->Cell(26, $CelHeight, html_entity_decode(utf8_decode('Data de Aquisição'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
            $pdf->Cell(26, $CelHeight, utf8_decode($value['dataaquisicao']), 0, 1, 'L', 0);

            $pdf->Cell($leftMargin);
            $pdf->Cell(18, $CelHeight, html_entity_decode('Fornecedor', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
            $pdf->Cell(18, $CelHeight, utf8_decode($value['nomefornecedor']), 0, 1, 'L', 0);

            $pdf->Cell($leftMargin);
            $pdf->Cell(13, $CelHeight, html_entity_decode(utf8_decode('Doação'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
            $pdf->Cell(13, $CelHeight, utf8_decode($value['doacao']), 0, 1, 'L', 0);

            $pdf->Cell($leftMargin);
            $pdf->Cell(18, $CelHeight, html_entity_decode('NF Entrada', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
            $pdf->Cell(18, $CelHeight, utf8_decode($value['nfentrada']), 0, 1, 'L', 0);

            if($valor == 'S') {
                $pdf->Cell($leftMargin);
                $pdf->Cell(10, $CelHeight, html_entity_decode('Valor', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                $pdf->Cell(10, $CelHeight, utf8_decode($value['valor']), 0, 1, 'L', 0);
            }

            $pdf->Cell($leftMargin);
            $pdf->Cell(25, $CelHeight, html_entity_decode(utf8_decode('Número de Série'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
            $pdf->Cell(25, $CelHeight, utf8_decode($value['numeroserie']), 0, 1, 'L', 0);

            $pdf->Cell($leftMargin);
            $pdf->Cell(25, $CelHeight, html_entity_decode('Data de Garantia', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
            $pdf->Cell(25, $CelHeight, utf8_decode($value['datagarantia']), 0, 1, 'L', 0);

            $pdf->Cell($leftMargin);
            $pdf->Cell(18, $CelHeight, html_entity_decode('Quantidade', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
            $pdf->Cell(18, $CelHeight, utf8_decode($value['quantidade']), 0, 1, 'L', 0);

            $pdf->Cell($leftMargin);
            $pdf->Cell(11, $CelHeight, html_entity_decode('Baixa', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
            $pdf->Cell(11, $CelHeight, utf8_decode($value['baixa']), 0, 1, 'L', 0);



        }

        $this->makePdfLine($pdf,$leftMargin,197);


        //Parâmetros para salvar o arquivo
        $filename = $_SESSION['SES_LOGIN_PERSON'] . "_report_".time().".pdf"; //nome do arquivo
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ; //caminho onde é salvo o arquivo
        $fileNameUrl   = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ; //link para visualização em nova aba/janela

        if(!is_writable($this->helpdezkPath . '/app/downloads/tmp/')) {//validação

            if( !chmod($this->helpdezkPath . '/app/downloads/tmp/', 0777) )
                $this->logIt("Make report request # ". $rsBens->fields['code_request'] . ' - Directory ' . $this->helpdezkPath . '/app/tmp/' . ' is not writable ' ,3,'general',__LINE__);

        }


        $pdf->Output($fileNameWrite,'F'); //a biblioteca cria o arquivo
        echo $fileNameUrl; //retorno para a função javascript


    }


}