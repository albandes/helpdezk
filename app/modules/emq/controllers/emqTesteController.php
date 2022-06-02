<?php
/**
 * Created by PhpStorm.
 * User: valentin.acosta
 * Date: 11/10/2019
 * Time: 10:19
 */
class emqTeste {
    public function __construct(){
        parent::__construct();
        session_start();
        $this->sessionValidate();
    }
}