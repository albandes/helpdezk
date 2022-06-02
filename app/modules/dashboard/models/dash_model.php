<?php

class dash_model extends Model {

    public function getCategories() {
		$sql = 	"
				select
				   idcategory,
				   title
				from dsh_tbcategory	
				where status = 'A'	
				";
        return $this->select($sql);
    }

    public function getTotalWidgets($id) {
		$sql = 	"
				select
				   count(idwidget) as total 
				from dsh_tbcategory_has_widget
				where idcategory = '$id'		
				";
				
        return $this->select($sql);
    }	

    public function getCategoryWidgets($id) 
	{
		$sql = 	"
				select
				   idwidget,
				   name,
				   dbhost,
				   dbuser,
				   dbpass,
				   dbname,
				   field1,
				   field2,
				   field3,
				   field4,
				   field5,
				   description,
				   creator,
				   controller,
				   image
				from dsh_tbwidget
				where idwidget in(select
									idwidget
								 from dsh_tbcategory_has_widget
								 where idcategory = '$id')
				";

        return $this->select($sql);
    }	

    public function getWidgetParam($idwidget) {
		$sql = 	"
				select
				  dbhost,
				  dbuser,
				  dbpass,
				  dbname,
				  field1,
				  field2,
				  field3,
				  field4,
				  field5
				from dsh_tbwidget	
				where idwidget = '$idwidget'
				";
        return $this->select($sql);
    }
	
    public function getWidget($id) {
        return $this->select("select widgets from dsh_tbwidgetusuario where idusuario = '$id'");
    }

    public function saveUserWidgets($id, $json) {
        return $this->db->Execute("insert into dsh_tbwidgetusuario (idusuario, widgets) values ('$id','$json')");
    }

    public function updateUserWidgets($id, $json) {
		//die("update dsh_tbwidgetusuario set widgets = '$json' where idusuario = '$id' ");
        return $this->db->Execute("update dsh_tbwidgetusuario set widgets = '$json' where idusuario = '$id'");
    }
}

?>
