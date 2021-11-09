<?php

use App\core\Controller;

use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\featureDAO;
use App\modules\admin\dao\mysql\personDAO;
use App\modules\admin\src\loginServices;
use App\modules\admin\src\adminServices;
use App\src\appServices;


class Holidays extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
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

    public function makeScreenHolidays()
    {
        $appSrc = new appServices();
		$adminSrc = new adminServices();
		$params = $appSrc->_getDefaultParams();
		$params = $adminSrc->_makeNavAdm($params);
		
        return $params;
    }

}