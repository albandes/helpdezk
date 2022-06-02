<?php

if(class_exists('Model')){
    class dtitles_model extends Model{}
}elseif(class_exists('cronModel')){
    class dtitles_model extends cronModel{}
}elseif(class_exists('apiModel')){
    class dtitles_model extends apiModel{}
}

class titles_model extends dtitles_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    //Para as operações seguintes deve ser criada a tabela do programa
   public function getTitles($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT idtitle,a.idmaterialtype,b.name materialtype, a.idcollection,c.name collection,
        a.name,cutter,isbn,issn,a.idcdd,d.code cdd,cdu,a.idpublishingcompany,
        e.name publishingcompany,a.idcolor,f.name color,a.idclassification,g.name classification,
        a.flagcollection
        FROM lmm_tbtitle a 
        JOIN lmm_tbmaterialtype b
        ON a.idmaterialtype = b.idmaterialtype 
        LEFT OUTER JOIN lmm_tbcollection c
	    ON a.idcollection = c.idcollection
        JOIN lmm_tbcdd d
	    ON a.idcdd = d.idcdd
        JOIN lmm_tbpublishingcompany e
        ON a.idpublishingcompany = e.idpublishingcompany
        JOIN lmm_tbcolor f
        ON a.idcolor = f.idcolor
        JOIN lmm_tbclassification g 
        ON a.idclassification = g.idclassification
        $where $group $order $limit";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function getExemplar($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT idbookcopy,a.`idtitle`,b.name title,a.`idlibrary`,c.name library, dtacquisition,a.`idorigin`,d.name origin,volume, edition, bookyear, hascd
        FROM lmm_tbbookcopy a, lmm_tbtitle b, lmm_tblibrary c, lmm_tborigin d
        WHERE a.idtitle = b.idtitle
        AND a.idlibrary = c.idlibrary
        AND a.idorigin = d.idorigin
        $where $group $order $limit";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function getAuthor($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT `idtitle`,a.`idauthor`,b.name author,b.cutter
        FROM lmm_tbtitle_has_author a, lmm_tbauthor b
        WHERE a.idauthor = b.idauthor


        $where $group $order $limit";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function insertTitles($idmaterialtype,$flagcollection,$idcollection,$title,$cutter,$isbn,$issn,$idcdd,$cdu,$idpublishingcompany,$idcolor,$idclassification) {
        $sql = "INSERT INTO lmm_tbtitle(`idmaterialtype`,`flagcollection`,`idcollection`,`name`,`cutter`,`isbn`,`issn`,`idcdd`,`cdu`,`idpublishingcompany`,`idcolor`,`idclassification`) 
                VALUES($idmaterialtype,'$flagcollection',$idcollection,'$title','$cutter','$isbn','$issn',$idcdd,'$cdu',$idpublishingcompany,$idcolor,$idclassification)";
    
        $ret = $this->db->Execute($sql); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function insertExemplar($idtitle,$idlibrary,$dtacquisition,$origin,$volume,$edition,$bookyear,$hascd) {
        $sql = "INSERT INTO lmm_tbbookcopy(`idtitle`,`idlibrary`,`dtacquisition`,`idorigin`,`volume`,`edition`,`bookyear`,`hascd`) 
                VALUES($idtitle,$idlibrary,'$dtacquisition','$origin','$volume','$edition',$bookyear,'$hascd')";
    
        $ret = $this->db->Execute($sql); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }   

    public function insertHasAuthor($idtitle,$idauthor) {
        $sql = "INSERT INTO lmm_tbtitle_has_author(`idtitle`,`idauthor`) 
                VALUES($idtitle,$idauthor)";
    
        $ret = $this->db->Execute($sql); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }  



    
    public function updateTitles($idtitle,$idmaterialtype,$flagcollection,$idcollection,$name,$cutter,$isbn,$issn,$idcdd,$cdu,$idpublishingcompany,$idcolor,$idclassification){
        $sql = "UPDATE lmm_tbtitle
                    SET `idmaterialtype`=$idmaterialtype,`flagcollection`='$flagcollection',`idcollection`='$idcollection',`name`='$name',`cutter`='$cutter',`isbn`='$isbn',`issn`='$issn',`idcdd`=$idcdd,`cdu`='$cdu',`idpublishingcompany`=$idpublishingcompany,`idcolor`=$idcolor,`idclassification`=$idclassification
                    WHERE idtitle = $idtitle";
    
        $ret = $this->db->Execute($sql); 
    
        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        
    } 


    
    public function updateExemplar($idbookcopy,$idtitle,$idlibrary,$dtacquisition,$idorigin,$volume,$edition,$bookyear,$hascd){
        $sql = "UPDATE lmm_tbbookcopy
                    SET `idtitle`=$idtitle,`idlibrary`=$idlibrary,`dtacquisition`='$dtacquisition',`idorigin`=$idorigin,`volume`='$volume',`edition`='$edition',`bookyear`='$bookyear',`hascd`='$hascd'
                    WHERE idbookcopy = $idbookcopy";
    
        $ret = $this->db->Execute($sql); 
    
        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        
    } 

    public function updateHasAuthor($idtitle,$idauthor){
        $sql = "UPDATE lmm_tbtitle_has_author
                    SET `idauthor`=$idauthor
                    WHERE idtitle = $idtitle";
    
        $ret = $this->db->Execute($sql);
    
        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        
    } 


    public function deleteTitles($idtitle) {
        $sql = "DELETE FROM lmm_tbtitle WHERE idtitle=$idtitle";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }


    public function DeleteHasAuthor($idtitle) {
        $sql = "DELETE FROM `lmm_tbtitle_has_author`
                WHERE idtitle=$idtitle";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }


    public function DeleteExemplar($idtitle) {
        $sql = "DELETE FROM `lmm_tbbookcopy`
                WHERE idtitle=$idtitle";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        }

    
}

?>