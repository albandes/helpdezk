<?php

session_start();

class Home extends Controllers {
    /**
     * Create an instance, check session time
     *
     * @access public
     */

    public function __construct(){
        parent::__construct();
        session_start();
        $this->validasessao();
    }

    public function index() {
        include 'includes/config/config.php';
        $smarty = $this->retornaSmarty();
        $cod_usu = $_SESSION['SES_COD_USUARIO'];

        $bd = new home_model();
        $usu_name = $bd->selectUserLogin($cod_usu);

        //$db = new logos_model();
        //$headerlogo = $db->getHeaderLogo();
        //if ($enterprise) {
        //    $smarty->assign('enterprisefeature', "");
        //} else {
        //    $smarty->assign('enterprisefeature', "style='display: none;'");
        //}

        $usertype = $_SESSION['SES_TYPE_PERSON'];
        $smarty->assign('headerlogo', $headerlogo->fields['file_name']);
        $smarty->assign('headerheight', $headerlogo->fields['height']);
        $smarty->assign('headerwidth', $headerlogo->fields['width']);
        $smarty->assign('nom_usuario', $usu_name);
        $smarty->assign('userid', $cod_usu);
        $smarty->assign('usertype', $usertype);



        /*

        include 'includes/config/config.php';

        if(substr($path_default, 0,1)!='/'){
            $path_default='/'.$path_default;
        }

        if ($path_default == "/..") {
            define(path,"");
        } else {
            define(path,$path_default);
        }

        $smarty = $this->retornaSmarty();
        session_start();
        $iduser = $_SESSION['SES_COD_USUARIO'];*/

        if(substr($path_default, 0,1)!='/'){
            $path_default='/'.$path_default;
        }

        if ($path_default == "/..") {
            define(path,"");
        } else {
            define(path,$path_default);
        }



        $bd = new dash_model();
        $wid = $bd->getWidget($cod_usu);

        if ($wid->fields) {
            $directory = path."/dashboard/index/getUserWidgets";
        } else {
            //$directory = path."/includes/classes/dashboardplugin/demo/jsonfeed/mywidgets.json";
            $directory = path."/includes/js/dashboard/json/widgets_default.json";
        }
//die(path);
        $smarty->assign('path', path);
        $smarty->assign('directory', $directory);
        $smarty->display('dashboard.tpl.html');
    }

    public function savechanges() {
        session_start();
        $iduser = $_SESSION['SES_COD_USUARIO'];
        $json = "{\"result\" :" . $_POST['settings'] . "}";

        $bd = new dash_model();
        $wid = $bd->getWidget($iduser);
        if ($wid->fields) {
            $upd = $bd->updateUserWidgets($iduser, $json);
            if ($upd) {
                echo "OK Update";
            } else {
                echo 'ERRO';
            }
        } else {
            $sav = $bd->saveUserWidgets($iduser, $json);
            if ($sav) {
                echo "OK Save";
            } else {
                echo 'ERRO';
            }
        }
    }

    public function getUserWidgets() {
        session_start();
        $iduser = $_SESSION['SES_COD_USUARIO'];

        $bd = new dash_model();
        $widgets = $bd->getWidget($iduser);
        echo $widgets->fields['widgets'];
    }


    public function getDashboardCategories()
    {
        include 'includes/config/config.php';
        if(substr($path_default, 0,1)!='/'){
            $path_default='/'.$path_default;
        }

        if ($path_default == "/..") {
            define(path,"");
        } else {
            define(path,$path_default);
        }

        $bd = new dash_model();
        $rswid = $bd->getCategories();
        if ($rswid->fields)
        {
            $aCat['categories'] = array();
            $aCat['categories']['category'] = array();
            $i = 1;
            while (!$rswid->EOF)
            {

                $rstot = $bd->getTotalWidgets($rswid->fields['idcategory']) ;
                if (!$rstot->fields) {
                    die( __FILE__ . " - " . __LINE__ . " - ERRO: Recordet \$bd->getTotalWidgets voltou vazio, id = " . $rswid->fields['idcategory'] );
                }
                $tmp = array(
                    'id' => $rswid->fields['idcategory'],
                    'title' => $rswid->fields['title'],
                    'amount' => $rstot->fields['total'],
                    'url' => path . "/dashboard/index/getDashBoardCategoryWidgets/id/"  . $rswid->fields['idcategory']
                );

                array_push($aCat['categories']['category'], $tmp);
                $i++;
                $rswid->MoveNext();
            }

            echo json_encode($aCat) ;

        }
    }

    public function getDashboardCategoryWidgets() {

        $id = $this->getParam('id');

        $bd = new dash_model();
        $rs = $bd->getCategoryWidgets($id);

        if ($rs->fields)
        {
            $aCat['result'] = array();
            $aCat['result']['data'] = array();
            while (!$rs->EOF)
            {
                $tmp = array(
                    'id' => "w" . $rs->fields['idwidget'],
                    'title' => $rs->fields['name'],
                    'description' => $rs->fields['description'],
                    'creator' => $rs->fields['creator'],
                    // /dashboard/hdk_requests/home/idwidget/2
                    'url' =>  path . '/dashboard/'.$rs->fields['controller'].'/home/idwidget/'.$rs->fields['idwidget'] ,
                    'image' => path . '/app/uploads/helpdezk/dashboard/' . $rs->fields['image']
                );

                array_push($aCat['result']['data'], $tmp);

                $rs->MoveNext();
            }
            echo json_encode($aCat) ;
        }

    }






    public function sessionDestroy() {
        session_start();
        session_unset();
        session_destroy();
    }
    
    
}

?>
