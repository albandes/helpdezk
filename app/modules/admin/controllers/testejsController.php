<?php
class testejs extends Controllers{
    public function index(){
        $smarty = $this->retornaSmarty();
        $smarty->display('testajs.tpl.html');
    }
}
?>
