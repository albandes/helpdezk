<?php
class emailconfig extends Controllers{
    public function index(){
        session_start();
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
        $program = $bd->selectProgramIDByController("emailconfig/");
        $access = $this->access($user, $program, $typeperson);
        $smarty = $this->retornaSmarty();
        $smarty->display('emailconfig.tpl.html');
    }
    public function json() {
        $smarty = $this->retornaSmarty();
        $varLang = $smarty->get_config_vars();
        $prog = "";
        $path = "";

        $page = $_POST['page'];
        $rp = $_POST['rp'];

        if (!$sortorder)
            $sortorder = 'asc';


        if (!$page)
            $page = 1;
        if (!$rp)
            $rp = 10;

        $start = (($page - 1) * $rp);

        $limit = "LIMIT $start, $rp";

        $query = $_POST['query'];
        $qtype = $_POST['qtype'];

        $sortname = $_POST['sortname'];
        $sortorder = $_POST['sortorder'];



        $where = "";
        if ($query) {
            switch ($qtype) {
                case 'name':
                    $where = "and  $qtype LIKE '$query%' ";
                    break;
                default:
                    $where = "";
                    break;
            }
        }
        if (!$sortname or !$sortorder) {

        } else {
            $order = " ORDER BY $sortname $sortorder ";
        }

        $limit = "LIMIT $start, $rp";

        $bd = new emailconfig_model();
        $rs = $bd->selectConfigs($where, $order, $limit);

        $qcount = $bd->countConfigs($where);
        $total = $qcount->fields['total'];

        $data['page'] = $page;
        $data['total'] = $total;
        while (!$rs->EOF) {
            $smarty = $rs->fields['smarty'];
            if ($rs->fields['status'] == 'A') {
                $status = "<img src='".path."/app/themes/".theme."/images/active.gif' height='10px' width='10px'>";
            } else {
                $status = "<img src='".path."/app/themes/".theme."/images/notactive.gif' height='10px' width='10px'>";
            }
            $rows[] = array(
                "id" => $rs->fields['idconfig'],
                "cell" => array(
                    $varLang["$smarty"],
                    $status
                )
            );
            $rs->MoveNext();
        }
        $data['rows'] = $rows;
        $data['params'] = $_POST;

        echo json_encode($data);
    }
    public function formedit(){
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
        $db = new emailconfig_model();
        $getid = $db->getTemplate($id);
        $temp = $getid->fields['idtemplate'];
        $data = $db->getTemplateData($temp);
        $smarty->assign('tempsubject', $data->fields['name']);
        $smarty->assign('description', $data->fields['description']);
        $smarty->assign('id',$temp);
        $smarty->display('modals/emailconfig/edit.tpl.html');
        //$smarty->display('templateedit.tpl.html');

    }
    public function edittemplate(){
        if (!$this->_checkToken()) return false;

        extract($_POST);

        $bd = new emailconfig_model();
        $upd = $bd->updateTemplate($id, $name, $description);
        if ($upd){
            $deac_sess = explode(",",$id);
            foreach($deac_sess as $id){
                $where = "AND idconfig = $id";
                $conf = $bd->selectConfigs($where);
                $_SESSION[$conf->fields['session_name']] = 1;
            }
            echo "ok";
        }
        else{
            return false;
        }
    }

    public function deactivatemodal() {
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
        $smarty->assign('id', $id);
        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->display('modals/emailconfig/disable.tpl.html');
    }

    public function deactivate() {
        if (!$this->_checkToken()) return false;
        $id = $_POST['id'];
        $bd = new emailconfig_model();
        $dea = $bd->emailConfigDeactivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }

    public function activatemodal() {
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
        $smarty->assign('id', $id);
        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->display('modals/emailconfig/active.tpl.html');
    }

    public function activate() {
        if (!$this->_checkToken()) return false;
        $id = $_POST['id'];
        $bd = new emailconfig_model();
        $dea = $bd->emailConfigActivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }
}
?>
