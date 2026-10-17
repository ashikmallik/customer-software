<?php

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
include '../model/oop.php';
$obj = new Controller();
$currentYear = date("Y");
$currentMonth = date("m");
$dateFrom = $_GET['datefrom'];
$dateto = $_GET['dateto'];
$token = $_GET['company_name'];

// echo $token;
// echo $dateto;
// echo $dateFrom;
// exit();

// Query to get the complaints and related data
$sql = "SELECT * FROM tbl_complains LEFT JOIN tbl_agent ON tbl_complains.customer_id = tbl_agent.ag_id WHERE tbl_complains.entry_date BETWEEN '$dateFrom' and '$dateto' or tbl_complains.company_id='$token' ORDER BY tbl_complains.id DESC";
$q = $obj->con->prepare($sql);
$q->execute();

$complain_data = $q->fetchAll(PDO::FETCH_ASSOC);

// var_dump($complain_data);
// exit();
// Export function
function exportToExcel($complain_data) {
    // Initialize PhpSpreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Table headers
    $headers = [
        'SL', 'TT Number', 'Link Name','Share With Link', 'Total Distance', 'Actual Distance',
        'Share Distance', 'Complain Details', 'Complain Time',
        'Solve Date', 'Status', 'Entry Date'
    ];

    // Set headers in the first row of the sheet
    $column = 1;
    foreach ($headers as $header) {
        $sheet->setCellValueByColumnAndRow($column++, 1, $header);
    }

    // Apply style to the header row (Row 1)
    $headerStyle = [
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4CAF50'], // Green color
        ],
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'], // White text color
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ];

    $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

    // Fill data from the $complain_data
    $row = 2; // Start from the second row (after the headers)
    $i = 1;

    foreach ($complain_data as $complain) {
        $column = 1;
        $sheet->setCellValueByColumnAndRow($column++, $row, $i++); // Serial Number
        $sheet->setCellValueByColumnAndRow($column++, $row, $complain['tt_name']);
        $sheet->setCellValueByColumnAndRow($column++, $row, $complain['link_name']);
        $sheet->setCellValueByColumnAndRow($column++, $row, $complain['share_link']);
        $sheet->setCellValueByColumnAndRow($column++, $row, $complain['total_distant']);
        $sheet->setCellValueByColumnAndRow($column++, $row, $complain['actual_distance']);
        $sheet->setCellValueByColumnAndRow($column++, $row, $complain['share_distance']);
        $sheet->setCellValueByColumnAndRow($column++, $row, $complain['details']);
        $sheet->setCellValueByColumnAndRow($column++, $row, date('d M Y h:i A', strtotime($complain['complain_date'])));
        $sheet->setCellValueByColumnAndRow($column++, $row, date('d M Y h:i A', strtotime($complain['solve_date'])));
        $sheet->setCellValueByColumnAndRow($column++, $row, $complain['status']);
        $sheet->setCellValueByColumnAndRow($column++, $row, date('d M Y', strtotime($complain['entry_date'])));

        // Apply alternate row color
        if ($row % 2 == 0) {
            $sheet->getStyle("A$row:K$row")->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F9F9F9'], // Light gray for even rows
                ],
            ]);
        }
        $row++;
    }

    // Apply borders to all rows
    $sheet->getStyle("A1:K" . ($row - 1))->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ]);

    // Auto-size columns
    foreach (range('A', 'K') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Freeze the header row
    $sheet->freezePane('A2');

    // Create Excel Writer
    $writer = new Xlsx($spreadsheet);

    // Output the Excel file directly to the browser
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="complaints_report.xlsx"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit();
}


// Trigger the export function with the data
exportToExcel($complain_data);

?>
