<?php

use App\core\Controller;

use App\modules\main\dao\mysql\externalappDAO;
use App\modules\helpdezk\dao\mysql\ticketDAO;

use App\modules\main\models\mysql\externalappModel;
use App\modules\helpdezk\models\mysql\ticketModel;

use App\modules\admin\src\adminServices;
use App\modules\helpdezk\src\hdkServices;
use App\src\trelloServices;

class HdkTrello extends Controller
{           
    /**
     * @var mixed
     */
    protected $_credentials;

    /**
     * @var mixed
     */
    protected $_key; 

    /**
     * @var mixed
     */
    protected $_secret;

    /**
     * @var mixed
     */
    protected $_token;

    public function __construct()
    {
        parent::__construct();

        $this->appSrc->_sessionValidate();

        $this->_credentials = $this->getCredentials();

        if($this->_credentials) {
            $this->_key = $this->_credentials['key'];
            $this->_token =  $this->_credentials['token'];
            $this->_secret = '';
    
            $this->trello = new trelloServices($this->_key, $this->_secret, $this->_token);
        }
    }
        
    /**
     * createCard
     *
     * @return void
     */
    public function createCard()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $ticketCode = $_POST['ticketCode'];
        $listId = $_POST['listId'];
        $cardTitle = addslashes(trim(strip_tags($_POST['cardTitle'])));
        $cardDescription = strip_tags($_POST['cardDescription'],'<p><br>');
        $cardDescription = str_replace(array("<p>","</p>","<br>"),array("","\n","\n"),$cardDescription);
        
        $data = $this->trello->_createCard($listId,$cardTitle,$cardDescription);
        
        if($data['success']){
            $ticketDAO = new ticketDAO();
            $ticketModel = new ticketModel();

            $cardId = $data['return']['id'];
            $ticketModel->setTicketCode($ticketCode)
                        ->setTrelloUserId($_SESSION['SES_COD_USUARIO'])
                        ->setTrelloCardId($cardId);

            $ret = $ticketDAO->insertTrelloCard($ticketModel);
            if(!$ret['status']){
                $st = false;
                $msg = $ret['message'];
                $cardId = "";
            }else{
                $st = true;
                $msg = "";
            }
            
        } else {
            $st = false;
            $msg = $data['message'];
            $cardId = "";
        }
        
        $aRet = array(
            "success" => $st,
            "message" => $msg,
            "cardId" => $cardId
        );

        echo json_encode($aRet);
    }
    
    /**
     * en_us Returns cards list
     * pt_br Retorna a lista de cartões
     *
     * @return string
     */
    public function getCards()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $idList = $_POST['listId'];
        $data = $this->trello->_getCards($idList);
        if($data['success']){
            echo $this->tableCards($data['return']);
        } else {
            print $data['message'];
        }

    }
    
    /**
     * en_us Formats  cards list in HTML table
     * pt_br Formata a lista de cartões na tabela HTML
     *
     * @param  mixed $data
     * @return void
     */
    function tableCards($data)
    {
        foreach ($data as $row) {
            $fieldsID[] = $row['id'];
            $values[]   = $row['name'];
        }

        $arrCards['ids']    = $fieldsID;
        $arrCards['values'] = $values;

        $table = "<table class='table'><thead><tr><th>#</th><th>{$this->translator->translate('Name')}</th></tr></thead><tbody>";
        
        $i = 0;
        foreach ( $arrCards['ids'] as $indexKey => $indexValue ) {
            $i++;
            $table .= '<tr><td>'.$i.'</td><td>'.$arrCards['values'][$indexKey].'</td></tr>';
        }

        $table .= '</tbody></table>';

        return $table;

    }
    
    /**
     * en_us Returns lists in HTML to dropdown list
     * pt_br Retorna listas em HTML para o combo
     *
     * @return void
     */
    public function getLists() {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }

        $boardId = $_POST['boardId'];
        $data = $this->trello->_getLists($boardId);
        if($data['success']){
            echo $this->_comboLists($data['return']);
        } else {
            print $data['message'];
        }

    }

    function _comboLists($data)
    {
        foreach ($data as $row) {
            $fieldsID[] = $row['id'];
            $values[]   = $row['name'];
        }

        $arrLists['ids']    = $fieldsID;
        $arrLists['values'] = $values;

        $select = '';
        foreach ( $arrLists['ids'] as $indexKey => $indexValue ) {
            if ($arrLists['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrLists['values'][$indexKey]."</option>";
        }
        return $select;


    }

    public function getBoards()
    {
        if (!$this->appSrc->_checkToken()) {
            $this->logger->error("Error Token - User: {$_SESSION['SES_LOGIN_PERSON']}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            return false;
        }
        
        $data = $this->trello->_getBoards();
        if($data['success']){
            echo $this->_comboBoards($data['return']);
        } else {
            print $data['message'];
        }

    }

    function _comboBoards($data)
    {
        foreach ($data as $row) {
            $fieldsID[] = $row['id'];
            $values[]   = $row['name'];
        }

        $arrBoards['ids']    = $fieldsID;
        $arrBoards['values'] = $values;

        $select = '';
        foreach ( $arrBoards['ids'] as $indexKey => $indexValue ) {
            if ($arrBoards['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrBoards['values'][$indexKey]."</option>";
        }
        return $select;


    }
    
    /**
     * en_us Returns array with user's access credentials
     * pt_br Retorna array com as credenciais de acesso do usuário
     *
     * @return void
     */
    public function getCredentials()
    {
        $externalappDAO = new externalappDAO();
        $externalappModel = new externalappModel();
        $externalappModel->setUserID($_SESSION['SES_COD_USUARIO']);
        $aRet = array();

        $ret = $externalappDAO->fetchExternalSettingsByUser($externalappModel);
        if(!$ret['status']){
            return false;
        }

        $settings = $ret['push']['object']->getSettingsList();
        
        foreach($settings as $key=>$val) {
            if ($val['idexternalapp'] == 50 && $val['fieldname'] == 'key' ) {
                $aRet['key'] = $val['value'];
            } elseif ($val['idexternalapp'] == 50 && $val['fieldname'] == 'token' ){
                $aRet['token'] = $val['value'];
            }
        }

        return $aRet;

    }
}