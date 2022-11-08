<?php

#### Roshan's very simple code to export data to excel   
#### Copyright reserved to Roshan Bhattarai - nepaliboy007@yahoo.com
#### if you find any problem contact me at http://roshanbh.com.np
#### fell free to visit my blog http://php-ajax-guru.blogspot.com

class ExportExcel
{
	//variable of the class
	var $titles=array();
	var $all_values=array();
	var $filename;
	var $head;
	var $data;
	
	//functions of the class
	function ExportExcel($f_name) //constructor
	{
		$this->filename=$f_name;
	}
	function setHeadersAndValues($hdrs,$all_vals) //set headers and query
	{
		$this->titles=$hdrs;
		$this->all_values=$all_vals;
	}
	function GenerateExcelFile() //function to generate excel file
	{
		foreach ($this->titles as $title_val) 
 		{ 
 			$this->head .= $title_val."\t"; 
 		} 
 		for($i=0;$i<sizeof($this->all_values);$i++) 
 		{ 
 			$line = ''; 
 			foreach($this->all_values[$i] as $value) 
			{ 
 				if ((!isset($value)) OR ($value == "")) 
				{ 
 					$value = "\t"; 
 				} //end of if
				else 
				{ 
 					$value = str_replace('"', '""', $value); 
 					$value = '"' . $value . '"' . "\t"; 
 				} //end of else
 				$line .= $value; 
 			} //end of foreach
 			$this->data .= trim($line)."\n"; 
 		}//end of the while 
 		$this->data = str_replace("\r", "", $this->data); 
		if ($this->data == "") 
 		{ 
 			$this->data = "\n(0) Records Found!\n"; 
 		} 
		//echo $data;
		header("Content-type: application/vnd.ms-excel"); 
		header("Content-Disposition: attachment; filename=$this->filename"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); 
		print "$this->head\n$this->data";  
	}
}
?>