<?php
session_start();

require(realpath(__DIR__ . '/../../../services/Model.php'));
$obj = new Model();
$db = $obj->getConnection();

$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;

function respond($success, $status, $extra = [])
{
    echo json_encode(array_merge(['success' => $success, 'status' => $status], $extra));
    exit;
}

function validDateOrNull($date)
{
    if (empty($date)) return null;
    $dt = DateTime::createFromFormat('Y-m-d', $date);
    return ($dt && $dt->format('Y-m-d') === $date) ? $date : null;
}

function getColumnMaxLength($obj, $table, $column)
{
    $table = addslashes($table);
    $column = addslashes($column);
    $col = $obj->rawSqlSingle("SHOW COLUMNS FROM `$table` LIKE '$column'");
    if (!$col || empty($col['Type'])) return null;
    if (preg_match('/varchar\((\d+)\)/i', $col['Type'], $m)) return (int)$m[1];
    return null;
}

function normalizeBatchForColumn($obj, $table, $column, $batchId)
{
    $batchId = trim((string)$batchId);
    $maxLen = getColumnMaxLength($obj, $table, $column);
    if ($maxLen !== null && $maxLen > 0 && strlen($batchId) > $maxLen) {
        return substr($batchId, 0, $maxLen);
    }
    return $batchId;
}

function getSaleRecord($obj, int $saleId)
{
    return $obj->rawSqlSingle("SELECT * FROM sales WHERE sale_id = '$saleId' AND deleted_at IS NULL");
}

/**
 * Resolve customer_type + customer_id + external_customer_name from POST.
 * Returns null and calls respond() directly if validation fails.
 */
function resolveCustomerInput()
{
    $customerType = ($_POST['customer_type'] ?? 'existing') === 'external' ? 'external' : 'existing';
    $externalName = trim((string)($_POST['external_customer_name'] ?? ''));

    if ($customerType === 'external') {
        if ($externalName === '') {
            respond(false, 'Please enter external customer name');
        }
        return ['type' => 'external', 'customer_id' => null, 'external_name' => $externalName];
    }

    $customerId = (int)($_POST['customer'] ?? 0);
    if ($customerId <= 0) {
        respond(false, 'Please select a customer');
    }
    return ['type' => 'existing', 'customer_id' => $customerId, 'external_name' => null];
}

/**
 * Add Sale
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['id'])) {
    $product = (int)($_POST['product'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    $sale_date = validDateOrNull($_POST['sale_date'] ?? '');
    $batch_id = trim((string)($_POST['batch_id'] ?? ''));
    $stock_id = (int)($_POST['stock_id'] ?? 0);
    $remark = trim($_POST['remark'] ?? '');

    if ($product <= 0 || $quantity <= 0 || !$sale_date || $batch_id === '' || $stock_id <= 0) {
        respond(false, 'Please fill all required sale fields');
    }

    $customerInput = resolveCustomerInput();
    $customer = $customerInput['customer_id'];
    $customer_type = $customerInput['type'];
    $external_customer_name = $customerInput['external_name'];

    $stock = $obj->rawSqlSingle("SELECT * FROM stock WHERE stock_id = $stock_id AND product_id = $product AND deleted_at IS NULL LIMIT 1");
    if (!$stock) {
        respond(false, 'Stock not found');
    }
    
    $sourceBatch = trim((string)$stock['batch_id']);
    $storeBatch = normalizeBatchForColumn($obj, 'sales', 'batch_id', $sourceBatch);
    
    if ((int)$stock['current_stock'] < $quantity) {
        respond(false, 'Insufficient stock');
    }

    try {
        $db->beginTransaction();
        $now = date('Y-m-d H:i:s');

        $saleStmt = $db->prepare("
            INSERT INTO sales (product_id, batch_id, quantity, customer_id, customer_type, external_customer_name, stock_id, sale_date, remark, created_by, created_at, deleted_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $saleStmt->execute([
            $product, $storeBatch, $quantity, $customer, $customer_type, $external_customer_name, $stock_id,
            $sale_date, $remark, $userid, $now, null
        ]);
        $saleId = (int)$db->lastInsertId();

        $newStockQty = (int)$stock['current_stock'] - $quantity;
        $updateStock = $db->prepare("
            UPDATE stock SET current_stock = ?, updated_at = ?, updated_by = ? WHERE stock_id = ?
        ");
        $updateStock->execute([$newStockQty, $now, $userid, $stock_id]);

        $db->commit();
        respond(true, 'Added successfully', ['sale_id' => $saleId]);
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        respond(false, $e->getMessage());
    }
}

/**
 * Edit Sale
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)($_POST['id'] ?? 0);
    $batch_id = trim((string)($_POST['batch_id'] ?? ''));
    $product = (int)($_POST['product'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    $sale_date = validDateOrNull($_POST['sale_date'] ?? '');
    $stock_id = (int)($_POST['stock_id'] ?? 0);
    $remark = trim($_POST['remark'] ?? '');

    if ($id <= 0 || $product <= 0 || $quantity <= 0 || !$sale_date || $batch_id === '' || $stock_id <= 0) {
        respond(false, 'Please fill all required fields');
    }

    $customerInput = resolveCustomerInput();
    $customer = $customerInput['customer_id'];
    $customer_type = $customerInput['type'];
    $external_customer_name = $customerInput['external_name'];

    $oldSale = getSaleRecord($obj, $id);
    if (!$oldSale) {
        respond(false, 'Sale not found');
    }

    $returnQty = (int)$oldSale['return_qty'];
    if ($returnQty > 0) {
        respond(false, "Cannot edit sale after return has started ({$returnQty} returned)");
    }

    $oldStock = $obj->rawSqlSingle("SELECT * FROM stock WHERE stock_id = " . (int)$oldSale['stock_id'] . " AND deleted_at IS NULL LIMIT 1");
    $newStock = $obj->rawSqlSingle("SELECT * FROM stock WHERE stock_id = $stock_id AND product_id = $product AND deleted_at IS NULL LIMIT 1");
    if (!$oldStock || !$newStock) {
        respond(false, 'Stock record missing');
    }
    
    $sourceBatch = trim((string)$newStock['batch_id']);
    $storeBatch = normalizeBatchForColumn($obj, 'sales', 'batch_id', $sourceBatch);

    try {
        $db->beginTransaction();
        $now = date('Y-m-d H:i:s');

        // Restore old stock
        $oldStockQty = (int)$oldStock['current_stock'] + (int)$oldSale['quantity'];
        $restoreStmt = $db->prepare("UPDATE stock SET current_stock = ?, updated_at = ?, updated_by = ? WHERE stock_id = ?");
        $restoreStmt->execute([$oldStockQty, $now, $userid, $oldStock['stock_id']]);

        // Deduct from new stock
        $effectiveNewStock = ((int)$oldStock['stock_id'] === $stock_id) ? $oldStockQty : (int)$newStock['current_stock'];
        $newFinalQty = $effectiveNewStock - $quantity;
        if ($newFinalQty < 0) {
            throw new Exception('Insufficient stock for update');
        }
        $deductStmt = $db->prepare("UPDATE stock SET current_stock = ?, updated_at = ?, updated_by = ? WHERE stock_id = ?");
        $deductStmt->execute([$newFinalQty, $now, $userid, $stock_id]);

        // Update sale
        $updateStmt = $db->prepare("
            UPDATE sales SET 
                product_id = ?, quantity = ?, batch_id = ?, 
                stock_id = ?, customer_id = ?, customer_type = ?, external_customer_name = ?,
                sale_date = ?, remark = ?, 
                updated_by = ?, updated_at = ? 
            WHERE sale_id = ?
        ");
        $updateStmt->execute([
            $product, $quantity, $storeBatch, $stock_id, $customer, $customer_type, $external_customer_name,
            $sale_date, $remark, $userid, $now, $id
        ]);

        $db->commit();
        respond(true, 'Updated successfully');
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        respond(false, $e->getMessage());
    }
}

/**
 * GET - List all sales for DataTable
 */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sales = $obj->raw_sql("
        SELECT s.*,
            p.product_name,
            CASE WHEN s.customer_type = 'external' THEN s.external_customer_name ELSE ag.ag_name END AS ag_name,
            _createuser.FullName
        FROM sales s
        LEFT JOIN products p ON s.product_id = p.product_id
        LEFT JOIN _createuser ON s.created_by = _createuser.UserId
        LEFT JOIN tbl_agent ag ON s.customer_id = ag.ag_id
        WHERE s.deleted_at IS NULL AND s.quantity > 0
        ORDER BY s.sale_id DESC
    ");

    $i = 1;
    foreach ($sales as &$row) {
        $row['sl'] = $i++;
        $row['return_qty'] = (int)($row['return_qty'] ?? 0);
    }

    echo json_encode($sales);
    exit;
}