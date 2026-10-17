<?php
/**
 * Monthly Bandwidth Bill Auto Generate
 * ------------------------------------
 * For every customer that had a bandwidth bill in the previous month, this creates
 * the same bill (same products / qty / rates = same amount) for the current month
 * as UNPAID. Running it more than once in a month is safe: a customer who already
 * has a bill for the month is skipped.
 *
 * Cron (cPanel), run on the 1st of every month at 00:05:
 *   5 0 1 * * /usr/local/bin/php /home/USER/public_html/cron/generate_bandwidth_bill.php
 *
 * Generate a specific month manually (e.g. a missed month):
 *   php cron/generate_bandwidth_bill.php 2026-10
 *
 * If your host can only call a URL, use: .../cron/generate_bandwidth_bill.php?key=CRON_KEY
 */

define('CRON_KEY', 'change-this-secret-key');

$is_cli = (php_sapi_name() === 'cli');
if (!$is_cli && (!isset($_GET['key']) || $_GET['key'] !== CRON_KEY)) {
    header('HTTP/1.1 403 Forbidden');
    exit('Forbidden');
}

date_default_timezone_set('Asia/Dhaka');

include __DIR__ . '/../model/oop.php';
$obj = new Controller();

// ---- Which month to generate (default: current month) ----
$month_arg = $is_cli ? (isset($argv[1]) ? $argv[1] : '') : (isset($_GET['month']) ? $_GET['month'] : '');
if ($month_arg !== '' && !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month_arg)) {
    exit("Invalid month. Use YYYY-MM, e.g. 2026-10\n");
}
$billing_month = $month_arg !== '' ? $month_arg : date('Y-m');
$prev_month    = date('Y-m', strtotime($billing_month . '-01 -1 month'));
$bill_date     = $billing_month . '-01';

// Billing month of a bill; old manual bills may have empty billing_month, then use its date
$month_expr = "COALESCE(NULLIF(billing_month, ''), DATE_FORMAT(`date`, '%Y-%m'))";

// Same customer = same name + phone (case / space insensitive)
function bw_customer_key($name, $phone)
{
    return strtolower(trim($name)) . '|' . preg_replace('/\D/', '', $phone);
}

// Customers that already have a bill for this month -> skip them
$existing = array();
foreach ($obj->raw_sql("SELECT customer_name, customer_phone FROM bandwidth_bill WHERE $month_expr = '$billing_month'") as $row) {
    $existing[bw_customer_key($row['customer_name'], $row['customer_phone'])] = true;
}

// Previous month's bills, latest first (if a customer has more than one, the latest is used)
$prev_bills = $obj->raw_sql("SELECT * FROM bandwidth_bill WHERE $month_expr = '$prev_month' ORDER BY id DESC");

$created = 0;
$skipped = 0;
$done    = array();

foreach ($prev_bills as $bill) {
    $key = bw_customer_key($bill['customer_name'], $bill['customer_phone']);

    if (isset($existing[$key]) || isset($done[$key])) {
        $skipped++;
        continue;
    }
    $done[$key] = true;

    $items = json_decode($bill['items'], true);
    if (!is_array($items) || empty($items)) {
        $skipped++;
        continue;
    }

    $form_data = array(
        'date'             => $bill_date,
        'customer_name'    => addslashes($bill['customer_name']),
        'customer_phone'   => addslashes($bill['customer_phone']),
        'billing_month'    => $billing_month,
        'items'            => addslashes($bill['items']),
        'total_quantity'   => $bill['total_quantity'],
        'total_purchase'   => $bill['total_purchase'],
        'total_benefit'    => $bill['total_benefit'],
        'total_sale_price' => $bill['total_sale_price'],
        'partial_paid'     => 0,
        'due'              => $bill['total_sale_price'],
        'payment_status'   => 'UNPAID',
        'remarks'          => 'Auto generated from ' . $prev_month . ' bill #' . (int) $bill['id'],
    );

    if ($obj->Insert_data('bandwidth_bill', $form_data)) {
        $created++;
    }
}

$msg = date('Y-m-d H:i:s') . " | Bandwidth bill $billing_month (copied from $prev_month): created $created, skipped $skipped\n";
echo $msg;
@file_put_contents(__DIR__ . '/bandwidth_bill_cron.log', $msg, FILE_APPEND);
