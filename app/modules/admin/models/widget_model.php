<?php

class widget_model extends Model {

    public function selectWidget($where, $order, $limit) {
        return $this->db->Execute(	"
									select
									  idwidget,
									  name,
									  `index`,
									  status
									from dsh_tbwidget
									  $where $order $limit
									");
    }

    public function countWidget($where = NULL) {
        return $this->db->Execute(	"
									select
									  count(idwidget) as total 
									from dsh_tbwidget		
									  $where
									");
    }
    
	
	public function selectCategoryAll() 
	{
        return $this->db->Execute(	"
									select
									  idcategory,
									  title
									from dsh_tbcategory		
									");
    }


    public function insertWidget($idcategory,$name,$description,$creator,$controller,$dbhost,$dbname,$dbuser,$dbpass,$field1,$field2,$field3,$field4,$field5,$image) {
        return $this->db->Execute(	"
									insert into dsh_tbwidget
												(
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
												)
									values (
											'$name',
											'$dbhost',
											'$dbuser',
											'$dbpass',
											'$dbname',
											'$field1',
											'$field2',
											'$field3',
											'$field4',
											'$field5',
											'$description',
											'$creator',
											'$controller',
											'$image'
											)		
									");
    }

    public function InsertID() {
        return $this->db->Insert_ID( );	
    }

    public function insertCategoryHasWidget($idwidget,$idcategory) 
	{
       return $this->db->Execute(	"
									insert into dsh_tbcategory_has_widget
												(idcategory,
												 idwidget)
									values ('$idcategory',
											'$idwidget');	   
									");  
	   
    }

    public function updateCategoryHasWidget($idwidget,$idcategory) 
	{
        return $this->db->Execute(	"
									update dsh_tbcategory_has_widget
									set idcategory = '$idcategory'
									where idwidget = '$idwidget'   
									");  
    }
	
    public function updateWidget($id,$name,$description,$creator,$controller,$dbhost,$dbname,$dbuser,$dbpass,$field1,$field2,$field3,$field4,$field5,$image) {
        return $this->db->Execute(	"
									update dsh_tbwidget
									set name = '$name',
									    dbhost = '$dbhost',
									    dbuser = '$dbuser',
									    dbpass = '$dbpass',
									    dbname = '$dbname',
									    field1 = '$field1',
									    field2 = '$field2',
									    field3 = '$field3',
									    field4 = '$field4',
									    field5 = '$field5',
									    description = '$description',
									    creator = '$creator',
									    controller = '$controller',
									    image = '$image'
									where idwidget = '$id'		
		
									");
    }

    public function widgetDeactivate($id) {
        return $this->db->Execute("UPDATE dsh_tbwidget set status = 'N' where idwidget in ($id)");
    }

    public function widgetActivate($id) {
        return $this->db->Execute("UPDATE dsh_tbwidget set status = 'A' where idwidget in ($id)");
    }
 

    public function widgetDelete($id) {
        die("delete from dsh_tbwidget where idwidget='$id'");
    }
	
    public function selectWidgetData($id) 
	{
        return $this->select("
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
								  image,
								  `index`,
								  status,
								  (select
									 idcategory
								   from dsh_tbcategory_has_widget
								   where idwidget = '$id') as idcategory
								from dsh_tbwidget
								where idwidget = '$id'
							");
    }


}

?>
