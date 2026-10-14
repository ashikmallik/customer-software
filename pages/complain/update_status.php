<?php
session_start();
date_default_timezone_set('Asia/Dhaka');
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Decode JSON input
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate input
        $id = $data['id'] ?? null;
        $status = $data['status'] ?? null;

        // Additional input validation
        if (!$id || !$status) {
            throw new Exception('Invalid input: Missing ID or status');
        }

        // Prepare form_data as an associative array
        $form_data = ['status' => $status];

        // status = 3 (Solved) হলে solve_date সেট করে দাও
        if ((int)$status === 3) {
            $date_time = date('Y-m-d g:i:sA');
            $form_data['solve_date'] = date('Y-m-d H:i:s', strtotime($date_time));
        }

        // Prepare where condition as an associative array
        $where_cond = ['id' => $id];

        // Update data
        $query = $obj->updateData("tbl_complains", $form_data, $where_cond);

        // Return success response
        if ($query === true) {
            echo json_encode([
                'success' => true,
                'message' => 'Status changed successfully'
            ]);
        } else {
            throw new Exception('Update failed: No rows affected');
        }
    } catch (Exception $e) {
        // Return error response
        http_response_code(400); // Bad request
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    // Handle non-POST requests
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
}