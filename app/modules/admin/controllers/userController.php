<?php
class user extends Controllers{
    public function index(){
        $smarty = $this->retornaSmarty();
        $smarty->display('user.tpl.html');
    }
}
?>
