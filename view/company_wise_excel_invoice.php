<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$obj = new Controller();
    
$allAgentData = $obj->view_all_by_cond('vw_agent', 'ag_id > 0 and ag_status=1 and taka > 0 ');
    
    


$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()
    ->setCreator('BSD')
    ->setLastModifiedBy('BSD ')
    ->setTitle('ISP')
    ->setSubject('ISP')
    ->setDescription('ISP')
    ->setKeywords('ISP')
    ->setCategory('CATEGORY-A');
    
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'client_type');
$sheet->setCellValue('B1', 'connection_type');
$sheet->setCellValue('C1', 'client_name');
$sheet->setCellValue('D1', 'bandwidth_distribution_point');
$sheet->setCellValue('E1', 'connectivity_type');
$sheet->setCellValue('F1', 'activation_date');
$sheet->setCellValue('G1', 'bandwidth_allocation');
$sheet->setCellValue('H1', 'allocated_ip');
$sheet->setCellValue('I1', 'division');
$sheet->setCellValue('J1', 'district');
$sheet->setCellValue('K1', 'thana');
$sheet->setCellValue('L1', 'address');
$sheet->setCellValue('M1', 'client_mobile');
$sheet->setCellValue('N1', 'client_email');
$sheet->setCellValue('O1', 'selling_price_bdt_excluding_vat');

$sl=1;
$cn=2;
foreach($allAgentData as $client){
$sheet->setCellValue('A'.$cn, 'Home');
$sheet->setCellValue('B'.$cn, 'Wired');
$sheet->setCellValue('C'.$cn, $client['ag_name']);
$sheet->setCellValue('D'.$cn, 'NOC');
$sheet->setCellValue('E'.$cn, 'Shared');
$sheet->setCellValue('F'.$cn, $client['connection_date']);
$sheet->setCellValue('G'.$cn, (int)$client['mb']);
$sheet->setCellValue('H'.$cn, $client['ip']);
$sheet->setCellValue('I'.$cn, 'Dhaka');
$sheet->setCellValue('J'.$cn, 'Dhaka');
$sheet->setCellValue('K'.$cn, $client['thana']);
$sheet->setCellValue('L'.$cn, $client['ag_office_address']);
$sheet->setCellValue('M'.$cn, $client['ag_mobile_no']);
$sheet->setCellValue('N'.$cn, $client['ag_email']);
$sheet->setCellValue('O'.$cn, $client['taka']);
$cn++;
}


$writer = new Xlsx($spreadsheet);
$writer->save('ISPDownStream.xlsx');

echo '<script> window.open("../excel/ISPDownStream.xlsx", "_blank") </script>';
echo '<script>window.location= "../?q=main_configuration" </script>';
