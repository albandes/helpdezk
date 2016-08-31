<?php
class home extends Controllers{
    public function logout(){
        session_start();
        session_destroy();
        header('Location:'.path.'/admin/login');
    }
}
?>
