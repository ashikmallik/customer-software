<?php
include '../model/oop.php';
require '../lib/fpdf.php';
$obj = new Controller();
include '../model/Bill.php';
$bill = new Bill();
include '../model/FormateHelper.php';
$formater = new FormateHelper();
//======= Object Created from Class ======
$date ="Date: ".date('Y-m-d');
$userName =isset($_GET['key']) ? $_GET['key']:NULL;
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid =isset($_SESSION['UserId']) ? $_SESSION['UserId']:NULL;
$day=date('d');

// Instanciation of inherited class
$i=0;
$pdf = new FPDF('P','mm','A4');

$pdf->SetTitle("All invoice pdf");
$pdf->SetAuthor('BSD');
$header=array("Date","Monthly Bill","Paid","Due","Received by");

foreach ($obj->view_all_by_cond("tbl_agent","ag_status='1' and pay_status='1'") as $value){
$i++;
$pdf->AddPage();
$pdf->SetFont('Times','',12);
$pdf->Cell(180,10,$date,10,5,"R");
$pdf->Cell(180,0,"Serial No.# ".$i,10,5,"R");
$pdf->Image('logo.jpg',75,60,50,"C");
$pdf->Ln(80);
$pdf->SetFont('Times','B',16);
$pdf->Cell(180,10,"ISP SOFTWARE COMPANY",10,5,"C");
$pdf->Ln(5);
$pdf->SetFont('Times','',12);
$pdf->Cell(180,2,"Managing Director: zzzz",10,5,"C");
$pdf->Ln(5);
$pdf->Cell(180,2,"Cell: 00000-000000",10,5,"C");
$pdf->Ln(5);
$pdf->Cell(180,2,"Email: support@isb.com",10,5,"C");
$pdf->Ln(20);
$pdf->Cell(45,2,"ID#:".$value['cus_id'],10,5,"L");//	cus_id
$pdf->Ln(5);
$pdf->Cell(45,2,"Name: ".$value['ag_name'],10,5,"L");
$pdf->Ln(5);
$pdf->Cell(45,2,"Address: ".$value['ag_office_address'],10,5,"L");
$pdf->Ln(5);
$pdf->Cell(45,2,"Mobile: ".$value['ag_mobile_no'],10,5,"L");
$pdf->Ln(5);
$pdf->Cell(45,2,"Email: ".$value['ag_email'],10,5,"L");



$pdf->Ln(5);
    $w = array(25,40, 35, 40, 45);
    // Header

    $pdf->Cell($w[0],7,$header[0],1,0,'L');
    $pdf->Cell($w[1],7,$header[1],1,0,'C');
    $pdf->Cell($w[2],7,$header[2],1,0,'C');
    $pdf->Cell($w[3],7,$header[3],1,0,'L');
    $pdf->Cell($w[4],7,$header[4],1,0,'L');
    $pdf->Ln();
    // Data


	$pdf->Cell($w[0],6,$value['entry_date'],'LR');
	//due calculation 	ag_id
	$token=$value['ag_id'];
	$details = $obj->details_by_cond("tbl_agent","ag_id='$token'");
	extract($details);

	$bday1=$details['entry_date'];
	$bday = new DateTime($bday1);
	$today = new DateTime(date('Y-m-d', time())); // for testing purposes
	$diff = $today->diff($bday);
	$total=0;
	$serviceamount1=0;


         foreach ($obj->view_all_by_cond("tbl_agent","ag_id='$token'") as $details1){
            extract($details1);
            $serviceamount1+=($details1['taka']);

            }
            if($diff->m!=0){
            $serviceamount2=($serviceamount1*$diff->y*12)+($serviceamount1*$diff->m);
            }
            else{
               $serviceamount2=$serviceamount1;
            }
            foreach ($obj->view_all_by_cond("vw_account","agent_id='$token' order by acc_id") as $customer_info){
            extract($customer_info);
            $total+=$customer_info['acc_amount'];
            }
			$bonus=0;
			foreach ($obj->view_all_by_cond("bonus","customerID='$token' ") as $customer_bonus){
            extract($customer_bonus);
            $bonus+=$customer_bonus['amount'];
            }
       $dueamount=$serviceamount2-($total+$bonus);

	//end due calculation

	$tmp_data=$value['taka']-$dueamount;
	$due=0;

	$pdf->Cell($w[1],6,number_format($value['taka']),'LR');
	$pdf->Cell($w[2],6,number_format($tmp_data),'LR');
	$pdf->Cell($w[3],6,number_format($dueamount),'LR');
	$pdf->Cell($w[4],6,$userName,'LR');
	$pdf->Ln();

    // Closing line

    $pdf->Cell(array_sum($w),0,'','T');
	$pdf->Ln(5);
	$pdf->Cell(110,2,"Total Dues: ".number_format($dueamount),10,5,"R");
	$pdf->Ln(20);
	$pdf->Cell(180,2,"-----------------------------------",10,5,"R");
	$pdf->Ln(5);
	$pdf->Cell(175,2,"Authorized Signature",10,5,"R");
}
$pdf->Output();


?>