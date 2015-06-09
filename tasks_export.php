<?php
/* -------------------------------------------------------------------------- *\
|* -[ Promotions - Export ]-------------------------------------------------- *|
\* -------------------------------------------------------------------------- */
require_once("../core/api.inc.php");
api_loadModule();
// include the TCPDF library
require_once('../core/tcpdf/tcpdf.php');
// extend the TCPDF class to create custom header and footer
class MYPDF extends TCPDF {
 // header
 public function Header(){
  $company=api_company();  // <<<----- eventualmente per il multi società sostituire con società attiva
  $name=stripslashes($company->fiscal_name);
  $address=stripslashes($company->address_address)." - ".stripslashes($company->address_zip)." ".stripslashes($company->address_city)." (".stripslashes($company->address_district).") ".stripslashes($company->address_country);
  if($company->phone_office){$contacts="Tel: ".stripslashes($company->phone_office);}
  if($company->phone_fax){$contacts.=" - Fax: ".stripslashes($company->phone_fax);}
  $fiscalData="P.IVA: ".stripslashes($company->fiscal_vat);
  if($company->fiscal_code){$fiscalData.=" - C.F: ".stripslashes($company->fiscal_code);}
  if($company->fiscal_rea){$fiscalData.=" - R.E.A: ".stripslashes($company->fiscal_rea);}
  // logo
  if(file_exists("../uploads/uploads/core/logo.png")){
   $logo_size=getimagesize("../uploads/uploads/core/logo.png");
   $logo_x=$logo_size[0];
   $logo_y=$logo_size[1];
   $x_padding=round($logo_x*12/$logo_y)+13;
   $this->Image("../uploads/uploads/core/logo.png",10,10,'',12,'PNG',api_getOption('owner_url'),'T',false,300,'',false,false,0,false,false,false);
  }else{
   $x_padding=10;
  }
  // build header
  $this->SetFont('freesans','B',15);
  $this->MultiCell(0,0,$name,0,'L',false,1,$x_padding,9);
  $this->SetFont('freesans','',9);
  $this->MultiCell(0,0,$address." - ".$contacts,0,'L',false,1,$x_padding,15);
  $this->SetFont('freesans','',7);
  $this->MultiCell(0,0,$fiscalData,0,'L',false,1,$x_padding,19);
 }
 // footer
 public function Footer(){
  $this->setY(-12);
  $this->SetFont('freesans','',6,'',true);
  $this->Cell(0,3,mb_strtoupper(api_text("tasks_export-page"),'UTF-8')." ".$this->getAliasNumPage()." ".mb_strtoupper(api_text("tasks_export-pageOf"),'UTF-8')." ".$this->getAliasNbPages(),0,0,'L',0);
  $this->Cell(0,3,mb_strtoupper(api_text("tasks_export-timestamp"),'UTF-8')." ".api_timestampFormat(api_now(),api_text("datetime")),0,0,'R',0);
 }
}
// create new pdf document
$pdf=new MYPDF('P','mm','A4',true,'UTF-8',false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(api_account()->name);
$pdf->SetTitle("Tasks - Coordinator.it");
$pdf->SetSubject("Tasks - Coordinator.it");
// header and footer
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);
// set margins
$pdf->SetMargins(10,27,10);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);
// fill
$fill=false;
$pdf->SetFillColor(245);
// border
$border='';
// set auto page breaks
$pdf->SetAutoPageBreak(true,15);
// set font
$pdf->SetFont('freesans','',12,'',true);
// definitions
$current_idAccount=0;
// check for export tasks stored
if(!is_array($_SESSION['tasks']['export'])){api_die("taskNotFound");}
// cycle tasks
foreach($_SESSION['tasks']['export'] as $task){
 // invert fill
 if($fill){$fill=false;}else{$fill=true;}
 // add page if not first
 if($current_idAccount<>$task->idAccount){
  $current_idAccount=$task->idAccount;
  $fill=true;
  $pdf->AddPage();
  $pdf->SetFont('freesans','B',12,'',true);
  $pdf->Cell('',6,mb_strtoupper(api_account($task->idAccount)->name,"UTF-8"),'B',1,'L');
  $pdf->Ln(2);
 }
 // status
 switch($task->status){
  case 2:$status="₪";break;
  case 3:$status="√";break;
  default:$status="";
 }
 $pdf->SetFont('freesans','',10,'',true);
 $pdf->Cell(6,5,$status,$border,0,'C',$fill,null,1);
 $pdf->Cell(154,5,$task->subject,$border,0,'L',$fill,null,1);
 $pdf->Cell('',5,api_timestampFormat($task->updDate,api_text("datetime")),$border,0,'R',$fill,null,1);
 $pdf->ln();
}
// close and output pdf document
if(strlen($file_path)>0){
 $pdf->Output($file_path,'F');
}else{
 $pdf->Output(date("YmdHis")."-tasks.pdf",'I');
}
?>