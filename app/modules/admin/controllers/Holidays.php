<?php

use App\core\Controller;


use App\modules\admin\dao\mysql\holidayDAO;

use App\modules\admin\src\adminServices;
use App\src\appServices;
use App\src\localeServices;


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

    /**
	 *  en_us Configure program screens
	 * 
	 *  pt_br Configura as telas do programa
	 */
    public function makeScreenHolidays()
    {
        $appSrc = new appServices();
		$adminSrc = new adminServices();
		$params = $appSrc->_getDefaultParams();
		$params = $adminSrc->_makeNavAdm($params);
		
        return $params;
    }

    public function jsonGrid()
    {
        $translator = new localeServices();
        $holidayDao = new holidayDao();
        $holidays = $holidayDao->fetchHolidays();
        //echo "",print_r($_GET),"\n";
        if(!is_null($holidays) && !empty($holidays)){
            foreach($holidays as $k=>$v) {
                if(isset($v['idperson'])){
                    $type_holiday = $v['name'];
                }else{
                    $type_holiday = $translator->translate('National_holiday');
                }

                $data[] = array(
                    'id'                  => $v['idholiday'],
                    'holiday_description' => $v['holiday_description'],
                    'holiday_date'        => $v['holiday_date'],
                    'company'             => $type_holiday
    
                );
            }
            
            echo json_encode($data);
        }else{
            echo json_encode(array());
        }
        

        
    }



}