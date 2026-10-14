<?php
session_start();

require(realpath(__DIR__ . '/../../../services/Model.php'));
$obj = new Model();
$db = $obj->getConnection();

$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;

function purchaseResponse($success, $status, $extra = [])
{
    echo json_encode(array_merge(['success' => $success, 'status' => $status], $extra));
    exit;
}

function getPurchaseRecord(Model $obj, int $purchaseId)
{
    return $obj->rawSqlSingle("SELECT * FROM purchases WHERE purchase_id = '$purchaseId' AND deleted_at IS NULL");
}

function getStockByBatch(Model $obj, string $batchId)
{
    return $obj->rawSqlSingle("SELECT * FROM stock WHERE batch_id = '$batchId' AND deleted_at IS NULL LIMIT 1");
}

/**
 * Add Purchase -> creates a purchase row + a matching stock row (stock IN).
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['id'])) {
    $product = (int)($_POST['product'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    $supplier = (int)($_POST['supplier'] ?? 0);
    $purchaseDate = trim($_POST['purchase_date'] ?? '');
    $batchId = trim($_POST['batch_id'] ?? '');

    if ($product <= 0 || $supplier <= 0 || $quantity <= 0 || $purchaseDate === '') {
        purchaseResponse(false, 'Please fill all required purchase fields');
    }

    if ($batchId === '') {
        $batchId = 'batch_' . date('YmdHis') . '_' . random_int(1000, 9999);
    }

    $existingBatch = $obj->rawSqlSingle("SELECT stock_id FROM stock WHERE batch_id = '$batchId' AND deleted_at IS NULL");
    if ($existingBatch) {
        $batchId = 'batch_' . date('YmdHis') . '_' . random_int(1000, 9999);
    }

    try {
        $db->beginTransaction();
        $now = date('Y-m-d H:i:s');

        $purchaseStmt = $db->prepare("
            INSERT INTO purchases (product_id, supplier_id, quantity, batch_id, return_qty, purchase_date, created_by, created_at, deleted_at)
            VALUES (?, ?, ?, ?, 0, ?, ?, ?, ?)
        ");
        $purchaseStmt->execute([$product, $supplier, $quantity, $batchId, $purchaseDate, $userid, $now, null]);
        $purchaseId = (int)$db->lastInsertId();

        $stockStmt = $db->prepare("
            INSERT INTO stock (product_id, current_stock, batch_id, supplier_id, created_by, created_at, deleted_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stockStmt->execute([$product, $quantity, $batchId, $supplier, $userid, $now, null]);

        $db->commit();
        purchaseResponse(true, 'Added successfully', ['purchase_id' => $purchaseId, 'batch_id' => $batchId]);
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        purchaseResponse(false, 'Failed to add purchase');
    }
}

/**
 * Edit Purchase -> updates purchase row + keeps the linked stock row in sync (stock adjustment).
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)($_POST['id'] ?? 0);
    $product = (int)($_POST['product'] ?? 0);
    $supplier = (int)($_POST['supplier'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    $purchaseDate = trim($_POST['purchase_date'] ?? '');

    if ($id <= 0 || $product <= 0 || $supplier <= 0 || $quantity <= 0 || $purchaseDate === '') {
        purchaseResponse(false, 'Please fill all required purchase fields');
    }

    $purchase = getPurchaseRecord($obj, $id);
    if (!$purchase) {
        purchaseResponse(false, 'Purchase not found');
    }

    $returnQty = (int)($purchase['return_qty'] ?? 0);
    if ($quantity < $returnQty) {
        purchaseResponse(false, "Quantity can't be less than the already returned quantity ({$returnQty})");
    }

    $batchId = trim($purchase['batch_id'] ?? '');
    if ($batchId === '') {
        purchaseResponse(false, 'Purchase batch not found');
    }

    try {
        $db->beginTransaction();
        $now = date('Y-m-d H:i:s');

        $purchaseStmt = $db->prepare("
            UPDATE purchases
            SET product_id = ?, supplier_id = ?, quantity = ?, purchase_date = ?, updated_by = ?, updated_at = ?
            WHERE purchase_id = ?
        ");
        $purchaseStmt->execute([$product, $supplier, $quantity, $purchaseDate, $userid, $now, $id]);

        $availableStock = $quantity - $returnQty;
        $stock = getStockByBatch($obj, $batchId);

        if ($stock) {
            $stockStmt = $db->prepare("
                UPDATE stock
                SET product_id = ?, supplier_id = ?, current_stock = ?, updated_by = ?, updated_at = ?
                WHERE stock_id = ?
            ");
            $stockStmt->execute([$product, $supplier, $availableStock, $userid, $now, (int)$stock['stock_id']]);
        } else {
            $insertStockStmt = $db->prepare("
                INSERT INTO stock (product_id, current_stock, batch_id, supplier_id, created_by, created_at, deleted_at)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $insertStockStmt->execute([$product, $availableStock, $batchId, $supplier, $userid, $now, null]);
        }

        $db->commit();
        purchaseResponse(true, 'Updated successfully', ['batch_id' => $batchId]);
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        purchaseResponse(false, 'Failed to update purchase');
    }
}

/**
 * List all purchases for the DataTable.
 */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $purchases = $obj->rawSql("
        SELECT
            p.*,
            pro.product_name,
            s.supplier_name,
            _createuser.FullName
        FROM purchases p
        LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
        LEFT JOIN products pro ON p.product_id = pro.product_id
        LEFT JOIN _createuser ON p.created_by = _createuser.UserId
        WHERE p.deleted_at IS NULL
        ORDER BY p.purchase_id DESC
    ");

    $i = 1;
    foreach ($purchases as &$row) {
        $row['sl'] = $i++;
        $row['return_qty'] = (int)($row['return_qty'] ?? 0);
    }

    echo json_encode($purchases);
    exit;
}