<?php
class newprogr extends Controllers{
    public function index(){
        session_start();
        $program = "New Program";
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
        $access = $this->access($user, $program, $typeperson);
        $smarty = $this->retornaSmarty();
        $db = new evaluation_model();
        $select = $db->selectQuestions();
        while (!$select->EOF) {
            $campos[] = $select->fields['idquestion'];
            $valores[] = $select->fields['question'];
            $select->MoveNext();
        }
        $smarty->assign('questionids', $campos);
        $smarty->assign('questionvals', $valores);
        $smarty->display('features.tpl.html');
    }
}
?>
