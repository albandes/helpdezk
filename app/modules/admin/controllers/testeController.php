<?php
class Teste extends Controllers{
    public function index(){
        $smarty = $this->retornaSmarty();
        $smarty->display('teste.tpl.html');
    }
}
?>
