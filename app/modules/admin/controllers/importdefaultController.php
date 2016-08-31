<?php

class importdefault extends Controllers {

    public function index() {
        $smarty = $this->retornaSmarty();
        $smarty->display('importdefault.tpl.html');
    }

    public function defaultperm() {
        $bd = new import_model();
        $progs = $bd->selectPrograms();
        $count = 0;
        while (!$progs->EOF) {
            $idprogram = $progs->fields['idprogram'];
            if ($idprogram != '14' && $idprogram != '17' && $idprogram != '20' && $idprogram != '21' && $idprogram != '22' && $idprogram != '24' && $idprogram != '26' && $idprogram != '27') {
                for ($i = 0; $i <= 4; $i++) {
                    $ins = $bd->insertDefaultPermissions($i, $idprogram);
                    $count++;
                }
            }
            $progs->MoveNext();
        }
        if ($ins) {
            echo "Foram inseridas $count linhas com sucesso!";
        } else {
            return false;
        }
    }

    public function groupperm() {
        $bd = new import_model();
        $defaults = $bd->selectDefaults();
        $count = 0;
        while (!$defaults->EOF) {
            $accesstype = $defaults->fields['idaccesstype'];
            $idprogram = $defaults->fields['idprogram'];
            for ($i = 0; $i <= 5; $i++) {
                if ($i == "1") {
                    $allow = "Y";
                    $ins = $bd->insertGroupPermissions($idprogram, $i, $accesstype, $allow);
                } else {
                    $allow = "N";
                    $ins = $bd->insertGroupPermissions($idprogram, $i, $accesstype, $allow);
                }
                $count++;
            }
            $defaults->MoveNext();
        }
        if ($ins) {
            echo "Foram inseridas $count linhas com sucesso!";
        } else {
            return false;
        }
    }

    public function rootperm() {
        $bd = new import_model();
        $defaults = $bd->selectGroups();
        $count = 0;
        $idperson = '1';
        while (!$defaults->EOF) {
            $idgroup = $defaults->fields['idpermissiongroup'];
            $ins = $bd->insertRootPermissions($idperson, $idgroup);
            $count++;
            $defaults->MoveNext();
        }
        if ($ins) {
            echo "Foram inseridas $count linhas com sucesso!";
        } else {
            return false;
        }
    }

}

?>
