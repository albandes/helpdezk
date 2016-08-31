<?php
class BankReturn {

    public function FileNumberOfLines($fileName){
        $arq = $this->OpenFile($fileName) ;
        if(!$arq) {
            return "Could not open the file $fileName" ;
        }
        $line=fgets($arq, 500);
        $len = strlen($line);

        if($len >= 240 and $len <= 242) {
            return "240";
        } else if ($len >= 400 and $len <= 402) {
            return "400";
        } else {
            return "Wrong file length" ;
        }

    }
	public function TestFile($codBank,$typeFile,$fileName)
	{	
		$arq = $this->OpenFile($fileName) ;	
		if(!$arq) {
			return "Could not open the file $fileName" ;
		}
		if ($typeFile == "CNAB240") {
			$line=fgets($arq, 500);
            $len = strlen($line);
			
			if($len >= 240 and $len <= 242) {
				if (substr($line,0,3) == $codBank) {
					return true ;		
				} else {
					return "Error Bank Code ";
				}
			} else {
				return "Wrong file length" ;
			}
		}
		
	}
	
	function OpenFile($fileName)
	{
		if($arq = fopen($fileName, "r")) {
			return $arq;
		} else {
			return false ;
		}	
	}

}


?>
