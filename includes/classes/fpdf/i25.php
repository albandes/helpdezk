<?php
//require('fpdf.php');
//require('includes/classes/fpdf/fpdf.php');
//die('aqui');
//require('C:\xampp\htdocs\trunk\includes\classes\fpdf\fpdf.php');
// based on the Code 39 script from The-eh
class PDF_i25 extends FPDF
{
function i25($xpos, $ypos, $code, $basewidth=1, $height=10){

	$wide = $basewidth;
	$narrow = $basewidth / 3 ;

	// wide/narrow codes for the digits
	$barChar['0'] = 'nnwwn';
	$barChar['1'] = 'wnnnw';
	$barChar['2'] = 'nwnnw';
	$barChar['3'] = 'wwnnn';
	$barChar['4'] = 'nnwnw';
	$barChar['5'] = 'wnwnn';
	$barChar['6'] = 'nwwnn';
	$barChar['7'] = 'nnnww';
	$barChar['8'] = 'wnnwn';
	$barChar['9'] = 'nwnwn';
	$barChar['A'] = 'nn';
	$barChar['Z'] = 'wn';

	// add leading zero if code-length is odd
	if(strlen($code) % 2 != 0){
		$code = '0' . $code;
	}

	$this->SetFont('Arial','',10);
	//$this->Text($xpos, $ypos + $height + 4, $code);
	$this->SetFillColor(0);

	// add start and stop codes
	$code = 'AA'.strtolower($code).'ZA';

	for($i=0; $i<strlen($code); $i=$i+2){
		// choose next pair of digits
		$charBar = $code{$i};
		$charSpace = $code{$i+1};
		// check whether it is a valid digit
		if(!isset($barChar[$charBar])){
			$this->Error('Invalid character in barcode: '.$charBar);
		}
		if(!isset($barChar[$charSpace])){
			$this->Error('Invalid character in barcode: '.$charSpace);
		}
		// create a wide/narrow-sequence (first digit=bars, second digit=spaces)
		$seq = '';
		for($s=0; $s<strlen($barChar[$charBar]); $s++){
			$seq .= $barChar[$charBar]{$s} . $barChar[$charSpace]{$s};
		}
		for($bar=0; $bar<strlen($seq); $bar++){
			// set lineWidth depending on value
			if($seq{$bar} == 'n'){
				$lineWidth = $narrow;
			}else{
				$lineWidth = $wide;
			}
			// draw every second value, because the second digit of the pair is represented by the spaces
			if($bar % 2 == 0){
				$this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
			}
			$xpos += $lineWidth;
		}
	}
}



	function DashLine($x1,$y1,$x2,$width=1,$nb=15)
	{
		$this->SetLineWidth($width);
		$longueur=abs($x1-$x2);
		//$hauteur=abs($y1-$y2);
		
		$Pointilles=($longueur/$nb)/4; // length of dashes
	
		for($i=$x1;$i<=$x2;$i+=$Pointilles+$Pointilles) {
			for($j=$i;$j<=($i+$Pointilles);$j++) {
				if($j<=($x2-1)) {
					$this->Line($j,$y1,$j+1,$y1); // upper dashes
					//$this->Line($j,$y2,$j+1,$y2); // lower dashes
				}
			}
		}
//		for($i=$y1;$i<=$y2;$i+=$Pointilles+$Pointilles) {
//			for($j=$i;$j<=($i+$Pointilles);$j++) {
//				if($j<=($y2-1)) {
//					$this->Line($x1,$j,$x1,$j+1); // left dashes
//					$this->Line($x2,$j,$x2,$j+1); // right dashes
//				}
//			}
//		}
	}
}



?>
