<?php
header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 0);
error_reporting(0);

// --------- Load Model ----------
$path = realpath(__DIR__ . '/../../services/Model.php');
if (!$path) {
    echo json_encode([
        'success' => false,
        'status'  => 'Model.php not found',
        'path'    => __DIR__ . '/../../services/Model.php'
    ]);
    exit;
}

require_once $path;

if (!class_exists('Model')) {
    echo json_encode([
        'success'  => false,
        'status'   => 'Model class not found after include',
        'included' => $path
    ]);
    exit;
}

$obj = new Model();

// --------- Validate Request ----------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'status' => 'Invalid request method']);
    exit;
}

$ag_id = isset($_POST['ag_id']) ? (int)$_POST['ag_id'] : 0;
$day   = isset($_POST['day']) ? (int)$_POST['day'] : 0;

if ($ag_id <= 0) {
    echo json_encode(['success' => false, 'status' => 'Invalid ag_id']);
    exit;
}

if ($day < 1 || $day > 31) {
    echo json_encode(['success' => false, 'status' => 'Invalid day (1-31)']);
    exit;
}

// --------- Optional: Check customer exists ----------
$agent = $obj->getSingleData('tbl_agent', [
    'where' => ['ag_id', '=', $ag_id]
]);

if (!$agent) {
    echo json_encode(['success' => false, 'status' => 'Customer not found']);
    exit;
}

// --------- Update only day ----------
$updated = $obj->updateData(
    'tbl_agent',
    ['mikrotik_disconnect' => $day], // ✅ only day save
    ['ag_id' => $ag_id]
);

if ($updated) {
    echo json_encode([
        'success' => true,
        'status'  => 'success',
        'new_day' => $day
    ]);
} else {
    echo json_encode([
        'success' => false,
        'status'  => 'Update failed'
    ]);
}
exit;
