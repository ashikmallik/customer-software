<?php
// Include the necessary files
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
include '../model/oop.php';
$obj = new Controller();

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$currentYear = date("Y");
$currentMonth = date("m");
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token1']) ? $_GET['token1'] : NULL;
$notification = "";

// Get the on-time charges and total bill for the current month
$ontime = $obj->get_sum_by_cond("acc_amount", "tbl_account", "company_id='$token' and acc_type=4");
$total_bill = $obj->get_sum_by_cond("taka", "tbl_agent", "company_id='$token'");
$com_name = $obj->details_by_cond("tbl_company", "company_id='$token'");

// Fetch data for the current month and year
$allDuePayment = $obj->view_all_by_cond("vw_agent", "company_id='$token'");

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set document properties
$spreadsheet->getProperties()
    ->setCreator('Your Company')
    ->setLastModifiedBy('Your Company')
    ->setTitle('Company Billing Information')
    ->setSubject('Billing')
    ->setDescription('Generated billing information for ' . date('F Y'))
    ->setKeywords('billing, report, company')
    ->setCategory('Billing');

// Set the header row with styles
$sheet->setCellValue('A1', 'Sl')
    ->setCellValue('B1', 'Zone')
    ->setCellValue('C1', 'Area')
    ->setCellValue('D1', 'Client Name')
    ->setCellValue('E1', 'District')
    ->setCellValue('F1', 'Service Start Date')
    ->setCellValue('G1', 'One-time Installation')
    ->setCellValue('H1', 'Regular Bill');

// Apply header styles
$headerStyle = [
    'font' => [
        'bold' => true,
        'size' => 12,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000']
        ]
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['argb' => '4CAF50'] // Green background for headers
    ]
];

// Apply header style to the first row
$sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

// Start populating the table rows
$row_index = 2; // Start at row 2 for data
$previous_zone = '';
$previous_area = '';
$rowspan_data = [];

// Organize the data by zone and area
foreach ($allDuePayment as $key => $value) {
    $zone = $value['zone_name'];
    $area = $value['sub_zone_name'];
    $rowspan_data[$zone][$area][] = $value;
}

// Fill in the Excel data with rowspan for Zone and Area
foreach ($rowspan_data as $zone => $areas) {
    foreach ($areas as $area => $rows) {
        $zone_rowspan = count($rows); // Number of rows for this zone
        $area_rowspan = count($rows); // Number of rows for this area

        foreach ($rows as $index => $row) {
            $is_first_zone_row = $index == 0; // First row of this zone
            $is_first_area_row = $index == 0; // First row of this area

            // Fill in the row data
            $sheet->setCellValue('A' . $row_index, $row_index - 1); // Serial number
            if ($is_first_zone_row) {
                $sheet->setCellValue('B' . $row_index, $zone); // Zone name
                $sheet->getRowDimension($row_index)->setRowHeight(20); // Set row height
                $sheet->mergeCells('B' . $row_index . ':B' . ($row_index + $zone_rowspan - 1)); // Merge cells for zone
            }
            if ($is_first_area_row) {
                $sheet->setCellValue('C' . $row_index, $area); // Area name
                $sheet->mergeCells('C' . $row_index . ':C' . ($row_index + $area_rowspan - 1)); // Merge cells for area
            }
            $sheet->setCellValue('D' . $row_index, $row['link_name']); // Client Name
            $sheet->setCellValue('E' . $row_index, $row['district']); // District
            $sheet->setCellValue('F' . $row_index, $row['connection_date']); // Service Start Date
            $sheet->setCellValue('G' . $row_index, $row['connect_charge']); // One-time Installation Charge
            $sheet->setCellValue('H' . $row_index, $row['taka']); // Regular Bill

            // Apply border style to the row cells
            $sheet->getStyle('A' . $row_index . ':H' . $row_index)->applyFromArray([ 
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000']
                    ]
                ]
            ]);

            $row_index++;
        }
    }
}

// Calculate VAT, Subtotal, and Grand Total
$grand_total = $ontime + $total_bill;
$vat_percentage = $com_name['vat']; // 5% VAT
$vat_amount = ($grand_total * $vat_percentage) / 100;
$grand_total_with_vat = $grand_total + $vat_amount; // Total including VAT

// Add the total and grand total at the bottom
// Add the total and grand total at the bottom with styling
$sheet->setCellValue('G' . $row_index, 'Sub Total');
$sheet->setCellValue('H' . $row_index, $ontime);
$sheet->getStyle('G' . $row_index . ':H' . $row_index)->applyFromArray([
    'font' => [
        'bold' => true
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000']
        ]
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['argb' => '70d0e0'] // Yellow background
    ]
]);

$sheet->setCellValue('G' . ($row_index + 1), 'Total');
$sheet->setCellValue('H' . ($row_index + 1), $total_bill);
$sheet->getStyle('G' . ($row_index + 1) . ':H' . ($row_index + 1))->applyFromArray([
    'font' => [
        'bold' => true
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000']
        ]
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['argb' => '3fd91c'] // Yellow background
    ]
]);

// VAT Row
$sheet->setCellValue('G' . ($row_index + 2), 'VAT (' . number_format($vat_percentage, 2) . '%)');
$sheet->setCellValue('H' . ($row_index + 2), $vat_amount);
$sheet->getStyle('G' . ($row_index + 2) . ':H' . ($row_index + 2))->applyFromArray([
    'font' => [
        'bold' => true
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000']
        ]
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['argb' => '70d0e0'] // Yellow background
    ]
]);

// Grand Total Row
$sheet->setCellValue('G' . ($row_index + 3), 'Grand Total');
$sheet->setCellValue('H' . ($row_index + 3), $grand_total_with_vat);
$sheet->getStyle('G' . ($row_index + 3) . ':H' . ($row_index + 3))->applyFromArray([
    'font' => [
        'bold' => true
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000']
        ]
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['argb' => '3fd91c'] // Yellow background
    ]
]);

// Apply bold style for the specific rows
$sheet->getStyle('G' . $row_index . ':H' . ($row_index + 3))->applyFromArray([
    'font' => [
        'bold' => true
    ]
]);

// Set column widths for better readability
foreach (range('A', 'H') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Generate Excel file
$writer = new Xlsx($spreadsheet);

// Save the file to the server or directly download it
$filename = 'Company_Billing_Report_' . date('F_Y') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Output the file to the browser
$writer->save('php://output');
exit;
?>
