<?php
if(class_exists('Model')) {
    class DynamicHome_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicHome_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicHome_model extends apiModel {}
}

class home_model extends DynamicHome_model
{

    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }


}