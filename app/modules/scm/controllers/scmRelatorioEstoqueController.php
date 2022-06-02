<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class scmRelatorioEstoque extends scmCommon
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
        $this->idprogram =  $this->getIdProgramByController('scmRelatorioEstoque');

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbTicket = $dbPerson;

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();

        $this->makeScreenRelatorioEstoque($smarty,'','create');
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->_displayButtons($smarty,$permissions);
        if($permissions[0] == "Y"){
            $smarty->display('scm-relatorioestoque-create.tpl');
        }else{
            $smarty->assign('href', $this->helpdezkUrl.'/scm/home');
            $smarty->display($this->helpdezkPath.'/app/modules/main/views/access_denied.tpl');
        }

    }

    function makeScreenRelatorioEstoque($objSmarty,$rs,$oper)
    {
        // --- Bens ---
        $fieldsID[0] = 'I';
        $values[0]   = 'Inventário';
        $fieldsID[1] = 'E';
        $values[1]   = 'Entrada de Produtos';

        $objSmarty->assign('tipoids',  $fieldsID);
        $objSmarty->assign('tipovals', $values);

        // --- Produto ---
        $arrProduto = $this->_comboProduto($relatorio = '0');
        $objSmarty->assign('produtoids',  $arrProduto['ids']);
        $objSmarty->assign('produtovals', $arrProduto['values']);

    }

    public function makeReport()
    {
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $dataInicial = $_POST['datainicial'];
        $dataFinal = $_POST['datafinal'];

        if (!empty($dataInicial) AND !empty($dataFinal)) {
            $where = "WHERE datacadastro BETWEEN '$dataInicial' AND '$dataFinal'";
        }

        $produto = $_POST['idproduto'];

        if ($produto != 0) {
            $where .= strlen($where) == 0 ? 'WHERE ' : ' AND ';
            $where .= " scm_tbitementradaproduto.idproduto = $produto";
        }

        $situacao = $_POST['situacao'];

        if ($situacao == 'E') {
            $where .= strlen($where) == 0 ? 'WHERE ' : ' AND ';
            $where .= " scm_tbitementradaproduto.quantidade > 0";
        } elseif ($situacao == 'S') {
            $where .= strlen($where) == 0 ? 'WHERE ' : ' AND ';
            $where .= " scm_tbitementradaproduto.quantidade < 1";

        }

        $rsEntradaProduto = $this->dbEntradaProduto->getRequestDataImprimir($where);

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
        $this->SetPdfTitle(html_entity_decode(utf8_decode('Relatório de Estoque'),ENT_QUOTES, "ISO8859-1")); //Titulo //Titulo
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

        $controlaTipo = 0;
        $controlaEntradaProduto = 0;
        foreach ($rsEntradaProduto as $key => $value) {

            $CelHeight = 4;

            $numeropedido = $value['numeropedido'];
            $identradaproduto = $value['identradaproduto'];
            $tipo = $value['tipo'];

            if ($controlaTipo != $identradaproduto) {
                if ($controlaTipo != 0) {
                    $pdf->Cell($leftMargin);

                    $pdf->Cell(18, $CelHeight * 2, '', 0, 1, 'L', 0);
                }
                $controlaTipo = $identradaproduto;
                $controlaEntradaProduto = 0;

                if($tipo == "C") {
                    $pdf->Cell($leftMargin);
                    $pdf->SetFont($this->pdfFontFamily, 'B', $this->pdfFontSyze);
                    $this->makePdfLineBlur($pdf, html_entity_decode(utf8_decode("N° Pedido $numeropedido"), ENT_QUOTES, "ISO8859-1"), $CelHeight, 179);
                    $pdf->SetFont($this->pdfFontFamily, $this->pdfFontStyle, $this->pdfFontSyze);
                    $pdf->SetFont('Arial', '', 8);
                } else {
                        $pdf->Cell($leftMargin);
                        $pdf->SetFont($this->pdfFontFamily, 'B', $this->pdfFontSyze);
                        $this->makePdfLineBlur($pdf, html_entity_decode(utf8_decode("N° Registro $identradaproduto"), ENT_QUOTES, "ISO8859-1"), $CelHeight, 179);
                        $pdf->SetFont($this->pdfFontFamily, $this->pdfFontStyle, $this->pdfFontSyze);
                        $pdf->SetFont('Arial', '', 8);

                }
            }

                if ($controlaEntradaProduto != $value['identradaproduto']) {
                    if ($controlaEntradaProduto != 0) {
                        $this->makePdfLine($pdf, $leftMargin, 197);
                    }

                    $controlaEntradaProduto = $value['identradaproduto'];

                    $pdf->Cell($leftMargin);
                    $pdf->Cell(20, $CelHeight, html_entity_decode(utf8_decode('Fornecedor'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                    $pdf->Cell(20, $CelHeight, utf8_decode($value['nomefornecedor']), 0, 1, 'L', 0);

                    if($tipo == 'C') {
                        $pdf->Cell($leftMargin);
                        $pdf->Cell(11, $CelHeight, html_entity_decode('Tipo', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                        $pdf->Cell(11, $CelHeight, utf8_decode('Compra'), 0, 1, 'L', 0);
                    } else {
                        $pdf->Cell($leftMargin);
                        $pdf->Cell(11, $CelHeight, html_entity_decode('Tipo', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                        $pdf->Cell(11, $CelHeight, utf8_decode('Lista de Materiais'), 0, 1, 'L', 0);
                    }
                    $pdf->Cell($leftMargin);
                    $pdf->Cell(24, $CelHeight, html_entity_decode(utf8_decode('N° Nota Fiscal'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                    $pdf->Cell(24, $CelHeight, utf8_decode($value['numeronotafiscal']), 0, 1, 'L', 0);

                    $pdf->Cell($leftMargin);
                    $pdf->Cell(32, $CelHeight, html_entity_decode(utf8_decode('Valor Total dos Itens'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                    $pdf->Cell(32, $CelHeight, utf8_decode($value['valortotal']), 0, 1, 'L', 0);

                    $pdf->Cell($leftMargin);
                    $pdf->Cell(38, $CelHeight, html_entity_decode(utf8_decode('Valor Total da Nota Fiscal'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                    $pdf->Cell(38, $CelHeight, utf8_decode($value['valornota']), 0, 1, 'L', 0);

                    $pdf->Cell($leftMargin);
                    $pdf->Cell(24, $CelHeight, html_entity_decode(utf8_decode('Data Cadastro'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                    $pdf->Cell(24, $CelHeight, utf8_decode($value['datadecadastro']), 0, 1, 'L', 0);

                }

                $pdf->Cell($leftMargin);
                $pdf->Cell(16, $CelHeight, html_entity_decode('Produto', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                $pdf->Cell(37, $CelHeight, utf8_decode($value['nomeproduto']), 0, 0, 'L', 0);

                $pdf->Cell($leftMargin);
                $pdf->Cell(16, $CelHeight, html_entity_decode('Quantidade', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                $pdf->Cell(30, $CelHeight, utf8_decode($value['quantidadeproduto']), 0, 0, 'L', 0);

                $pdf->Cell($leftMargin);
                $pdf->Cell(16, $CelHeight, html_entity_decode('Valor', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                $pdf->Cell(50, $CelHeight, $value['valor'], 0, 1, 'L', 0);


        }

        $this->makePdfLine($pdf,$leftMargin,197);


        //Parâmetros para salvar o arquivo
        $filename = $_SESSION['SES_LOGIN_PERSON'] . "_report_".time().".pdf"; //nome do arquivo
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ; //caminho onde é salvo o arquivo
        $fileNameUrl   = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ; //link para visualização em nova aba/janela

        if(!is_writable($this->helpdezkPath . '/app/downloads/tmp/')) {//validação

            if( !chmod($this->helpdezkPath . '/app/downloads/tmp/', 0777) )
                $this->logIt("Make report request # ". $rsEntradaProduto->fields['code_request'] . ' - Directory ' . $this->helpdezkPath . '/app/tmp/' . ' is not writable ' ,3,'general',__LINE__);

        }


        $pdf->Output($fileNameWrite,'F'); //a biblioteca cria o arquivo
        echo $fileNameUrl; //retorno para a função javascript


    }


}