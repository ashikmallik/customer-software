<?php

include '../model/oop.php';
include '../model/Bill.php';
include '../model/FormateHelper.php';
$obj = new Controller();
$bill = new Bill();
$formater = new FormateHelper();

//======= Object Created from Class ======
$custom_details = "";
$i = 0;
$cu_type = "CUSTOMER ID";
$type=isset($_GET['flag']) ? $_GET['flag']:"RECEIPT";
$agent_id = isset($_GET['token1']) ? $_GET['token1'] : "0";
$img = "foot.png";

$start = isset($_GET['start']) ? $_GET['start'] : 0;
$date = date('d.m.Y');
$month = date('F');
$prefix = date('Ym');
$html = "";


$i = 1;
$sum = 0;
$mainbody = "";
$diff = 0;
$tr = '';
$custom_details = '';
foreach ($obj->view_all_by_cond("tbl_agent", "ag_status='1' and ag_id='$agent_id'") as $detailsid) {
    $tr = '';
    $diff = $bill->get_customer_dues($detailsid['ag_id']) - $detailsid['taka'];
    $cat_id = $detailsid['bill_cat'];
    $cat = "";
    if ($cat_id == 1) {
        $cat = "Monthly";
    } else if ($cat_id == 2) {
        $cat = "Half Yearly";
    } else if ($cat_id == 3) {
        $cat = "Yearly";
    }
    if ($diff == 0) {
        $tr = $custom_details;
    } else if ($diff > 0) {
        $tr = '<tr>
					<td align="center">2</td>
					<td align="center">Previous due</td>
					<td align="center">--</td>
					<td align="center">--</td>
					<td align="center">' . number_format($diff, 2, ".", ",") . '</td>
				</tr>';
    } else if ($diff < 0) {
        $tr = '<tr>
					<td align="center">2</td>
					<td align="center">Advanced payment</td>
					<td align="center">--</td>
					<td align="center">--</td>
					<td align="center">' . number_format($diff * (-1), 2, ".", ",") . '</td>
				</tr>';
    }
    $tr = $tr . $custom_details;
	
	
		/*
        * Zone Search for Zone name
		*/
	
        if (isset($detailsid['zone']) && !empty($detailsid['zone'])) {
            $zoneData = $obj->details_by_cond('tbl_zone', "zone_id =" . $detailsid['zone'] . "");
        } else {
            $zoneData['zone_name'] = 'N/A';
        }
        /*
         *  Client ID from database agent id
         */


        $fifthDayOfMonth = date('d-m-y', strtotime('10.' . date('m') . '.' . date('Y') . ''));

        $STD = $detailsid['cus_id'];

    $mainbody .= '
               <div height = "40%" style="padding-top:1%">
                <div width="100%" class="text-center">
                   <h4>Client Copy</h4>
                </div>
               <div width = "94%" style="margin:3%;" class="rightAlign">
                    <div width = "100%" style="margin-top:7px" class="heading text-center">
                        <img style="height:500px; width:auto;" src="./img/header.png"/>
                    </div>
                
                    <div width="100%" class ="toUser leftAlign">                    
                        <p>To</p>
                        <p class="text-bold">Name : ' . $detailsid['ag_name'] . '
                        <p class="text-bold">Address : ' . $detailsid['ag_office_address'] . '
                        <p>Mobile : ' . $detailsid['ag_mobile_no'] . '</p>
                    </div>
                                    
                    <div width="100%" class="pan">
                        <table class="table infoTable table-bordered">
                            <th>
                                <tr style="border:1px solid #ddd;">
                                    <td class="col-md-4"><span class ="text-bold">ID :' . $STD . '</span></td>
                                    <td class="col-md-4"><span class ="text-bold">' . $type . ' No :</span>' . $prefix . $detailsid['ag_id'] . '</td>
                                    <td class="col-md-4"><span class ="text-bold">Issue Date :</span>' . $date . '</td>
                                    <td class="col-md-4"><span class ="text-bold">Due Date:</span>' . $fifthDayOfMonth . ' </td>
                                </tr>
                                <tr style="border:1px solid #ddd;">
                                     <td class="col-md-4"><span class ="text-bold">IP :</span>' . $detailsid['ip'] . '</td>
                                    <td class="col-md-4"><span class ="text-bold">Speed :</span>' .
            str_replace('_', '.', $detailsid['mb']) . '</td>
                                    <td class="col-md-4"><span class ="text-bold">Zone :</span>' . str_replace('_', ' ', $zoneData['zone_name']) . '</td>
                                </tr>
                            </th>
                        </table>
                    </div>
                    
                    <table width="100%" style="padding:3mm 0;" class="table-bordered items table">
                        <thead>
                            <tr class="bg-grey">
                                <td width="7%">Sl.</td>
                                <td width="43%">Description</td>
                                <td width="10%">Month</td>
                                <td width="15%">Speed</td>
                                <td width="25%">Bill Amount</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">1</td>
                                <td align="center">Bandwidth Charge - ' . $cat . ' </td>
                                <td align="center"> ' . $month . '</td>
                                <td align="center">' . $detailsid['mb'] . '</td>
                                <td align="center">' . number_format($detailsid['taka'], 2, ".", ",") . '</td>
                            </tr>
                            ' . $tr . '
                        </tbody>
                        <!-- END ITEMS HERE -->
                    </table>
                    <div width = "100%"  class="totalSection">
                        <div width="50%" class="leftAlign">
                        
                            <img class="pull-left termsCondition" src="./img/termsCondition.png"/>
    
                        </div>
                        
                        <div width = "50%" class="rightAlign">
                            <div class="rightAlign">
                                <div width = "78%" style="float:right;">
                                    <div class="total">
                                        Total : ' . number_format($bill->get_customer_dues($detailsid['ag_id']), 2, ".", ",") . ' BDT
                                    </div>
                                    <div class="inWord">
                                        In Word : 
    ' . $formater -> convert_number_to_words($bill->get_customer_dues($detailsid['ag_id'])) . ' Taka.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div width="100%" class="text-center">
                            <div width="90% rightAlign">
                                <span style="">Signature................................. </span>
                            </div>
                        </div>
                        <div width="100%" class = "text-center">
                            <hr style = "margin:3px;">
                            <p class="text-muted text-bold" style="font-size: 7pt;">Powered By Bangladesh Software Development (BSD)</p>
                        </div>
                    </div>
                </div>
                <div width="100%">
                <img src="./img/divider.png"/>
                </div>
            </div>
            <div height = "40%" style="padding-top:0%">
                <div width="100%" class="text-center">
                   <h4>Office Copy</h4>
                </div>
               <div width = "94%" style="margin:3%;" class="rightAlign">
                    <div width = "100%" style="margin-top:10px" class="heading text-center">
                        <img style="height:500px; width:auto;" src="./img/header.png"/>
                    </div>
                
                    <div width="100%" class ="toUser leftAlign">                    
                        <p>To</p>
                        <p class="text-bold">' . $detailsid['ag_name'] . '</p> ' . $detailsid['ag_office_address'] . '
                        <p>Mob-' . $detailsid['ag_mobile_no'] . '</p>
                    </div>
                                    
                    <div width="100%" class="pan">
                        <table class="table infoTable table-bordered">
                            <th>
                                <tr style="border:1px solid #ddd;">
                                    <td class="col-md-4"><span class ="text-bold">ID :' . $STD . '</span></td>
                                     <td class="col-md-4"><span class ="text-bold">' . $type . ' No :</span>' . $prefix . $detailsid['ag_id'] . '</td>
                                    <td class="col-md-4"><span class ="text-bold">Issue Date :</span>' . $date . '</td>
                                    <td class="col-md-4"><span class ="text-bold">Due Date:</span>' . $fifthDayOfMonth . ' </td>
                                </tr>
                                <tr style="border:1px solid #ddd;">
                                     <td class="col-md-4"><span class ="text-bold">IP :</span>' . $detailsid['ip'] . '</td>
                                    <td class="col-md-4"><span class ="text-bold">Speed :</span>' .
            str_replace('_', '.', $detailsid['mb']) . '</td>
                                    <td class="col-md-4"><span class ="text-bold">Zone :</span>' . str_replace('_', ' ', $zoneData['zone_name']) . '</td>
                                </tr>
                            </th>
                        </table>
                    </div>
                    
                    <table width="100%" style="padding:3mm 0;" class="table-bordered items table">
                        <thead>
                            <tr class="bg-grey">
                                <td width="7%">Sl.</td>
                                <td width="43%">Description</td>
                                <td width="10%">Month</td>
                                <td width="15%">Speed</td>
                                <td width="25%">Bill Amount</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">1</td>
                                <td align="center">Bandwidth Charge - ' . $cat . ' </td>
                                <td align="center"> ' . $month . '</td>
                                <td align="center">' . $detailsid['mb'] . '</td>
                                <td align="center">' . number_format($detailsid['taka'], 2, ".", ",") . '</td>
                            </tr>
                            ' . $tr . '
                        </tbody>
                        <!-- END ITEMS HERE -->
                    </table>
                    <div width = "100%"  class="totalSection">
                        <div width="50%" class="leftAlign">
                        
                            <img class="pull-left termsCondition" src="./img/termsCondition.png"/>
    
                        </div>
                        
                        <div width = "50%" class="rightAlign">
                            <div class="rightAlign">
                                <div width = "78%" style="float:right;">
                                    <div class="total">
                                        Total : ' . number_format($bill->get_customer_dues($detailsid['ag_id']), 2, ".", ",") . ' BDT
                                    </div>
                                    <div class="inWord">
                                        In Word : 
    ' . $formater -> convert_number_to_words($bill->get_customer_dues($detailsid['ag_id'])) . ' Taka.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div width="100%" class="text-center">
                            <div width="90% rightAlign">
                                 <span style="">Signature................................. </span>
                            </div>
                        </div>
                        <div width="100%" class = "text-center">
                            <hr style = "margin:3px;">
                            <p class="text-muted text-bold" style="font-size: 7pt;">Powered By Bangladesh Software Development (BSD)</p>
                        </div>
                    </div>
                </div>
            </div>
                ';
}

$html =
        '<html>
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- External CSS -->
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/style.css">
        <style>
            td{
                height:27px;
            }
        </style>
        </head>
        <body>
            <div style="height:297mm; width: 210mm; margin: 4mm;">
                ' . $mainbody . '
            </div><!-- Contain -->
            
        </body>
    </html>';
//==============================================================
//==============================================================
//==============================================================
//==============================================================
//==============================================================
//==============================================================

define('_MPDF_PATH', './');
include("./mpdf.php");

$mpdf = new mPDF('c', 'A4', '', '', 0, 0, 0, 0, 0, 0);
$mpdf->SetProtection(array('print'));
$mpdf->SetTitle($type . " | Developed By BSD");
$mpdf->SetAuthor("BSD.");
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($html);


$mpdf->Output();
exit;

exit;
?>
