<?php

/** Example Controller
 * @author Alejandro Schmechel - Pipegreg
 */
class Example extends Controllers {

    /** Shows the default index view for the controller
     *  @name index
     */
    public function index() {

        /** Starts smarty for use, by calling the function retornaSmarty in system/system.php
         *  @name $smarty
         */
        $smarty = $this->retornaSmarty();
        /** displays the view 'exemplo.tpl.html'
         * @name $smarty->display
         */
        $smarty->display('example.tpl.html');
    }
     /** Inserts a new record.
     *   @name insert
      *  @return response
     */
    public function insert() {
        /** extracts the $_POST vars for use
         *  
         */
        extract($_POST);

        /** starts the model for use and puts its functions in var $db
         * @name $db
         */
        $db = new example_model();
        /** puts the result of example_model's insertPeopleAge function in var $ins
         * @name $ins
         */
        $ins = $db->insertPeopleAge($name, $age);

        if ($ins) {
            /** returns message if insert went ok.
             * 
             */
            echo "Successfully inserted";
        } else {
            /** returns false if can't insert.
             * 
             */
            return false;
        }
    }
     /** Shows the registered people
     * @name people
     * 
     */
    public function people() {
        $smarty = $this->retornaSmarty();
        $db = new example_model();
        /** puts the result of example_model's selectPeopleAges function in var $ret
         * @name $ret
         */
        $ret = $db->selectPeopleAges();
        while (!$ret->EOF) {
            /** mounts an array with all records for posting in examplepeople
             * 
             */
            $table[] = array('id' => $ret->fields['idex'], 'name' => $ret->fields['name'], 'age' => $ret->fields['age']);
            $ret->MoveNext();
        }
        /** sets the smarty var 'table' with record's value.
         * 
         */
        $smarty->assign('table', $table);
        $smarty->display('examplepeople.tpl.html');
    }
    /** mounts the edit form with person's informations.
     *  @name editform
     */
    public function editform() {
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
        $bd = new example_model();
        /**
         * Selects the data for the selected item to fill the edit form's fields.
         *
         */
        $ret = $bd->selectData($id);
        /**  assigns smarty variables for use in exampleformedit.tpl.html
         *  @name $smarty->assign
         *  
         */
        $smarty->assign('id', $id);
        $smarty->assign('name', utf8_decode($ret->fields['name']));
        $smarty->assign('age', utf8_decode($ret->fields['age']));
        $smarty->display('exampleformedit.tpl.html');
    }
    /** deletes the selected record.
     *  @name delete
     *  @return response
     */
    public function delete() {
        $id = $_POST['id'];
        $db = new example_model;
        /** calls the deletePerson function in example_model, passing the specified ID
         *  @name $del
         */
        $del = $db->deletePerson($id);
        /** if delete went ok, shows success message.
         * 
         */
        if ($del) {
            echo "Successfully deleted";
        } else {
            /** if an error occurred, returns false for the calling jQuery function
             *
             */
            return false;
        }
    }
    /** updates the selected record
     *  @name edit
     *  @return response
     */
    public function edit() {
        /** extracts the $_POST vars for use
         * 
         */
        extract($_POST);

        $db = new example_model;
        /** calls the updatePerson function in example_model, passing the specified ID, and the new values of $name and $age
         *  @name $upd
         */
        $upd = $db->updatePerson($id, $name, $age);
        if ($upd) {
            /** if update went ok, shows success message.
             * 
             */
            echo "Successfully updated";
        } else {
            /** if an error occurred, returns false for the calling jQuery function
             *
             */
            return false;
        }
    }

}

?>
