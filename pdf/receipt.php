<?php
include '../model/oop.php';
include '../model/Bill.php';
include '../model/FormateHelper.php';
$obj = new Controller();
$bill = new Bill();
$formater = new FormateHelper();
//======= Object Created from Class ======
$i = 0;
$type = "Receipt";
$token = isset($_GET['token']) ? $_GET['token'] : NULL;

$html = "";
$account = $obj->details_by_cond('tbl_account', "company_id='$token'");
$amount = $account['acc_amount'];
$acc_description = $account['acc_description'];
$entry_date = date("d.m.Y", strtotime($account['entry_date']));
$date = date('d.m.Y');
$month = date('F', strtotime($account['entry_date']));
$prefix = date('Ym', strtotime($account['entry_date']));
$id = $account['company_id'];
$detailsid = $obj->details_by_cond('vw_account', "company_id='$token'");


$i = 1;
$sum = 0;
$mainbody = "";
$diff = 0;

$mainbody .= '
<div height = "49%" style="padding-top:1%">
                <div width="100%" class="text-center">
                   <h4>Client Copy</h4>
                </div>
               <div width = "94%" style="margin:3%;" class="rightAlign">
                    <div width = "100%" style="margin-top:7px" class="heading text-center">
                        <img style="height:500px; width:auto;" src="./img/header.png"/>
                    </div>
                
                    <div width="100%" class ="toUser">
                        <div width="50%" class="leftAlign">
                            <p>To</p>
                            <p class="text-bold">Name : ' . $detailsid['company_name'] . '</p>
                            <p class="text-bold">Address : ' . $detailsid['link_name'] . '</p>
                            <p>Mobile : ' . $detailsid['phone'] . '</p>
                        </div>
                        
                        <div width="50%" class="leftAlign">
                            <h2 style="border:2px solid #000; text-align:center">Receipt</h2>
                        </div>
                    </div>
                                    
                    <div width="100%" class="pan">
                        <table class="table infoTable table-bordered">
                            <th>
                                <tr style="border:1px solid #ddd;">
                                    <td class="col-md-4"><span class ="text-bold">ID : ' . $detailsid['cus_id'] . '</span></td>
                                    <td class="col-md-4"><span class ="text-bold">' . $type . ' No :</span>' . $prefix . $token . '</td>
                                    <td class="col-md-4"><span class ="text-bold">Date : </span>' . $date . '</td>
                                    <td class="col-md-4"><span class ="text-bold">Issue Date : </span>' . $entry_date . ' </td>
                                </tr>
                                <tr style="border:1px solid #ddd;">
                                  
                                    
                                    <td class="col-md-4"><span class ="text-bold">Bill Amount : </span>' . number_format($detailsid['taka'], 2, ".", ",") . 'BDT </td>
                                    <td class="col-md-4"><span class ="text-bold">Due Amount : </span>' . number_format($bill->get_customer_dues($detailsid['ag_id'])) . ' BDT</td>
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
                                <td width="25%">Amount</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">1</td>
                                <td align="center">' . $acc_description . ' </td>
                                <td align="center"> ' . $month . '</td>
                               
                                <td align="center">' . number_format($amount, 2, ".", ",") . '</td>
                            </tr>
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
                                        Total : ' . number_format($amount, 2, ".", ",") . ' BDT
                                    </div>
                                    <div class="inWord">
                                        In Word : 
    ' . $formater->convert_number_to_words($amount) . ' Taka.
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
            
            <div height = "49%" style="padding-top:1%">
                <div width="100%" class="text-center">
                   <h4>Office Copy</h4>
                </div>
               <div width = "94%" style="margin:3%;" class="rightAlign">
                    <div width = "100%" style="margin-top:7px" class="heading text-center">
                        <img style="height:500px; width:auto;" src="./img/header.png"/>
                    </div>
                
                    <div width="100%" class ="toUser">
                        <div width="50%" class="leftAlign">
                            <p>To</p>
                            <p class="text-bold">Name : ' . $detailsid['company_name'] . '</p>
                            <p class="text-bold">Address : ' . $detailsid['link_address'] . '</p>
                            <p>Mobile : ' . $detailsid['phone'] . '</p>
                        </div>
                        
                        <div width="50%" class="leftAlign">
                            <h2 style="border:2px solid #000; text-align:center">Receipt</h2>
                        </div>
                    </div>
                                    
                    <div width="100%" class="pan">
                        <table class="table infoTable table-bordered">
                            <th>
                                <tr style="border:1px solid #ddd;">
                                    <td class="col-md-4"><span class ="text-bold">ID : ' . $detailsid['cus_id'] . '</span></td>
                                    <td class="col-md-4"><span class ="text-bold">' . $type . ' No :</span>' . $prefix . $token . '</td>
                                    <td class="col-md-4"><span class ="text-bold">Date : </span>' . $date . '</td>
                                    <td class="col-md-4"><span class ="text-bold">Issue Date: </span>' . $entry_date . ' </td>
                                </tr>
                                <tr style="border:1px solid #ddd;">
                                   
                

                                    <td class="col-md-4"><span class ="text-bold">Bill Amount : </span>' . number_format($detailsid['taka'], 2, ".", ",") . 'BDT </td>
                                    <td class="col-md-4"><span class ="text-bold">Due Amount : </span>' . number_format($bill->get_customer_dues($detailsid['ag_id'])) . ' BDT</td>
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
                                <td width="25%">Amount</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">1</td>
                                <td align="center">' . $acc_description . ' </td>
                                <td align="center"> ' . $month . '</td>
                                <td align="center">' . $detailsid['mb'] . '</td>
                                <td align="center">' . number_format($amount, 2, ".", ",") . '</td>
                            </tr>
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
                                        Total : ' . number_format($amount, 2, ".", ",") . ' BDT
                                    </div>
                                    <div class="inWord">
                                        In Word : 
    ' . $formater->convert_number_to_words($amount) . ' Taka.
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
            </div>';


$html .= '
    <html>
        <head>
            <!-- Required meta tags -->
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

            <!-- External CSS -->
            <link rel="stylesheet" href="css/bootstrap.css">
            <link rel="stylesheet" href="css/style.css">
            <style>
                td{
                    height:30px;
                }
            </style>
        </head>
        <body>
            <div style="height:297mm; width: 210mm; margin: 4mm;">
                ' . $mainbody . '
            </div><!-- Contain -->
            
        </body>
    </html>';

define('_MPDF_PATH', './');
include("./mpdf.php");

$mpdf = new mPDF('c', 'A4', '', '', 0, 0, 0, 0, 0, 0);
$mpdf->SetProtection(array('print'));
$mpdf->SetTitle($type . " | Developed By BSD");
$mpdf->SetAuthor("MD Furkanul Islam.");
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($html);


$mpdf->Output();
exit;

exit;

?>