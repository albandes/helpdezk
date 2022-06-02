<?php

require_once('../../includes/classes/fpdf/' . 'fpdf.php');

$LineCabec = array("Login", "Name", "Type Person"); 

class PDF extends FPDF
        { 
        function Header()
        {
            global $titulo;
            //$this->Image('logo_entrada.gif',10,8,30);
            $this->Ln(2);
            $this->SetFont('Arial','B',10);
            //Move to the right
            //$this->Cell(30);
            //Title
            $this->Cell(0,5,$titulo,0,0,'C');
            //Line break
            $this->Ln(8);
            $this->cabec();
        }
        function Footer()
        {
            //Position at 1.5 cm from bottom
            $this->SetY(-15);
            $this->SetFont('Arial','I',6);
            //Page number
            $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
        }


        function cabec()
        {
            global $line;
            $this->SetFont('Arial','',8);
            $this->SetFillColor(91,207,170);
            $this->Cell(30,4,$LineCabec[0],0,0,'L',1);
            $this->Cell(80,4,$LineCabec[1],0,0,'L',1);
            $this->Cell(50,4,$LineCabec[2],0,0,'L',1);
            $this->Ln(5);
        }

}


        $titulo = "Person Report" ;

        $var = "Login\tName\tEmail\ndemonstration\tDemonstration Corp.\tdemonstration@gmail.com\npipeadm\tPipegrep\tpipegrep@pipegrep.com\n\t\t"; 
        $line = parse_ajax(explode("\n", $var));


        $pdf= new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();


        for ($i = 1; $i <= count($line); $i++)	
        {
        if (isset($line[$i])) {
                $pdf->Cell(30,4,$line[$i][0],0,0);
        $pdf->Cell(80,4,$line[$i][1],0,0);		
                $pdf->Cell(50,4,$line[$i][2],0,0);		
        }	
        $pdf->Ln(4);

        }

        $pdf->Output();


function parse_ajax($arr){
        $i = 0;
        $line = array();
        foreach ($arr as &$value) {
                $line[$i] = explode("\t", $value);
                $i++;

        }
        return $line;
}  


?>
