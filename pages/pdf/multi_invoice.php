<?php
require_once __DIR__ . '/vendor/autoload.php';
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST']; // e.g. skynetwork.bsdisp.com
$baseUrl = $protocol . $host; // full base URL

$url = $baseUrl . '/';
use Mpdf\Mpdf;

@set_time_limit(0);
@ini_set('memory_limit', '1024M');

try {
    require(realpath(__DIR__ . '/../../services/Model.php'));
    $obj = new Model();

    $data = $_GET['data'] ?? '';
    if (!$data) die('No data');

    $tokens = json_decode(base64_decode(urldecode($data)), true);
    if (!is_array($tokens)) die('Invalid');

    $tokens = array_values(array_filter(array_map('intval', $tokens)));
    
    

    $mpdf = new Mpdf([
        'format' => 'A4',
        'default_font' => 'nikosh',
        'margin_left' => 6,
        'margin_right' => 6,
        'margin_top' => 5,
        'margin_bottom' => 5,        // এটা কম রাখা হয়েছে
        'margin_header' => 0,
        'margin_footer' => 0,
    ]);

    $mpdf->SetAutoPageBreak(false);

    $month = date('F');
    $count = 0;
    $logo = $obj->getSettingValue('logo', 'logo');
		
	$CompanyName = $obj->getSettingValue('excel', 'name');
	$address = $obj->getSettingValue('address', 'address');
	$mobile = $obj->getSettingValue('mobile', 'mobile');
	$email = $obj->getSettingValue('email', 'email');

    foreach ($tokens as $token) {
        $agentDetail = $obj->view_all_by_cond('tbl_agent', "ag_id=$token");
        if (empty($agentDetail)) continue;

        foreach ($agentDetail as $agentDetails) {
            $count++;

            if ($count % 3 == 1 && $count > 1) {
                $mpdf->AddPage();
            }

            $ag_id = $agentDetails['ag_id'];
            $invoiceNo = date('Ym') . str_pad($ag_id, 4, '0', STR_PAD_LEFT);
            $rowDue = $obj->rawSqlSingle("SELECT dueadvance FROM customer_billing WHERE agid='$ag_id'");
            $totalDue = intval($rowDue["dueadvance"] ?? 0);
            $agTaka = intval($agentDetails["taka"] ?? 0);
            $billAmount = ($totalDue < $agTaka) ? max(0, $totalDue) : $agTaka;
            $vat = round($billAmount * 0.05, 2);           // 5% VAT
            $grandTotal = $billAmount + $vat;

            $extraRow = '';
            if ($totalDue > $agTaka) {
                $prevBill = $totalDue - $agTaka;
                $prevVat = round($prevBill * 0.05, 2);
                $extraRow = '
                <tr>
                    <td align="center">2</td>
                    <td>Previous Due</td>
                    <td align="center">'.$month.'</td>
                    <td align="right">'.number_format($prevBill,2).'</td>
                    <td align="right">'.number_format($prevVat,2).'</td>
                    <td align="right">'.number_format($prevBill + $prevVat,2).'</td>
                </tr>';
            }

                                    $html = '
                                            <div style="">
                                            
                                                <!-- ==================== অ্যাডভান্সড হেডার ==================== -->
                                                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-bottom: 12px;">
                                                    <tr>
                                                        <!-- Left: Logo -->
                                                        <td width="28%" style="text-align: left; vertical-align: middle; padding-right: 12px;">
                                                            <img src="' . $url . $logo . '" style="max-height: 23mm; width: auto; max-width: 100%; display:block;">
                                                        </td>
                                                       
                                                        <!-- Center: Company Name + Address -->
                                                        <td width="44%" style="text-align: center; vertical-align: middle;">
                                                            <div style="font-size: 17pt; font-weight: bold; color: #1e6b1e; line-height: 1.1;">
                                                                ' . htmlspecialchars($CompanyName) . '
                                                            </div>
                                                            <div style="font-size: 9.5pt; color: #555; margin: 5px 0 7px 0;">
                                                                ' . (isset($address) && $address != '' ? htmlspecialchars($address) : '') . '
                                                            </div>
                                                        </td>
                                                       
                                                        <!-- Right: Contact Details -->
                                                        <td width="28%" style="text-align: right; vertical-align: middle; font-size: 9.5pt; line-height: 1.55;">
                                                            ' . (isset($mobile) && $mobile != '' ? 
                                                                '<span style="color: #c19100; font-weight: bold;">Hotline: ' . htmlspecialchars($mobile) . '</span><br>' : '') . '
                                                            
                                                            ' . (isset($email) && $email != '' ? 
                                                                '<span style="color: #333;">' . htmlspecialchars($email) . '</span>' : '') . '
                                                        </td>
                                                    </tr>
                                                </table>
                                            
                                                <!-- Horizontal Line with better spacing -->
                                                <hr style="border: none; border-top: 2px solid #4CAF50; margin: 10px 0 14px 0;">
                                            
                                                <!-- To + Info Table -->
                                                <table width="100%" style="font-size:12.5px; line-height:1.35;">
                                                    <tr>
                                                        <td style="width:100%;">
                                                            To <br>
                                                            <b>Name:</b> '.$agentDetails["ag_name"].'<br>
                                                            <b>Mobile:</b> '.$agentDetails["ag_mobile_no"].'<br>
                                                            <b>Address:</b> '.$agentDetails["ag_office_address"].'
                                                        </td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td width="100%" align="right" valign="top" style="padding-top:8px;">
                                                            <table width="100%" style="border:1px solid #ddd; background:#f9f9f9;">
                                                                <tr>
                                                                    <td style="padding:6px 8px; border-right:1px solid #ddd;"><b>IP :</b> '.($agentDetails["ip"] ?? $agentDetails["ip_address"] ?? 'N/A').'</td>
                                                                    <td style="padding:6px 8px; border-right:1px solid #ddd;"><b>Total Due :</b> '.number_format($billAmount,2).' BDT</td>
                                                                    <td style="padding:6px 8px; border-right:1px solid #ddd;"><b>INVOICE No :</b> '.$invoiceNo.'</td>
                                                                    <td style="padding:6px 8px;"><b>Date :</b> '.date("d.m.Y").'</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            
                                                <!-- Bill Table -->
                                                <table width="100%" border="1" cellspacing="0" cellpadding="5" style="border-collapse:collapse; font-size:12.5px; margin-top:12px;">
                                                    <tr style="background:#f0f0f0; font-weight:bold;">
                                                        <th width="5%">SL.</th>
                                                        <th width="45%">Description</th>
                                                        <th width="20%">Month</th>
                                                        <th width="30%">Amount</th>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">1</td>
                                                        <td>Bandwidth Charge - Monthly</td>
                                                        <td align="center">'.$month.'</td>
                                                        <td align="right">'.number_format($billAmount,2).'</td>
                                                    </tr>
                                                    '.$extraRow.'
                                                </table>
                                            
                                                <div style="text-align:right; margin-top:12px; font-weight:bold; font-size:13.5px;">
                                                    Total: '.number_format(max(0, $billAmount),2).' BDT
                                                    <br><span style="font-size:12px;">In Word: '.ucwords(convert_number_to_words(($billAmount < 0 ? 0 : $billAmount))).' Taka</span>
                                                    
                                                    <!-- Dashed Perforation Line (আপনার ইমেজ অনুসারে) -->
                                                    <div style="margin: 14px 0 6px 0; 
                                                                border-top: 2px dashed #333; 
                                                                height: 1px; 
                                                                width: 100%;"></div>
                                                </div>
                                            </div>';

            $mpdf->WriteHTML($html);
        }
    }

    $mpdf->Output('Receipts.pdf', 'I');

} catch (Exception $e) {
    echo $e->getMessage();
}


// সংখ্যা শব্দে
function convert_number_to_words($number)
{
    $dictionary = [
        0=>'zero',1=>'one',2=>'two',3=>'three',4=>'four',5=>'five',6=>'six',
        7=>'seven',8=>'eight',9=>'nine',10=>'ten',11=>'eleven',12=>'twelve',
        13=>'thirteen',14=>'fourteen',15=>'fifteen',16=>'sixteen',17=>'seventeen',
        18=>'eighteen',19=>'nineteen',20=>'twenty',30=>'thirty',40=>'forty',
        50=>'fifty',60=>'sixty',70=>'seventy',80=>'eighty',90=>'ninety',
        100=>'hundred',1000=>'thousand',1000000=>'million'
    ];

    if ($number < 21) return $dictionary[$number];
    elseif ($number < 100) {
        $tens = ((int)($number/10))*10;
        $units = $number%10;
        return $dictionary[$tens].($units?'-'.$dictionary[$units]:'');
    }
    elseif ($number < 1000) {
        return $dictionary[(int)($number/100)].' hundred '.convert_number_to_words($number%100);
    }
    else {
        foreach([1000000,1000] as $base){
            if($number >= $base){
                return convert_number_to_words((int)($number/$base)).' '.$dictionary[$base].' '.convert_number_to_words($number%$base);
            }
        }
    }
}
?>