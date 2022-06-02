<?php

require_once(HELPDEZK_PATH . '/app/modules/acd/controllers/acdCommonController.php');
/*require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/pipeDateTime.php');*/

class home extends acdCommon {
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

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );

        //
        $this->modulename = 'Academico' ;
        //

        //$this
        $dbCommon = new common();
        $id = $dbCommon->getIdModule($this->modulename) ;
        if(!$id) {
            die('Module ' .$this->modulename. ' don\'t exists in tbmodule !!!') ;
        } else {
            $this->idmodule = $id ;
        }

        /*
        $this->loadModel('home_model');
        $dbHome = new home_model();
        $this->dbHome = $dbHome;
        */


    }

    public function index()
    {
        $cod_usu = $_SESSION['SES_COD_USUARIO'];
        //echo "<pre>"; print_r($_SESSION); echo "</pre>";
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavAcd($smarty);

        // $this->makeDash($smarty);

        //$this->makeMessages($smarty);

        $smarty->assign('jquery_version', $this->jquery);

        // -- navbar
        $smarty->assign('navBar', 'file:'.$this->getHelpdezkPath().'/app/modules/main/views/nav-main.tpl');

        /** combo Ano Letivo */
        $arrYear = $this->_comboAcdYear(2013);
        $smarty->assign('acdyearids',  $arrYear['ids']);
        $smarty->assign('acdyearvals', $arrYear['values']);
        $smarty->assign('idacdyear', 2013 );

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('acd-main.tpl');



    }

    function makeDash($smarty)
    {
        $smarty->assign('dtatualiza', $this->_getDataAtualizacao());
        $smarty->assign('total_mq', $this->_getNumFuncEmpresa(2));
        $smarty->assign('total_mario', $this->_getNumFuncEmpresa(1));
        $smarty->assign('total_tresv', $this->_getNumFuncEmpresa(3));
    }

    public function ajaxMediasGraph()
    {
        $this->loadModel('acdindicadoresnotas_model');
        $dbNota = new acdindicadoresnotas_model();

        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 30
                )
            )
        );

        $year = $_POST['cmbYear'];

        $response = file_get_contents($this->_serverApi.'/api/src/public/mediaGrafico/'.$year.'/ALL',false,$ctx);

        if(!$response) {
            if ($this->log)
                $this->logIt('Sem conexao com o servidor da Perseus - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            //return false;
        }

        $response = json_decode($response, true);

        if (!$response['status']){
            if ($this->log)
                $this->logIt('Nao retornou dados do servidor da Perseus - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            //return false;
        }

        $a = $response['result'];

        $aLabel = array();
        $aData = array();
        $aBG = array();
        $aBorder = array();

        if($response){
            foreach ($a as $item){
                $where = "WHERE sigla = '".$item['discsigla']."'";
                $retDisc = $dbNota->getDisciplina($where);

                array_push($aLabel,$item['discsigla']);
                array_push($aData,number_format($item['mediaanual'],0,',','.'));
                array_push($aBG,$this->hex2rgba($retDisc->fields['cor'],0.2));
                array_push($aBorder,$this->hex2rgba($retDisc->fields['cor'],1));
            }

            $aRet = array(
                "labels" => $aLabel,
                "datasets" => array(
                    array(
                        "label" => "Médias ".$year,
                        "data" => $aData,
                        "fill" => false,
                        "backgroundColor" => $aBG,
                        "borderColor" => $aBorder,
                        "borderWidth" => 1
                    )

                )
            );
        }else{
            $aRet = array(
                "labels" => array(),
                "datasets" => array()

            );
        }

        echo json_encode($aRet);
    }

    public function ajaxAreaGraph()
    {
        $this->loadModel('acdindicadoresnotas_model');
        $dbNota = new acdindicadoresnotas_model();

        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 30
                )
            )
        );

        $yearEnd = date('Y');

        $aLabel = array();
        for($i = 2013;$i <= $yearEnd;$i++){
            array_push($aLabel,$i);
        }

        $retArea = $dbNota->getArea();

        $aDataSet = array();
        while(!$retArea->EOF){
            $where = "WHERE idareaconhecimento = '".$retArea->fields['idareaconhecimento']."'";

            $aDataArea = array();

            foreach ($aLabel as $year){
                $retDisc = $dbNota->getDisciplina($where);

                $mediaYear = 0;
                $tDisc = 0;

                while(!$retDisc->EOF){

                    $response = file_get_contents($this->_serverApi.'/api/src/public/mediaGrafico/'.$year.'/'.$retDisc->fields['sigla'],false,$ctx);

                    if(!$response) {
                        if ($this->log)
                            $this->logIt('Sem conexao com o servidor da Perseus - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                        //return false;
                    }

                    $response = json_decode($response, true);

                    if (!$response['status']){
                        if ($this->log)
                            $this->logIt('Nao retornou dados do servidor da Perseus - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                        //return false;
                    }

                    $a = $response['result'];

                    if($response){
                        foreach ($a as $item){
                            $mediaYear += $item['mediaanual'];
                        }
                        $tDisc++;
                    }

                    $retDisc->MoveNext();
                }

                $mediaYear = number_format(($mediaYear/$tDisc),0,',','.');
                array_push($aDataArea,$mediaYear);

            }

            $bus = array(
                            "label" => $retArea->fields['descricaoabrev'],
                            "data" => $aDataArea,
                            "backgroundColor" => $this->hex2rgba($retArea->fields['cor'],0.2),
                            "borderColor" => $this->hex2rgba($retArea->fields['cor'],1),
                            "borderWidth" => 1
                        );

            array_push($aDataSet,$bus);
            $retArea->MoveNext();
        }

        $aRet = array(
            "labels" => $aLabel,
            "datasets" => $aDataSet
        );

        echo json_encode($aRet);
    }




}

?>
