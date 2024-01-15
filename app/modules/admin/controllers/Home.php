<?php

use App\core\Controller;
use App\src\appServices;
use App\modules\admin\src\adminServices;

class Home extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->appSrc->_sessionValidate();
        
        if($_SESSION['SES_COD_USUARIO'] != 1 && $_SESSION['SES_TYPE_PERSON'] != 1)
            $this->appSrc->_accessDenied();
    }
    
    /**
     * index
     *
     * en_us Calls the method that renders the module's home template
     * pt_br Chama o método que renderiza o template da home do módulo
     *
     * @return void
     */
    public function index()
    {
        $params = $this->makeScreenAdmHome();
        
        $this->view('admin','main',$params);
    }
    
    /**
     * makeScreenAdmHome
     * 
     * en_us Configure program screens
     * pt_br Configura as telas do programa
     * 
     * @return void
     */
    public function makeScreenAdmHome()
    {
        $adminSrc = new adminServices();
        $params = $this->appSrc->_getDefaultParams();
        $params = $adminSrc->_makeNavAdm($params);
        
        return $params;
    }
}