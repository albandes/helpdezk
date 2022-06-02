<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class scmRelatorioPedido extends scmCommon
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
        $this->iduser = $_SESSION['SES_COD_USUARIO'];

        $this->modulename = 'suprimentos' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('scmRelatorioPedido');

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbTicket = $dbPerson;

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));

        $this->makeScreenRelatorio($smarty,'','create');

        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,'suprimentos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->_displayButtons($smarty,$permissions);
        if($permissions[0] == "Y"){
            $smarty->display('scm-relatoriopedido-create.tpl');
        }else{
            $smarty->assign('href', $this->helpdezkUrl.'/scm/home');
            $smarty->display($this->helpdezkPath.'/app/modules/main/views/access_denied.tpl');
        }

    }

    function makeScreenRelatorio($objSmarty,$rs,$oper)
    {
        // --- Data Entrega ---
        $enddate = date("d/m/Y", mktime (0, 0, 0, date("m"), date("d")+30, date("Y")));
        $objSmarty->assign('startdate',date("d/m/Y"));
        $objSmarty->assign('enddate',$enddate);

        // --- Solicitante ---
        $order = "ORDER BY name";
        $userRole = $this->_getUserRole($_SESSION['SES_LOGIN_PERSON']);
        if($userRole == 2){
            $retUserGroup = $this->_getGrupoOperador("AND ghp.idperson = ".$this->iduser);
            $groups = '';
            while(!$retUserGroup->EOF){
                $groups .= $retUserGroup->fields['idgroup'].',';
                $retUserGroup->MoveNext();
            }
            $groups = substr($groups,0,-1);
            $arrPerson = $this->_getRequesterByGroup($groups,'A','Y');

        }else{
            $where = "WHERE status = 'A' AND idtypeperson IN (2,3)";
            $arrPerson = $this->_getPerson('YES',$where,null,$order);
        }


        $objSmarty->assign('personids',  $arrPerson['ids']);
        $objSmarty->assign('personvals', $arrPerson['values']);
        $objSmarty->assign('idpersonsel', $arrPerson['ids'][0]);

        // --- Centro de Custo ---
        $arrCentroCusto = $this->_comboCentroCusto($relatorio = '0');
        $objSmarty->assign('centrocustoids',  $arrCentroCusto['ids']);
        $objSmarty->assign('centrocustovals', $arrCentroCusto['values']);


        // --- Produto ---
        $arrProduto = $this->_comboProduto($relatorio = '0');
        $objSmarty->assign('produtoids',  $arrProduto['ids']);
        $objSmarty->assign('produtovals', $arrProduto['values']);


        // --- Status ---
        $arrStatus = $this->_comboStatus('report',1);
        $result = [];
        foreach ($arrStatus['values'] as $key => $value){
            $result[] = $value;
        }
        $objSmarty->assign('statusids',  $arrStatus['ids']);
        $objSmarty->assign('statusvals', $result);




    }

    public function makeReport()
    {
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $tipo = $_POST['tipo'];

        $startTmp = explode('/',$_POST['datainicial']);
        $endTmp = explode('/',$_POST['datafinal']);
        $dataInicial = $startTmp[2].'-'.$startTmp[1].'-'.$startTmp[0];
        $dataFinal   = $endTmp[2].'-'.$endTmp[1].'-'.$endTmp[0];

        if($_POST['tipoperiodo'] == 'E') {
            $where = "WHERE datapedido BETWEEN '$dataInicial' AND '$dataFinal'";
        }else{
            $where = "WHERE dataentrega BETWEEN '$dataInicial' AND '$dataFinal'";
        }

        switch ($tipo){
            case 'S':
                $order = "ORDER BY nomepessoa asc, vw.idpedido asc";
                break;
            case 'T':
                $rsCarrierList = $this->dbPedidoCompra->getIdPedidoCarrier("AND a.flg_carrier = 'S' AND b.idstatus = 16 AND c.idstatus IN (10,14)");
                $carrierList = '';
                while(!$rsCarrierList->EOF){
                    $carrierList .= $rsCarrierList->fields['idpedido'].',';
                    $rsCarrierList->MoveNext();
                }
                $carrierList = substr($carrierList,0,-1);
                if($carrierList != ''){
                    $where .= strlen($where) == 0 ? 'WHERE ' : ' AND ';
                    $where .= " vw.idpedido IN ($carrierList)";
                }else{
                    echo "A pesquisa não obteve resultados";
                    return false;
                }

                break;
            default:
                $order = "ORDER BY idcentrocusto asc, vw.idpedido asc";
                break;
        }

        $solicitante = $_POST['solicitante'];

        if($solicitante != 0) {
            $where .= strlen($where) == 0 ? 'WHERE ' : ' AND ';
            $where .= " vw.idperson = $solicitante";
        }else{
            $userRole = $this->_getUserRole($_SESSION['SES_LOGIN_PERSON']);
            if($userRole == 2){
                $retUserGroup = $this->_getGrupoOperador("AND ghp.idperson = ".$this->iduser);
                $groups = '';
                while(!$retUserGroup->EOF){
                    $groups .= $retUserGroup->fields['idgroup'].',';
                    $retUserGroup->MoveNext();
                }
                $groups = substr($groups,0,-1);
                
                $where .= strlen($where) == 0 ? 'WHERE ' : ' AND ';
                $where .= " (shg.idgroup IN ($groups) OR vw.idperson IN (SELECT ghp.idperson FROM hdk_tbgroup_has_person ghp WHERE ghp.idgroup IN ($groups)))";
            }            
        }

        $centroDeCusto = $_POST['idcentrocusto'];

        if($centroDeCusto != 0) {
            $where .= strlen($where) == 0 ? 'WHERE ' : ' AND ';
            $where .= " vw.idcentrocusto = $centroDeCusto";
        }

        $produto = $_POST['idproduto'];

        if($produto  != 0)  {
            $rsPedidoList = $this->dbPedidoCompra->getPedidoProduto($produto);
            $pedidoList = '';
            while(!$rsPedidoList->EOF){
                $pedidoList .= $rsPedidoList->fields['idpedido'].',';
                $rsPedidoList->MoveNext();
            }
            $pedidoList = substr($pedidoList,0,-1);

            $where .= strlen($where) == 0 ? 'WHERE ' : ' AND ';
            $where .= " vw.idpedido IN ($pedidoList)";
        }

        $pedido = $_POST['idpedido'];

        if(!empty($pedido))  {
            $where .= strlen($where) == 0 ? 'WHERE ' : ' AND ';
            $where .= " vw.idpedido = $pedido";
        }

        $status = $_POST['idstatus'];

        if($status  != 0)  {
            $where .= strlen($where) == 0 ? 'WHERE ' : ' AND ';
            $where .= " vw.idstatus = $status";
        }

        $tipoimpressao = $_POST['tipoimpressao'];

        $rsPedidoCompra = $this->dbPedidoCompra->getPedidoMaster($where,$order);

        //permissão para download do relatório
        $idperson = $_SESSION['SES_COD_USUARIO'];

        if($rsPedidoCompra->RecordCount() > 0){
            // class FPDF with extension to parsehtml
            // Cria o objeto da biblioteca FPDF
            $pdf = $this->returnHtml2pdf();

            /*
             *  Variables
             */
            //Parâmetros para o cabeçalho
            $this->SetPdfLogo($this->helpdezkPath . '/app/uploads/logos/' .  $this->getReportsLogoImage() ); //Logo
            $leftMargin   = 10; //variável para margem à esquerda
            $this->SetPdfTitle(html_entity_decode(utf8_decode('Relatório de Pedidos'),ENT_QUOTES, "ISO8859-1")); //Titulo //Titulo
            $this->SetPdfPage(utf8_decode($langVars['PDF_Page'])) ; //numeração página
            $this->SetPdfleftMargin($leftMargin);
            //Parâmetros para a Fonte a ser utilizado no relatório
            $this->pdfFontFamily = 'Arial';
            $this->pdfFontStyle  = '';
            $this->pdfFontSyze   = 8;
            $CelHeight = 4;

            $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);

            $pdf->AliasNbPages();
            //$this->SetPdfHeaderData($a_HeaderData);

            $pdf->AddPage(); //Cria a página no arquivo pdf

            $pdf = $this->ReportPdfHeader($pdf); //Insere o cabeçalho no arquivo

            $tituloItens = array(
                array('title'=>'Itens','cellWidth'=>100,'cellHeight'=>4,'titleAlign'=>'C'),
                array('title'=>'Qtd','cellWidth'=>19,'cellHeight'=>4,'titleAlign'=>'C'),
                array('title'=>'Status','cellWidth'=>60,'cellHeight'=>4,'titleAlign'=>'C')
            );

            while(!$rsPedidoCompra->EOF){
                $tituloPedido = array(array('title'=>html_entity_decode(utf8_decode("N° Pedido ".$rsPedidoCompra->fields['idpedido']), ENT_QUOTES, "ISO8859-1"),'cellWidth'=>179,'cellHeight'=>4,'titleAlign'=>'C'));
                $pdf->Cell($leftMargin);
                $pdf->SetFont($this->pdfFontFamily, 'B', $this->pdfFontSyze);
                $this->makePdfLineBlur($pdf, $tituloPedido);
                $pdf->SetFont($this->pdfFontFamily, $this->pdfFontStyle, $this->pdfFontSyze);
                $pdf->SetFont('Arial', '', 8);

                $pdf->Cell($leftMargin);
                $pdf->Cell(15, $CelHeight, html_entity_decode('Solicitante', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                $pdf->Cell(122, $CelHeight, utf8_decode($rsPedidoCompra->fields['nomepessoa']), 0, 0, 'L', 0);

                $pdf->Cell(15, $CelHeight, html_entity_decode('Data/Hora', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                $pdf->Cell(25, $CelHeight, $rsPedidoCompra->fields['fmt_datapedido'], 0, 1, 'L', 0);

                $pdf->Cell($leftMargin);
                $pdf->Cell(23, $CelHeight, html_entity_decode(utf8_decode('Centro de Custo'),ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                $pdf->Cell(67, $CelHeight, utf8_decode($rsPedidoCompra->fields['codigonomecentrodecusto']), 0, 0, 'L', 0);

                $pdf->Cell(23, $CelHeight, html_entity_decode(utf8_decode('Conta Contábil'), ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                $pdf->Cell(66, $CelHeight, utf8_decode($rsPedidoCompra->fields['codigonomecontacontabil']), 0, 1, 'L', 0);

                $pdf->Cell($leftMargin);
                $pdf->Cell(18, $CelHeight, html_entity_decode('Data entrega', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);

                if ($rsPedidoCompra->fields['idturma']) {
                    $pdf->Cell(18, $CelHeight, utf8_decode($rsPedidoCompra->fields['fmt_dataentrega']), 0, 0, 'L', 0);
                    $pdf->Cell(95, $CelHeight, '', 0, 0, 'L', 0);
                    $pdf->Cell(18, $CelHeight, html_entity_decode('Turma', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                    $pdf->Cell(18, $CelHeight, utf8_decode($rsPedidoCompra->fields['nometurma']), 0, 1, 'L', 0);
                }else{
                    $pdf->Cell(18, $CelHeight, utf8_decode($rsPedidoCompra->fields['fmt_dataentrega']), 0, 1, 'L', 0);
                }

                $pdf->Cell($leftMargin);
                $pdf->Cell(21, $CelHeight, html_entity_decode('Motivo Compra', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                $pdf->MultiCell(158, $CelHeight, utf8_decode($rsPedidoCompra->fields['motivo']), 0, 'L', 0);

                $pdf->Cell($leftMargin);
                $pdf->Cell(23, $CelHeight, html_entity_decode('Status do Pedido', ENT_QUOTES, "ISO8859-1") . ':', 0, 0, 'R', 0);
                $pdf->Cell(23, $CelHeight, utf8_decode($rsPedidoCompra->fields['nomestatus']), 0, 1, 'L', 0);
                $pdf->Ln(4);

                $pdf->Cell($leftMargin);
                $pdf->SetFont($this->pdfFontFamily, 'B', $this->pdfFontSyze);
                $this->makePdfLineBlur($pdf, $tituloItens);
                $pdf->SetFont($this->pdfFontFamily, $this->pdfFontStyle, $this->pdfFontSyze);
                $pdf->SetFont('Arial', '', 8);

                if($tipo == 'T'){$wDetail = " AND d.flg_carrier = 'S'";}
                else{$wDetail = "";}

                $rsDetail = $this->dbPedidoCompra->getPedidoDetail("WHERE a.idpedido = ".$rsPedidoCompra->fields['idpedido'].$wDetail);
                while(!$rsDetail->EOF){
                    $pdf->Cell($leftMargin);
                    $pdf->Cell(100, $CelHeight, utf8_decode($rsDetail->fields['nomeproduto']), 0, 0, 'L', 0);
                    $pdf->Cell(19, $CelHeight, utf8_decode($this->_scmformatNumber($rsDetail->fields['quantidade'])), 0, 0, 'C', 0);
                    $pdf->Cell(60, $CelHeight, utf8_decode($rsDetail->fields['stitem']), 0, 1, 'C', 0);

                    if($tipo == 'T'){

                        $rsCotacao = $this->dbPedidoOperador->getItemCotacao("AND scm_tbitempedido.iditempedido = ".$rsDetail->fields['iditempedido']);
                        if($rsCotacao->RecordCount() > 0){
                            $pdf->Cell($leftMargin);
                            $pdf->Cell(25, $CelHeight, html_entity_decode('Fornecedor', ENT_QUOTES, "ISO8859-1") . ':', 'LTB', 0, 'R', 0);
                            if($rsCotacao->fields['nomefantasia'] != ''){$fornecedorName = $rsCotacao->fields['nomefantasia'];}
                            else{$fornecedorName = $rsCotacao->fields['nomefornecedor'];}

                            if($tipoimpressao == 2) {
                                $pdf->Cell(93, $CelHeight, utf8_decode($fornecedorName), 'TB', 0, 'L', 0);
                                $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Valor unitário'), ENT_QUOTES, "ISO8859-1") . ':', 'TB', 0, 'R', 0);
                                $pdf->Cell(15, $CelHeight, utf8_decode(number_format($rsCotacao->fields['valor_unitario'],2,',','.')), 'TB', 0, 'L', 0);

                                $pdf->Cell(15, $CelHeight, html_entity_decode('Valor total', ENT_QUOTES, "ISO8859-1") . ':', 'TB', 0, 'R', 0);
                                $pdf->Cell(15, $CelHeight, utf8_decode(number_format($rsCotacao->fields['valor_total'],2,',','.')), 'RTB', 1, 'L', 0);
                            }else{
                                $pdf->Cell(153, $CelHeight, utf8_decode($fornecedorName), 'RTB', 0, 'L', 0);
                            }
                            $pdf->Ln();
                        }
                    }else{
                        if($tipoimpressao == 2) {

                            $rsCotacao = $this->dbPedidoOperador->getItemCotacao("AND scm_tbitempedido.iditempedido = ".$rsDetail->fields['iditempedido']);
                            if($rsCotacao->RecordCount() > 0){
                                $pdf->Cell($leftMargin);
                                $pdf->Cell(25, $CelHeight, html_entity_decode('Fornecedor', ENT_QUOTES, "ISO8859-1") . ':', 'LTB', 0, 'R', 0);
                                if($rsCotacao->fields['nomefantasia'] != ''){$fornecedorName = $rsCotacao->fields['nomefantasia'];}
                                else{$fornecedorName = $rsCotacao->fields['nomefornecedor'];}
                                $pdf->Cell(93, $CelHeight, utf8_decode($fornecedorName), 'TB', 0, 'L', 0);

                                $pdf->Cell(15, $CelHeight, html_entity_decode(utf8_decode('Valor unitário'), ENT_QUOTES, "ISO8859-1") . ':', 'TB', 0, 'R', 0);
                                $pdf->Cell(15, $CelHeight, utf8_decode(number_format($rsCotacao->fields['valor_unitario'],2,',','.')), 'TB', 0, 'L', 0);

                                $pdf->Cell(15, $CelHeight, html_entity_decode('Valor total', ENT_QUOTES, "ISO8859-1") . ':', 'TB', 0, 'R', 0);
                                $pdf->Cell(15, $CelHeight, utf8_decode(number_format($rsCotacao->fields['valor_total'],2,',','.')), 'RTB', 1, 'L', 0);
                                $pdf->Ln();
                            }
                        }
                    }

                    $rsDetail->MoveNext();
                }
                $pdf->Cell($leftMargin);
                $this->makePdfLine($pdf,$leftMargin,198);
                $pdf->Ln(15);

                $rsPedidoCompra->MoveNext();

            }


            //Parâmetros para salvar o arquivo
            $filename = $_SESSION['SES_LOGIN_PERSON'] . "_report_".time().".pdf"; //nome do arquivo
            $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ; //caminho onde é salvo o arquivo
            $fileNameUrl   = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ; //link para visualização em nova aba/janela

            if(!is_writable($this->helpdezkPath . '/app/downloads/tmp/')) {//validação

                if( !chmod($this->helpdezkPath . '/app/downloads/tmp/', 0777) )
                    $this->logIt("Make report request # ". $rsPedidoCompra->fields['code_request'] . ' - Directory ' . $this->helpdezkPath . '/app/downloads/tmp/' . ' is not writable ' ,3,'general',__LINE__);

            }


            $pdf->Output($fileNameWrite,'F'); //a biblioteca cria o arquivo
            echo $fileNameUrl; //retorno para a função javascript
        }else{
            echo "A pesquisa não obteve resultados";
        }        


    }


}