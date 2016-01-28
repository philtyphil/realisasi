<?php

require_once('tcpdf/tcpdf.php');


class Outpdf{
    var $pdf;
    var $html='';
    
	
	function out($html,$gen=FALSE,$name,$type=null){
		  
      	   $this->html = $html;
      	   
      	   $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);     	
					 $this->pdf->SetAuthor('Sulistyo Nur Anggoro');
					 $this->pdf->setBarcode('BGR - @philtyphils');
					 $this->pdf->SetTitle('Print Report');
					 //$this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, '');
					 $this->pdf->SetSubject('Report');
					 $this->pdf->SetKeywords('Report');
					 $this->pdf->setPrintHeader(false);

					 $this->pdf->SetFontSubsetting(false);				

					 $this->pdf->SetFont('times', '', 12);
					 
					$this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
					$this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
					
					// set default monospaced font
					$this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
					
					//set margins
					$this->pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
					$this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
					$this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
					
					//set auto page breaks
					$this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
					
					//set image scale factor
					$this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);					 
					 
					//$this->pdf->AddPage(); 
					
					if($type !== null){
						 $this->pdf->AddPage($type, 'A4');
					}else{
						 $this->pdf->AddPage(); 
					}

					$this->pdf->writeHTML($this->html, true, 0, true, 0);
					$this->pdf->lastPage();
					if($gen === TRUE){
					   $this->pdf->Output($name, 'F');
					   system("chmod 777 -R ".$name);	
					}else{
					 	 $this->pdf->Output('', 'I');	
					}
					   					      	   
      }
      
} 
?>