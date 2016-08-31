<?php
class example_model extends Model{
    public function selectPeopleAges(){ // selects people and ages to mount the table
        return $this->select("select idex, name, age from tbteste");
    }
    public function insertPeopleAge($name,$age){ // inserts person and its age
        return $this->db->Execute("INSERT into tbteste (name,age) values ('$name','$age')");
    }
    public function selectData($id){ //selects the data from specified person
        return $this->select("select name, age from tbteste where idex='$id'");
    }
    public function deletePerson($id){ //deletes person data
        return $this->db->Execute("delete from tbteste where idex='$id'");
    }
    public function updatePerson($id,$name,$age){ // updates person data
        return $this->db->Execute("UPDATE tbteste set name='$name', age='$age' where idex='$id'");
    }
}
?>
